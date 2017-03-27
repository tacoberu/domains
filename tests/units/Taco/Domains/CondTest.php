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
		$this->assertEquals('((i = 5 OR i = 19) AND (a = 5 OR b = 19) AND x = abc)', (string)$c);
	}

}
