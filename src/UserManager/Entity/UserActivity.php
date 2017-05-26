<?php

namespace App\Mepatek\UserManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="UsersActivity",
 *     indexes={
 *     @ORM\Index(name="IDX_Deleted", columns={"Deleted"}),
 *     @ORM\Index(name="IDX_UserName", columns={"UserName"}),
 *     @ORM\Index(name="IDX_PwToken", columns={"PwToken"}),
 *     @ORM\Index(name="IDX_Disabled", columns={"Disabled"}),
 * })
 *
 * @package Mepatek\UserManager\Entity
 */
class UserActivity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="UserActivityID")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id;
	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="UserID", referencedColumnName="UserID")
	 * @var User
	 */
	protected $user;
	/**
	 * @ORM\Column(type="string", length=50, name="IP", nullable=true)
	 * @var string
	 */
	protected $ip;
	/**
	 * @ORM\Column(type="string", length=30, name="ActivityType")
	 * @var string
	 */
	protected $type;
	/**
	 * @ORM\Column(type="datetime", name="ActivityDateTime")
	 * @var \DateTime
	 */
	protected $datetime;
	/**
	 * @ORM\Column(type="text", name="Description", nullable=true)
	 * @var string
	 */
	protected $description;

	/**
	 * UserActivity constructor.
	 */
	public function __construct()
	{
		$this->datetime = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getIp(): string
	{
		return $this->ip;
	}

	/**
	 * @param string $ip
	 */
	public function setIp(string $ip)
	{
		$this->ip = $ip;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @return \DateTime
	 */
	public function getDatetime(): \DateTime
	{
		return $this->datetime;
	}

	/**
	 * @param \DateTime $datetime
	 */
	public function setDatetime(\DateTime $datetime)
	{
		$this->datetime = $datetime;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;
	}


}
