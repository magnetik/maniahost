<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

use Analytics\TimeUnits;

class AnalyticsService extends AbstractService
{

	function getRental($unit = 'day', $from = '-30 days', $to = 'now')
	{
		$formats = TimeUnits::getInstance()->getFormats($unit);

		$result = $this->db()->execute(
						'SELECT DATE_FORMAT(rentDate, %s) AS formattedDate, '.
						'COUNT(id) '.
						'FROM Rents '.
						'WHERE rentDate > FROM_UNIXTIME(%d) AND rentDATE < FROM_UNIXTIME(%d) '.
						'GROUP BY formattedDate '.
						'ORDER BY formattedDate ASC', $this->db()->quote($formats->mysql),
						strtotime($from), strtotime($to)
				)->fetchArrayOfRow();

		return $this->fetchResults($result, $unit, $from, $to);
	}

	function getRevenuesAnalytics($unit = 'day', $from = '-7 days', $to = 'now')
	{
		$formats = TimeUnits::getInstance()->getFormats($unit);

		$result = $this->db()->execute(
						'SELECT DATE_FORMAT(transactionDate, %s) AS formattedDate, '.
						'revenue '.
						'FROM Incomes '.
						'WHERE transactionDate > FROM_UNIXTIME(%d) AND transactionDate < FROM_UNIXTIME(%d) '.
						'ORDER BY formattedDate ASC', $this->db()->quote($formats->mysql),
						strtotime($from), strtotime($to)
				)->fetchArrayOfRow();

		return $this->fetchResults($result, $unit, $from, $to);
	}

	function getServerList($offset = 0, $length = 7)
	{
		return $this->db()->execute(
						'SELECT serverLogin FROM Analytics GROUP BY serverLogin %s',
						\ManiaLib\Database\Tools::getLimitString($offset, $length)
				)->fetchArrayOfSingleValues();
	}

	function getServerAudience($serverLogin, $unit = 'day', $from = '-7 days',
			$to = 'now')
	{
		$formats = TimeUnits::getInstance()->getFormats($unit);

		$result = $this->db()->execute(
						'SELECT DATE_FORMAT(insertDate, %s) AS formattedDate, '.
						'AVG(avgPlayers), MAX(maxPlayer) '.
						'FROM Analytics '.
						'WHERE serverLogin = %s '.
						'AND insertDate > FROM_UNIXTIME(%d) AND insertDate < FROM_UNIXTIME(%d) '.
						'GROUP BY formattedDate '.
						'ORDER BY formattedDate ASC', $this->db()->quote($formats->mysql),
						$this->db()->quote($serverLogin), strtotime($from), strtotime($to)
				)->fetchArrayOfRow();

		return $this->fetchResults($result, $unit, $from, $to);
	}

	protected function fetchResults(array $result, $unit, $from, $to)
	{
		$data = array();
		$dates = TimeUnits::getInstance()->getTimeUnits($unit, $from, $to);

		$value = array_shift($result);
		foreach($dates as $date)
		{
			if($value)
			{
				$count = (count($value) > 1 ? count($value) - 1 : 1);
			}

			if($value && $value[0] == $date)
			{
				$data[] = $value;
				$value = array_shift($result);
			}
			else
			{
				$data[] = array_merge(array($date), array_fill(0, $count, null));

			}
		}

		return $data;
	}

}

?>