<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Views\Admin;

use ManiaLib\Gui\Manialink;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Cards\Panel;
use ManiaLib\Gui\Elements\Icons128x128_1;

class AudienceList extends \ManiaLib\Application\View
{

	public function display()
	{
		$manialink = $this->request->createLink('/rent/');
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->quitButton->setManialink($manialink);
		$ui->subTitle->setText('Admin panel');

		$manialink = $this->request->createLink('/admin/');
		$ui->addItem();
		$ui->lastItem->text->setText('Incomes');
		$ui->lastItem->icon->setSubStyle(Icons128x128_1::Coppers);
		$ui->lastItem->setManialink($manialink);

		$ui->addItem();
		$ui->lastItem->text->setText('Servers Audience');
		$ui->lastItem->icon->setSubStyle(Icons128x128_1::Statistics);
		$ui->lastItem->setSelected();

		$ui->save();

		Manialink::beginFrame(32, 40);
		{
			$ui = new Panel(100, 90);
			$ui->setHalign('center');
			+
					$ui->title->setText('Server list');
			$ui->save();

			$ui = new Label(100);
			$ui->setHalign('center');
			$ui->setPosition(0, -23, 0.1);
			$ui->setStyle(Label::TextTips);
			$ui->setText('Choose a server');
			$ui->save();

			$layout = new \ManiaLib\Gui\Layouts\VerticalFlow(30, 50);
			Manialink::beginFrame(-40, -30, 0.1, 1, $layout);
			{
				foreach($this->response->servers as $server)
				{
					$this->request->set('serverLogin', $server);
					$manialink = $this->request->createLink('../server-audience/');
					$this->request->restore('serverLogin');
					$ui = new Label(30, 7);
					$ui->setText($server);
					$ui->setStyle(Label::TextCardMedium);
					$ui->setManialink($manialink);
					$ui->save();
				}
			}
			Manialink::endFrame();
			$ui = $this->response->multipage;
			$ui->pageNavigator->setPosition(0, -85, 0.1);
			$ui->savePageNavigator();
		}
		Manialink::endFrame();
	}

}

?>