<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Controllers;

define('APP_ROOT', APP_PATH);

abstract class AbstractController extends \ManiaLib\Application\Controller
{

	function onConstruct()
	{
//		$this->addFilter(new \ManiaLib\Application\Filters\UserAgentCheck());
		$this->addFilter(new \ManiaLib\WebServices\ManiaConnectFilter());
	}

}

?>