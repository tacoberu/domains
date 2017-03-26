<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;


/**
 * @call phpunit ParserTest.php
 * @author Martin Takáč <martin@takac.name>
 */
class ParserTest extends PHPUnit_Framework_TestCase
{

	function testEmpty()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Empty where-conds.');
		Parser::parseWhere('');
	}



	function testFailRequireArg()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Unexcepted bound of arguments. Require 0s index.');
		Parser::parseWhere('id =');
	}



	function testFailRequireArg2()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Unexcepted bound of arguments. Require 0s index.');
		Parser::parseWhere('id');
	}



	function _testFailRequireArg3()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Unexcepted bound of arguments. Require 0s index.');
		Parser::parseWhere('id', [1, 5]);
	}



	function testCondAnd()
	{
		$a = Parser::parseWhere('(id != ? AND id = ?)', [42, 43]);
		$this->assertEquals('(id != 42 AND id = 43)', (string)$a);
	}



	function testCondOr()
	{
		$a = Parser::parseWhere('(id != ? OR id = ?)', [42, 43]);
		$this->assertEquals('(id != 42 OR id = 43)', (string)$a);
	}



	/**
	 * @dataProvider provideParseWhereWithBind
	 */
	function testParseWhereWithBind($expr, $bindmap, $expected)
	{
		$this->assertEquals($expected, Parser::parseWhere($expr, $bindmap));
	}



	function provideParseWhereWithBind()
	{
		return [
			'is' =>
				[ 'id = ?'
				, [6]
				, new ExprIs('id', 6)
				],
			'is 1' =>
				[ 'id ='
				, [7]
				, new ExprIs('id', 7)
				],
			//~ 'is 2' =>
				//~ [ 'id = '
				//~ , [8]
				//~ , new ExprIs('id', 8)
				//~ ],
			'is 3' =>
				[ 'id = ?'
				, [9]
				, new ExprIs('id', 9)
				],
			//~ 'is 2' =>
				//~ [ 'id'
				//~ , [8]
				//~ , new ExprIs('id', 8)
				//~ ],
			'is not' =>
				[ 'id !='
				, [7]
				, new ExprIsNot('id', 7)
				],
			'is not 2' =>
				[ 'id <>'
				, [7]
				, new ExprIsNot('id', 7)
				],
			'is <' =>
				[ 'id <'
				, [7]
				, new ExprLessThan('id', 7)
				],
			'is <=' =>
				[ 'id <='
				, [7]
				, new ExprLessOrEqualThan('id', 7)
				],
			'is >' =>
				[ 'id >'
				, [7]
				, new ExprGreaterThan('id', 7)
				],
			'is >=' =>
				[ 'id >='
				, [7]
				, new ExprGreaterOrEqualThan('id', 7)
				],
			'is null' =>
				[ 'id ISNULL'
				, ['fake']
				, new ExprIsNull('id')
				],
			'is null 2' =>
				[ 'id NULL'
				, ['fake']
				, new ExprIsNull('id')
				],
			'is not null' =>
				[ 'id ISNOTNULL'
				, ['fake']
				, new ExprIsNotNull('id')
				],
			'in' =>
				[ 'id IN'
				, [[222]]
				, new ExprIn('id', [222])
				],
			'in 2' =>
				[ 'id IN'
				, [[222, 555]]
				, new ExprIn('id', [222, 555])
				],
			'not in' =>
				[ 'id NOTIN'
				, [[222]]
				, new ExprNotIn('id', [222])
				],
			'not in 2' =>
				[ 'id NOTIN'
				, [[222, 888]]
				, new ExprNotIn('id', [222, 888])
				],
			'like' =>
				[ 'id LIKE'
				, ["abc"]
				, new ExprLike('id', "abc")
				],
			'not like' =>
				[ 'id NOTLIKE'
				, ["abc"]
				, new ExprNotLike('id', "abc")
				],
			'condition with and' =>
				[ '(age = ? AND access = false)'
				, [5]
				, new CondAnd([ new ExprIs('age', 5), new ExprIs('access', false), ])
				],
			'condition with or' =>
				[ '(age = ? OR access = false)'
				, [5]
				, new CondOr([ new ExprIs('age', 5), new ExprIs('access', false), ])
				],
		];
	}



	/**
	 * @dataProvider provideParseFilter
	 */
	function testParseFilter($expr, $expected)
	{
		$this->assertEquals($expected, Parser::parseFilter($expr));
	}



	function provideParseFilter()
	{
		return [
			'is' =>
				[ 'Article[(id = 5)]'
				, (new Filter('Article'))->where('id =', 5)
				],
			'is 2' =>
				[ 'Article[(id = 5) ] '
				, (new Filter('Article'))->where('id =', 5)
				],
			'like' =>
				[ 'Article[(id LIKE "L*") ] '
				, (new Filter('Article'))->where('id LIKE', "L*")
				],
			'Conditon with OR' =>
				[ 'Article[(id = 5 OR id = 6) ] '
				, (new Filter('Article'))->where(new CondOr( [ new ExprIs('id', 5), new ExprIs('id', 6) ] ))
				],
			'Conditon with AND' =>
				[ 'Article[(id = 5 AND id = 6) ] '
				, (new Filter('Article'))->where(new CondAnd( [ new ExprIs('id', 5), new ExprIs('id', 6) ] ))
				],
			'many' =>
				[ 'Article[(name = "foo" OR title = "boo")]'
				, (new Filter('Article'))->where(new CondOr( [ new ExprIs('name', "foo"), new ExprIs('title', "boo") ] ))
				],
			'many 2' =>
				[ 'Article[(name = "foo" OR (title = "boo" OR name LIKE "a*"))]'
				, (new Filter('Article'))->where(new CondOr(
					[ new ExprIs('name', "foo")
					, new CondOr(
						[ new ExprIs('title', "boo")
						, new ExprLike('name', "a*")
						])
					]))
				],
			'many' =>
				[ 'Article[(name = "foo" OR title = "boo" OR (name LIKE "a*" OR name LIKE "b*" OR name LIKE "c*"))]'
				, (new Filter('Article'))->where(new CondOr(
					[ new ExprIs('name', "foo")
					, new ExprIs('title', "boo")
					, new CondOr(
						[ new ExprLike('name', "a*")
						, new ExprLike('name', "b*")
						, new ExprLike('name', "c*")
						])
					]))
				],
		];
	}

}
