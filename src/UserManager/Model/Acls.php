<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 06.08.2017
 * Time: 15:55
 */

namespace Mepatek\UserManager\Model;


use Doctrine\ORM\EntityManager;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class Acls
{

	/** @var EntityManager */
	private $em;
	/** @var Cache */
	private $cache;


	const CACHE_TAG_ALCS = "RolesList";

	public function __construct(
		EntityManager $em,
		IStorage $storage
	) {
		$this->em = $em;
		$this->cache = new Cache($storage, "Usermanager.Acls");
	}

}
