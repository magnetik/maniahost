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

class ServerAudience extends \ManiaLib\Application\View
{

	public function display()
	{
		$manialink = $this->request->createLinkArgList('../audience-list/');
		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->quitButton->setManialink($manialink);
		$ui->subTitle->setText('Admin panel');
		$ui->save();

		Manialink::beginFrame(32);
		{
			$ui = new \ManiaLib\Gui\Elements\Quad(180, 60);
			$ui->setPosY(64);
			$ui->setHalign('center');
			$ui->setImage($this->response->avgGraphUrl, true);
			$ui->save();

			$ui = new \ManiaLib\Gui\Elements\Quad(180, 60);
			$ui->setPosY(-4);
			$ui->setHalign('center');
			$ui->setImage($this->response->maxGraphUrl, true);
			$ui->save();
		}
		Manialink::endFrame();
	}

}

?>