<?php

/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */
namespace ManiaHost\Services;

abstract class AbstractService
{
	/**
	 * @var \ManiaLib\Database\Connection
	 */
	private $db;

	/**
	 * Overide in service implementations to select a default database
	 */
	protected $databaseName = 'ManiaHost';

	function __construct()
	{
	    $this->databaseName = \ManiaLib\Database\Config::getInstance()->database;
	}

	/**
	 * Returns an DB instance. Only instanciate the DB if needed, so if you have
	 * caching layer it will avoid creating DB connections for nothing.
	 *
	 * @return \ManiaLib\Database\Connection
	 */
	protected function db()
	{
		if(!$this->db)
		{
			$this->db = \ManiaLib\Database\Connection::getInstance();
		}
		$this->db->select($this->databaseName);
		return $this->db;
	}
}

?>