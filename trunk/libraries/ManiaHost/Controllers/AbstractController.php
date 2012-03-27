<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Controllers;

abstract class AbstractController extends \ManiaLib\Application\Controller
{

	private $connection;

	function onConstruct()
	{
		$this->addFilter(new \ManiaLib\Application\Filters\UserAgentCheck());
		$this->addFilter(new \ManiaLib\WebServices\ManiaConnectFilter());
		\ManiaLive\Utilities\Logger::getLog('Runtime')->disableLog();
	}

	/**
	 * @return \ManiaLive\DedicatedApi\Connection
	 */
	protected function getServerConnection()
	{
		if($this->connection)
		{
			return $this->connection;
		}

		$service = new \ManiaHost\Services\ServerService();
		$server = end($service->getList(0, 1));
		if(!$server)
		{
			throw new \Exception('No server launched');
		}
		$config = \ManiaLive\DedicatedApi\Config::getInstance();
		$config->host = $server->hostname;
		$config->port = $server->port;
		$config->user = 'SuperAdmin';
		$config->password = $server->superAdminPassword;
		return \ManiaLive\DedicatedApi\Connection::getInstance();
	}

}

?>