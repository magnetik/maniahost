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
use ManiaLive\DedicatedApi\Structures\Map;

class Plugin extends \ManiaLive\PluginHandler\Plugin implements \ManiaLive\PluginHandler\WaitingCompliant
{

	protected $stopTime = null;
	protected $tick = 0;
	protected $avgPlayer;
	protected $maxPlayer = 0;

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
	}

	function onReady()
	{
		$quotedHost = $this->db->quote(Config::getInstance()->host);
		$port = Config::getInstance()->port;
		try
		{
			$result = $this->db->query(
					'SELECT R.* FROM Rents R '.
					'INNER JOIN Servers S ON S.idRent = R.id '.
					'WHERE NOT ISNULL(S.idRent) '.
					'AND DATE_ADD(R.rentDate, INTERVAL R.duration HOUR) > NOW() '.
					'AND S.hostname = %s AND S.port = %d '.
					'LIMIT 1', $quotedHost, $port
			);
			$datas = $result->fetchAssoc();
			if($datas)
			{
				if($this->storage->serverStatus->code == Status::WAITING)
				{
					$this->configServer($datas);
					$this->db->execute('UPDATE Servers SET idRent = %d WHERE hostname = %s AND port = %d',
							$datas['id'], $quotedHost, $port);
					$this->configPlugin(strtotime($datas['rentDate']),
							$datas['duration'] * 3600);
					$this->connection->startServerInternet();
				}
				else
				{
					$this->configPlugin(strtotime($datas['rentDate']),
							$datas['duration'] * 3600);
				}
			}
		}
		catch(\Exception $e)
		{
			\ManiaLive\Application\ErrorHandling::displayAndLogError($e);
		}
	}

	function onPreLoop()
	{
		if(count($this->storage->players) + count($this->storage->spectators) > $this->maxPlayer)
		{
			$this->maxPlayer = count($this->storage->players) + count($this->storage->spectators);
		}

		if($this->storage->serverStatus->code == Status::WAITING)
		{
			try
			{
				$result = $this->db->query('SELECT idRent FROM Servers WHERE NOT ISNULL(idRent)');

				$ids = array();
				while($tmp = $result->fetchScalar())
				{
					$ids[] = $tmp;
				}

				$result = $this->db->query(
						'SELECT R.* FROM Rents R '.
						'WHERE DATE_ADD(rentDate, INTERVAL duration HOUR) > NOW() '.
						'AND rentDate <= NOW() '.
						($ids ? 'AND id NOT IN('.implode(',', $ids).')' : '').
						'LIMIT 1'
				);
				$datas = $result->fetchAssoc();
				if($datas)
				{
					$quotedHost = $this->db->quote(Config::getInstance()->host);
					$port = Config::getInstance()->port;

					$this->configServer($datas);
					$this->db->execute('UPDATE Servers SET idRent = %d WHERE hostname = %s AND port = %d',
							$datas['id'], $quotedHost, $port);

					if($this->db->affectedRows())
					{
						$this->configPlugin(strtotime($datas['rentDate']),
								$datas['duration'] * 3600);
						$this->connection->startServerInternet();
					}
				}
			}
			catch(\Exception $e)
			{
				\ManiaLive\Application\ErrorHandling::displayAndLogError($e);
			}
		}
		elseif(time() > $this->stopTime)
		{
			//Check if StopTime Changed
			$result = $this->db->query(
					'SELECT UNIX_TIMESTAMP(rentDate) + duration * 3600 '.
					'FROM Rents R INNER JOIN Servers S ON S.idRent = R.id '.
					'WHERE S.hostname = %s AND S.port = %d',
					$this->db->quote(Config::getInstance()->host), Config::getInstance()->port
			);
			$stopTime = $result->fetchRow();
			$stopTime = $stopTime[0];
			if($stopTime > $this->stopTime)
			{
				$this->stopTime = $stopTime;
			}
			else
			{
				$this->timeleft = null;
				$this->duration = null;
				$this->startDate = null;
				$this->connection->stopServer();
				$maps = $this->connection->getMapList(-1, 0);
				$filenames = array_map(function ($m)
						{
							return $m->fileName;
						}, $maps);
				$this->connection->removeMapList($filenames);

				$quotedHost = $this->db->quote(Config::getInstance()->host);
				$port = Config::getInstance()->port;

				$this->clearFiles();

				$this->db->execute('UPDATE Servers SET idRent = NULL WHERE hostname = %s AND port = %d',
						$quotedHost, $port);
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

	function onTick()
	{
		++$this->tick;
		if($this->tick % 3600 == 0)
		{
			$this->db->execute('INSERT INTO Analytics VALUES (%s,%s,%f,%d)',
					$this->db->quote($this->storage->serverLogin),
					$this->db->quote(date('Y-m-d H:00:00')), $this->avgPlayer,
					$this->maxPlayer);
			$this->avgPlayer = 0;
			$this->maxPlayer = 0;
		}
		else
		{
			$values = $this->avgPlayer * ($this->tick % 3600) + count($this->storage->players) + count($this->storage->spectators);
			$this->avgPlayer = (float) $values / ($this->tick % 3600);
		}
	}

	function configServer(array $datas)
	{
		$files = $this->connection->getMapList(-1, 0);
		$files = array_map(function (Map $m)
				{
					return $m->fileName;
				}, $files);

		if($files)
		{
			$this->connection->removeMapList($files);
		}

		$gameInfos = unserialize($datas['gameInfos']);
		$serverOptions = unserialize($datas['serverOptions']);
		$maps = unserialize($datas['maps']);

		$this->connection->setGameInfos($gameInfos);
		$this->connection->setServerOptions($serverOptions);
		$this->connection->addMapList($maps);
	}

	function configPlugin($startDate, $duration)
	{
		$this->stopTime = $startDate + $duration;
	}

	function clearFiles()
	{
		try
		{
			$login = $this->db->query(
							'SELECT playerLogin FROM Servers S '.
							'INNER JOIN Rents R ON S.idRent = R.id '.
							'WHERE hostname = %s AND port = %d',
							$this->db->quote(Config::getInstance()->host),
							Config::getInstance()->port
					)->fetchScalar();

			$count = $this->db->query(
							'SELECT count(*) '.
							'FROM Rents '.
							'WHERE DATE_ADD(rentDate, INTERVAL duration HOUR) > NOW() '.
							'AND playerLogin = %s', $this->db->quote($login)
					)->fetchScalar();

			$filesPath = realpath(\ManiaLive\Config\Config::getInstance()->dedicatedPath).DIRECTORY_SEPARATOR.'UserData'.DIRECTORY_SEPARATOR.'Maps'.DIRECTORY_SEPARATOR;

			if($count && file_exists($filesPath.'$uploaded'.DIRECTORY_SEPARATOR.$login))
			{
				$maps = scandir($filesPath.'$uploaded'.DIRECTORY_SEPARATOR.$login);
				foreach($maps as $map)
				{
					if($map != '..' && $map != '.')
					{
						unlink($filesPath.'$uploaded'.DIRECTORY_SEPARATOR.$login.DIRECTORY_SEPARATOR.$map);
					}
				}
				rmdir($filesPath.'$uploaded'.DIRECTORY_SEPARATOR.$login);
			}
		}
		catch(\Exception $e)
		{
			\ManiaLive\Application\ErrorHandling::displayAndLogError($e);
		}
	}

}

?>