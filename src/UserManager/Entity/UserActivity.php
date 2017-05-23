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
	 * @ORM\ManyToOne(targetEntity="Address")
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
	 * @ORM\Column(type="datetime", name="RoleName")
	 * @var \DateTime
	 */
	protected $datetime;
	/**
	 * @ORM\Column(type="text", name="Description", nullable=true)
	 * @var string
	 */
	protected $description;


}
