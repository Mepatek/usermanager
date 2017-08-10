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
	/** @var ResourceRepository */
	private $resourceRepository;

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

		$this->resourceRepository = $resourceRepository;
		$this->rolesModel = new Roles($em, $storage);

		$roles = $this->rolesModel->getCachedRoles();

		$roleAdmin = null;
		foreach ($roles as $role) {
			if ($role->getRole()=="admin") {
				$roleAdmin = $role;
			}
			$this->addRole($role->getRole());
		}

		$this->aclsModel = new Acls($em, $resourceRepository, $roleAdmin, $storage);

		// resources
		$resources = $resourceRepository->findBy([]);

		foreach ($resources as $resource) {
			$this->addResource($resource->resource);

		}

		// acl
		$acls = $this->aclsModel->getCachedAcls();

		foreach ($acls as $acl) {
			if ($acl->getAllowed() !== null) {
				$this->allow($acl->getRole()->getRole(), $acl->getResource(), $acl->getAllowArray());
			}
			if ($acl->getDenied() !== null) {
				$this->deny($acl->getRole()->getRole(), $acl->getResource(), $acl->getDenyArray());
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

	/**
	 * @return ResourceRepository
	 */
	public function getResourceRepository()
	{
		return $this->resourceRepository;
	}

}
