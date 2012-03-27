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

class DataTableData
{
	/**
	 * @var array[\NadeoLib\Google\Visualization\DataTableColumn]
	 */
	public $cols = array();
	/**
	 * @var array[\NadeoLib\Google\Visualization\DataTableRow]
	 */
	public $rows = array();
}

?>