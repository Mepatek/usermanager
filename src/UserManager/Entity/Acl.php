<?php

namespace App\Mepatek\UserManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="RolesAcl",
 *     indexes={
 *     @ORM\Index(name="IDX_RolesAcl_Resource", columns={"Resource"}),
 * })
 *
 * @package Mepatek\UserManager\Entity
 */
class Acl
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer", name="AclID")
	 * @ORM\GeneratedValue
	 * @var integer
	 */
	protected $id = null;
	/**
	 * @ORM\ManyToOne(targetEntity="Role")
	 * @ORM\JoinColumn(name="Role", referencedColumnName="Role")
	 * @var Role
	 */
	protected $role;
	/**
	 * @ORM\Column(type="string", length=100, name="Resource")
	 * @var string
	 */
	protected $resource;
	/**
	 * @ORM\Column(type="string", length=255, name="[Allow]", nullable=true)
	 * @var array|null
	 */
	protected $allow = null;
	/**
	 * @ORM\Column(type="string", length=255, name="[Deny]", nullable=true)
	 * @var array|null
	 */
	protected $deny = null;

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
	 * @return Role
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param Role $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}

	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @param string $resource
	 */
	public function setResource($resource)
	{
		$this->resource = $resource;
	}


	/**
	 * @return string|null
	 */
	public function getAllow()
	{
		return is_array($this->allow) ? join(",", $this->allow) : $this->allow;
	}

	/**
	 * @return array|null
	 */
	public function getAllowArray()
	{
		return $this->allow;
	}

	/**
	 * @param array|string|null $allow
	 */
	public function setAllow($allow)
	{
		if (is_string($allow)) {
			$allow = explode(",", $allow);
		}
		if (is_array($allow) and count($allow) > 0) {
			$this->allow = $allow;
		} else {
			$this->allow = null;
		}
	}

	/**
	 * @return string|null
	 */
	public function getDeny()
	{
		return is_array($this->deny) ? join(",", $this->deny) : $this->deny;
	}

	/**
	 * @return array|null
	 */
	public function getDenyArray()
	{
		return $this->deny;
	}

	/**
	 * @param array|string|null $deny
	 */
	public function setDeny($deny)
	{
		if (is_string($deny)) {
			$deny = explode(",", $deny);
		}
		if (is_array($deny) and count($deny) > 0) {
			$this->deny = $deny;
		} else {
			$this->deny = null;
		}
	}

}
