<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */


namespace Analytics;

class TimeUnitFormats
{
	public $human;
	public $php;
	public $mysql;
	public $interval;
	public $groupby;
	public $from;
	public $to;

	function __construct($data)
	{
		$object = $this;
		array_walk($data,
			function ($value, $key) use ($object){
				$object->$key = $value;
			}
		);
	}

	function getFromTimestamp($date)
	{
		return strtotime($date ?: $this->from);
	}

	function getToTimestamp($date)
	{
		$dt = new \DateTime($date ?: $this->to);
		$dt->add(new \DateInterval($this->interval));
		return $dt->getTimestamp();
	}
}

?>