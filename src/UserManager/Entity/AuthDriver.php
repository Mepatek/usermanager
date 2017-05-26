<?php

namespace App\Mepatek\UserManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UsersAuthDrivers",
 *     indexes={
 *     @ORM\Index(name="IDX_AuthID", columns={"AuthID"}),
 * })
 *
 * @package Mepatek\UserManager\Entity
 */
class AuthDriver
{

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authDrivers")
	 * @ORM\JoinColumn(name="UserID", referencedColumnName="UserID")
	 */
	private $user;
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=30, name="AuthDriver")
	 * @var string
	 */
	private $authDriver;
	/**
	 * @ORM\Column(type="string", length=255, name="AuthID")
	 * @var string
	 */
	private $authId;

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getAuthDriver()
	{
		return $this->authDriver;
	}

	/**
	 * @param string $authDriver
	 */
	public function setAuthDriver($authDriver)
	{
		$this->authDriver = $authDriver;
	}

	/**
	 * @return string
	 */
	public function getAuthId()
	{
		return $this->authId;
	}

	/**
	 * @param string $authId
	 */
	public function setAuthId($authId)
	{
		$this->authId = $authId;
	}

}
