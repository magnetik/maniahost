<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Views\Rent;

use ManiaLib\Gui\Manialink;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\UIConstructionSimple_Buttons;
use ManiaLib\Gui\Elements\Icons64x64_1;

class SelectMap extends \ManiaLib\Application\View
{

	function display()
	{
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->subTitle->setText('Select maps');

		$manialink = $this->request->createLinkArgList('../my-maps/');
		$ui->addItem();
		$ui->lastItem->text->setText('My maps');
		$ui->lastItem->icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Browse);
		$ui->lastItem->setManialink($manialink);

		$manialink = $this->request->createLinkArgList('../default-maps/');
		$ui->addItem();
		$ui->lastItem->text->setText('Default maps');
		$ui->lastItem->icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Browse);
		$ui->lastItem->setManialink($manialink);

		$manialink = $this->request->createLinkArgList('../');
		$ui->quitButton->text->setText('Back');
		$ui->quitButton->setManialink($manialink);
		$ui->save();
	}

}

?>