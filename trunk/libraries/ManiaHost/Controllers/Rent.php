<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Controllers;

use ManiaLive\DedicatedApi\Structures\GameInfos;

class Rent extends AbstractController
{

	function index()
	{
		$this->request->registerReferer();

		$multipage = new \ManiaLib\Utils\MultipageList(6);
		list($offset, $length) = $multipage->getLimit();

		$service = new \ManiaHost\Services\RentService();
		$rents = $service->getCurrents($this->session->login, $offset, $length + 1);

		$isAvailable = $service->isAvailable();

		$multipage->checkArrayForMorePages($rents);

		$this->response->rents = $rents;
		$this->response->multipage = $multipage;
		$this->response->isAvailable = $isAvailable;
	}

	function selectDuration()
	{

	}

	function rentServer($days = 0, $hours = 0)
	{
		if($days == 0 && $hours == 0 && $this->session->get('duration') == 0)
		{
			throw new UserException(_('You have to rent a server for at least 1 hour'));
		}

		if(!$this->session->get('duration'))
		{
			$this->session->set('duration', $hours + $days * 24);
		}
		$this->request->delete('hours');
		$this->request->delete('days');

		$this->request->registerReferer();

		$this->response->name = $this->request->get('name', $this->session->nickname);
		$this->response->comment = $this->request->get('comment');
		$this->response->maxPlayer = $this->request->get('maxPlayer', 16);
		$this->response->gameMode = $this->request->get('gameMode',
				GameInfos::GAMEMODE_TIMEATTACK);

		$cupWarmup = 2;
		$defaultWarmup = 0;
		switch($this->response->gameMode)
		{
			case GameInfos::GAMEMODE_CUP:$this->response->pointLimit = $this->request->get('cupPointLimit',
						100);
				$cupWarmup = $this->request->get('cupWarmup', 2);
				break;
			case GameInfos::GAMEMODE_ROUNDS:$this->response->pointLimit = $this->request->get('roundsPointLimit',
						50);
				$defaultWarmup = $this->request->get('defaultWarmup');
				break;
			case GameInfos::GAMEMODE_TEAM:$this->response->pointLimit = $this->request->get('teamPointLimit',
						5);
			case GameInfos::GAMEMODE_LAPS:$this->response->timeLimit = $this->request->get('lapsTimeLimit',
						'0:00:00');
				$defaultWarmup = $this->request->get('defaultWarmup');
				break;
			default : $this->response->pointLimit = 0;
				$defaultWarmup = $this->request->get('defaultWarmup');
				$this->response->timeLimit = $this->request->get('timeLimit', '0:05:00');
		}

		$this->response->lapsNumber = $this->request->get('lapsNumber', 5);
		$this->response->rounds = $this->request->get('rounds', 5);
		$this->response->winners = $this->request->get('winner', 3);
		$this->response->passwordPlayer = $this->request->get('passwordPlayer');
		$this->response->maxSpec = $this->request->get('maxSpec', 16);
		$this->response->passwordSpec = $this->request->get('passwordSpec');
		$this->response->callVoteThreshold = $this->request->get('callVoteThreshold',
				50);
		$this->response->callVoteDuration = $this->request->get('callVoteDuration',
				'1:00');
		$this->response->defaultWarmup = ($defaultWarmup ? : '0');
		$this->response->cupWarmup = $cupWarmup;
		$this->response->chatTime = $this->request->get('chatTime', '0:10:00');
		$this->response->passwordReferee = $this->request->get('passwordReferee');
		$this->response->refereeMode = $this->request->get('refereeMode', 0);
	}

	function validate($name = '', $comment = '', $maxPlayer = 16,
			$timeLimit = '0:05:00', $gameMode = 2, $passwordPlayer = '', $maxSpec = 16,
			$passwordSpec = '', $cupWarmup = 2, $defaultWarmup = 0,
			$callVoteThreshold = 0.5, $callVoteDuration = '0:01:00',
			$chatTime = '0:10:00', $teamPointLimit = 5, $lapsTimeLimit = 0,
			$lapsNumber = 5, $cupPointLimit = 100, $rounds = 5, $winners = 2,
			$roundsPointLimit = 50, $allowDownload = true, $refereeMode = 0,
			$alternateRules = false, $passwordReferee = '')
	{
		if($gameMode != GameInfos::GAMEMODE_ROUNDS && $gameMode != GameInfos::GAMEMODE_TIMEATTACK
				&& $gameMode != GameInfos::GAMEMODE_TEAM && $gameMode != GameInfos::GAMEMODE_LAPS && $gameMode != GameInfos::GAMEMODE_CUP
				&& $gameMode != GameInfos::GAMEMODE_STUNTS)
		{
			throw new \InvalidArgumentException(_('Unknown Game Mode'));
		}

		if(!$name)
		{
			throw new ManiaLib\Application\UserException(_('You must name your server'));
		}

		if($maxPlayer + $maxSpec > \ManiaHost\Config::getInstance()->maxPlayerPerServer)
		{
			throw new ManiaLib\Application\UserException(sprintf(_('The number of player and spectator has to be inferior to %d'),
							\ManiaHost\Config::getInstance()->maxPlayerPerServer));
		}

		$serverOptions = array();
		$serverOptions['AllowMapDownload'] = ($allowDownload ? true : false);
		$serverOptions['CallVoteRatio'] = ((double) $callVoteThreshold) / 100;
		$serverOptions['Comment'] = $comment;
		$serverOptions['HideServer'] = 0;
		$serverOptions['Name'] = $name;

		$tmp = explode(':', $callVoteDuration);
		$callVoteDuration = 0;
		$i = 0;
		while($time = array_pop($tmp))
		{
			$callVoteDuration += $time * pow(60, $i++);
		}
		$callVoteDuration *= 1000;

		$serverOptions['NextCallVoteTimeOut'] = $callVoteDuration;
		$serverOptions['NextMaxPlayers'] = (int) $maxPlayer;
		$serverOptions['NextMaxSpectators'] = (int) $maxSpec;
		$serverOptions['Password'] = $passwordPlayer;
		$serverOptions['PasswordForSpectator'] = $passwordSpec;
		$serverOptions['RefereePassword'] = $passwordReferee;
		$serverOptions['RefereeMode'] = $refereeMode;

		$serverOptions['AutoSaveReplays'] = false;
		$serverOptions['AutoSaveValidationReplays'] = false;
		$serverOptions['IsP2PDownload'] = true;
		$serverOptions['IsP2PUpload'] = true;

		$this->session->set('serverOptions', $serverOptions);

		$gameInfos = new GameInfos();
		$gameInfos->allWarmUpDuration = (int) $defaultWarmup;
		$tmp = explode(':', $chatTime);
		$chatTime = 0;
		$i = -1;
		while($time = array_pop($tmp))
		{
			$chatTime += ($i < 0 ? (int) str_pad($time, 3, '0', STR_PAD_RIGHT) : $time * pow(60,
									$i) * 1000);
			++$i;
		}
		$gameInfos->chatTime = (int) $chatTime;
		$gameInfos->cupNbWinners = (int) $winners;
		$gameInfos->cupPointsLimit = (int) $cupPointLimit;
		$gameInfos->cupRoundsPerMap = (int) $rounds;
		$gameInfos->cupWarmUpDuration = (int) $cupWarmup;
		$gameInfos->disableRespawn = 0;
		$gameInfos->finishTimeout = 1;
		$gameInfos->forceShowAllOpponents = 0;
		$gameInfos->gameMode = (int) $gameMode;
		$gameInfos->lapsNbLaps = (int) $lapsNumber;

		$tmp = explode(':', $lapsTimeLimit);
		$lapsTimeLimit = 0;
		$i = 0;
		while($time = array_pop($tmp))
		{
			$lapsTimeLimit += $time * pow(60, $i++);
		}
		$lapsTimeLimit *= 1000;

		$gameInfos->lapsTimeLimit = $lapsTimeLimit;
		$gameInfos->roundsForcedLaps = 0;
		$gameInfos->roundsPointsLimit = (int) $roundsPointLimit;
		$gameInfos->roundsPointsLimitNewRules = (int) $roundsPointLimit;
		$gameInfos->roundsUseNewRules = ($alternateRules ? true : false);
		$gameInfos->teamMaxPoints = (int) $teamPointLimit;
		$gameInfos->teamPointsLimitNewRules = (int) $teamPointLimit;
		$gameInfos->teamPointsLimit = (int) $teamPointLimit;
		$gameInfos->teamUseNewRules = ($alternateRules ? true : false);

		$tmp = explode(':', $timeLimit);
		$timeLimit = 0;
		$i = 0;
		while($time = array_pop($tmp))
		{
			$timeLimit += $time * pow(60, $i++);
		}
		$timeLimit *= 1000;
		$gameInfos->timeAttackLimit = $timeLimit;
		$gameInfos->timeAttackSynchStartPeriod = 0;


		$this->session->set('gameInfos', $gameInfos);

		$this->request->redirectArgList('../select-map/');
	}

	function selectMap($path = '')
	{
		$path = realpath(\ManiaHost\Config::getInstance()->pathToDedicated).DIRECTORY_SEPARATOR.'UserData'.DIRECTORY_SEPARATOR.'Maps'.$path;

		$fileService = new \ManiaHost\Services\MapService();

		$count = $fileService->getCount($path, true);

		$multipage = new \ManiaLib\Utils\MultipageList();
		$multipage->setPerPage(12);
		$multipage->setSize($count);

		list($offset, $length) = $multipage->getLimit();

		$files = $fileService->getList($path, true, $offset, $length);

		foreach($files as $key => $map)
		{
			try
			{
				$datas = $fileService->getData($map->path.DIRECTORY_SEPARATOR.$map->filename);
			}
			catch(\Exception $e)
			{
				$datas['name'] = stristr($map->filename, '.map.gbx', true);
				$datas['author'] = '';
				$datas['authorTime'] = '';
				$datas['environment'] = '';
			}
			$map->name = $datas['name'];
			$map->author = $datas['author'];
			$map->authorTime = $datas['authorTime'];
			$map->environment = $datas['environment'];
			$files[$key] = $map;
		}

		$this->response->files = $files;
		$this->response->mapCount = $count;
		$this->response->multipage = $multipage;
		$this->response->selected = $this->session->get('selected', array());
		$this->response->cost = $this->session->getStrict('duration') * \ManiaHost\Config::getInstance()->hourlyCost;
	}

	function checkout()
	{
		$selectedTracks = $this->session->get('selected');
		$gameInfos = $this->session->get('gameInfos');
		$serverOptions = $this->session->get('serverOptions');
		if(!$selectedTracks)
		{
			throw new \ManiaLib\Application\UserException(_('You have to select maps'));
		}
		if(!$gameInfos)
		{
			throw new \ManiaLib\Application\UserException(_('You have to configure your server'));
		}
		if(!$serverOptions)
		{
			throw new \ManiaLib\Application\UserException(_('You have to configure your server'));
		}

		$service = new \ManiaHost\Services\RentService();
		if(!$service->isAvailable())
		{
			throw new \ManiaLib\Application\UserException('There is no more server available');
		}

		$t = new \Maniaplanet\WebServices\Transaction();
		$t->creatorLogin = \ManiaHost\Config::getInstance()->transactionLogin;
		$t->creatorPassword = \ManiaHost\Config::getInstance()->transactionPassword;
		$t->creatorSecurityKey = \ManiaHost\Config::getInstance()->transactionSecurityKey;
		$t->fromLogin = $this->session->login;
		$t->toLogin = \ManiaHost\Config::getInstance()->transactionLogin;
		$t->cost = $this->session->getStrict('duration') * \ManiaHost\Config::getInstance()->hourlyCost;

		$payments = new \Maniaplanet\WebServices\Payments();
		$t->id = $payments->create($t);
		$this->request->set('transaction', $t->id);
		$url = \ManiaLib\Application\Config::getInstance()->manialink.'?transaction='.$t->id.'&ml-forcepathinfo=%2Frent%2Ffinalize%2F';
		$this->request->redirectAbsolute($url);
	}

	function finalize($transaction)
	{
		$payments = new \Maniaplanet\WebServices\Payments(\ManiaLive\Features\WebServices\Config::getInstance()->username, \ManiaLive\Features\WebServices\Config::getInstance()->password);
		if(!$payments->isPaid($transaction))
		{
			throw new \ManiaLib\Application\UserException('You have to pay to rent the server');
		}

		$selectedTracks = $this->session->get('selected');
		$gameInfos = $this->session->get('gameInfos');
		$serverOptions = $this->session->get('serverOptions');

		$selectedTracks = array_map(array($this, 'dedicatedFilename'), $selectedTracks);
		$rent = new \ManiaHost\Services\Rent();
		$rent->playerLogin = $this->session->login;
		$rent->duration = $this->session->get('duration');
		$rent->cost = $rent->duration * \ManiaHost\Config::getInstance()->hourlyCost;
		$rent->gameInfos = $gameInfos;
		$rent->maps = $selectedTracks;
		$rent->serverOptions = $serverOptions;

		$service = new \ManiaHost\Services\RentService();
		$service->register($rent);

		$this->session->delete('selected');
		$this->session->delete('gameInfos');
		$this->session->delete('serverOptions');

		$this->request->redirectArgList('../');
	}

	function select($filename)
	{
		$selected = $this->session->get('selected', array());
		$this->request->delete('filename');
		$selected[] = $filename;

		$selected = array_unique($selected);
		$this->session->set('selected', $selected);
		$this->request->redirect('../selectMap');
	}

	function unselect($filename)
	{
		$selected = $this->session->get('selected', array());
		$this->request->delete('filename');
		$keys = array_keys($selected, $filename);
		if(count($keys))
		{
			foreach($keys as $key)
			{
				unset($selected[$key]);
			}
			$this->session->set('selected', $selected);
		}
		$this->request->redirect('../selectMap');
	}

	function selectAll($path = '')
	{
		$path = realpath(\ManiaHost\Config::getInstance()->pathToDedicated).DIRECTORY_SEPARATOR.'UserData'.DIRECTORY_SEPARATOR.'Maps'.$path;

		$fileService = new \ManiaHost\Services\MapService();
		$files = $fileService->getList($path, true);
		$object = $this;
		$files = array_map(function (\ManiaHost\Services\Map $m) use ($object)
				{
					return $m->path.DIRECTORY_SEPARATOR.$m->filename;
				}, $files);
		$this->session->set('selected', $files);
		$this->request->redirect('../select-map');
	}

	function unselectAll($path = '')
	{
		$this->session->set('selected', array());
		$this->request->redirect('../select-map');
	}

	function changeMapDownload($allowDownload = true)
	{
		$this->request->set('allowDownload', !$allowDownload);
		$this->request->redirect('../rent-server/');
	}

	function changeReferees($refereeMode = 0)
	{
		$this->request->set('refereeMode', !$refereeMode);
		$this->request->redirect('../rent-server/');
	}

	function changeAlternate($alternateRules = false)
	{
		$this->request->set('alternateRules', !$alternateRules);
		$this->request->redirect('../rent-server/');
	}

	function changeMode($gameMode)
	{
		$this->request->set('gameMode', ($gameMode % 6) + 1);
		$this->request->redirect('../rent-server/');
	}

	protected function dedicatedFilename($filename)
	{
		$search = realpath(\ManiaHost\Config::getInstance()->pathToDedicated).DIRECTORY_SEPARATOR.'UserData'.DIRECTORY_SEPARATOR.'Maps'.DIRECTORY_SEPARATOR;
		return str_ireplace($search, '', $filename);
	}

}

?>