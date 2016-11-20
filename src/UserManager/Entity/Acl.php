<?php
namespace Mepatek\UserManager\Entity;

use Mepatek\Entity\AbstractEntity;

/**
 * Class Acl
 * @package Mepatek\UserManager\Entity
 */
class Acl extends AbstractEntity
{
	/** @var int primary */
	protected $id = null;
	/** @var string 30 */
	protected $role;
	/** @var string 100 */
	protected $resource;
	/** @var array|null */
	protected $allow = null;
	/** @var array|null */
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
		if ($this->id === null) {
			$this->id = $id;
		}
	}

	/**
	 * @return string
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param string $role
	 */
	public function setRole($role)
	{
		$this->role = $this->StringTruncate($role, 30);
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
		$this->resource = $this->StringTruncate($resource, 100);
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
