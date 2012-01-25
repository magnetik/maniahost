<?php
/**
 * ManiaHome - Social Network for TrackMania
 *
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @version     $Revision$:
 * @author     $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Views;

use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Quad;
use ManiaLib\Gui\Manialink;

class Header extends \ManiaLib\Application\Views\Header
{

	static $showBackground = true;

	function display()
	{
		parent::display();
		$config = \ManiaHost\Config::getInstance();

		if(static::$showBackground)
		{
			$ui = new Quad(320, 180);
			$ui->setAlign('center', 'center');
			$ui->setImage($config->background, true);
			$ui->save();
		}

		$ui = new \ManiaLib\Gui\Elements\IncludeManialink();
		$ui->setUrl('manialib.xml', false);
		$ui->save();

		Manialink::beginFrame(110,-81,0.1);
		{
			$ui = new \ManiaLib\Gui\Elements\IncludeManialink();
			$query = array();
			$query['url'] = \ManiaLib\Application\Config::getInstance()->manialink;
			$query['name'] = $config->appName;
			$ui->setUrl('http://maniahome.maniaplanet.com/add/?'.http_build_query($query,
							'', '&'));
			$ui->save();
		}
		Manialink::endFrame();
	}

}

?>