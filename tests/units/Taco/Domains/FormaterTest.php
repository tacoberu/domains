<?php
/**
 * This file is part of the Taco Library (http://dev.taco-beru.name)
 *
 * Copyright (c) 2004, 2011 Martin Takáč (http://martin.takac.name)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 *
 * PHP version 5.3
 */


require_once __dir__ . '/../../../../libs/Taco/Domains/ICriteria.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Criteria.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Parser.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Expr.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Cond.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/exceptions.php';

require_once __dir__ . '/../../../../libs/Taco/Domains/DibiFormater.php';


use Taco\Domains;


/**
 *
 * @call phpunit FormaterTest.php tests_libs_taco_dhe_FormaterTest
 */
class tests_libs_taco_dhe_FormaterTest extends PHPUnit_Framework_TestCase
{



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testSample()
	{
		$c = Domains\Criteria::create('Article')->where('id', 5);
		$f = new Domains\DibiFormater();
		$sql = $f->format($c);
		$this->assertEquals('SELECT [a].* FROM [article] AS [a]  WHERE [a].[id] = %i', $sql->code);
		$this->assertEquals(array(5), $sql->args);
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testWhereComplex()
	{
		$c = Domains\Criteria::create('Article')->where('id', 5)->where('code', 15);
		$f = new Domains\DibiFormater();
		$sql = $f->format($c);
		$this->assertEquals('SELECT [a].* FROM [article] AS [a]  WHERE [a].[id] = %i AND [a].[code] = %i', $sql->code);
		$this->assertEquals(array(5, 15), $sql->args);
	}


}
