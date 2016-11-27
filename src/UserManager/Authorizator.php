<?php

namespace Mepatek\UserManager;


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
	 * @param RoleRepository     $roleRepository
	 * @param ResourceRepository $resourceRepository
	 */
	public function __construct(IStorage $storage, RoleRepository $roleRepository, ResourceRepository $resourceRepository, AclRepository $aclRepository)
	{
		// roles
		$roles = $roleRepository->findBy(["NOT role" => ["admin", "guest"]]);

		$this->addRole("guest");    // special role for not logged in users
		$this->addRole("admin");    // special role for admin -> all privileges if not set in acl

		foreach ($roles as $role) {
			$this->addRole($role->role);
		}

		// resources
		$resources = $resourceRepository->findBy([]);

		foreach ($resources as $resource) {
			$this->addResource($resource->resource);

		}

		// acl
		$acls = $aclRepository->findBy([]);

		$adminSet = false;
		foreach ($acls as $acl) {
			if ($acl->role == "admin") {
				$adminSet = true;
			}
			if ($acl->allow !== null) {
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
