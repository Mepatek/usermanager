<?php

namespace Mepatek\UserManager\UI\Roles;


use App\Mepatek\UserManager\Entity\Acl;
use App\Mepatek\UserManager\Entity\Role;
use Mepatek\Components\Form;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\Authorizator;
use Mepatek\UserManager\Entity\ResourceObject;
use Mepatek\UserManager\Model\Acls;
use Mepatek\UserManager\Model\Roles;
use Mepatek\UserManager\Repository\ResourceRepository;

class RoleEditControl extends RoleControl
{

	/** @var ResourceRepository */
	private $resourceRepository;
	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkList;
	/** @var Authorizator */
	private $authorizator;
	/** @var Acls */
	private $aclsModel;

	/** @var boolean */
	private $permittedDelete = true;

	/** @var Role */
	private $role = null;

	/** @var ResourceObject[] */
	private $resources = [];

	/**
	 * RoleEditControl constructor.
	 *
	 * @param Authorizator $authorizator
	 * @param GridFactory  $gridFactory
	 * @param FormFactory  $formFactory
	 * @param string       $linkList
	 */
	public function __construct(
		Authorizator $authorizator,
		GridFactory $gridFactory,
		FormFactory $formFactory,
		$linkList
	) {
		$this->authorizator = $authorizator;
		$this->rolesModel = $authorizator->getRolesModel();
		$this->aclsModel = $authorizator->getAclsModel();
		$this->resourceRepository = $authorizator->getResourceRepository();
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
		$this->linkList = $linkList;
		$this->resources = $this->resourceRepository->findBy([]);

		parent::__construct();
	}

	public function render()
	{
		$template = $this->getTemplate();

		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

		$this->readRole();
		$template->role = $this->role;
		$template->resources = $this->resources;
		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');


	}


	/**
	 * @param int|null $id
	 *
	 * @return Role
	 */
	private function readRole($id = null)
	{
		if (!$this->role) {
			$id = $id ? $id : $this->getPresenter()->getParameter("role");
			$this->role = $this->findRole($id);
		}
		return $this->role;
	}

	/**
	 * @param $name
	 *
	 * @return \Mepatek\Components\FormBootstrap
	 */
	public function createComponentRoleEditForm($name)
	{
		$role = $this->readRole();

		$form = $this->formFactory->createBootstrap("vertical");

		$form->addHidden("id");

		if (!$role) {
			$form->addText("role", "rolemanager.role")
				->setRequired(true);
		}
		$form->addText("name", "rolemanager.role_name")
			->setRequired(true);
		$form->addTextArea("description", "rolemanager.role_description");

		$form->addSubmit("send", "rolemanager.role_save");
		if ($this->permittedDelete) {
			$form->addSubmit("delete", "rolemanager.role_delete");
		}

		if ($role) {
			$form->setDefaults(
				[
					"id"          => $role->getRole(),
					"role"        => $role->getRole(),
					"name"        => $role->getName(),
					"description" => $role->getDescription(),
				]
			);

		} else {
//			$form->addSubmit("send", "usermanager.user_save");
		}

		$form->onSuccess[] = function (Form $form, $values) {
			$role = $values->id;

			switch ($form->isSubmitted()->getName()) {
				// save
				case "send":
					if ($role) {
						$role = $this->findRole($role);
					} else {
						$role = new Role();
						$role->setRole($values->role);
					}
					$role->setName($values->name);
					$role->setDescription($values->description);
					$this->rolesModel->save($role);
					$this->presenter->flashMessage("rolemanager.role_saved_msg");

					$this->presenter->redirect("this", ["role" => $role->getRole()]);

					break;
				case "delete":
					// delete
					$role = $this->findRole($role);
					$this->deleteRole($role);
					$this->presenter->flashMessage("rolemanager.role_deleted_msg");
					$this->presenter->redirect($this->linkList);
					break;
			}

		};

		return $form;

	}

	/**
	 * @param $name
	 *
	 * @return \Mepatek\Components\FormBootstrap
	 */
	public function createComponentAclForm($name)
	{
		$role = $this->readRole();

		$form = $this->formFactory->createBootstrap();

		$form->addHidden("role", $role->getRole());
		foreach ($this->resources as $resource) {
			foreach ($resource->getPrivileges() as $privilege => $privilegeTitle) {
				$inputName = $resource->getResource() . "_" . $privilege;
				$isAllowed = $this->authorizator->isAllowed(
					$role->getRole(),
					$resource->getResource(),
					$privilege
				);
				$form->addCheckbox($inputName, $privilegeTitle)
					->setDefaultValue($isAllowed);
			}
		}

		$form->addSubmit("send", "rolemanager.acl_save");


		$form->onSuccess[] = function (Form $form, $values) {
			$role = $this->findRole($values->role);

			foreach ($this->resources as $resource) {
				foreach ($resource->getPrivileges() as $privilege => $privilegeTitle) {
					$inputName = $resource->getResource() . "_" . $privilege;
					$acl = $this->aclsModel->findByRoleAndResource($role, $resource->getResource());
					if (!$acl) {
						$acl = new Acl();
						$acl->setRole($role);
						$acl->setResource($resource->getResource());
					}
					$isAllowed = $values->$inputName;
					if ($isAllowed) {
						$acl->allow($privilege);
					} else {
						$acl->deny($privilege);
					}
					$this->aclsModel->save($acl);
				}
			}

			if ($this->presenter->isAjax()) {
				$this->presenter->redrawControl("flashes");
				$this->redrawControl("permissions");
			}

		};

		return $form;

	}

}
