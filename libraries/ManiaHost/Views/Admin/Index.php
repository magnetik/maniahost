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
use ManiaLib\Gui\Elements\Icons128x128_1;

class Index extends \ManiaLib\Application\View
{

	public function display()
	{
		$manialink = $this->request->createLink('/rent/');
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->quitButton->setManialink($manialink);
		$ui->subTitle->setText('Admin panel');

		$ui->addItem();
		$ui->lastItem->text->setText('Incomes');
		$ui->lastItem->icon->setSubStyle(Icons128x128_1::Coppers);
		$ui->lastItem->setSelected();

		$manialink = $this->request->createLink('/admin/audience-list/');
		$ui->addItem();
		$ui->lastItem->text->setText('Servers Audience');
		$ui->lastItem->icon->setSubStyle(Icons128x128_1::Statistics);
		$ui->lastItem->setManialink($manialink);

		$ui->save();

		Manialink::beginFrame(35);
		{
			$quad = new Quad(180, 60);
			$quad->setPosY(64);
			$quad->setHalign('center');
			$quad->setImage($this->response->rentalUrl, true);
			$quad->save();

			$quad = new Quad(180, 60);
			$quad->setPosY(-4);
			$quad->setHalign('center');
			$quad->setImage($this->response->incomesUrl, true);
			$quad->save();
		}
		Manialink::endFrame();
	}

}

?>