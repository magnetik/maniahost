<?php
/**
 * @copyright   Copyright (c) 2009-2012 NADEO (http://www.nadeo.com)
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version     $Revision$:
 * @author      $Author$:
 * @date        $Date$:
 */

namespace ManiaHost\Services;

use Maniaplanet\WebServices\Transaction;

class TransactionService extends AbstractService
{

	/**
	 * @param string $login
	 * @param string $cost
	 * @return \Maniaplanet\WebServices\Transaction
	 */
	function create($login, $cost)
	{


		$t = new Transaction();
		$t->creatorLogin = \ManiaHost\Config::getInstance()->transactionLogin;
		$t->creatorPassword = \ManiaHost\Config::getInstance()->transactionPassword;
		$t->creatorSecurityKey = \ManiaHost\Config::getInstance()->transactionSecurityKey;
		$t->fromLogin = $login;
		$t->toLogin = \ManiaHost\Config::getInstance()->transactionLogin;
		$t->cost = $cost;

		if($cost != 0)
		{
			$payments = new \Maniaplanet\WebServices\Payments(\ManiaLive\Features\WebServices\Config::getInstance()->username, \ManiaLive\Features\WebServices\Config::getInstance()->password);
			$t->id = $payments->create($t);
		}

		return $t;
	}

	function isPaid(Transaction $transaction)
	{
		$payments = new \Maniaplanet\WebServices\Payments(\ManiaLive\Features\WebServices\Config::getInstance()->username, \ManiaLive\Features\WebServices\Config::getInstance()->password);
		$isPaid = ($transaction->cost ? $payments->isPaid($transaction->id) : true);
		if($isPaid)
		{
			$this->db()->execute(
					'INSERT INTO Incomes VALUES (%s,%d,1) '.
					'ON DUPLICATE KEY UPDATE revenue = revenue + VALUES(revenue), transactionCount = transactionCount + 1',
					$this->db()->quote(date('Y-m-d 00:00:00')), $transaction->cost
			);
		}
		return $isPaid;
	}

}

?>