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
	public $host = '127.0.0.1';
	public $username = 'root';
	public $password = '';
	public $database = 'ManiaHost';
	public $type = 'MySQL';
	public $port = 3306;
}

?>