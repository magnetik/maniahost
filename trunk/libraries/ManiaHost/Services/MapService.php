<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

class MapService extends AbstractService
{

	protected $mapPath;

	function __construct($mapPath)
	{
		parent::__construct();
		$this->mapPath = $mapPath;
	}

	function get($filename)
	{
		$result = $this->db()->execute(
				'SELECT * FROM Maps WHERE CONCAT(path,filename) = %s',
				$this->db()->quote($filename)
		);
		return Map::fromRecordSet($result);
	}

	function getUsed($login)
	{
		if(!preg_match('/^[a-zA-Z0-9-_\.]{1,25}$/', $login))
		{
			throw new \InvalidArgumentException();
		}

		$results = $this->db()->execute(
				'SELECT maps FROM Rents WHERE playerLogin = %s '.
				'AND DATE_ADD(rentDate, INTERVAL duration HOUR) > NOW()',
				$this->db()->quote($login)
		);
		$maps = array();
		while($tmp = $results->fetchSingleValue())
		{
			if($tmp)
			{
				$maps = array_merge($maps, unserialize($tmp));
			}
		}
		$maps = array_unique($maps);
		return $maps;
	}

	function getList($path, array $excludePath = array(), $environment = null,
			$nbLaps = null, $type = null, $mapType = null, $recursive = false,
			$offset = null, $length = null)
	{
		$keys = call_user_func_array(array($this, 'getKeys'), func_get_args());

		$keys = array_map(array($this->db(), 'quote'), $keys);

		$result = $this->db()->execute(
				'SELECT * FROM Maps '.
				'WHERE CONCAT(path,filename) IN (%s)'.
				'ORDER BY name ASC', implode(',', $keys)
		);
		return Map::arrayFromRecordSet($result);
	}

	function getCount($path, array $excludePath = array(), $environment = null,
			$nbLaps = null, $type = null, $mapType = null, $recursive = false)
	{
		return count(call_user_func_array(array($this, 'getKeys'), func_get_args()));
	}

	function getSize($path, array $excludePath = array(), $environment = null,
			$nbLaps = null, $type = null, $mapType = null, $recursive = false,
			$offset = null, $length = null)
	{
		$keys = call_user_func_array(array($this, 'getKeys'), func_get_args());

		$keys = array_map(array($this->db(), 'quote'), $keys);

		return $this->db()->execute(
						'SELECT SUM(fileSize) FROM Maps '.
						'WHERE CONCAT(path,filename) IN (%s) ', implode(',', $keys)
				)->fetchSingleValue();
	}

	function scanAndRegisterDir()
	{
		$maps = $this->doRecursiveSearch('');

		foreach($maps as $key => $map)
		{
			try
			{
				$datas = $this->getData($this->mapPath.$map->path.$map->filename);
				$map->name = $datas['name'];
				$map->author = $datas['author'];
				$map->authorTime = $datas['authorTime'];
				$map->environment = $datas['environment'];
				$map->type = $datas['type'];
				$map->nbLaps = $datas['nbLaps'];
				$map->fileSize = filesize($this->mapPath.$map->path.$map->filename);
				if($map->authorTime != -1)
				{
					$this->register($map);
				}
			}
			catch(\Exception $e)
			{
				throw $e;
			}
		}
	}

	function register(Map $map)
	{
		$this->db()->execute(
				'INSERT INTO ManiaHost.Maps VALUES (%s,%s,%s,%s,%d,%s,%d,%s,%d) '.
				'ON DUPLICATE KEY UPDATE name = VALUES(name), author = VALUES(author), '.
				'authorTime = VALUES(authorTime), type = VALUES(type), '.
				'nbLaps = VALUES(nbLaps), environment = VALUES(environment), '.
				'fileSize = VALUES(fileSize)', $this->db()->quote($map->path),
				$this->db()->quote($map->filename), $this->db()->quote($map->name),
				$this->db()->quote($map->author), $map->authorTime,
				$this->db()->quote($map->type), $map->nbLaps,
				$this->db()->quote($map->environment), $map->fileSize
		);
	}

	function getData($filename)
	{
		if(!file_exists($filename))
				throw new \InvalidArgumentException($filename.' : file does not exist');
		$file = file_get_contents($filename);

		if($file === false || (stristr($file, '<header type="map"') === false && stristr($file,
						'<header type="challenge"') === false))
		{
			throw new \InvalidArgumentException('file is not a map');
		}


		$header = stristr($file, '<header');
		$header = stristr($header, '</header>', true).'</header>';

		$xmlData = simplexml_load_string($header);

		$infos['name'] = (string) $xmlData->ident->attributes()->name;
		$infos['author'] = (string) $xmlData->ident->attributes()->author;
		$infos['environment'] = (string) $xmlData->desc->attributes()->envir;
		$infos['mood'] = (string) $xmlData->desc->attributes()->mood;
		$infos['type'] = (string) $xmlData->desc->attributes()->type;
		$infos['nbLaps'] = (int) $xmlData->desc->attributes()->nblaps;
		$infos['authorTime'] = (int) $xmlData->times->attributes()->authortime;

		return $infos;
	}

	function delete($filename)
	{
		$this->db()->execute(
				'DELETE FROM Maps WHERE CONACT(path,filename) = %s',
				$this->db()->quote($filename)
		);

		if($this->db()->affectedRows && file_exists($this->mapPath.$filename))
		{
			unlink($this->mapPath.$filename);
		}
	}

	protected function getKeys($path, array $excludePath = array(),
			$environment = null, $nbLaps = null, $type = null, $mapType = null,
			$recursive = false, $offset = null, $length = null)
	{
		$path .= ($recursive ? '%' : '');
		$path = str_replace('\\', '/', $path);
		$excludePath = array_map(array($this->db(), 'quote'), $excludePath);
		return $this->db()->execute(
						'SELECT CONCAT(path,filename) FROM Maps '.
						'WHERE path LIKE %s '.
						($excludePath ? 'AND path NOT IN (%3$s) ' : '').
						($environment ? 'AND environment = %4$s ' : '').
						($nbLaps ? 'AND nbLaps != 0 ' : '').
						($type ? 'AND type = %5$s ' : '').
						($mapType ? 'AND mapType = %6$s ' : '').
						'ORDER BY name ASC %s', $this->db()->quote($path),
						\ManiaLib\Database\Tools::getLimitString($offset, $length),
						implode($excludePath), $this->db()->quote($environment),
						$this->db()->quote($type), $this->db()->quote($mapType)
				)->fetchArrayOfSingleValues();
	}

	protected function doRecursiveSearch($path)
	{
		$maps = array();

		$workPath = $this->mapPath.$path;
		if(!substr_compare($workPath, '/', -1, 1))
		{
			$workPath .= '/';
		}

		foreach(scandir($workPath) as $filename)
		{
			if($filename != '.' && $filename != '..' && is_dir($workPath.$filename.'/'))
			{
				$datas = $this->doRecursiveSearch($path.$filename.'/');
				$maps = array_merge($maps, $datas);
			}
			elseif(preg_match('/\\.map\\.gbx$/ixu', $filename))
			{
				$file = new Map();
				$file->isDirectory = false;
				$file->filename = $filename;
				$file->path = $path;
				$maps[] = $file;
			}
		}
		return $maps;
	}

	protected

	function fileSortCallback(Map $a, Map $b)
	{
		if($a->isDirectory && !$b->isDirectory)
		{
			return -1;
		}
		elseif(!$a->isDirectory && $b->isDirectory)
		{
			return 1;
		}
		else
		{
			return($a->filename < $b->filename ? -1 : 1);
		}
	}

}

?>