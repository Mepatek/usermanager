<?php

namespace Mepatek\UserManager\Repository;

use Mepatek\Repository\AbstractRepository;

use Mepatek\Mapper\IMapper,
	Mepatek\UserManager\Entity\Acl;

/**
 * Class AclRepository
 * @package Mepatek\UserManager\Repository
 */
class AclRepository extends AbstractRepository
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
	 * @param Acl $item
	 *
	 * @return boolean
	 */
	public function save(Acl &$item)
	{
		return $this->mapper->save($item);
	}


	/**
	 * Delete Acl
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
	 * @return Acl
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
	 * @return Acl
	 */
	public function findOneBy(array $values, $order = null)
	{
		return $this->mapper->findOneBy($values, $order);
	}

}
