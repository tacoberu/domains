<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit ExprTest.php
 * @author Martin Takáč <martin@takac.name>
 */
class ExprTest extends PHPUnit_Framework_TestCase
{

	function testExprIs()
	{
		$a = new ExprIs(1, 4);
		$this->assertState('=', 1, 4, $a);
		$this->assertEquals('1 = 4', (string)$a);
	}



	function testExprIsNot()
	{
		$a = new ExprIsNot(1, 4);
		$this->assertState('!=', 1, 4, $a);
		$this->assertEquals('1 != 4', (string)$a);
	}



	function testExprNull()
	{
		$a = new ExprIsNull(8);
		$this->assertStateUnary('ISNULL', 8, $a);
		$this->assertEquals($a->type(), 'ISNULL');
	}



	function testExprNotNull()
	{
		$a = new ExprIsNotNull(9);
		$this->assertStateUnary('ISNOTNULL', 9, $a);
		$this->assertEquals($a->type(), 'ISNOTNULL');
	}



	function testExprIn()
	{
		$a = new ExprIn('id', array(4, 6, 8));
		$this->assertState('IN', 'id', [4, 6, 8], $a);
		$this->assertEquals((string)$a, 'id IN (4, 6, 8)');
	}



	function testExprNotIn()
	{
		$a = new ExprNotIn('id', array(4, 6, 8));
		$this->assertState('NOTIN', 'id', [4, 6, 8], $a);
		$this->assertEquals((string)$a, 'id NOTIN (4, 6, 8)');
	}



	function testExprLike()
	{
		$a = new ExprLike('title', 'lorm ipusm');
		$this->assertState('LIKE', 'title', 'lorm ipusm', $a);
		$this->assertEquals((string)$a, 'title LIKE \'lorm ipusm\'');
	}



	function testExprNotLike()
	{
		$a = new ExprNotLike('title', 'lorm ipusm');
		$this->assertState('NOTLIKE', 'title', 'lorm ipusm', $a);
		$this->assertEquals((string)$a, 'title NOTLIKE \'lorm ipusm\'');
	}



	function testExprNot()
	{
		$a = new ExprNot(4);
		$this->assertEquals('NOT', $a->type());
		$this->assertNull($a->prop());
		$this->assertEquals(4, $a->value());
		$this->assertEquals('NOT 4', (string)$a);
	}



	private function assertState($type, $prop, $value, $val)
	{
		$this->assertEquals($type, $val->type());
		$this->assertEquals($prop, $val->prop());
		$this->assertEquals($value, $val->value());
	}



	private function assertStateUnary($type, $prop, $val)
	{
		$this->assertEquals($type, $val->type());
		$this->assertEquals($prop, $val->prop());
	}

}
