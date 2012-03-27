<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace Analytics;

class TimeUnits extends \ManiaLib\Utils\Singleton
{

	protected $formats;

	protected function __construct()
	{
		// Analytics "human" only works if "php" can be parsed by strtotime
		$this->formats['hour'] = new TimeUnitFormats(array(
				'php' => 'Y-m-d H',
				'mysql' => '%Y-%m-%d %H',
				'interval' => 'PT1H',
				'groupby' => 'DATE(%1$s), HOUR(%1$s) ',
				'from' => '-24 hour',
				'to' => 'now',
			));
		$this->formats['day'] = new TimeUnitFormats(array(
				'human' => 'M D jS',
				'php' => 'Y-m-d',
				'mysql' => '%Y-%m-%d',
				'interval' => 'P1D',
				'groupby' => 'DATE(%s) ',
				'from' => '-7 day',
				'to' => 'now',
			));
		$this->formats['week'] = new TimeUnitFormats(array(
				'php' => 'Y W',
				'mysql' => '%Y %u',
				'interval' => 'P1W',
				'groupby' => 'YEARWEEK(%s) ',
				'from' => '-4 week',
				'to' => 'now',
			));
		$this->formats['month'] = new TimeUnitFormats(array(
				'human' => 'M Y',
				'php' => 'Y-m',
				'mysql' => '%Y-%m',
				'interval' => 'P1M',
				'groupby' => 'YEAR(%1$s), MONTH(%1$s) ',
				'from' => '-12 month',
				'to' => 'now',
			));
		$this->formats['year'] = new TimeUnitFormats(array(
				'human' => 'Y',
				'php' => 'Y',
				'mysql' => '%Y',
				'interval' => 'P1Y',
				'groupby' => 'YEAR(%1$s) ',
				'from' => '-3 year',
				'to' => 'now',
			));
	}

	/**
	 * @param string Time unit, can be hour, day, week, month or year
	 * @param mixed from date, any format accepted by strtotime()
	 * @param mixed to date, any format accepted by strtotime()
	 */
	function getTimeUnits($unit='day', $from=null, $to=null)
	{
		$formats = $this->getFormats($unit);
		$datetime = new \DateTime($from ? : $formats->from);
		$toDatetime = new \DateTime($to ? : $formats->to);

		$timeUnits = array();
		while($datetime <= $toDatetime)
		{
			$timeUnits[] = $datetime->format($formats->php);
			$datetime->add(new \DateInterval($formats->interval));
		}
		return $timeUnits;
	}

	/**
	 * @param string Time unit, can be hour, day, week, month or year
	 * @param mixed from date, any format accepted by strtotime()
	 * @param mixed to date, any format accepted by strtotime()
	 */
	function getTimeUnits2($unit='day', $from=null, $to=null)
	{
		$formats = $this->getFormats($unit);
		$datetime = new \DateTime($from ? : $formats->from);
		$toDatetime = new \DateTime($to ? : $formats->to);

		$timeUnits = array();
		while($datetime <= $toDatetime)
		{
			$timeUnits[] = array($datetime->format($formats->php));
			$datetime->add(new \DateInterval($formats->interval));
		}
		return $timeUnits;
	}

	function getEmptyData($unit='day', $from=null, $to=null, $defaultValue=0)
	{
		$units = $this->getTimeUnits($unit, $from, $to);
		return array_fill_keys($units, $defaultValue);
	}

	/**
	 * @return \NadeoLib\Analytics\TimeUnitFormats
	 */
	function getFormats($unit)
	{
		if(array_key_exists($unit, $this->formats))
		{
			return $this->formats[$unit];
		}
		throw new \InvalidArgumentException('Invalid time unit');
	}

}

?>