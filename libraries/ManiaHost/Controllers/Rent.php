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

		$serverService = new \ManiaHost\Services\ServerService();
		$isAvailable = $serverService->availableCount();

		$multipage->checkArrayForMorePages($rents);

		$config = \ManiaHost\Config::getInstance();
		$this->response->rents = $rents;
		$this->response->multipage = $multipage;
		$this->response->isAvailable = $isAvailable;
		$this->response->isAdmin = in_array($this->session->login,
				$config->adminLogins);
	}

	function selectDuration()
	{

	}

	function rentServer($days = 0, $hours = 0)
	{
		if($days == 0 && $hours == 0 && $this->session->get('duration') == 0)
		{
			throw new \ManiaLib\Application\UserException('You have to rent a server for at least 1 hour');
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
			throw new \InvalidArgumentException('Unknown Game Mode');
		}

		if(!$name)
		{
			throw new \ManiaLib\Application\UserException('You must name your server');
		}

		if($maxPlayer + $maxSpec > \ManiaHost\Config::getInstance()->maxPlayerPerServer)
		{
			throw new \ManiaLib\Application\UserException(sprintf('The number of player and spectator has to be inferior to %d',
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

	function selectMap()
	{
		$this->request->registerReferer();
	}

	function upload($message = '')
	{
		$connection = $this->getServerConnection();
		$mapsPath = $connection->getMapsDirectory();
		$path = '$uploaded/'.$this->session->login.'/';

		$mapService = new \ManiaHost\Services\MapService($mapsPath);
		$size = $mapService->getSize($path, array(), null, null, null, null, true);

		$availableSpace = 20 * pow(2, 20) - $size;
		$this->response->message = $message;
		$this->response->availableSpace = $availableSpace;
		$this->response->hRatioProgressBar = 1 - $availableSpace / (20 * pow(2, 20));
	}

	function doUpload($file)
	{
		if(!preg_match('/(\.map\.gbx)$/ixu', $file))
		{
			throw new \ManiaLib\Application\UserException('Please upload only maps');
		}

		try
		{
			$path = '$uploaded/'.$this->session->login.'/';

			$connection = $this->getServerConnection();
			$mapsPath = $connection->getMapsDirectory();

			$mapService = new \ManiaHost\Services\MapService($mapsPath);
			$maxSize = 20 * pow(2, 20) - $mapService->getSize($path, array(), null, null,
							null, null, true);
			$maxSize = ($maxSize > pow(2, 20) ? pow(2, 20) : $maxSize);

			$data = file_get_contents('php://input', null, null, null, $maxSize);

			$connection->writeFileFromString($path.$file, $data);

			$datas = $mapService->getData($mapsPath.$path.$file);
			$map = new \ManiaHost\Services\Map();
			$map->filename = $file;
			$map->path = $path;
			foreach($datas as $key => $value)
			{
				$map->$key = $value;
			}
			$mapService->register($map);
		}
		catch(\Exception $e)
		{
			\ManiaLib\Application\ErrorHandling::logException($e);
			$this->request->set('message', 'An error append during upload');
			$this->request->redirectArgList('../upload/', 'message');
		}

		$this->request->set('message', 'file successfully uploaded');
		$this->request->redirectArgList('../upload/', 'message');
	}

	function myMaps()
	{
		$this->request->registerReferer();

		$connection = $this->getServerConnection();

		$mapsPath = $connection->getMapsDirectory();

		$path = '$uploaded/'.$this->session->login.'/';

		$mapService = new \ManiaHost\Services\MapService($mapsPath);

		$count = $mapService->getCount($path, array(), null, null, null, null, true);
		$used = $mapService->getUsed($this->session->login);

		if(!$count)
		{
			$this->request->redirect('../upload/');
		}

		$multipage = new \ManiaLib\Utils\MultipageList();
		$multipage->setPerPage(12);
		$multipage->setSize($count);

		list($offset, $length) = $multipage->getLimit();

		$files = $mapService->getList($path, array(), null, null, null, null, true,
				$offset, $length);
		$this->chooseSelector($path, false);

		$this->response->path = $path;
		$this->response->files = $files;
		$this->response->mapCount = $count;
		$this->response->multipage = $multipage;
		$this->response->selected = $this->session->get('selected', array());
		$this->response->used = $used;
	}

	function defaultMaps($path = '')
	{
		$this->request->registerReferer();

		$excludePath = array('$uploaded/'.$this->session->login.'/');
		$connection = $this->getServerConnection();
		$mapPath = $connection->getMapsDirectory();

		$fileService = new \ManiaHost\Services\MapService($mapPath);

		$count = $fileService->getCount($path, $excludePath, null, null, null, null,
				true);

		$multipage = new \ManiaLib\Utils\MultipageList();
		$multipage->setPerPage(12);
		$multipage->setSize($count);

		list($offset, $length) = $multipage->getLimit();

		$files = $fileService->getList($path, $excludePath, null, null, null, null,
				true, $offset, $length);

		$this->chooseSelector($path, true);

		$this->response->files = $files;
		$this->response->mapCount = $count;
		$this->response->multipage = $multipage;
		$this->response->selected = $this->session->get('selected', array());
	}

	function checkout()
	{
		$selectedTracks = $this->session->get('selected');
		$gameInfos = $this->session->get('gameInfos');
		$serverOptions = $this->session->get('serverOptions');
		if(!$selectedTracks)
		{
			throw new \ManiaLib\Application\UserException('You have to select maps');
		}
		if(!$gameInfos)
		{
			throw new \ManiaLib\Application\UserException('You have to configure your server');
		}
		if(!$serverOptions)
		{
			throw new \ManiaLib\Application\UserException('You have to configure your server');
		}

		$service = new \ManiaHost\Services\ServerService();
		if(!$service->availableCount())
		{
			throw new \ManiaLib\Application\UserException('There is no more server available');
		}

		$transactionService = new \ManiaHost\Services\TransactionService();
		$t = $transactionService->create($this->session->login, $this->session->getStrict('duration') * \ManiaHost\Config::getInstance()->hourlyCost);

		$this->session->set('transaction-'.$this->session->login, $t);

		$url = \ManiaLib\Application\Config::getInstance()->manialink.'?&ml-forcepathinfo=%2Frent%2Ffinalize%2F'.($t->id ? '&transaction='.$t->id : '');
		$this->request->redirectAbsolute($url);
	}

	function finalize($transaction = 0)
	{
		$transactionObject = $this->session->getStrict('transaction-'.$this->session->login);
		$transactionService = new \ManiaHost\Services\TransactionService();
		if(!$transactionService->isPaid($transactionObject))
		{
			throw new \ManiaLib\Application\UserException('You have to pay to rent the server');
		}

		$selectedTracks = $this->session->get('selected');
		$gameInfos = $this->session->get('gameInfos');
		$serverOptions = $this->session->get('serverOptions');

		$rent = new \ManiaHost\Services\Rent();
		$rent->playerLogin = $this->session->login;
		$rent->duration = $this->session->get('duration');
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

	function renew($idRent)
	{
		$rentService = new \ManiaHost\Services\RentService();
		$rent = $rentService->get($idRent);
		if($this->session->login != $rent->playerLogin)
		{
			throw new \ManiaLib\Application\UserException('You are not allowed to edit this rental');
		}
	}

	function checkoutExtend($idRent, $hours = 0, $days = 0)
	{
		if($hours < 0 || $days < 0 || $hours + $days == 0)
		{
			throw new \ManiaLib\Application\UserException('You have to enter a positive duration');
		}

		$duration = $hours + 24 * $days;

		$transactionService = new \ManiaHost\Services\TransactionService();
		$transaction = $transactionService->create($this->session->login,
				$duration * \ManiaHost\Config::getInstance()->hourlyCost);

		$query = array();
		$query['ml-forcepathinfo'] = '/rent/pay-extend/';
		if($transaction->id)
		{
			$query['transaction'] = $transaction->id;
		}
		\ManiaLib\Utils\Logger::info((int)$idRent);
		$query['idRent'] = $idRent;
		$query['duration'] = $duration;

		$this->session->set('transaction-'.$this->session->login, $transaction);

		$queryString = http_build_query($query);
		$url = \ManiaLib\Application\Config::getInstance()->manialink.'?'.$queryString;
		$this->request->redirectAbsolute($url);
	}

	function payExtend($idRent, $duration)
	{
		\ManiaLib\Utils\Logger::info((int)$idRent);
		$transactionObject = $this->session->getStrict('transaction-'.$this->session->login);

		$transactionService = new \ManiaHost\Services\TransactionService();
		if(!$transactionService->isPaid($transactionObject))
		{
			throw new \ManiaLib\Application\UserException('You have to pay to rent the server');
		}


		$rentService = new \ManiaHost\Services\RentService();
		$rent = $rentService->get($idRent);
		\ManiaLib\Utils\Logger::info($rent);
		$rent->duration += $duration;
		$rentService->updateRent($rent);
		$this->request->redirectArgList('/rent/');
	}

	function select($filename)
	{
		$selected = $this->session->get('selected', array());
		$this->request->delete('filename');
		$selected[] = $filename;

		$selected = array_unique($selected);
		$this->session->set('selected', $selected);
		$this->request->redirectToReferer();
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
		$this->request->redirectToReferer();
	}

	function selectAll($path = '')
	{
		$connection = $this->getServerConnection();
		$mapsPath = $connection->getMapsDirectory();

		$fileService = new \ManiaHost\Services\MapService($mapsPath);
		$files = $fileService->getList($path, array(), null, null, null, null, true);
		$files = array_map(function (\ManiaHost\Services\Map $m)
				{
					return $m->path.$m->filename;
				}, $files);
		$files = array_merge($files, $this->session->get('selected', array()));
		$files = array_unique($files);
		$this->session->set('selected', $files);
		$this->request->redirectToReferer();
	}

	function unselectAll($path = '')
	{
		$connection = $this->getServerConnection();
		$mapsPath = $connection->getMapsDirectory();

		$mapService = new \ManiaHost\Services\MapService($mapsPath);
		$maps = $mapService->getList($path, array(), null, null, null, null, true);

		$maps = array_map(function (\ManiaHost\Services\Map $m)
				{
					return $m->path.$m->filename;
				}, $maps);

		$selected = array_diff($this->session->get('selected', array()), $maps);
		$this->session->set('selected', $selected);
		$this->request->redirectToReferer();
	}

	function deleteMap($filename)
	{
		if(file_exists($filename))
		{
			unlink($filename);
		}

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
		$this->request->redirectToReferer();
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
		$this->request->set('gameMode', ($gameMode % 5) + 1);
		$this->request->redirect('../rent-server/');
	}

	protected function chooseSelector($path, $recursive)
	{
		$connection = $this->getServerConnection();
		$mapsPath = $connection->getMapsDirectory();

		$mapService = new \ManiaHost\Services\MapService($mapsPath);
		$maps = $mapService->getList($path, array(), null, null, null, null,
				$recursive);
		$maps = array_map(function (\ManiaHost\Services\Map $m)
				{
					return $m->path.$m->filename;
				}, $maps);

		$selected = $this->session->get('selected', array());
		$count = 0;
		foreach($maps as $map)
		{
			if(in_array($map, $selected))
			{
				$count++;
			}
		}

		if($count == count($maps))
		{
			$this->response->selector = '../unselect-all/';
		}
		else
		{
			$this->response->selector = '../select-all/';
		}
	}

}

?>