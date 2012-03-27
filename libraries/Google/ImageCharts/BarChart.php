<?php
/**
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace Google\ImageCharts;

class BarChart
{

	const URL = 'http://chart.apis.google.com/chart';

	protected $chxs = '0,676767,11.5,0,_,333333';
	protected $chma = '10,10,10,10';
	protected $chxt = 'y';
	protected $chxl;
	protected $chbh = 'a';
	protected $chs = '500x150';
	protected $cht = 'bvg';
	protected $chco = '0099ff';
	protected $chdlp = 't';
	protected $chg = '-1,-1,1,1';
	protected $chts = '333333,17';
	protected $chtt;
	protected $chof = 'png';
	protected $data;

	function setData(array $data)
	{
		$this->data = $data;
	}

	function setTitle($tile)
	{
		$this->chtt = $tile;
	}

	function setAbscissaAxis(array $labels)
	{
		$this->chxt .= ',x';
		$this->chxl = sprintf('1:|%s|', implode('|', $labels));
	}

	function setSize($sizeX, $sizeY)
	{
		$this->chs = sprintf('%dx%d', $sizeX, $sizeY);
	}

	function getUrl()
	{
		$params = array(
			'chxs' => $this->chxs,
			'chma' => $this->chma,
			'chxt' => $this->chxt,
			'chxl' => $this->chxl,
			'chbh' => $this->chbh,
			'chs' => $this->chs,
			'cht' => $this->cht,
			'chco' => $this->chco,
			'chdlp' => $this->chdlp,
			'chg' => $this->chg,
			'chts' => $this->chts,
			'chtt' => $this->chtt,
			'chof' => $this->chof,
			'chds' => sprintf('0,%d', ceil(max($this->data) * 1.10)),
			'chxr' => sprintf('0,0,%d', ceil(max($this->data) * 1.10)),
			'chd' => 't:'.implode(',',
					array_map(
							function($a)
							{
								return ($a === null ? '_' : $a);
							}, $this->data
					)),
			'dummytype' => '.png',);

		foreach($params as $key => $value)
		{
			$params[$key] = $key.'='.$value;
		}

		return self::URL.'?'.implode('&', $params);
	}

}

?>