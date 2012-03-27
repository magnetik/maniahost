<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Controllers;

class Admin extends AbstractController
{

	function index()
	{
		$connection = $this->getServerConnection();

		$mapService = new \ManiaHost\Services\MapService($connection->getMapsDirectory());
		$usedSpace = $mapService->getSize('$uploaded/', array(), null, null, null,
				null, true);

		$analytics = new \ManiaHost\Services\AnalyticsService();
		$data = $analytics->getRental('day', '-6 days');
		$abscissa = array();
		$values = array();
		foreach($data as $value)
		{
			$abscissa[] = $value[0];
			$values[] = $value[1];
		}

		$barChart = new \Google\ImageCharts\BarChart();
		$barChart->setSize(600,200);

		$barChart->setTitle('Rental per day');
		$barChart->setAbscissaAxis($abscissa);
		$barChart->setData($values);
		$RentalUrl = $barChart->getUrl();

		$data = $analytics->getRevenuesAnalytics('day', '-6 days');
		$abscissa = array();
		$values = array();
		foreach($data as $value)
		{
			$abscissa[] = $value[0];
			$values[] = $value[1];
		}

		$barChart = new \Google\ImageCharts\BarChart();
		$barChart->setSize(600,200);
		$barChart->setTitle('Incomes per day');
		$barChart->setAbscissaAxis($abscissa);
		$barChart->setData($values);
		$IncomesUrl = $barChart->getUrl();

		$this->response->usedSpace = $usedSpace;
		$this->response->rentalUrl = $RentalUrl;
		$this->response->incomesUrl = $IncomesUrl;
	}

	function audienceList()
	{
		$service = new \ManiaHost\Services\AnalyticsService();

		$multipage = new \ManiaLib\Utils\MultipageList(21);
		list($offset, $length) = $multipage->getLimit();

		$servers = $service->getServerList($offset, $length + 1);
		$multipage->checkArrayForMorePages($servers);

		$this->response->multipage = $multipage;
		$this->response->servers = $servers;
	}

	function serverAudience($serverLogin, $unit = 'day', $from = '-6 days', $to = 'now')
	{
		$service = new \ManiaHost\Services\AnalyticsService();

		$data = $service->getServerAudience($serverLogin, $unit, $from, $to);
		\ManiaLib\Utils\Logger::info($data);
		$abscissa = array();
		$avgValues = array();
		$maxValues = array();
		foreach($data as $value)
		{
			$abscissa[] = $value[0];
			$avgValues[] = $value[1];
			$maxValues[] = $value[2];
		}

		$barChart = new \Google\ImageCharts\BarChart();
		$barChart->setSize(600,200);
		$barChart->setTitle('Average audience on '.$serverLogin);
		$barChart->setAbscissaAxis($abscissa);
		$barChart->setData($avgValues);
		$avgGraphUrl = $barChart->getUrl();

		$barChart = new \Google\ImageCharts\BarChart();
		$barChart->setSize(600,200);
		$barChart->setTitle('Audience max on '.$serverLogin);
		$barChart->setAbscissaAxis($abscissa);
		$barChart->setData($maxValues);
		$maxGraphUrl = $barChart->getUrl();

		$this->response->avgGraphUrl = $avgGraphUrl;
		$this->response->maxGraphUrl = $maxGraphUrl;
	}

}

?>