<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Utils;
/**
 * http://code.google.com/intl/fr/apis/chart/image/docs/gallery/bar_charts.html
 * http://chart.apis.google.com/chart?chxl=0:|2012-02-22|2012-02-27|2012-02-28|2012-02-29|2012-03-01&chxt=x,y&chbh=a&chs=440x220&cht=bvg&chds=a&chd=t:2,1,1,-1,2&chdl=Rent+Number&blabla=.jpg
 * http://code.google.com/intl/fr/apis/chart/image/docs/data_formats.html
 */
class Charts
{

	protected $chartType;
	protected $chartTitle;
	protected $series = array();
	protected $seriesColor = array();

	function __construct($chartType)
	{
		$this->chartType = $chartType;
	}

	function setTitle($title)
	{
		$this->chartTitle = $title;
	}

	function addSerie($title, array $values, $scaling = '')
	{
		$this->series[] = array($title, $values, $scaling);
	}

}

?>