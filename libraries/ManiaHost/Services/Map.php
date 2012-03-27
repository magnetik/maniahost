<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

class Map extends AbstractObject
{

	public $filename;
	public $path;
	public $isDirectory = false;
	public $isSelected = false;
	public $name;
	public $author;
	public $authorTime;
	public $type;
	public $nbLaps;
	public $environment;
	public $fileSize;

}

?>