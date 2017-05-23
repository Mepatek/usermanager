<?php

namespace App\Mepatek\UserManager\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="Roles",
 *     indexes={
 *     @ORM\Index(name="IDX_Deleted", columns={"Deleted"}),
 * })
 *
 * @package Mepatek\UserManager\Entity
 */
class Role
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=30, name="Role")
	 * @var string
	 */
	protected $role;
	/**
	 * @ORM\Column(type="string", length=100, name="RoleName", nullable=true)
	 * @var string
	 */
	protected $name;
	/**
	 * @ORM\Column(type="text", name="Description", nullable=true)
	 * @var string
	 */
	protected $description;
	/**
	 * @ORM\Column(type="smallint", name="Deleted")
	 * @var boolean
	 */
	protected $deleted = 0;

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
		$this->role = $role;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
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

}
