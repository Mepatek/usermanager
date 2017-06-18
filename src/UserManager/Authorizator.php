<?php

namespace Mepatek\UserManager;


use App\Mepatek\UserManager\Entity\Acl;
use App\Mepatek\UserManager\Entity\Role;
use Kdyby\Doctrine\EntityManager;
use Mepatek\UserManager\Repository\AclRepository;
use Mepatek\UserManager\Repository\ResourceRepository;
use Mepatek\UserManager\Repository\RoleRepository;
use Nette\Caching\IStorage;
use Nette\Security\Permission;

/**
 * Class Authorizator
 * @package Mepatek\UserManager
 */
class Authorizator extends Permission
{


	/**
	 * Authorizator constructor.
	 *
	 * @param IStorage           $storage
	 * @param EntityManager      $em
	 * @param ResourceRepository $resourceRepository
	 */
	public function __construct(IStorage $storage, EntityManager $em, ResourceRepository $resourceRepository)
	{
		// roles
		$qb = $em->getRepository(Role::class)
			->createQueryBuilder("r");
		$roles = $qb
			->where($qb->expr()->notIn("r.role", ["admin", "guest", "authenticated"]))
			->getQuery()
			->getResult();


		$this->addRole("guest");    // special role for not logged in users
		$this->addRole("admin");    // special role for admin -> all privileges if not set in acl
		$this->addRole("authenticated");    // special role for user withour any role


		foreach ($roles as $role) {
			$this->addRole($role->getRole());
		}

		// resources
		$resources = $resourceRepository->findBy([]);

		foreach ($resources as $resource) {
			$this->addResource($resource->resource);

		}

		// acl
		$acls = $em->getRepository(Acl::class)->findAll();

		$adminSet = false;
		foreach ($acls as $acl) {
			if ($acl->getRole() == "admin") {
				$adminSet = true;
			}
			if ($acl->getAllow() !== null) {
				$this->allow($acl->role, $acl->resource, $acl->getAllowArray());
			}
			if ($acl->deny !== null) {
				$this->deny($acl->role, $acl->resource, $acl->getDenyArray());
			}
		}
		// set admin privileges if not set
		if (!$adminSet) {
			foreach ($resources as $resource) {
				$this->allow("admin", $resource->resource, self::ALL);
			}
		}
	}
}
