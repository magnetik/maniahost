<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Cards;

use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\Label;

class Rent extends Bgs1
{

	/**
	 * @var Label
	 */
	public $name;

	/**
	 * @var Label
	 */
	public $remainingTime;

	/**
	 * @var Label
	 */
	public $login;

	/**
	 * @var Label
	 */
	public $renew;

	function __construct($sizeX = 200, $sizeY = 15)
	{
		parent::__construct($sizeX, $sizeY);
		$this->setSubStyle('BgCardOnline');

		$this->login = new Label(120);
		$this->login->setValign('bottom');
		$this->login->setPosition(5, -13, 0.1);
		$this->addCardElement($this->login);

		$this->name = new Label(180);
		$this->name->setPosition(5, -2, 0.1);
		$this->addCardElement($this->name);

		$this->remainingTime = new Label(60);
		$this->remainingTime->setHalign('right');
		$this->remainingTime->setValign('bottom');
		$this->remainingTime->setPosition(195, -13, 0.1);
		$this->addCardElement($this->remainingTime);

		$this->renew = new Label(60);
		$this->renew->setHalign('right');
		$this->renew->setPosition(195, -2, 0.1);
		$this->addCardElement($this->renew);
	}

}

?>