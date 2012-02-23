<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Cards;

use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Icons64x64_1;

class File extends Bgs1
{

	/**
	 * @var Label
	 */
	public $name;

	/**
	 * @var Icons64x64_1
	 */
	public $deleteIcon;

	function __construct($sizeX = 90, $sizeY = 8)
	{
		parent::__construct($sizeX, $sizeY);

		$this->setSubStyle(Bgs1::BgCardChallenge);

		$this->name = new Label(85);
		$this->name->setValign('center2');
		$this->name->setPosition(5, -4, 0.1);
		$this->addCardElement($this->name);

		$this->deleteIcon = new Icons64x64_1;
		$this->deleteIcon->setSubStyle(Icons64x64_1::Close);
		$this->deleteIcon->setAlign('right', 'center');
		$this->deleteIcon->setPosition($sizeX - 3, -$sizeY / 2);
		$this->addCardElement($this->deleteIcon);
	}

}

?>