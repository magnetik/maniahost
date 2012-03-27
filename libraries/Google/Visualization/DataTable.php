<?php
/**
 * Google Visualization API
 *
 * @see http://code.google.com/apis/visualization/documentation/reference.html
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace Google\Visualization;

class DataTable
{
	/**
	 * @var \NadeoLib\Google\Visualization\DataTableData
	 */
	public $data;
	public $version;

	function __construct($data = null, $version = '0.6')
	{
		$this->data = $data ?: new DataTableData();
	}

	function addColumn($type, $label='', $id='')
	{
		$col = new DataTableColumn();
		$col->type = $type;
		$col->label = $label;
		$col->id = $id;
		$this->data->cols[] = $col;
	}

	function addRow($opt_cellArray = null)
	{
		$row = new DataTableRow();
		foreach($opt_cellArray as $_key => $c)
		{
			$cell = new DataTableCell();
			if(is_object($c))
			{
				if(property_exists($c, 'v'))
				{
					$cell->v = $c->v;
				}
				if(property_exists($c, 'f'))
				{
					$cell->f = $c->f;
				}
			}
			else
			{
				$cell->v = $c;
			}
			if(isset($this->data->cols[$_key]))
			{
				switch($this->data->cols[$_key]->type)
				{
					case 'number':
						$cell->v = (float)$cell->v;
						break;

					case 'string':
						$cell->v = (string)$cell->v;
						break;

					case 'datetime':
						break;
				}
			}
			$row->c[] = $cell;
		}
		$this->data->rows[] = $row;
	}

	function addRows(array $rows)
	{
		foreach ($rows as $row)
		{
			$this->addRow($row);
		}
	}

}

?>