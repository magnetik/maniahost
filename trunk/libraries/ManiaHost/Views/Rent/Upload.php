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
use ManiaLib\Gui\Manialink;

class Upload extends \ManiaLib\Application\View
{

	function display()
	{
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->subTitle->setText(_('Select maps'));

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

		$manialink = $this->request->createLinkArgList('../select-map/');
		$ui->quitButton->text->setText('Back');
		$ui->quitButton->setManialink($manialink);
		$ui->save();

		Manialink::beginFrame(40, 65, 0.1);
		{
			$ui = new \ManiaLib\Gui\Cards\Panel(123, 65);
			$ui->setHalign('center');
			$ui->title->setText('Upload');
			$ui->save();

			$ui = new \ManiaLib\Gui\Elements\Label(115);
			$ui->setHalign('center');
			$ui->setPosition(0, -25, 0.1);
			$ui->setText('Your maps will be stored until the end of your rentals');
			$ui->setStyle(\ManiaLib\Gui\Elements\Label::TextTips);
			$ui->save();

			$ui = new \ManiaLib\Gui\Elements\FileEntry(100, 6);
			$ui->setHalign('center');
			$ui->setPosition(0, -35, 0.9);
			$ui->setFolder('Maps');
			$ui->setName('file');
			$ui->save();

			$this->request->set('file', 'file');

			$manialink = $this->request->createLink('../do-upload/');


			$ui = new \ManiaLib\Gui\Elements\Button();
			$ui->setHalign('center');
			$ui->setPosition(0, -45, 0.1);
			$ui->setText('Send');
			$ui->setManialink(sprintf('POST(%s,file)', $manialink));
			$ui->save();

			if($this->response->message)
			{
				$ui = new \ManiaLib\Gui\Elements\Label(110);
				$ui->setHalign('center');
				$ui->setPosition(0, -56, 0.1);
				$ui->setStyle(\ManiaLib\Gui\Elements\Label::TextTitleError);
				$ui->setText($this->response->message);
				$ui->save();
			}
		}
		Manialink::endFrame();

		Manialink::beginFrame(40, -10, 0.1);
		{
			$ui = new \ManiaLib\Gui\Cards\Panel(123, 50);
			$ui->setHalign('center');
			$ui->title->setText('Available space');
			$ui->titleBg->setSubStyle(Bgs1::BgTitle3_2);
			$ui->save();

			$ui = new \ManiaLib\Gui\Elements\Label(80);
			$ui->setHalign('center');
			$ui->setPosition(0, -25, 0.1);
			$ui->setText(sprintf('%.2fMo / 20Mo',
							$this->response->availableSpace / pow(2, 20)));
			$ui->save();

			$ui = new Bgs1(100, 6);
			$ui->setAlign('center', 'center');
			$ui->setPosition(0, -35, 0.1);
			$ui->setSubStyle(Bgs1::BgProgressBar);
			$ui->save();

			$ui = new Bgs1($this->response->hRatioProgressBar * 100, 5);
			$ui->setValign('center');
			$ui->setPosition(-50, -35, 0.2);
			$ui->setSubStyle(Bgs1::ProgressBar);
			$ui->save();
		}
		Manialink::endFrame();
	}

}

?>