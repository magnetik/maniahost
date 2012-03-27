<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

class Rent extends AbstractObject
{

	public $id;
	public $playerLogin;
	public $login;
	public $rentDate;
	public $duration;
	public $gameInfos;
	public $maps;
	public $serverOptions;

	function __construct()
	{
		if($this->rentDate)
		{
			$this->rentDate = strtotime($this->rentDate);
		}
		if(is_string($this->gameInfos))
		{
			$this->gameInfos = unserialize($this->gameInfos);
		}
		if(is_string($this->maps))
		{
			$this->maps = unserialize($this->maps);
		}
		if(is_string($this->serverOptions))
		{
			$this->serverOptions = unserialize($this->serverOptions);
		}
	}

}

?>