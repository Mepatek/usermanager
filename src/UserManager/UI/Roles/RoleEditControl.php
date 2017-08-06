<?php

namespace Mepatek\UserManager\UI\Roles;


use App\Mepatek\UserManager\Entity\Role;
use Mepatek\Components\Form;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\Model\Roles;

class RoleEditControl extends RoleControl
{

	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkList;

	/** @var boolean */
	private $permittedDelete = true;

	/** @var Role */
	private $role = null;

	/**
	 * RoleEditControl constructor.
	 *
	 * @param Roles       $rolesModel
	 * @param GridFactory $gridFactory
	 * @param FormFactory $formFactory
	 * @param string      $linkList
	 */
	public function __construct(
		Roles $rolesModel,
		GridFactory $gridFactory,
		FormFactory $formFactory,
		$linkList
	) {
		$this->rolesModel = $rolesModel;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
		$this->linkList = $linkList;
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
		$form->addText("role", "rolemanager.role")
			->setRequired(true);
		$form->addText("name", "rolemanager.role_name")
			->setRequired(true);
		$form->addText("description", "rolemanager.role_description");

		$form->addSubmit("send", "rolemanager.role_save");
		if ($this->permittedDelete) {
			$form->addSubmit("delete", "rolemanager.role_delete");
		}

		if ($role) {
			$form["role"]->setDisabled(true);
			$form->setDefaults(
				[
					"id"        => $role->getRole(),
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
						$role = new User();
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


}
