<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use PHPUnit_Framework_TestCase;


/**
 * @call phpunit FormaterTest.php tests_libs_taco_dhe_FormaterTest
 * @author Martin Takáč <martin@takac.name>
 */
class PrinterTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @dataProvider provideFormatWhere
	 */
	function testFormatWhere($expr, $expected)
	{
		$this->assertEquals($expected, Printer::formatWhere($expr));
	}



	function provideFormatWhere()
	{
		return [
			'<empty>' =>
				[ null
				, ''
				],
			'ExprIs int' =>
				[ new ExprIs('attr', 42)
				, 'attr = 42'
				],
			'ExprIs float' =>
				[ new ExprIs('attr', 4.2)
				, 'attr = 4.2'
				],
			'ExprIs string' =>
				[ new ExprIs('attr', 'abc')
				, 'attr = "abc"'
				],
			'ExprIs false' =>
				[ new ExprIs('attr', false)
				, 'attr = false'
				],
			'ExprIs true' =>
				[ new ExprIs('attr', true)
				, 'attr = true'
				],
			'ExprIs string' =>
				[ new ExprIs('attr', true)
				, 'attr = true'
				],
			'ExprIsNot int' =>
				[ new ExprIsNot('attr', 42)
				, 'attr != 42'
				],
			'ExprGreaterThan int' =>
				[ new ExprGreaterThan('attr', 42)
				, 'attr > 42'
				],
			'ExprGreaterOrEqualThan int' =>
				[ new ExprGreaterOrEqualThan('attr', 42)
				, 'attr >= 42'
				],
			'ExprLessThan int' =>
				[ new ExprLessThan('attr', 42)
				, 'attr < 42'
				],
			'ExprLessOrEqualThan int' =>
				[ new ExprLessOrEqualThan('attr', 42)
				, 'attr <= 42'
				],
			'ExprLike' =>
				[ new ExprLike('attr', 'L*')
				, 'attr LIKE "L*"'
				],
			'ExprNotLike' =>
				[ new ExprNotLike('attr', 'L*')
				, 'attr NOTLIKE "L*"'
				],
			'ExprIsNull' =>
				[ new ExprIsNull('attr')
				, 'attr ISNULL'
				],
			'ExprIsNotNull' =>
				[ new ExprIsNotNull('attr')
				, 'attr ISNOTNULL'
				],
			'ExprIn' =>
				[ new ExprIn('attr', [])
				, 'attr IN []'
				],
			'ExprIn one' =>
				[ new ExprIn('attr', [45])
				, 'attr IN [45]'
				],
			'ExprIn many' =>
				[ new ExprIn('attr', [4, 5])
				, 'attr IN [4, 5]'
				],
			'ExprNotIn one' =>
				[ new ExprNotIn('attr', [45])
				, 'attr NOTIN [45]'
				],
			'ExprNotIn many' =>
				[ new ExprNotIn('attr', [4, 5])
				, 'attr NOTIN [4, 5]'
				],
		];
	}



	/**
	 * @dataProvider provideFormatFilter
	 */
	function testFormatFilter($expr, $expected)
	{
		$this->assertEquals($expected, Printer::formatFilter($expr));
	}



	function provideFormatFilter()
	{
		return [
			'<empty>' =>
				[ new Filter('Article')
				, 'Article'
				],
			'is' =>
				[ (new Filter('Article'))->where('id', 5)
				, 'Article[(id = 5)]'
				],
			'is many' =>
				[ (new Filter('Article'))->where('id', 5)->where('code', 15)
				, 'Article[(id = 5 AND code = 15)]'
				],
		];
	}



	/**
	 * @dataProvider provideFormat
	 */
	function testFormat($criteria, $expected)
	{
		$this->assertEquals($expected, Printer::format($criteria));
	}



	function provideFormat()
	{
		return [
			'<empty>' =>
				[ Criteria::create('Article')
				, 'Article'
				],
			'is' =>
				[ Criteria::create('Article')->where('id', 5)
				, 'Article[(id = 5)]'
				],
			'is many' =>
				[ Criteria::create('Article')->where('id', 5)->where('code', 15)
				, 'Article[(id = 5 AND code = 15)]'
				],
			'is not' =>
				[ Criteria::create('Article')->where('id !=', 5)
				, 'Article[(id != 5)]'
				],
			'is null' =>
				[ Criteria::create('Article')->where('id ISNULL', 5)
				, 'Article[(id ISNULL)]'
				],
			'is not null' =>
				[ Criteria::create('Article')->where('id ISNOTNULL', 5)
				, 'Article[(id ISNOTNULL)]'
				],
			'is like' =>
				[ Criteria::create('Article')->where('id LIKE', 'foo*')
				, 'Article[(id LIKE "foo*")]'
				],
			'is not like' =>
				[ Criteria::create('Article')->where('id NOTLIKE', 'foo*')
				, 'Article[(id NOTLIKE "foo*")]'
				],
			'is in' =>
				[ Criteria::create('Article')->where('id IN', ['a', 'b', 'c'])
				, 'Article[(id IN ["a", "b", "c"])]'
				],
			'is not in' =>
				[ Criteria::create('Article')->where('id NOTIN', ['a', 'b', 'c'])
				, 'Article[(id NOTIN ["a", "b", "c"])]'
				],
			'or' =>
				[ Criteria::create('Article')->where('id', 11)->where('name', 'foo')
				, 'Article[(id = 11 AND name = "foo")]'
				],
			'and' =>
				[ Criteria::create('Article')->where('id', 11)->where(new CondAnd([new ExprIs('name', 'foo'), new ExprIs('title', 'boo')]))
				, 'Article[(id = 11 AND (name = "foo" AND title = "boo"))]'
				],
			'or 2' =>
				[ Criteria::create('Article')->where(new CondOr([new ExprIs('name', 'foo'), new ExprIs('title', 'boo')]))
				, 'Article[(name = "foo" OR title = "boo")]'
				],
			'tree' =>
				[ Criteria::create('Article')->where(new CondOr([
						new ExprIs('name', 'foo'),
						new ExprIs('title', 'boo'),
						new CondOr([
							new ExprLike('name', 'a'),
							new ExprLike('name', 'b'),
							new ExprLike('name', 'c'),
						]),
					]))
				, 'Article[(name = "foo" OR title = "boo" OR (name LIKE "a" OR name LIKE "b" OR name LIKE "c"))]'
				],
			'with limit' =>
				[ Criteria::create('Article')->where('id', 5)->limit(10)
				, 'Article[(id = 5)|:10]'
				],
			'with offset' =>
				[ Criteria::create('Article')->where('id', 5)->offset(18)
				, 'Article[(id = 5)|18:]'
				],
			'with limit and offset' =>
				[ Criteria::create('Article')->where('id', 5)->limit(10)->offset(25)
				, 'Article[(id = 5)|25:10]'
				],
			'with key' =>
				[ Criteria::create('Article')->where('id', 5)->with('*')->with('poka')
				, 'Article[(id = 5)]{*, poka}'
				],
			'with all' =>
				[ Criteria::create('Article')->where('id', 5)->with('*')
				, 'Article[(id = 5)]{*}'
				],
		];
	}

}
