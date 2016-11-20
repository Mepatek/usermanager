<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\Repository\AbstractRepository;

use Mepatek\Mapper\IMapper,
	Mepatek\UserManager\Entity\ResourceObject;

/**
 * Class ResourceRepository
 * @package Mepatek\UserManager\Repository
 */
class ResourceRepository extends AbstractRepository
{

	/**
	 * Constructor
	 *
	 * @param IMapper $mapper
	 */
	public function __construct(IMapper $mapper)
	{
		$this->mapper = $mapper;
	}

	/**
	 * Save
	 *
	 * @param ResourceObject $item
	 *
	 * @return boolean
	 */
	public function save(ResourceObject &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete ResourceObject
	 *
	 * @param string $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		return $this->mapper->delete((string)$id);
	}


	/**
	 * Find by id
	 *
	 * @param string $id
	 *
	 * @return ResourceObject
	 */
	public function find($id)
	{
		return $this->mapper->find((string)$id);
	}

	/**
	 * Find first item by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return ResourceObject
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}

}
