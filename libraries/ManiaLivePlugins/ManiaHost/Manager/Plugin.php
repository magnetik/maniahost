<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaLivePlugins\ManiaHost\Manager;

use ManiaLive\DedicatedApi\Config;
use ManiaLivePlugins\ManiaHost\Manager\Config as ManagerConfig;
use ManiaLive\DedicatedApi\Structures\GameInfos;
use ManiaLive\DedicatedApi\Structures\Status;

class Plugin extends \ManiaLive\PluginHandler\Plugin implements \ManiaLive\PluginHandler\WaitingCompliant
{

	protected $duration = null;
	protected $startDate = null;
	protected $timeleft = null;

	function onLoad()
	{
		parent::onLoad();

		$config = ManagerConfig::getInstance();
		$this->db = \ManiaLive\Database\Connection::getConnection(
						$config->host, $config->username, $config->password, $config->database,
						$config->type, $config->port
		);
		$status = $this->connection->getStatus();
		$quotedHost = $this->db->quote(Config::getInstance()->host);
		$quotedLogin = $this->db->quote($this->storage->serverLogin);
		$quotedPassword = $this->db->quote(Config::getInstance()->password);
		$quotedStatus = $this->db->quote($status->name);
		$this->db->execute(
				'INSERT INTO Servers (hostname, port, login, superAdminPassword, status) '.
				'VALUES (%s,%d,%s,%s,%s) '.
				'ON DUPLICATE KEY UPDATE login = VALUES(login), '.
				'superAdminPassword = VALUES(superAdminPassword), '.
				'status = VALUES(status)', $quotedHost, Config::getInstance()->port,
				$quotedLogin, $quotedPassword, $quotedStatus
		);
		$this->enableDedicatedEvents();
		$this->enableApplicationEvents();
		$this->enableTickerEvent();
		if($this->storage->serverStatus->code != Status::WAITING)
				$this->connection->stopServer();
	}

	function onReady()
	{
		if($this->storage->serverStatus->code == Status::WAITING)
		{
			$quotedHost = $this->db->quote(Config::getInstance()->host);
			$port = Config::getInstance()->port;

			$result = $this->db->query(
					'SELECT R.* FROM Rents R '.
					'INNER JOIN Servers S ON S.idRent = R.id '.
					'WHERE NOT ISNULL(S.idRent) '.
					'AND UNIX_TIMESTAMP(R.rentDate) + R.duration * 3600 > UNIX_TIMESTAMP() '.
					'AND S.hostname = %s AND S.port = %d '.
					'LIMIT 1', $quotedHost, $port
			);
			$datas = $result->fetchAssoc();
			if($datas)
			{
				$this->configServer($datas);
				$this->connection->startServerInternet();
			}
		}
		else
		{
			$quotedHost = $this->db->quote(Config::getInstance()->host);
			$port = Config::getInstance()->port;

			$result = $this->db->query(
					'SELECT R.rentDate, R.duration FROM Rents R '.
					'INNER JOIN Servers S ON S.idRent = R.id '.
					'WHERE NOT ISNULL(S.idRent) '.
					'AND UNIX_TIMESTAMP(R.rentDate) + R.duration * 3600 > UNIX_TIMESTAMP() '.
					'AND S.hostname = %s AND S.port = %d '.
					'LIMIT 1', $quotedHost, $port
			);
			$datas = $result->fetchRow();
			if($datas)
			{
				$this->configPlugin(strtotime($datas[0]), $datas[1] * 3600);
			}
		}
	}

	function onTick()
	{
		if($this->timeleft)
		{
			$this->timeleft--;
			if($this->timeleft == 0)
			{
				$this->timeleft = null;
				$this->duration = null;
				$this->startDate = null;
				$maps = $this->connection->getMapList(-1, 0);
				$filenames = \ManiaLive\DedicatedApi\Structures\Map::getPropertyFromArray($maps,
								'fileName');
				$this->connection->removeMapList($filenames);
				$this->connection->stopServer();

				$quotedHost = $this->db->quote(Config::getInstance()->host);
				$port = Config::getInstance()->port;

				$this->db->execute('UPDATE Servers SET idRent = NULL WHERE hostname = %s AND port = %d',
						$quotedHost, $port);
			}
		}
	}

	function onPreLoop()
	{
		if($this->storage->serverStatus->code == Status::WAITING)
		{
			$result = $this->db->query(
					'SELECT R.* FROM Rents R '.
					'LEFT JOIN Servers S ON S.idRent = R.id '.
					'WHERE (UNIX_TIMESTAMP(rentDate) + duration * 3600 > UNIX_TIMESTAMP() '.
					'AND rentDate <= NOW() AND S.idRent IS NULL) '.
					'OR (NOT ISNULL(S.idRent) '.
					'AND UNIX_TIMESTAMP(R.rentDate) + R.duration * 3600 > UNIX_TIMESTAMP()) '.
					'LIMIT 1'
			);
			$datas = $result->fetchAssoc();
			if($datas)
			{
				$this->configServer($datas);
				//TODO Change to start on internet
				$this->connection->startServerLan();
			}
		}
	}

	function onStatusChanged($statusCode, $statusName)
	{
		$quotedHost = $this->db->quote(Config::getInstance()->host);
		$quotedStatus = $this->db->quote($statusName);

		$this->db->execute('UPDATE Servers SET status = %s WHERE hostname = %s AND port = %d',
				$quotedStatus, $quotedHost, Config::getInstance()->port
		);
	}

	function onUnload()
	{
		$this->db->execute('DELETE FROM Servers WHERE hostname = %s AND port = %d',
				$this->db->quote(Config::getInstance()->host), Config::getInstance()->port);
		parent::onUnload();
	}

	function configServer(array $datas)
	{
		$gameInfos = unserialize($datas['gameInfos']);
		$serverOptions = unserialize($datas['serverOptions']);
		$maps = unserialize($datas['maps']);

		$this->connection->setGameInfos($gameInfos);
		$this->connection->setServerOptions($serverOptions);
		$this->connection->addMapList($maps);

		$quotedHost = $this->db->quote(Config::getInstance()->host);
		$port = Config::getInstance()->port;

		$this->db->execute('UPDATE Servers SET idRent = %d WHERE hostname = %s AND port = %d',
				$datas['id'], $quotedHost, $port);

		$this->configPlugin(strtotime($datas['rentDate']), $datas['duration'] * 3600);
	}

	function configPlugin($startDate, $duration)
	{
		$this->startDate = $startDate;
		$this->duration = $duration;
		$this->timeleft = $this->duration + $this->startDate - time();
	}

}

?>