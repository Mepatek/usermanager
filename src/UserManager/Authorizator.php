<?php

namespace Mepatek\UserManager;


use App\Mepatek\UserManager\Entity\Acl;
use Kdyby\Doctrine\EntityManager;
use Mepatek\UserManager\Model\Acls;
use Mepatek\UserManager\Model\Roles;
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

	/** @var Roles */
	private $rolesModel;
	/** @var Acls */
	private $aclsModel;

	/**
	 * Authorizator constructor.
	 *
	 * @param IStorage           $storage
	 * @param EntityManager      $em
	 * @param ResourceRepository $resourceRepository
	 */
	public function __construct(
		IStorage $storage,
		EntityManager $em,
		ResourceRepository $resourceRepository
	) {

		$this->rolesModel = new Roles($em, $storage);
		$this->aclsModel = new Acls($em, $storage);

		$roles = $this->rolesModel->getCachedRoles();

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

	/**
	 * @return Roles
	 */
	public function getRolesModel()
	{
		return $this->rolesModel;
	}

	/**
	 * @return Acls
	 */
	public function getAclsModel()
	{
		return $this->aclsModel;
	}
}
