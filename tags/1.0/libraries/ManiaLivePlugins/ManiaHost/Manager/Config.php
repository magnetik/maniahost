<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaLivePlugins\ManiaHost\Manager;

class Config extends \ManiaLib\Utils\Singleton
{

	public $host;
	public $username;
	public $password;
	public $database;
	public $type = 'MySQL';
	public $port = 3306;

	protected function __construct()
	{
		$config = \ManiaLive\Database\Config::getInstance();
		$this->host = $config->host;
		$this->username = $config->username;
		$this->password = $config->password;
		$this->database = $config->database;
	}

}

?>