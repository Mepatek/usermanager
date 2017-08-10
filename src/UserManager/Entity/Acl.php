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
	 * @ORM\Column(type="string", length=255, name="Allowed", nullable=true)
	 * @var array|null
	 */
	protected $allowed = null;
	/**
	 * @ORM\Column(type="string", length=255, name="Denied", nullable=true)
	 * @var array|null
	 */
	protected $denied = null;

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
	public function getAllowed()
	{
		return is_array($this->allowed) ? join(",", $this->allowed) : $this->allowed;
	}

	/**
	 * @return array|null
	 */
	public function getAllowArray()
	{
		if (is_array($this->allowed)) {
			return $this->allowed;
		}
		return explode(",", trim($this->allowed));
	}

	/**
	 * @param array|string|null $allowed
	 */
	public function setAllowed($allowed)
	{
		if (is_string($allowed)) {
			$this->allowed = $allowed;
		}
		if (is_array($allowed) and count($allowed) > 0) {
			$this->allowed = join(",", $allowed);
		} else {
			$this->allowed = null;
		}
	}

	/**
	 * @return string|null
	 */
	public function getDenied()
	{
		return is_array($this->denied) ? join(",", $this->denied) : $this->denied;
	}

	/**
	 * @return array|null
	 */
	public function getDenyArray()
	{
		if (is_array($this->denied)) {
			return $this->denied;
		}
		return explode(",", trim($this->denied));
	}

	/**
	 * @param array|string|null $denied
	 */
	public function setDenied($denied)
	{
		if (is_string($denied)) {
			$this->denied = $denied;
		}
		if (is_array($denied) and count($denied) > 0) {
			$this->denied = join(",", $denied);
		} else {
			$this->denied = null;
		}
	}

	/**
	 * @param string $privilege
	 */
	public function allow($privilege)
	{
		$denied = array_flip($this->getDenyArray());
		$allowed = array_flip($this->getAllowArray());

		if (isset($denied[$privilege])) {
			unset($denied[$privilege]);
		}
		$allowed[$privilege] = true;

		if (isset($allowed[""])) {
			unset($allowed[""]);
		}
		if (isset($denied[""])) {
			unset($denied[""]);
		}

		$this->setDenied(array_keys($denied));
		$this->setAllowed(array_keys($allowed));
	}

	/**
	 * @param string $privilege
	 */
	public function deny($privilege)
	{
		$denied = array_flip($this->getDenyArray());
		$allowed = array_flip($this->getAllowArray());

		if (isset($allowed[$privilege])) {
			unset($allowed[$privilege]);
		}
		$denied[$privilege] = true;

		if (isset($allowed[""])) {
			unset($allowed[""]);
		}
		if (isset($denied[""])) {
			unset($denied[""]);
		}

		$this->setDenied(array_keys($denied));
		$this->setAllowed(array_keys($allowed));
	}
}
