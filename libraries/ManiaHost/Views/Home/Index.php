<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Views\Home;

use ManiaLib\Gui\Manialink;
use ManiaLib\Gui\Elements\Button;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Icons128x128_1;
use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\Quad;
use ManiaHost\Cards\Bullet;

class Index extends \ManiaLib\Application\View
{

	public function display()
	{
		$ui = new \ManiaLib\Gui\Cards\Panel(135, 135);
		$ui->setAlign('center', 'center');
		$ui->title->setText('Welcome on '.$this->response->manialinkName);
		$ui->save();

		$layout = new \ManiaLib\Gui\Layouts\Column();
		$layout->setMarginHeight(2);
		Manialink::beginFrame(0, 40, 0.1, 1, $layout);
		{
			$ui = new Bullet();
			$ui->setHalign('center');
			$ui->bullet->setStyle(Quad::Icons128x128_1);
			$ui->bullet->setSubStyle(Icons128x128_1::ServersAll);
			$ui->title->setText(sprintf('Rent a server in 3 simple steps for %d planets per hours',
							$this->response->hourlyCost));
			$ui->save();

			$ui = new Bullet();
			$ui->setHalign('center');
			$ui->bullet->setStyle(Quad::Icons64x64_1);
			$ui->bullet->setSubStyle(Icons64x64_1::First);
			$ui->title->setText('Select duration');
			$ui->save();
			$ui = new Bullet();
			$ui->setHalign('center');
			$ui->bullet->setStyle(Quad::Icons64x64_1);
			$ui->bullet->setSubStyle(Icons64x64_1::Second);
			$ui->title->setText('Configure your server');
			$ui->save();
			$ui = new Bullet();
			$ui->setHalign('center');
			$ui->bullet->setStyle(Quad::Icons64x64_1);
			$ui->bullet->setSubStyle(Icons64x64_1::Third);
			$ui->title->setText('Select your tracks');
			$ui->save();
			$ui = new Bullet();
			$ui->setHalign('center');
			$ui->bullet->setStyle(Quad::Icons128x128_1);
			$ui->bullet->setSubStyle(Icons128x128_1::ServersAll);
			$ui->title->setText('Your server is ready');
			$ui->save();
		}
		Manialink::endFrame();
		$manialink = $this->request->createLink('/rent/');
		$ui = new Button();
		$ui->setHalign('center');
		$ui->setPosition(0, -55, 0.1);
		$ui->setStyle(Button::CardButtonMediumWide);
		$ui->setText('Rent a server');
		$ui->setManialink($manialink);
		$ui->save();
	}

}

?>