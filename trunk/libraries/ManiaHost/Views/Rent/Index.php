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
use ManiaLib\Gui\Cards\Navigation\Menu;
use ManiaLib\Gui\Elements\Icons128x128_1;
use ManiaLib\Gui\Elements\Bgs1;

class Index extends \ManiaLib\Application\View
{

	function display()
	{
		$manialink = $this->request->createLink('/');
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->quitButton->setManialink($manialink);
		$ui->subTitle->setText('Powered by ManiaHost');


		$manialink = $this->request->createLink('../select-duration/');

		if($this->response->isAvailable)
		{
			$ui->addItem();
			$ui->lastItem->text->setText('Rent a server');
			$ui->lastItem->setManialink($manialink);
		}
		if($this->response->isAdmin)
		{
			$manialink = $this->request->createLinkArgList('/admin/');
			$ui->addItem(Menu::BUTTONS_BOTTOM);
			$ui->lastItem->text->setText('Admin panel');
			$ui->lastItem->icon->setSubStyle(Icons128x128_1::Options);
			$ui->lastItem->setManialink($manialink);
		}
		$ui->save();

		$ui = new \ManiaLib\Gui\Cards\Panel(206, 10);
		$ui->setHalign('center');
		$ui->setPosition(40, 80, 0.1);
		$ui->title->setText('Current rents');
		$ui->save();

		if(!count($this->response->rents))
		{
			$ui = new \ManiaLib\Gui\Elements\Label(60);
			$ui->setStyle(\ManiaLib\Gui\Elements\Label::TextInfoMedium);
			$ui->setText('$000You have no rents in progress');
			$ui->setPosition(-60, 55, 0.1);
			$ui->save();
		}
		else
		{
			$layout = new \ManiaLib\Gui\Layouts\Column();
			$layout->setMarginHeight(4);
			Manialink::beginFrame(-60, 60, 0.1, 1, $layout);
			{
				foreach($this->response->rents as $rent)
				{
					$remaining = ($rent->rentDate + $rent->duration * 3600) - time();
					$remaining = round(((double) $remaining) / 3600, 2);

					$card = new \ManiaHost\Cards\Rent();
					$card->name->setText(sprintf('%s', $rent->serverOptions['Name']));
					if($rent->login)
					{
						$card->login->setText(sprintf('$oserver login$o: %s', $rent->login));
						$card->setManialink('maniaplanet://#join='.$rent->login);
					}
					else
					{
						$card->login->setText('Your server will start soon. Refresh the page to see it. If it does not start contact the admin.');
					}
					$card->remainingTime->setText(sprintf('$oRemaining time$o: %s hours',
									$remaining));

					$this->request->set('idRent', $rent->id);
					$manialink = $this->request->createLink('/rent/renew/');
					$this->request->restore('idRent');
					$card->renew->setText('Renew');
					$card->renew->setManialink($manialink);
					$card->save();
				}
			}
			Manialink::endFrame();

			$this->response->multipage->pageNavigator->setPosition(40, -60);
			$this->response->multipage->savePageNavigator();
		}
	}

}

?>