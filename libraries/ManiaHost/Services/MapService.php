<?php
/**
 * @copyright   Copyright (c) 2009-2011 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

class MapService
{

	function getList($path, $recursive = false, $offset = null, $length = null)
	{
		$workPath = $this->securePath($path);
		$maps = array();

		if($workPath === false)
		{
			return $maps;
		}

		if($recursive)
		{
			$maps = $this->doRecursiveSearch($workPath);
		}
		else
		{
			$files = scandir($workPath);
			foreach($files as $filename)
			{
				if(stristr($filename, 'map.gbx') || (is_dir($workPath.$filename) && $filename != '.' && $filename != '..' && $filename != '$uploaded'))
				{
					$file = new Map();
					$file->isDirectory = is_dir($workPath.$filename);
					$file->filename = $filename;
					$file->path = $path;
					$maps[] = $file;
				}
			}
		}
		usort($maps, array($this, 'fileSortCallback'));

		if($offset !== null)
		{
			$maps = array_slice($maps, $offset, $length);
		}

		foreach($maps as $key => $map)
		{
			try
			{
				$datas = $this->getData($map->path.DIRECTORY_SEPARATOR.$map->filename);
			}
			catch(\Exception $e)
			{
				$datas['name'] = stristr($map->filename, '.map.gbx', true);
				$datas['author'] = '';
				$datas['authorTime'] = '';
				$datas['environment'] = '';
			}
			$map->name = $datas['name'];
			$map->author = $datas['author'];
			$map->authorTime = $datas['authorTime'];
			$map->environment = $datas['environment'];
			$maps[$key] = $map;
		}

		return $maps;
	}

	function getCount($path, $recursive = false)
	{
		$count = 0;
		$workPath = $this->securePath($path);

		if($workPath === false)
		{
			return $count;
		}

		if($recursive)
		{
			$files = scandir($path);
			foreach($files as $filename)
			{
				if($filename != '$uploaded' && $filename != '.' && $filename != '..' && is_dir($path.DIRECTORY_SEPARATOR.$filename))
				{
					$count += $this->getCount($path.DIRECTORY_SEPARATOR.$filename, true);
				}
				elseif(preg_match('/(\\.map\\.gbx)$/ixu', $path.$filename))
				{
					$count++;
				}
			}
		}
		else
		{
			$files = scandir($workPath);
			foreach($files as $filename)
			{
				if(preg_match('/(\\.map\\.gbx)$/ixu', $path.$filename))
				{
					$count++;
				}
			}
		}
		return $count;
	}

	function getSize($path, $recursive = false)
	{
		$size = 0;
		$workPath = $this->securePath($path);

		if($workPath === false)
		{
			return $size;
		}

		if($recursive)
		{
			$files = scandir($path);
			foreach($files as $filename)
			{
				if($filename != '$uploaded' && $filename != '.' && $filename != '..' && is_dir($path.$filename))
				{
					$size += $this->getSize($path.$filename.DIRECTORY_SEPARATOR, $recursive);
				}
				elseif(preg_match('/(\\.map\\.gbx)$/ixu', $path.$filename))
				{
					$size += filesize($path.$filename);
				}
			}
		}
		else
		{
			$files = scandir($workPath);
			foreach($files as $filename)
			{
				if(preg_match('/(\\.map\\.gbx)$/ixu', $path.$filename))
				{
					$size += filesize($path.$filename);
				}
			}
		}
		return $size;
	}

	function getData($filename)
	{
		if(!file_exists($filename))
				throw new \InvalidArgumentException($filename.' : file does not exist');
		$file = file_get_contents($filename);

		if($file === false || stristr($file, '<header type="map"') === false)
		{
			throw new \InvalidArgumentException('file is not a map');
		}


		$search = strstr($file, '<header');
		$pos = stristr($search, '</header>', true);

		$start = strlen('<header');
		$header = substr($pos, $start);

		$search = array('&apos;', '&amp;', '&gt;', '&lt;');

		$tmp = stristr($header, 'name="');
		$name = substr(stristr($tmp, '" author', true), 6);
		$infos['name'] = urldecode(str_replace('&apos;', "'",
						htmlspecialchars_decode($name, ENT_QUOTES)));
		$tmp = stristr($tmp, 'author="');
		$author = substr(stristr($tmp, '" authorzone', true), 8);
		$infos['author'] = urldecode(str_replace('&apos;', "'",
						htmlspecialchars_decode($author, ENT_QUOTES)));
		$tmp = stristr($tmp, 'envir="');
		$environment = substr(stristr($tmp, '" mood', true), 7);
		$infos['environment'] = urldecode(str_replace('&apos;', "'",
						htmlspecialchars_decode($environment, ENT_QUOTES)));
		$tmp = stristr($tmp, 'authortime="');
		$authorTime = substr(stristr($tmp, '" authorscore', true), 12);
		$infos['authorTime'] = urldecode(str_replace('&apos;', "'",
						htmlspecialchars_decode($authorTime, ENT_QUOTES)));

		return $infos;
	}

	protected function doRecursiveSearch($path)
	{
		$maps = array();

		$files = scandir($path);
		foreach($files as $filename)
		{
			if($filename != '$uploaded' && $filename != '.' && $filename != '..' && is_dir($path.$filename.DIRECTORY_SEPARATOR))
			{
				$datas = $this->doRecursiveSearch($path.$filename.DIRECTORY_SEPARATOR);
				$maps = array_merge($maps, $datas);
			}
			elseif(stristr($path.$filename, 'map.gbx'))
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

	protected function fileSortCallback(Map $a, Map $b)
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

	protected function securePath($path)
	{
		$path = realpath((stripos(PHP_OS, 'WIN') === 0 ? utf8_decode($path) : $path));
		if($path === false)
		{
			return false;
		}
		else
		{
			return $path.DIRECTORY_SEPARATOR;
		}
	}

}

?>