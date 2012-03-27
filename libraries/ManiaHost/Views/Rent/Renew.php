<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Views\Rent;

use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Manialink;
use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Button;

class Renew extends \ManiaLib\Application\View
{

	function display()
	{
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->subTitle->setText('Powered by ManiaHost');
		$manialink = $this->request->createLinkArgList('../');
		$ui->quitButton->text->setText('Back');
		$ui->quitButton->setManialink($manialink);
		$ui->save();

		$ui = new \ManiaLib\Gui\Cards\Panel(90, 70);
		$ui->title->setText('Duration');
		$ui->titleBg->setSubStyle(Bgs1::BgTitle3_5);
		$ui->setPosX(-5);
		$ui->setValign('center');
		$ui->save();


		Manialink::beginFrame(40, 7, 0.1);
		{
			$ui = new Label(85);
			$ui->setHalign('center');
			$ui->setStyle(Label::TextTips);
			$ui->enableAutonewline();
			$ui->setText('How long do you want to extend your rental?');
			$ui->save();

			$ui = new Label(25);
			$ui->setAlign('right', 'bottom');
			$ui->setStyle(Label::TextInfoSmall);
			$ui->setPosition(-2, -14);
			$ui->setText('Days');
			$ui->save();

			$ui = new Entry(10, 4.5);
			$ui->setValign('bottom');
			$ui->setPosition(2, -14);
			$ui->setName('days');
			$ui->save();
			$this->request->set('days', 'days');

			$ui = new Label(25);
			$ui->setAlign('right', 'bottom');
			$ui->setStyle(Label::TextInfoSmall);
			$ui->setPosition(-2, -23);
			$ui->setText('Hours');
			$ui->save();

			$ui = new Entry(10, 4.5);
			$ui->setValign('bottom');
			$ui->setPosition(2, -23);
			$ui->setName('hours');
			$ui->save();
			$this->request->set('hours', 'hours');

			$manialink = $this->request->createLink('../checkout-extend/');
			$ui = new Button();
			$ui->setHalign('center');
			$ui->setPosY(-30);
			$ui->setStyle(Button::CardButtonMediumWide);
			$ui->setText('Pay the rental extension');
			$ui->setManialink($manialink);
			$ui->save();
		}
		Manialink::endFrame();
	}

}

?>