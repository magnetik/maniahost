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

class File extends Bgs1
{

	/**
	 * @var Label
	 */
	public $name;

	function __construct($sizeX = 90, $sizeY = 8)
	{
		parent::__construct($sizeX, $sizeY);

		$this->setSubStyle(Bgs1::BgCardChallenge);

		$this->name = new Label(85);
		$this->name->setValign('center2');
		$this->name->setPosition(5, -4, 0.1);
		$this->addCardElement($this->name);

	}
}

?>