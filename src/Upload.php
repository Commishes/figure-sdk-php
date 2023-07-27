<?php namespace commishes\figureSdk;

class Upload
{
	private int $id;
	private string $secret;
	
	private string $lqip;
	private string $contentType;
	private string $md5;
	
	private bool $animated;
	private int $length;
	
	public function __construct(int $id, string $secret, string $lqip, string $contentType, bool $animated, string $md5, int $length)
	{
		$this->id = $id;
		$this->secret = $secret;
		$this->lqip = $lqip;
		$this->animated = $animated;
		$this->contentType = $contentType;
		$this->md5 = $md5;
		$this->length = $length;
	}

	/**
	 * Get the value of id
	 *
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * Get the value of secret
	 *
	 * @return string
	 */
	public function getSecret(): string
	{
		return $this->secret;
	}

	/**
	 * Get the value of lqip
	 *
	 * @return string
	 */
	public function getLqip(): string
	{
		return $this->lqip;
	}

	/**
	 * Get the value of contentType
	 *
	 * @return string
	 */
	public function getContentType(): string
	{
		return $this->contentType;
	}

	/**
	 * Get the value of md5
	 *
	 * @return string
	 */
	public function getMd5(): string
	{
		return $this->md5;
	}

	/**
	 * Get the value of length
	 *
	 * @return int
	 */
	public function getLength(): int
	{
		return $this->length;
	}

	/**
	 * Get the value of animated
	 *
	 * @return bool
	 */
	public function getAnimated(): bool
	{
		return $this->animated;
	}
}
