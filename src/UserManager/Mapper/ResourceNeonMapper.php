<?php

namespace Mepatek\UserManager\Mapper;

use Mepatek\Mapper\AbstractNeonMapper;
use Mepatek\Mapper\IMapper;

use Mepatek\UserManager\Entity\ResourceObject;
use Nette\Caching\IStorage;
use Nette\Neon\Neon;

/**
 * Class ResourceNeonMapper
 * @package Mepatek\UserManager\Mapper
 */
class ResourceNeonMapper extends AbstractNeonMapper implements IMapper
{
	/**
	 * ResourceNeonMapper constructor.
	 *
	 * @param string   $neonFile
	 * @param Neon     $neon
	 * @param IStorage $storage
	 */
	public function __construct($neonFile, Neon $neon, IStorage $storage)
	{
		$this->neonFile = $neonFile;
		$this->neon = $neon;
		$this->storage = $storage;
		$this->objectClass = "Mepatek\\UserManager\\Entity\\ResourceObject";
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
	 * @return ResourceObject
	 */
	public function find($id)
	{
		return parent::find($id);
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return ResourceObject
	 */
	public function findOneBy(array $values, $order = null)
	{
		return parent::find($values, $order);
	}

}
