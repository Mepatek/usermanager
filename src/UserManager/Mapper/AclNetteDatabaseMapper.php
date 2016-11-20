<?php

namespace Mepatek\UserManager\Mapper;

use Mepatek\Mapper\AbstractNetteDatabaseMapper,
	Mepatek\Mapper\IMapper;

use Nette\Database\Context,
	Mepatek\UserManager\Entity\Acl;

/**
 * Class AclNetteDatabaseMapper
 * @package Mepatek\UserManager\Mapper
 */
class AclNetteDatabaseMapper extends AbstractNetteDatabaseMapper implements IMapper
{

	/**
	 * AclNetteDatabaseMapper constructor.
	 *
	 * @param Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}

	/**
	 * Save item
	 *
	 * @param Acl $item
	 *
	 * @return boolean
	 */
	public function save(&$item)
	{
		$data = $this->itemToData($item);
		unset($data["AclID"]);
		$retSave = false;

		if (!$item->id) { // new --> insert

			$row = $this->getTable()
				->insert($data);
			if ($row) {
				$item->id = $row["AclID"];
				$newItem = $this->find($item->id);
				if ($newItem) {
					$item = $newItem;
					$retSave = true;
				}
			}
		} else { // update

			$this->getTable()
				->where("AclID", $item->id)
				->update($data);

			$retSave = true;
		}

		return $retSave;
	}

	/**
	 * Item data to array
	 *
	 * @param Acl $item
	 *
	 * @return array
	 */
	private function itemToData(Acl $item)
	{
		$data = [];

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$data[$columnSql] = $item->$property;
		}

		return $data;
	}

	/**
	 * Get array map of item property vs SQL columns name for Tasks table
	 * @return array
	 */
	protected function mapItemPropertySQLNames()
	{
		return [
			"id"    => "AclID",
			"role"  => "Role",
			"name"  => "Resource",
			"allow" => "Allow",
			"deny"  => "Deny",
		];
	}

	/**
	 * Get view object
	 * @return \Nette\Database\Table\Selection
	 */
	protected function getTable()
	{
		$table = $this->database->table("RolesAcl");
		return $table;
	}

	/**
	 * Find 1 entity by ID
	 *
	 * @param string $id
	 *
	 * @return Acl
	 */
	public function find($id)
	{
		$values["AclID"] = $id;
		$item = $this->findOneBy($values);
		return $item;
	}

	/**
	 * Find first entity by $values (key=>value)
	 *
	 * @param array $values
	 * @param array $order Order => column=>ASC/DESC
	 *
	 * @return Acl
	 */
	public function findOneBy(array $values, $order = null)
	{
		$items = $this->findBy($values, $order, 1);
		if (count($items) > 0) {
			return $items[0];
		} else {
			return null;
		}
	}

	/**
	 * Delete item
	 *
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function delete($id)
	{
		$deletedRow = 0;
		if (($item = $this->find($id))) {

			$deletedRow = $this->getTable()
				->where("AclID", $id)
				->delete();
		}
		return $deletedRow > 0;
	}


	/**
	 * from data to item
	 *
	 * @param \Nette\Database\IRow $data
	 *
	 * @return Acl
	 */
	protected function dataToItem($data)
	{
		$item = new Acl;

		foreach ($this->mapItemPropertySQLNames() as $property => $columnSql) {
			$item->$property = $data->$columnSql;
		}

		return $item;
	}
}
