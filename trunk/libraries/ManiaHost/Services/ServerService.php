<?php

/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */
namespace ManiaHost\Services;

class ServerService extends AbstractService
{
	function availableCount()
	{
		return $this->db()->execute(
						'SELECT count(*) '.
						'FROM Servers S '.
						'LEFT JOIN Rents R ON S.idRent = R.id '.
						'WHERE S.idRent IS NULL '.
						'OR DATE_ADD(R.rentDate, INTERVAL R.duration HOUR) < NOW()'
				)->fetchSingleValue();
	}

	/**
	 * @return Server[]
	 */
	function getList($offset = null, $length = null)
	{
		$result = $this->db()->execute(
				'SELECT S.* FROM Servers S %s',
				\ManiaLib\Database\Tools::getLimitString($offset, $length)
		);
		return Server::arrayFromRecordSet($result);
	}

	/**
	 * Return Server information for the given login
	 * @param string $login
	 * @return Server
	 */
	function get($login)
	{
		$result = $this->db()->execute(
				'SELECT * FROM Servers S WHERE login = %s',
				$this->db()->quote($login)
				);
		return Server::fromRecordSet($result);
	}
}

?>