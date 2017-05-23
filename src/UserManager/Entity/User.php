<?php
namespace Mepatek\UserManager\Entity;

use Mepatek\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM;


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
class User extends AbstractEntity
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="UserID")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id = null;
	/**
	 * @ORM\Column(type="string", length=255, name="FullName")
	 * @var string
	 */
	protected $fullName;
	/**
	 * @ORM\Column(type="string", length=50, name="UserName")
	 * @var string
	 */
	protected $userName;
	/**
	 * @ORM\Column(type="string", length=255, name="Email")
	 * @var string
	 */
	protected $email;
	/**
	 * @ORM\Column(type="string", length=255, name="Phone")
	 * @var string
	 */
	protected $phone;
	/**
	 * @ORM\Column(type="string", length=100, name="Title")
	 * @var string
	 */
	protected $title;
	/**
	 * @ORM\Column(type="string", length=10, name="Language")
	 * @var string
	 */
	protected $language;
	/**
	 * @ORM\Column(type="text", name="Thumbnail")
	 * encoded image for atribute src
	 * @var string
	 */
	protected $thumbnail;
	/**
	 * @ORM\Column(type="datetime", name="Created")
	 * @var \DateTime
	 */
	protected $created;
	/**
	 * @ORM\Column(type="datetime", name="LastLogged")
	 * @var \DateTime
	 */
	protected $lastLogged;
	/**
	 * @ORM\Column(type="smallint", name="Disabled")
	 * @var boolean
	 */
	protected $disabled = false;
	/**
	 * @ORM\Column(type="smallint", name="Deleted")
	 * @var boolean
	 */
	protected $deleted = false;

	/** @var array */
	protected $roles = [];

	/** @var array authDriverName=>authId */
	protected $authDrivers = [];

	/** @var string */
	protected $authMethod;

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
		// ONLY if id is not set
		if (!$this->id) {
			$this->id = (int)$id;
		}
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
		$this->fullName = $this->StringTruncate($fullName, 255);
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
		$this->userName = $this->StringTruncate($userName, 50);
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
		$this->email = $this->StringTruncate($email);
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
		$this->phone = $this->StringTruncate($phone, 255);
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
		$this->title = $this->StringTruncate($title, 100);
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
		$this->language = $this->StringTruncate($language, 10);
	}

	/**
	 * @return string
	 */
	public function getThumbnail()
	{
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
	 * @return \Nette\Utils\DateTime
	 */
	public function getCreated()
	{
		return $this->created;
	}

	/**
	 * @param \Nette\Utils\DateTime $created
	 */
	public function setCreated($created)
	{
		$this->created = $this->DateTime($created);
	}

	/**
	 * @return \Nette\Utils\DateTime
	 */
	public function getLastLogged()
	{
		return $this->lastLogged;
	}

	/**
	 * @param \Nette\Utils\DateTime $lastLogged
	 */
	public function setLastLogged($lastLogged)
	{
		$this->lastLogged = $this->DateTime($lastLogged);
	}

	/**
	 * @return boolean
	 */
	public function getDisabled()
	{
		return $this->disabled ? true : false;
	}

	/**
	 * @param boolean $disabled
	 */
	public function setDisabled($disabled)
	{
		$this->disabled = $disabled ? true : false;
	}

	/**
	 * @return boolean
	 */
	public function getDeleted()
	{
		return $this->deleted ? true : false;
	}

	/**
	 * @param boolean $deleted
	 */
	public function setDeleted($deleted)
	{
		$this->deleted = $deleted ? true : false;
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
	 * Get role array
	 * @return Role[]
	 */
	public function getRoles()
	{
		return array_values($this->roles);
	}

	/**
	 * @param array $roles
	 */
	public function setRoles(array $roles)
	{
		$this->deleteAllRoles();
		foreach ($roles as $role) {
			$this->addRole($role);
		}
	}

	/**
	 * delete all roles
	 */
	public function deleteAllRoles()
	{
		$this->roles = [];
	}

	/**
	 * Add role
	 *
	 * @param string $role
	 */
	public function addRole($role)
	{
		$this->roles[$role] = $role;
	}

	/**
	 * Delete role
	 *
	 * @param string $role
	 */
	public function deleteRole($role)
	{
		if (isset($this->roles[$role])) {
			unset ($this->roles[$role]);
		}
	}

	/**
	 * Get authDrivers with ID
	 * @return array
	 */
	public function getAuthDrivers()
	{
		return $this->authDrivers;
	}

	/**
	 * @param array $authDrivers
	 */
	public function setAuthDrivers(array $authDrivers)
	{
		$this->deleteAllAuthDrivers();
		foreach ($authDrivers as $authDriver => $authId) {
			$this->addAuthDriver($authDriver, $authId);
		}
	}

	/**
	 * delete all authDrivers
	 */
	public function deleteAllAuthDrivers()
	{
		$this->authDrivers = [];
	}

	/**
	 * Add authDriver
	 *
	 * @param string $authDriver
	 * @param string $authId
	 */
	public function addAuthDriver($authDriver, $authId)
	{
		$this->authDrivers[$authDriver] = $authId;
	}

	/**
	 * Delete authDriver
	 *
	 * @param string $authDriver
	 */
	public function deleteAuthDriver($authDriver)
	{
		if (isset($this->authDrivers[$authDriver])) {
			unset ($this->authDrivers[$authDriver]);
		}
	}

}
