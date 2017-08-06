<?php

namespace Mepatek\UserManager\Model;


use App\Mepatek\UserManager\Entity\Role;
use Kdyby\Doctrine\EntityManager;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class Roles
{

	/** @var EntityManager */
	private $em;
	/** @var Cache */
	private $cache = null;


	public function __construct(
		EntityManager $em,
		IStorage $storage = null
	) {
		$this->em = $em;
		if ($storage) {
			$this->cache = new Cache($storage, "Usermanager.Roles");
		}
	}

	/**
	 * Cached list of roles
	 * @return Role[]
	 */
	public function getCachedRoles()
	{
		$roles = $this->cache->load("Roles list");
		if (!$roles) {
			$roles = $this->getRoles();
			$this->cache->save(
				"Roles list",
				$roles,
				[
					Cache::EXPIRE  => "1 month",
					Cache::SLIDING => true,
					Cache::TAGS    => "rolesList",
				]
			);
		}
		return $roles;
	}

	/**
	 * List of roles with builtIn
	 * @return Role[]
	 */
	public function getRoles()
	{
		// roles
		$roles = $this->em->getRepository(Role::class)
			->createQueryBuilder("r")
			->getQuery()
			->getResult();

		$builtInRoles = [
			"admin",                    // special role for not logged in users
			"guest",                    // special role for admin -> all privileges if not set in acl
			"authenticated",            // special role for user without any role
		];

		foreach ($roles as $role) {
			foreach ($builtInRoles as $key => $bir) {
				if ($role->getRole() == $bir) {
					unset($builtInRoles[$key]);
				}
			}
		}

		foreach ($builtInRoles as $bir) {
			$builtInRole = new Role();
			$builtInRole->setRole($bir);
			$roles[] = $builtInRole;
		}
		return $roles;


	}

}
