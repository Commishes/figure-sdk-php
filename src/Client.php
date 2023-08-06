<?php namespace commishes\figureSdk;

use Exception;
use GraphQL\Client as GraphQLClient;
use GraphQL\Mutation;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Utils;
use League\Glide\Signatures\Signature;
use League\Glide\Urls\UrlBuilder;

class Client
{
	
	private string $salt;
	private string $url;
	private string $apitoken;
	private GraphQLClient $client;
	
	public function __construct(string $url, ?string $apitoken, ?string $salt = null)
	{
		$this->url = $url;
		$this->apitoken = $apitoken;
		$this->salt     = $salt?: bin2hex(random_bytes(10));
		
		$this->client = new GraphQLClient(
			$url . 'api/v1',
			$apitoken? ['Authorization' => sprintf('Bearer %s', $apitoken)] : []
		);
	}
	
	public function upload(string $filename) : Upload
	{
		$httpClient = new GuzzleHttpClient([
			'base_uri' => $this->url
		]);
		
		$response = $httpClient->post('/api/v1/upload.json', [
			'query' => [
				'XDEBUG_SESSION' => 'session_name'
			],
			'multipart' => [
				[
					'name'     => 'upload',
					'contents' => Utils::tryFopen($filename, 'r'),
					'headers'  => ['Content-Type' => mime_content_type($filename)]
				],
			]
		]);
		
		$json = json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
		
		return new Upload(
			$json->id,
			$json->secret,
			$json->lqip,
			$json->meta->contentType,
			$json->meta->animated,
			$json->meta->md5,
			$json->meta->length
		);
	}
	
	public function claim(int $id, string $secret, string $blame = '') : bool
	{
		$gql = (new Mutation('claimUpload'))
			->setSelectionSet(['id'])
			->setArguments([
				'id' => $id,
				'secret' => $secret,
				'blame'  => $blame
			]);
		
		try {
			$res = $this->client->runQuery($gql);
			
		}
		catch(ServerException $e) {
			var_dump($e);
			var_dump($e->getRequest());
			var_dump($e->getResponse()->getBody());
		}
		return true;
	}
	
	public function delete(int $id) : bool
	{
		$gql = (new Mutation('deleteUpload'))
			->setArguments([
				'id' => $id
			]);
		
		try {
			$res = $this->client->runQuery($gql);
			
		}
		catch(ServerException $e) {
			var_dump($e->getRequest());
			echo($e->getResponse()->getBody()->getContents());
		}
		return true;
	}
	
	public function url(int $id, array $options = [], ?int $ttl = null): string
	{
		
		if ($ttl === null) { $expires = null; }
		elseif ($ttl > 50 * 365 * 86400) { $expires = $ttl; }
		else { $expires = time() + $ttl; }
		
		$builder = new UrlBuilder('/image/', new Signature($this->apitoken));
		
		return rtrim($this->url, '/') . $builder->getUrl(
			sprintf('%d/%s/%s', $id, $expires === null? 'never' : $expires, $this->salt), 
			$options
		);
	}
	
	public function video(int $id, array $options = [], ?int $ttl = null): string
	{
		
		if ($ttl === null) { $expires = null; }
		elseif ($ttl > 50 * 365 * 86400) { $expires = $ttl; }
		else { $expires = time() + $ttl; }
		
		$builder = new UrlBuilder('/video/', new Signature($this->apitoken));
		
		return rtrim($this->url, '/') . $builder->getUrl(
			sprintf('%d/%s/%s', $id, $expires === null? 'never' : $expires, $this->salt), 
			$options
		);
	}
}
