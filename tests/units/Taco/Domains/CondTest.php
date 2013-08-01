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


require_once __dir__ . '/../../../../libs/Taco/Domains/Parser.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Expr.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Cond.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/exceptions.php';



use Taco\Domains;


/**
 *
 * @call phpunit CondTest.php tests_libs_taco_dhe_CondTest
 */
class tests_libs_taco_dhe_CondTest extends PHPUnit_Framework_TestCase
{



	/**
	 *
	 */
	public function testCoreExpr()
	{
		$a = new Domains\CondAnd();
		$this->assertEquals($a->type(), 'AND');
		//$this->assertNull($a->id());
		$this->assertEquals((string)$a, '');
	}



	/**
	 *
	 */
	public function testCoreOne()
	{
		$a = new Domains\CondAnd();
		$a->add(new Domains\ExprIs('i', 5));
		$this->assertEquals($a->type(), 'AND');
		//$this->assertEquals($a->id(), 'AND');
		$this->assertEquals((string)$a, '(i = 5)');
	}



	/**
	 *
	 */
	public function testCoreMany()
	{
		$a = new Domains\CondAnd();
		$a->add(new Domains\ExprIs('i', 5));
		$a->add(new Domains\ExprIs('i', 19));
		$a->add(new Domains\ExprIs('i', 8));
		$this->assertEquals($a->type(), 'AND');
		$this->assertEquals((string)$a, '(i = 5 AND i = 19 AND i = 8)');
	}







}
