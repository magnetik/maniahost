<?php

/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */
namespace ManiaHost\Services;

abstract class AbstractObject
{

	/**
	 * Fetches a single object from the record set
	 */
	static function fromRecordSet(\ManiaLib\Database\RecordSet $result,
		$strict=true, $default=null, $message='Object not found')
	{
		if(!($object = $result->fetchObject(get_called_class())))
		{
			if($strict)
			{
				throw new NotFoundException(sprintf($message, get_called_class()));
			}
			else
			{
				return $default;
			}
		}
		return $object;
	}

	/**
	 * Fetches an array of object from the record set
	 */
	static function arrayFromRecordSet(\ManiaLib\Database\RecordSet $result)
	{
		$array = array();
		while($object = static::fromRecordSet($result, false))
		{
			$array[] = $object;
		}
		return $array;
	}
}

?>