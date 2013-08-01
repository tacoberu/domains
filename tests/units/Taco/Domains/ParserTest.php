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
 * @call phpunit ParserTest.php tests_libs_taco_dhe_ParserTest
 */
class tests_libs_taco_dhe_ParserTest extends PHPUnit_Framework_TestCase
{



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testEmpty()
	{
		$a = Domains\Parser::formatWhere(array(''));
		$this->assertEquals($a, array());
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function __testSample()
	{
		$a = Domains\Parser::formatWhere(array('id ='));
		$this->assertEquals($a, array());
	}




	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function _testSampleIs()
	{
		$a = Domains\Parser::formatWhere(array('id'));
		print_r($a);
		//$this->assertEquals($a, array());
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testSampleIsA()
	{
		$a = Domains\Parser::formatWhere(array('id', 42));
		$this->assertEquals(count($a), 1);
		$this->assertEquals(get_class($a[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($a[0]->type(), '=');
		$this->assertEquals($a[0]->prop(), 'id');
		$this->assertEquals($a[0]->value(), 42);
	}




	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testSampleIsB()
	{
		$a = Domains\Parser::formatWhere(array('id =', 42));
		$this->assertEquals(count($a), 1);
		$this->assertEquals(get_class($a[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($a[0]->type(), '=');
		$this->assertEquals($a[0]->prop(), 'id');
		$this->assertEquals($a[0]->value(), 42);
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testSampleIsB2()
	{
		$a = Domains\Parser::formatWhere(array('id=', 42));
		$this->assertEquals(count($a), 1);
		$this->assertEquals(get_class($a[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($a[0]->type(), '=');
		$this->assertEquals($a[0]->prop(), 'id');
		$this->assertEquals($a[0]->value(), 42);
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function _testSampleIsC()
	{
		$a = Domains\Parser::formatWhere(array('id = %i', 42));
		$this->assertEquals(count($a), 1);
		$this->assertEquals(get_class($a[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($a[0]->type(), '=');
		$this->assertEquals($a[0]->prop(), 'id');
		$this->assertEquals($a[0]->value(), 42);
	}




}
