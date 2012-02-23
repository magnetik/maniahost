<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
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

class MyMaps extends \ManiaLib\Application\View
{

	public function display()
	{
		\ManiaLib\ManiaScript\Main::begin();

		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->subTitle->setText(_('Select maps'));

		$manialink = $this->request->createLinkArgList('../default-maps/');
		$ui->addItem();
		$ui->lastItem->text->setText('Default maps');
		$ui->lastItem->icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Browse);
		$ui->lastItem->setManialink($manialink);

		$manialink = $this->request->createLinkArgList('../upload/');
		$ui->addItem(\ManiaLib\Gui\Cards\Navigation\Menu::BUTTONS_BOTTOM);
		$ui->lastItem->text->setText('Upload');
		$ui->lastItem->icon->setSubStyle(\ManiaLib\Gui\Elements\Icons128x128_1::Save);
		$ui->lastItem->setManialink($manialink);

		$manialink = $this->request->createLinkArgList('../select-map/');
		$ui->quitButton->text->setText('Back');
		$ui->quitButton->setManialink($manialink);
		$ui->save();

		Manialink::beginFrame(17, 66, 0.2);
		{
			$ui = new \ManiaLib\Gui\Cards\Panel(94, 20);
			$ui->setHalign('center');
			$ui->title->setText(_('Maps'));
			$ui->save();

			$ui = new Label();
			$ui->setText($this->response->mapCount);
			$ui->setHalign('right');
			$ui->setPosition(43, -7, 0.1);
			$ui->save();

			Manialink::beginFrame(0, -20, 0);
			{
				$ui = new Bgs1(90, 6);
				$ui->setSubStyle(Bgs1::BgList);
				$ui->setHalign('center');
				$ui->save();

				$manialink = $this->request->createLink();

				$ui = new Icons64x64_1(6, 6);
				$ui->setPosition(-45, 0, 0.1);
				$ui->setSubStyle(Icons64x64_1::ToolUp);
				$ui->setManialink($manialink);
				$ui->save();

				$ui = new Icons64x64_1(6, 6);
				$ui->setHalign('right');
				$ui->setPosition(45, 0, 0.1);
				$ui->setSubStyle(Icons64x64_1::Refresh);
				$ui->setManialink($manialink);
				$ui->save();

				$this->request->set('path', $this->response->path);
				$manialink = $this->request->createLink($this->response->selector);
				$this->request->delete('path');

				$ui = new Icons64x64_1(6, 6);
				$ui->setHalign('right');
				$ui->setPosition(39, 0, 0.1);
				$ui->setSubStyle(Icons64x64_1::ToolRoot);
				$ui->setManialink($manialink);
				$ui->save();
			}
			Manialink::endFrame();

			$layout = new \ManiaLib\Gui\Layouts\Column();
			Manialink::beginFrame(-45, -30, 0.1, 1, $layout);
			{
				foreach($this->response->files as $file)
				{
					$this->request->set('filename', $file->path.$file->filename);

					$card = new \ManiaHost\Cards\File();
					if(in_array($file->path.$file->filename, $this->response->selected))
					{
						$manialink = $this->request->createLink('../unselect/');
						$card->setSubStyle(Bgs1::BgCard);
					}
					else
					{
						$manialink = $this->request->createLink('../select/');
					}
					if(!in_array($file->path.$file->filename, $this->response->used))
					{
						$manialink = $this->request->createLink('../delete-map');

						$card->deleteIcon->setId('delete');
						$card->deleteIcon->setScriptEvents();

						\ManiaLib\ManiaScript\UI::dialog('delete',
								'Dou you want to delete this map '.$file->name,
								array(\ManiaLib\ManiaScript\Action::manialink, $manialink));
					}
					else
					{
						$card->deleteIcon->setScale(0);
					}
					$card->setManialink($manialink);
					$card->name->setText($file->name);
					$card->save();
				}
			}
			Manialink::endFrame();

			$this->response->multipage->pageNavigator->setPosition(0, -135, 0.1);
			$this->response->multipage->savePageNavigator();

			$manialink = $this->request->createLink('../checkout');

			$ui = new \ManiaLib\Gui\Elements\Button();
			$ui->setText(_('Play'));
			$ui->setAlign('center', 'center2');
			$ui->setPosition(100, -136.25);
			$ui->setManialink($manialink);
			$ui->save();
		}
		Manialink::endFrame();

		\ManiaLib\ManiaScript\Main::loop();
		\ManiaLib\ManiaScript\Main::end();
	}

}

?>