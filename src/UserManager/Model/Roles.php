<?php

namespace Mepatek\UserManager\Model;


use App\Mepatek\UserManager\Entity\Role;
use Doctrine\ORM\UnitOfWork;
use Kdyby\Doctrine\EntityManager;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class Roles
{

	/** @var EntityManager */
	private $em;
	/** @var Cache */
	private $cache;


	const CACHE_TAG_ROLES_LIST = "RolesList";

	public function __construct(
		EntityManager $em,
		IStorage $storage
	) {
		$this->em = $em;
		$this->cache = new Cache($storage, "Usermanager.Roles");
	}

	/**
	 * Cached list of roles
	 * @return Role[]
	 */
	public function getCachedRoles()
	{
		$roles = $this->cache->load("Roles list");
		if (!is_array($roles)) {
			$roles = $this->getRoles();
			$this->cache->save(
				"Roles list",
				$roles,
				[
					Cache::EXPIRE  => "1 month",
					Cache::SLIDING => true,
					Cache::TAGS    => [self::CACHE_TAG_ROLES_LIST],
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
			->where("r.deleted = 0")
			->getQuery()
			->getResult();


		$builtInRoles = $this->getBuiltInRoles();

		foreach ($roles as $role) {
			$key = $role->getRole();
			if (isset($builtInRoles[$key])) {
				unset($builtInRoles[$key]);
			}
		}

		foreach ($builtInRoles as $key => $bir) {
			$builtInRole = new Role();
			$builtInRole->setRole($key);
			$builtInRole->setDescription($bir);
			$roles[] = $builtInRole;
		}
		return $roles;


	}


	public function getBuiltInRoles()
	{
		$builtInRoles = [
			"admin"         => "Administrators",    // special role for not logged in users
			"guest"         => "Guests",    // special role for admin -> all privileges if not set in acl
			"authenticated" => "Authenticated users",    // special role for user without any role
		];

		return $builtInRoles;
	}


	/**
	 * @param string $id
	 *
	 * @return null|Role
	 */
	public function find($id)
	{
		return $this->em->find(Role::class, $id);
	}

	/**
	 * Save role, clear cache
	 *
	 * @param Role $role
	 */
	public function save(Role $role)
	{
		switch ($this->em->getUnitOfWork()->getEntityState($role)) {
			case UnitOfWork::STATE_NEW:
				$this->em->persist($role);
				break;
			case UnitOfWork::STATE_DETACHED:
				$this->em->merge($role);
				break;
		}
		$this->em->flush();
		$this->clearCache(self::CACHE_TAG_ROLES_LIST);
	}


	/**
	 * Delete role (deleted), clear cache
	 *
	 * @param Role $role
	 */
	public function delete(Role $role)
	{
		$role->setDeleted(true);
		$this->save($role);
	}

	/**
	 * Clear cache
	 *
	 * @param null|string|array $tags null (all) or tags
	 */
	public function clearCache($tags = null)
	{
		if (is_string($tags)) {
			$clean = [
				CACHE::TAGS => [$tags],
			];
		} elseif (is_array($tags)) {
			$clean = [
				CACHE::TAGS => $tags,
			];
		} else {
			$clean = [
				CACHE::ALL => true,
			];
		}
		$this->cache->clean($clean);
	}
}
