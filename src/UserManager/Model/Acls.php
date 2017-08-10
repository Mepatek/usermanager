<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 06.08.2017
 * Time: 15:55
 */

namespace Mepatek\UserManager\Model;


use App\Mepatek\UserManager\Entity\Acl;
use App\Mepatek\UserManager\Entity\Role;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Mepatek\UserManager\Authorizator;
use Mepatek\UserManager\Repository\ResourceRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class Acls
{

	/** @var EntityManager */
	private $em;
	/** @var ResourceRepository */
	private $resourceRepository;
	/** @var Role */
	private $adminRole;
	/** @var Cache */
	private $cache;


	const CACHE_TAG_ACLS_LIST = "AclsList";

	public function __construct(
		EntityManager $em,
		ResourceRepository $resourceRepository,
		Role $adminRole,
		IStorage $storage
	) {
		$this->em = $em;
		$this->resourceRepository = $resourceRepository;
		$this->adminRole = $adminRole;
		$this->cache = new Cache($storage, "Usermanager.Acls");
	}

	/**
	 * Cached list of ACL
	 * @return Acl[]
	 */
	public function getCachedAcls()
	{
		$acls = $this->cache->load("Acls list");
		if (!is_array($acls)) {
			$acls = $this->getAcls();
			$this->cache->save(
				"Acls list",
				$acls,
				[
					Cache::EXPIRE  => "1 month",
					Cache::SLIDING => true,
					Cache::TAGS    => [self::CACHE_TAG_ACLS_LIST],
				]
			);
		}
		return $acls;
	}

	/**
	 * List of acls
	 * @return Acl[]
	 */
	public function getAcls()
	{
		// acls
		$acls = $this->em->getRepository(Acl::class)
			->createQueryBuilder("a")
			->getQuery()
			->getResult();

		$resources = $this->resourceRepository->findBy([]);

		$adminSet = false;
		foreach ($acls as $acl) {
			if ($acl->getRole()->getRole() == "admin") {
				$adminSet = true;
			}
		}
		// set admin privileges if not set
		if (!$adminSet) {
			foreach ($resources as $resource) {
				$acl = new Acl();

				$acl->setRole($this->adminRole);
				$acl->setResource($resource->getResource());
				foreach ($resource->getPrivileges() as $privilege => $privilegeTitle) {
					$allow[] = $privilege;
				}
				$acl->setAllowed($allow);
				$acls[] = $acl;
			}
		}

		return $acls;


	}


	/**
	 * @param integer $id
	 *
	 * @return null|Acl
	 */
	public function find($id)
	{
		return $this->em->find(Acl::class, $id);
	}

	/**
	 * @param Role   $role
	 * @param string $resource
	 *
	 * @return null|Acl
	 */
	public function findByRoleAndResource(Role $role, $resource)
	{
		return $this->em->getRepository(Acl::class)
			->findOneBy(
				[
					"role"     => $role,
					"resource" => $resource,
				]
			);
	}

	/**
	 * Save acl, clear cache
	 *
	 * @param Acl $acl
	 */
	public function save(Acl $acl)
	{
		switch ($this->em->getUnitOfWork()->getEntityState($acl)) {
			case UnitOfWork::STATE_NEW:
				$this->em->persist($acl);
				break;
			case UnitOfWork::STATE_DETACHED:
				$this->em->merge($acl);
				break;
		}
		$this->em->flush();
		$this->clearCache(self::CACHE_TAG_ACLS_LIST);
	}


	/**
	 * Delete acl, clear cache
	 *
	 * @param Acl $acl
	 */
	public function delete(Acl $acl)
	{
		$this->em->remove($acl);
		$this->em->flush();
		$this->clearCache(self::CACHE_TAG_ACLS_LIST);
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
