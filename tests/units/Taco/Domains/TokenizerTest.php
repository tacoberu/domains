<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;


class TokenizerTest extends PHPUnit_Framework_TestCase
{

	function testParseExprNull()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Empty expression.');
		$tok = new Tokenizer();
		$tok->parseExpr(null);
	}



	function testParseExprEmptyString()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Empty expression.');
		$tok = new Tokenizer();
		$tok->parseExpr('');
	}



	function testParseCondsNull()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Empty expression.');
		$tok = new Tokenizer();
		$tok->parseConds(null);
	}



	function testParseCondsEmptyString()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Empty expression.');
		$tok = new Tokenizer();
		$tok->parseConds('');
	}



	/**
	 * @dataProvider providerParseExpr
	 */
	function testParseExpr($src, $tail, $expected)
	{
		$tok = new Tokenizer();
		$a = $tok->parseExpr($src);
		$this->assertEquals($expected, $a[0]);
		$this->assertEquals($tail, $a[1]);
	}



	function providerParseExpr()
	{
		return [
			'is' =>
				[ 'a = 1.'
				, '.'
				, new ExprIs('a', 1)
				],
			'is 1' =>
				[ 'in = 18'
				, ''
				, new ExprIs('in', 18)
				],
			'is 2' =>
				[ 'IN = 18'
				, ''
				, new ExprIs('IN', 18)
				],
			'is 3' =>
				[ 'atr =  "abc"'
				, ''
				, new ExprIs('atr', 'abc')
				],
			'is false' =>
				[ 'a = false'
				, ''
				, new ExprIs('a', false)
				],
			'is true' =>
				[ 'a = true'
				, ''
				, new ExprIs('a', true)
				],
			'is TRUE' =>
				[ 'a = TRUE'
				, ''
				, new ExprIs('a', true)
				],
			'is string' =>
				[ 'a = "abc"'
				, ''
				, new ExprIs('a', 'abc')
				],
			'isnot' =>
				[ 'i != 18'
				, ''
				, new ExprIsNot('i', 18)
				],
			'isnot 2' =>
				[ 'id != 188'
				, ''
				, new ExprIsNot('id', 188)
				],
			'like' =>
				[ 'a LIKE "L*".'
				, '.'
				, new ExprLike('a', 'L*')
				],
			'tuple' =>
				[ 'id IN (4, 6, 8).'
				, '.'
				, new ExprIn('id', [4, 6, 8])
				],
			'is null' =>
				[ 'id ISNULL 188'
				, '188'
				, new ExprIsNull('id')
				],
			'uncomplete' =>
				[ 'a = 1 AND a = 2'
				, 'AND a = 2'
				, new ExprIs('a', 1)
				],
		];
	}



	/**
	 * @dataProvider providerParseExprWithBind
	 */
	function testParseExprWithBind($src, $bindmap, $tail, $expected)
	{
		$tok = new Tokenizer($bindmap);
		$a = $tok->parseExpr($src);
		$this->assertEquals($expected, $a[0]);
		$this->assertEquals($tail, $a[1]);
	}



	function providerParseExprWithBind()
	{
		return [
			'is 0' =>
				[ 'name ='
				, ['abx' => 'Foo']
				, ''
				, new ExprIs('name', 'Foo')
				],
			'is 1' =>
				[ 'name = ?.'
				, ['abx' => 'Foo']
				, '.'
				, new ExprIs('name', 'Foo')
				],
			'is 2' =>
				[ 'name = ?'
				, ['Foo']
				, ''
				, new ExprIs('name', 'Foo')
				],
			'is 3' =>
				[ 'name = ?'
				, [42]
				, ''
				, new ExprIs('name', 42)
				],
			//~ 'is 4' =>
				//~ [ 'name=?'
				//~ , [42]
				//~ , ''
				//~ , new ExprIs('name', 42)
				//~ ],
			'is 5' =>
				[ 'name='
				, [42]
				, ''
				, new ExprIs('name', 42)
				],
			'is 7' =>
				[ 'name=?'
				, [42]
				, ''
				, new ExprIs('name', 42)
				],
			'is 6' =>
				[ 'name'
				, [42]
				, ''
				, new ExprIs('name', 42)
				],
			'<' =>
				[ 'name < ?'
				, [42]
				, ''
				, new ExprLessThan('name', 42)
				],
			'< 1' =>
				[ 'name <'
				, [42]
				, ''
				, new ExprLessThan('name', 42)
				],
			'< 2' =>
				[ 'name <'
				, [42]
				, ''
				, new ExprLessThan('name', 42)
				],
			'in' =>
				[ 'name IN'
				, [[1, 5, 8]]
				, ''
				, new ExprIn('name', [1, 5, 8])
				],
			'in 2' =>
				[ 'name IN'
				, [['foo', 'goo']]
				, ''
				, new ExprIn('name', ['foo', 'goo'])
				],
			'in 3' =>
				[ 'name IN ?'
				, [['foo', 'goo']]
				, ''
				, new ExprIn('name', ['foo', 'goo'])
				],
			'not in' =>
				[ 'name NOTIN'
				, [[1, 5, 8]]
				, ''
				, new ExprNotIn('name', [1, 5, 8])
				],
			'like' =>
				[ 'name LIKE'
				, ['Foo']
				, ''
				, new ExprLike('name', 'Foo')
				],
			'like 2' =>
				[ 'name LIKE ?"'
				, ['Foo']
				, '"'
				, new ExprLike('name', 'Foo')
				],
			'not like' =>
				[ 'name NOTLIKE'
				, ['Foo']
				, ''
				, new ExprNotLike('name', 'Foo')
				],
			'uncomplete' =>
				[ 'a = %{abx} AND a = %{ddd}'
				, ['abx' => 42]
				, 'AND a = %{ddd}'
				, new ExprIs('a', 42)
				],
		];
	}



	/**
	 * @dataProvider providerParseConds
	 */
	function testParseConds($src, $tail, $expected)
	{
		$tok = new Tokenizer();
		$a = $tok->parseConds($src);
		$this->assertEquals($expected, $a[0]);
		$this->assertSame($tail, $a[1]);
	}



	function providerParseConds()
	{
		return [
			'bracket' =>
				[ '(key != "slug").'
				, '.'
				, new CondAnd( [ new ExprIsNot('key', 'slug') ] )
				],
			'bracket-without-tail' =>
				[ '(id = 5)'
				, ''
				, new CondAnd( [ new ExprIs('id', 5) ] )
				],
			'Condition with OR' =>
				[ '(id = 5 OR id = 6)'
				, ''
				, new CondOr( [ new ExprIs('id', 5), new ExprIs('id', 6) ] )
				],
			'Condition with AND' =>
				[ '(id = 5 AND id = 6)'
				, ''
				, new CondAnd( [ new ExprIs('id', 5), new ExprIs('id', 6) ] )
				],
			'Condition with AND' =>
				[ '(id = 5 AND id =  6 AND  ident = 888)'
				, ''
				, new CondAnd( [ new ExprIs('id', 5), new ExprIs('id', 6), new ExprIs('ident', 888) ] )
				],
			'tree 3' =>
				[ '(id = 5 AND (id = 6 AND ident = 888))'
				, ''
				, new CondAnd(
					[ new ExprIs('id', 5)
					, new CondAnd(
						[ new ExprIs('id', 6)
						, new ExprIs('ident', 888)
						])
					])
				],
			'tree 4' =>
				[ '(id = 5 AND (id = 6 AND ident = 888) AND id = 88)'
				, ''
				, new CondAnd(
					[ new ExprIs('id', 5)
					, new CondAnd(
						[ new ExprIs('id', 6)
						, new ExprIs('ident', 888)
						])
					, new ExprIs('id', 88)
					])
				],
			'tree 5' =>
				[ "(id = 5
					AND (id = 6 AND ident = 888)
					AND id = 88)"
				, ''
				, new CondAnd(
					[ new ExprIs('id', 5)
					, new CondAnd(
						[ new ExprIs('id', 6)
						, new ExprIs('ident', 888)
						])
					, new ExprIs('id', 88)
					])
				],
		];
	}

}
