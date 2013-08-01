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
 * @call phpunit ExprTest.php tests_libs_taco_dhe_ExprTest
 */
class tests_libs_taco_dhe_ExprTest extends PHPUnit_Framework_TestCase
{



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testCoreExpr()
	{
		$a = new Domains\Expr('Not Null', 1, 4);
		$this->assertEquals($a->type(), 'NOT NULL');
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testExprIs()
	{
		$a = new Domains\ExprIs(1, 4);
		$this->assertEquals($a->type(), '=');
		$this->assertEquals($a->prop(), 1);
		$this->assertEquals($a->id(), 1);
		$this->assertEquals($a->value(), 4);
		$this->assertEquals((string)$a, '1 = 4');
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testExprIn()
	{
		$a = new Domains\ExprIn('id', array(4, 6, 8));
		$this->assertEquals($a->type(), 'IN');
		$this->assertEquals($a->prop(), 'id');
		$this->assertEquals($a->id(), 'id');
		$this->assertEquals($a->value(), array(4, 6, 8));
		$this->assertEquals((string)$a, 'id IN (4, 6, 8)');
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testExprLike()
	{
		$a = new Domains\ExprLike('title', 'lorm ipusm');
		$this->assertEquals($a->type(), 'LIKE');
		$this->assertEquals($a->prop(), 'title');
		$this->assertEquals($a->id(), 'title');
		$this->assertEquals($a->value(), 'lorm ipusm');
		$this->assertEquals((string)$a, 'title LIKE \'lorm ipusm\'');
	}



}
