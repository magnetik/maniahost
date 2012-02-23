<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace ManiaHost\Controllers;

class Home extends \ManiaLib\Application\Controller
{
    function index()
    {
		$this->response->hourlyCost = \ManiaHost\Config::getInstance()->hourlyCost;
		$this->response->manialinkName = \ManiaHost\Config::getInstance()->appName;
    }
}

?>
