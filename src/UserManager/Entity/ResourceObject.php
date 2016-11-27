<?php
namespace Mepatek\UserManager\Entity;

use Mepatek\Entity\AbstractEntity;

/**
 * Class ResourceObject
 * @package Mepatek\UserManager\Entity
 */
class ResourceObject extends AbstractEntity
{
	/** @var string 100 */
	protected $resource=null;
	/** @var string 100 */
	protected $title;
	/** @var string */
	protected $description;
	/** @var array privilege=>description */
	protected $privileges = [];

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->resource;
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->setResource($id);
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
		if ($this->resource==null) {
			$this->resource = $this->StringTruncate($resource, 100);
		}
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
	 * @return array
	 */
	public function getPrivileges()
	{
		return $this->privileges;
	}

	/**
	 * @param array $privileges
	 */
	public function setPrivileges(array $privileges)
	{
		$this->privileges = $privileges;
	}

	/**
	 * Is privilege in privileges?
	 *
	 * @param string $privilege
	 *
	 * @return bool
	 */
	public function isInPrivileges($privilege)
	{
		return isset($this->privileges[$privilege]);
	}


}
