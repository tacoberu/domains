<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;


/**
 * @call phpunit FilterTest.php
 * @author Martin Takáč <martin@takac.name>
 */
class FilterTest extends PHPUnit_Framework_TestCase
{


	function testFail()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Type object of filter not found.');
		$a = new Filter(Null);
	}



	function testEmpty()
	{
		$a = new Filter('Album');
		$this->assertEquals('Album', $a->getTypeName());
		$this->assertNull($a->getWhere());
	}



	function testById()
	{
		$a = (new Filter('Article'))
				->where('id', 5);
		$this->assertEquals('Article', $a->getTypeName());
		$this->assertEqualsWhereExpr('(id = 5)', $a->getWhere());
	}



	function testCondDuplicate()
	{
		$a = (new Filter('Article'))
			->where('id', 5)
			//~ ->where('id', 4) // @TODO duplicated
			->where('id', 8)
			->where('id', 4);
		$this->assertEqualsWhereExpr('(id = 5 AND id = 8 AND id = 4)', $a->getWhere());
	}



	function testMakeWithConstructor()
	{
		$a = (new Filter('Article', new CondOr([
			new ExprIs('id', 5),
			new ExprIs('id', 8),
			new ExprIs('id', 4),
			])));
		$this->assertEqualsWhereExpr('(id = 5 OR id = 8 OR id = 4)', $a->getWhere());
	}



	function _testCondDuplicateFail()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Očekáváme právě jeden parametr.');
		$a = (new Filter('Article'))
			->where('id', 4, 7);
	}



	function testCondIs()
	{
		$a = (new Filter('Article'))
			->where(Expr::is('id', 5))
			->where(Expr::in('id', array(1, 3, 4)))
			->where(Expr::like('title', 'text'))
			->where(new CondOr([Expr::is('vote', 1), Expr::is('vote', 6)]));
		$this->assertEqualsWhereExpr('(id = 5 AND id IN [1, 3, 4] AND title LIKE "text" AND (vote = 1 OR vote = 6))', $a->getWhere());
	}



	function testCondWithParsing()
	{
		$a = (new Filter('Article'))
			->where('id', 5)
			->where('id IN', [1, 3, 4])
			->where('title LIKE', 'text')
			->where('(vote = ? OR vote = ?)', 5, 6);
		$this->assertEqualsWhereExpr('(id = 5 AND id IN [1, 3, 4] AND title LIKE "text" AND (vote = 5 OR vote = 6))', $a->getWhere());
	}



	function testCondWithParsing2()
	{
		$a = (new Filter('Article'))
			->where('(id = ?
				AND id IN ?
				AND title LIKE ?
				AND (vote = ? OR vote = ?))', 5, [1, 3, 4], 'text', 5, 6);
		$this->assertEqualsWhereExpr('(id = 5 AND id IN [1, 3, 4] AND title LIKE "text" AND (vote = 5 OR vote = 6))', $a->getWhere());
	}



	function _testWhereBySelf()
	{
		$album = new \stdClass;
		$album->id = 8;

		$a = (new Filter('Article'))
			->where('@', $album);

		$where = $a->getWhere();
		$list = $where->expresions();
		$this->assertInstanceOf(ExprThisIsEquals::class, $list[0]);
		$this->assertEquals((object)array('id' => 8), $list[0]->value());
	}



	function testFreeze()
	{
		$this->setExpectedException(InvalidStateException::class, 'Cannot modify a frozen object Taco\Domains\Filter.');
		$a = (new Filter('Article'))
			->where('id', 5)
			->where('id IN', array(1, 3, 4))
			->where('title LIKE', 'text')
			->freeze();
		$this->assertTrue($a->isFrozen());
		$a->where('count', 6);
	}



	private function assertEqualsWhereExpr($desc, IExpr $expr)
	{
		$this->assertEquals($desc, Printer::formatWhere($expr));
	}

}
