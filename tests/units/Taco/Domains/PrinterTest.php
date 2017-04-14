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

}
