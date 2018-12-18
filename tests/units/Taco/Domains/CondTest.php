<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;


/**
 * @call phpunit CondTest.php CondTest
 * @author Martin Takáč <martin@takac.name>
 */
class CondTest extends PHPUnit_Framework_TestCase
{

	function testAndEmpty()
	{
		$a = new CondAnd([]);
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([], $a->expresions());
		$this->assertEquals('', (string)$a);
	}



	function testAndSingle()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('i', 5));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([
			new ExprIs('i', 5),
		], $a->expresions());
		$this->assertEquals('(i = 5)', (string)$a);
	}



	function testAndMany()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('i', 5));
		$a->add(new ExprIs('i', 19));
		$a->add(new ExprIs('i', 8));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals('(i = 5 AND i = 19 AND i = 8)', (string)$a);
		$this->assertEquals([
			new ExprIs('i', 5),
			new ExprIs('i', 19),
			new ExprIs('i', 8),
		], $a->expresions());
	}



	function testOrEmpty()
	{
		$a = new CondOr([]);
		$this->assertEquals('OR', $a->type());
		$this->assertEquals([], $a->expresions());
		$this->assertEquals('', (string)$a);
	}



	function testOrSingle()
	{
		$a = new CondOr([]);
		$a->add(new ExprIs('i', 5));
		$this->assertEquals('OR', $a->type());
		$this->assertEquals([
			new ExprIs('i', 5),
		], $a->expresions());
		$this->assertEquals('(i = 5)', (string)$a);
	}



	function testOrMany()
	{
		$a = new CondOr([]);
		$a->add(new ExprIs('i', 5));
		$a->add(new ExprIs('i', 19));
		$a->add(new ExprIs('i', 8));
		$this->assertEquals('OR', $a->type());
		$this->assertEquals('(i = 5 OR i = 19 OR i = 8)', (string)$a);
		$this->assertEquals([
			new ExprIs('i', 5),
			new ExprIs('i', 19),
			new ExprIs('i', 8),
		], $a->expresions());
	}



	function testCombine()
	{
		$a = new CondOr([]);
		$a->add(new ExprIs('i', 5));
		$a->add(new ExprIs('i', 19));
		$b = new CondOr([]);
		$b->add(new ExprIs('a', 5));
		$b->add(new ExprIs('b', 19));
		$c = new CondAnd([]);
		$c->add($a);
		$c->add($b);
		$c->add(new ExprIs('x', 'abc'));
		$this->assertEquals('((i = 5 OR i = 19) AND (a = 5 OR b = 19) AND x = "abc")', (string)$c);
	}



	function testAndSingleEmptyVal()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('name', ''));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([
			new ExprIs('name', ''),
		], $a->expresions());
		$this->assertEquals('(name = "")', (string)$a);
	}



	function testAndSingleNullVal()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('name', Null));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([
			new ExprIs('name', Null),
		], $a->expresions());
		$this->assertEquals('(name = Null)', (string)$a);
	}



	function testAndSingleBoolTrueVal()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('name', True));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([
			new ExprIs('name', True),
		], $a->expresions());
		$this->assertEquals('(name = True)', (string)$a);
	}



	function testAndSingleBoolFalseVal()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('name', False));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals([
			new ExprIs('name', False),
		], $a->expresions());
		$this->assertEquals('(name = False)', (string)$a);
	}



	function testArrayAccess()
	{
		$a = new CondAnd([]);
		$a->add(new ExprIs('name', 'Tim'));
		$this->assertEquals('AND', $a->type());
		$this->assertEquals(new ExprIs('name', 'Tim'), $a[0]);
	}



	function testArrayAccessMany()
	{
		$a = new CondOr([]);
		$a->add(new ExprIs('i', 5));
		$a->add(new ExprIs('i', 19));
		$a->add(new ExprIs('i', 8));
		$this->assertEquals('OR', $a->type());
		$this->assertEquals('(i = 5 OR i = 19 OR i = 8)', (string)$a);
		$this->assertEquals(new ExprIs('i', 5), $a[0]);
		$this->assertEquals(new ExprIs('i', 19), $a[1]);
		$this->assertEquals(new ExprIs('i', 8), $a[2]);
	}


}
