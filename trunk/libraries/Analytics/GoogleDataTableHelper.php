<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace Analytics;

class GoogleDataTableHelper
{
	/**
	 * @var \NadeoLib\Analytics\TimeUnits
	 */
	protected $timeUnits;
	/**
	 * @var \NadeoLib\Google\Visualization\DataTable
	 */
	protected $dataTable;
	protected $emptyData;


	function __construct($unit = 'day', $from='-1 month', $to='now')
	{
		$this->timeUnits = TimeUnits::getInstance();
		$this->dataTable = new \NadeoLib\Google\Visualization\DataTable();
		$this->emptyData = $this->timeUnits->getTimeUnits2($unit, $from, $to);
	}

	function addColumn($type, $label='', $id='')
	{
		$this->dataTable->addColumn($type, $label, $id);
	}

	function addRows($rows)
	{
		$units = array();
		foreach($this->emptyData as $_key => $_row)
		{
			$date = reset($_row);
			$maxColNum = 0;
			foreach($rows as $_key2 => $_row2)
			{
				if(reset($_row2) == $date)
				{
					$units[$_key] = $_row2;
					break;
				}
				$maxColNum = (count($_row2) > $maxColNum ? count($_row2) : $maxColNum);
			}
			if(!isset($units[$_key]))
			{
				$units[$_key] = array_merge($_row, array_fill(0, max(array($maxColNum-1, 1)), null));
			}
		}
		$this->dataTable->addRows($units);
	}

	function getData()
	{
		return $this->dataTable->data;
	}
}

?>