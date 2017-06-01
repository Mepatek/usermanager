<?php

namespace App\Mepatek\UserManager\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;


/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Users",
 *     indexes={
 *     @ORM\Index(name="IDX_Deleted", columns={"Deleted"}),
 *     @ORM\Index(name="IDX_UserName", columns={"UserName"}),
 *     @ORM\Index(name="IDX_PwToken", columns={"PwToken"}),
 *     @ORM\Index(name="IDX_Disabled", columns={"Disabled"}),
 * })
 *
 * @package Mepatek\UserManager\Entity
 */
class User
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="UserID")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id = null;
	/**
	 * @ORM\Column(type="string", length=255, name="FullName", nullable=true)
	 * @var string
	 */
	protected $fullName;
	/**
	 * @ORM\Column(type="string", length=50, name="UserName")
	 * @var string
	 */
	protected $userName;
	/**
	 * @ORM\Column(type="string", length=200, name="PwHash")
	 * @var string
	 */
	protected $pwHash = "";
	/**
	 * @ORM\Column(type="string", length=255, name="Email")
	 * @var string
	 */
	protected $email;
	/**
	 * @ORM\Column(type="string", length=255, name="Phone", nullable=true)
	 * @var string
	 */
	protected $phone;
	/**
	 * @ORM\Column(type="string", length=100, name="Title", nullable=true)
	 * @var string
	 */
	protected $title;
	/**
	 * @ORM\Column(type="string", length=10, name="Language", nullable=true)
	 * @var string
	 */
	protected $language;
	/**
	 * @ORM\Column(type="blob", name="Thumbnail", nullable=true)
	 * encoded image for atribute src
	 * @var string
	 */
	protected $thumbnail;
	/**
	 * @ORM\Column(type="string", length=255, name="PwToken", nullable=true)
	 * @var string
	 */
	protected $pwToken;
	/**
	 * @ORM\Column(type="datetime", name="PwTokenExpire", nullable=true)
	 * @var \DateTime
	 */
	protected $pwTokenExpire;
	/**
	 * @ORM\Column(type="datetime", name="Created", nullable=true)
	 * @var \DateTime
	 */
	protected $created;
	/**
	 * @ORM\Column(type="datetime", name="LastLogged", nullable=true)
	 * @var \DateTime
	 */
	protected $lastLogged;
	/**
	 * @ORM\Column(type="smallint", name="Disabled", nullable=true)
	 * @var boolean
	 */
	protected $disabled = false;
	/**
	 * @ORM\Column(type="smallint", name="Deleted")
	 * @var boolean
	 */
	protected $deleted = false;
	/**
	 * @ORM\ManyToMany(targetEntity="Role")
	 * @ORM\JoinTable(name="UsersRoles",
	 *      joinColumns={@ORM\JoinColumn(name="UserID", referencedColumnName="UserID")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="Role", referencedColumnName="Role")}
	 *      )
	 * @var Role[]
	 */
	protected $roles;

	/**
	 * @ORM\OneToMany(targetEntity="AuthDriver", mappedBy="user")
	 * @var AuthDriver[]
	 */
	protected $authDrivers = [];

	/** @var string */
	protected $authMethod;


	public function __construct() {
		$this->roles = new ArrayCollection();
		$this->authDrivers = new ArrayCollection();
		$this->created = new \DateTime();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->fullName;
	}

	/**
	 * @param string $fullName
	 */
	public function setFullName($fullName)
	{
		$this->fullName = $fullName;
	}

	/**
	 * @return string
	 */
	public function getUserName()
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return string
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @param string $language
	 */
	public function setLanguage($language)
	{
		$this->language = $language;
	}

	/**
	 * @return string
	 */
	public function getThumbnail()
	{
//		bdump($this->thumbnail);
		if (is_resource($this->thumbnail)) {
			return stream_get_contents($this->thumbnail);
		}
		return $this->thumbnail;
	}

	/**
	 * @param string $thumbnail
	 */
	public function setThumbnail($thumbnail)
	{
		$this->thumbnail = $thumbnail;
	}

	/**
	 * @return boolean
	 */
	public function hasThumbnail()
	{
		return (boolean)$this->thumbnail;
	}

	/**
	 * @param string $newPwHash
	 */
	public function changePassword($newPwHash)
	{
		$this->pwHash = $newPwHash;
	}

	/**
	 * @return string
	 */
	public function getPwHash()
	{
		return $this->pwHash;
	}


	/**
	 * @param \DateInterval $interval
	 * @return string
	 */
	public function resetPwToken(\DateInterval $interval)
	{
		$dateTokenExpire = new \DateTime();
		$dateTokenExpire->add($interval);

		$this->pwToken = md5(md5(uniqid(rand(), true)));
		$this->pwTokenExpire = $dateTokenExpire;
		return $this->pwToken;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param \DateTime $created
	 */
	public function setCreated($created)
	{
		$this->created = $created;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastLogged()
	{
		return $this->lastLogged;
	}

	/**
	 * @param \DateTime $lastLogged
	 */
	public function setLastLogged($lastLogged)
	{
		$this->lastLogged = $lastLogged;
	}

	/**
	 * @return bool
	 */
	public function isDisabled()
	{
		return $this->disabled;
	}

	/**
	 * @param bool $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = $disabled;
	}

	/**
	 * @return bool
	 */
	public function isDeleted()
	{
		return $this->deleted;
	}

	/**
	 * @param bool $deleted
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted;
	}

	/**
	 * @return Role[]
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param Role[] $roles
	 */
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}

	/**
	 * @return AuthDriver[]
	 */
	public function getAuthDrivers()
	{
		return $this->authDrivers;
	}

	/**
	 * @param AuthDriver[] $authDrivers
	 */
	public function setAuthDrivers($authDrivers)
	{
		$this->authDrivers = $authDrivers;
	}

	/**
	 * @return string
	 */
	public function getAuthMethod()
	{
		return $this->authMethod;
	}

	/**
	 * @param string $authMethod
	 */
	public function setAuthMethod($authMethod)
	{
		$this->authMethod = $authMethod;
	}

	/**
	 * @return array
	 */
	public function getIdentityRoles()
	{
		$roles = [];
		foreach ($this->roles as $role) {
			$roles[] = $role->getRole();
		}
		return $roles;
	}


	const PROPERTIES_FOR_IDENTITY = "fullName,userName,email,phone,title,language,thumbnail";

	/**
	 * @return array
	 */
	public function getIdentityData()
	{
		$identityData = [];
		foreach (explode(",", self::PROPERTIES_FOR_IDENTITY) as $property) {
			$getter = "get" . Strings::firstUpper($property);
			$identityData[$property] = $this->$getter();
		}
		return $identityData;
	}

}
