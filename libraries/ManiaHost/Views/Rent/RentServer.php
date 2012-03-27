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
use ManiaLib\Gui\Elements\Bgs1;
use ManiaLib\Gui\Elements\Button;
use ManiaLib\Gui\Elements\Label;
use ManiaLib\Gui\Elements\Entry;
use ManiaLib\Gui\Elements\Icons64x64_1;
use ManiaLib\Gui\Elements\UIConstructionSimple_Buttons;
use ManiaLib\ManiaScript\UI;
use ManiaLib\ManiaScript\Event;
use ManiaLib\ManiaScript\Action;
use ManiaLive\DedicatedApi\Structures\GameInfos;

class RentServer extends \ManiaLib\Application\View
{

	function display()
	{
		Manialink::appendScript('main() {');

		$this->request->set('name', 'name');
		$this->request->set('comment', 'comment');
		$this->request->set('maxPlayer', 'maxPlayer');
		switch($this->response->gameMode)
		{
			case GameInfos::GAMEMODE_ROUNDS:
				$this->request->set('roundsPointLimit', 'roundsPointLimit');
				break;
			case GameInfos::GAMEMODE_TIMEATTACK:
				$this->request->set('timeLimit', 'timeLimit');
				break;
			case GameInfos::GAMEMODE_TEAM:
				$this->request->set('teamPointLimit', 'teamPointLimit');
				break;
			case GameInfos::GAMEMODE_LAPS:
				$this->request->set('lapsTimeLimit', 'lapsTimeLimit');
				$this->request->set('lapsNumber', 'lapsNumber');
				break;
			case GameInfos::GAMEMODE_CUP:
				$this->request->set('cupPointLimit', 'cupPointLimit');
				$this->request->set('rounds', 'rounds');
				$this->request->set('winners', 'winners');
				break;
			case GameInfos::GAMEMODE_STUNTS:
				$this->request->set('timeLimit', 'timeLimit');
				break;
		}
		$this->request->set('gameMode', $this->response->gameMode);
		$this->request->set('passwordPlayer', 'passwordPlayer');
		$this->request->set('maxSpec', 'maxSpec');
		$this->request->set('passwordSpec', 'passwordSpec');
		if($this->response->gameMode == GameInfos::GAMEMODE_CUP)
		{
			$this->request->set('cupWarmup', 'cupWarmup');
		}
		else
		{
			$this->request->set('defaultWarmup', 'defaultWarmup');
		}
		$this->request->set('callVoteThreshold', 'callVoteThreshold');
		$this->request->set('callVoteDuration', 'callVoteDuration');
		$this->request->set('chatTime', 'chatTime');

		$ui = new \ManiaLib\Gui\Cards\Navigation\Menu();
		$ui->title->setText(\ManiaHost\Config::getInstance()->appName);
		$ui->subTitle->setText('Powered by ManiaHost');
		$manialink = $this->request->createLink('../select-duration');
		$ui->quitButton->text->setText('Back');
		$ui->quitButton->setManialink($manialink);
		$ui->save();

		Manialink::beginFrame(0, 60);
		{
			$ui = new \ManiaLib\Gui\Cards\Panel(85, 110);
			$ui->setAlign('center');
			$ui->titleBg->setSubStyle(Bgs1::BgTitle3_5);
			$ui->title->setText('Server parameters');
			$ui->save();



			Manialink::beginFrame(0, -24.5, 0.5);
			{
				$ui = new Label(80);
				$ui->setHalign('center');
				$ui->setStyle(Label::TextTips);
				$ui->setText('Game Name');
				$ui->save();

				$ui = new Entry(69);
				$ui->setAlign('center', 'bottom');
				$ui->setPosY(-10.5);
				$ui->setName('name');
				$ui->setDefault($this->response->name);
				$ui->save();

				$ui = new Label(80);
				$ui->setHalign('center');
				$ui->setPosY(-12);
				$ui->setStyle(Label::TextTips);
				$ui->setText('Comment');
				$ui->save();

				$ui = new Entry(69, 18);
				$ui->setPosition(-34.5, -16);
				$ui->setName('comment');
				$ui->setDefault($this->response->comment);
				$ui->save();

				$ui = new Label(80);
				$ui->setAlign('right', 'bottom');
				$ui->setPosition(-2, -40);
				$ui->setText('Max players');
				$ui->save();

				$ui = new Entry(9, 5);
				$ui->setValign('bottom');
				$ui->setPosition(2, -40);
				$ui->setName('maxPlayer');
				$ui->setDefault($this->response->maxPlayer);
				$ui->setStyle(Label::TextValueSmall);
				$ui->save();
				$this->request->set('maxPlayer', 'maxPlayer');

				$ui = new UIConstructionSimple_Buttons(7, 7);
				$ui->setSubStyle(UIConstructionSimple_Buttons::Help);
				$ui->setValign('bottom');
				$ui->setPosition(12, -41);
				$ui->setId('maxPlayerHelp');
				$ui->setScriptEvents();
				$ui->save();

				UI::tooltip('maxPlayerHelp', sprintf('Max player allowed is %d', \ManiaHost\Config::getInstance()->maxPlayerPerServer));

				$ui = new Label(80);
				$ui->setHalign('center');
				$ui->setPosY(-44);
				$ui->setStyle(Label::TextTips);
				$ui->setText('Game mode');
				$ui->save();

				switch($this->response->gameMode)
				{
					case GameInfos::GAMEMODE_ROUNDS:
						$gameModeLabel = 'Rounds';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Point limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('roundsPointLimit');
						$ui->setDefault($this->response->pointLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
					case GameInfos::GAMEMODE_TEAM:
						$gameModeLabel = 'Team';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Point limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('teamPointLimit');
						$ui->setDefault($this->response->pointLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
					case GameInfos::GAMEMODE_LAPS:
						$gameModeLabel = 'Laps';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Time limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('lapsTimeLimit');
						$ui->setDefault($this->response->timeLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();

						$ui = new Label(40);
						$ui->setHalign('right');
						$ui->setPosition(0, -65);
						$ui->setText('Number of laps');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -69);
						$ui->setName('lapsNumber');
						$ui->setDefault($this->response->lapsNumber);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
					case GameInfos::GAMEMODE_CUP:
						$gameModeLabel = 'Cup';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Point limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('cupPointLimit');
						$ui->setDefault($this->response->pointLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();

						$ui = new Label(40);
						$ui->setHalign('right');
						$ui->setPosition(0, -65);
						$ui->setText('Rounds per map');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -69);
						$ui->setName('rounds');
						$ui->setDefault($this->response->rounds);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();

						$ui = new Label(40);
						$ui->setHalign('right');
						$ui->setPosition(0, -71);
						$ui->setText('Number of winners');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -75);
						$ui->setName('winners');
						$ui->setDefault($this->response->winners);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
					case GameInfos::GAMEMODE_STUNTS:
						$gameModeLabel = 'Stunts';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Time limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('timeLimit');
						$ui->setDefault($this->response->timeLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
					case GameInfos::GAMEMODE_TIMEATTACK:
						$gameModeLabel = 'Time Attack';

						$ui = new Label(20);
						$ui->setHalign('right');
						$ui->setPosition(0, -59);
						$ui->setText('Time limit');
						$ui->save();

						$ui = new Entry(32, 4.5);
						$ui->setValign('bottom');
						$ui->setPosition(2, -63);
						$ui->setName('timeLimit');
						$ui->setDefault($this->response->timeLimit);
						$ui->setStyle(Label::TextValueSmall);
						$ui->save();
						break;
				}

				$manialink = $this->request->createLink('../change-mode');

				$ui = new Label(69, 5);
				$ui->setPosY(-50.5);
				$ui->setAlign('center', 'center2');
				$ui->setStyle(Label::TextValueMedium);
				$ui->setText($gameModeLabel);
				$ui->setManialink($manialink);
				$ui->save();

				$ui = new Button();
				$ui->setHalign('center');
				$ui->setPosition(0,-76,0.1);
				$ui->setText('Advanced');
				$ui->setStyle(Button::CardButtonSmall);
				$ui->setId('advanceSwitch');
				$ui->setScriptEvents();
				$ui->save();

				Event::addListener('advanceSwitch', Event::mouseClick, array(Action::toggle, 'advancedPanel'));
				\ManiaLib\ManiaScript\Manipulation::hide('advancedPanel');
			}
			Manialink::endFrame();

			Manialink::beginFrame(60, -20, 0.1);
			Manialink::setFrameId('advancedPanel');
			{
				$ui = new Bgs1(83, 103);
				$ui->setSubStyle(Bgs1::BgWindow2);
				$ui->save();

				Manialink::beginFrame(46, -10, 0.5);
				{
					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Password (optional)');
					$ui->save();

					$ui = new Entry(32, 4.5);
					$ui->setPosition(2, 0);
					$ui->setValign('bottom');
					$ui->setName('passwordPlayer');
					$ui->setDefault($this->response->passwordPlayer);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Max spectators');
					$ui->setPosY(-6);
					$ui->save();

					$ui = new Entry(8, 4);
					$ui->setPosition(2, -5);
					$ui->setValign('bottom');
					$ui->setName('maxSpec');
					$ui->setDefault($this->response->maxSpec);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Spectator password (opt.)');
					$ui->setPosY(-12);
					$ui->save();

					$ui = new Entry(32, 4.5);
					$ui->setPosition(2, -12);
					$ui->setValign('bottom');
					$ui->setName('passwordSpec');
					$ui->setDefault($this->response->passwordSpec);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					if($this->response->gameMode == GameInfos::GAMEMODE_ROUNDS || $this->response->gameMode == GameInfos::GAMEMODE_TEAM)
					{
						$ui = new Label(40);
						$ui->setAlign('right', 'bottom');
						$ui->setText('Use alternate rules');
						$ui->setPosY(-18);
						$ui->save();

						$default = $this->request->get('alternateRules', false);
						$manialink = $this->request->createLink('../change-alternate');
						$ui = new Label(10, 5);
						$ui->setAlign('center', 'center2');
						$ui->setPosition(7, -16.5);
						$ui->setText('$o'.($default ? 'Yes' : 'No'));
						$ui->setStyle(Label::TextValueSmall);
						$ui->setFocusAreaColor1(($default ? '0808' : '8008'));
						$ui->setFocusAreaColor2(($default ? '0B0F' : 'B00F'));
						$ui->setManialink($manialink);
						$ui->save();
					}

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Warm-up phase duration');
					$ui->setPosY(-48);
					$ui->save();

					$ui = new Entry(15, 4.5);
					$ui->setPosition(2, -48);
					$ui->setValign('bottom');
					if($this->response->gameMode == GameInfos::GAMEMODE_CUP)
					{
						$ui->setName('cupWarmup');
						$ui->setDefault($this->response->cupWarmup);
					}
					else
					{
						$ui->setName('defaultWarmup');
						$ui->setDefault($this->response->defaultWarmup);
					}
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Allow map download');
					$ui->setPosY(-53.5);
					$ui->save();

					$default = $this->request->get('allowDownload', true);
					$manialink = $this->request->createLink('../change-map-download');
					$ui = new Label(10, 5);
					$ui->setAlign('center', 'center2');
					$ui->setPosition(7, -51.5);
					$ui->setText('$o'.($default ? 'Yes' : 'No'));
					$ui->setStyle(Label::TextValueSmall);
					$ui->setFocusAreaColor1(($default ? '0808' : '8008'));
					$ui->setFocusAreaColor2(($default ? '0B0F' : 'B00F'));
					$ui->setManialink($manialink);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('CallVote threshold (%)');
					$ui->setPosY(-59);
					$ui->save();

					$ui = new Entry(8, 4.5);
					$ui->setPosition(2, -59);
					$ui->setValign('bottom');
					$ui->setName('callVoteThreshold');
					$ui->setDefault($this->response->callVoteThreshold);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('CallVote timeout');
					$ui->setPosY(-65);
					$ui->save();

					$ui = new Entry(15, 4.5);
					$ui->setPosition(2, -65);
					$ui->setValign('bottom');
					$ui->setName('callVoteDuration');
					$ui->setDefault($this->response->callVoteDuration);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Chat time');
					$ui->setPosY(-71);
					$ui->save();

					$ui = new Entry(15, 4.5);
					$ui->setPosition(2, -71);
					$ui->setValign('bottom');
					$ui->setName('chatTime');
					$ui->setDefault($this->response->chatTime);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();
					$this->request->set('chatTime', 'chatTime');

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Referee password (opt.)');
					$ui->setPosY(-77);
					$ui->save();

					$ui = new Entry(32, 4.5);
					$ui->setPosition(2, -77);
					$ui->setValign('bottom');
					$ui->setName('passwordReferee');
					$ui->setDefault($this->response->passwordReferee);
					$ui->setStyle(Label::TextValueSmall);
					$ui->save();

					$ui = new Label(40);
					$ui->setAlign('right', 'bottom');
					$ui->setText('Players to validate');
					$ui->setPosY(-83);
					$ui->save();

					$default = $this->request->get('refereeMode', false);
					$manialink = $this->request->createLink('../change-referees');
					$ui = new Label(15, 5);
					$ui->setAlign('left', 'center2');
					$ui->setPosition(2, -81);
					$ui->setText('$o'.($default ? 'Top3' : 'All'));
					$ui->setStyle(Label::TextValueSmall);
					$ui->setManialink($manialink);
					$ui->save();
				}
				Manialink::endFrame();
			}
			Manialink::endFrame();

			$manialink = $this->request->createLink('../validate/');

			$ui = new Button();
			$ui->setHalign('center');
			$ui->setPosY(-115);
			$ui->setText('Launch');
			$ui->setManialink($manialink);
			$ui->save();
		}
		Manialink::endFrame();

		Manialink::appendScript('manialib_main_loop();}');
	}

}

?>