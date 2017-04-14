<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;
use Exception;
use PHPUnit_Framework_TestCase;


/**
 * @call phpunit CriteriaTest.php CriteriaTest
 * @author Martin Takáč <martin@takac.name>
 */
class CriteriaTest extends PHPUnit_Framework_TestCase
{

	function testFail()
	{
		$this->setExpectedException(InvalidArgumentException::class, 'Type object of criteria not found.');
		new Criteria(Null);
	}



	/**
	 *	Získání informací.
	 */
	function testCondIs()
	{
		$a = Criteria::create('Domains\Album')
			->where(Expr::is('id', 5))
			->where(Expr::in('id', array(1, 3, 4)))
			->where(Expr::like('title', 'text'))
			//->where(Domains\Cond::_or(Domains\Expr::is('vote', 1), Domains\Expr::is('vote', 6)))
			;
		$where = $a->getWhere();
		$this->assertEquals($where->type(), 'AND');
		$list = $where->expresions();

		$this->assertEquals(count($list), 3);
		$this->assertEquals($list[0]->prop(), 'id');
		$this->assertEquals($list[0]->value(), 5);
		$this->assertEquals(get_class($list[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($list[1]->prop(), 'id');
		$this->assertEquals($list[1]->value(), array(1, 3, 4));
		$this->assertEquals(get_class($list[1]), 'Taco\Domains\ExprIn');
		$this->assertEquals($list[2]->prop(), 'title');
		$this->assertEquals($list[2]->value(), 'text');
		$this->assertEquals(get_class($list[2]), 'Taco\Domains\ExprLike');
	}



	/**
	 *	Získání informací.
	 */
	function testCondOr()
	{
		$a = Criteria::create('Domains\Album')
			->where(Expr::is('id', 5))
			->where(new CondOr([Expr::is('vote', 11), Expr::is('vote', 6)]))
			;
		$where = $a->getWhere();
		$this->assertEquals($where->type(), 'AND');
		$list = $where->expresions();

		$this->assertEquals(count($list), 2);
		$this->assertEquals($list[0]->prop(), 'id');
		$this->assertEquals($list[0]->value(), 5);
		$this->assertEquals(get_class($list[0]), 'Taco\Domains\ExprIs');
		//$this->assertEquals($list[1]->prop(), 'id');
		//$this->assertEquals($list[1]->values(), array(1, 3, 4));
		$this->assertEquals(get_class($list[1]), 'Taco\Domains\CondOr');
		$list = $list[1]->expresions();
		$this->assertEquals(count($list), 2);
		$this->assertEquals($list[0]->prop(), 'vote');
		$this->assertEquals($list[0]->value(), 11);
		$this->assertEquals(get_class($list[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($list[1]->prop(), 'vote');
		$this->assertEquals($list[1]->value(), 6);
		$this->assertEquals(get_class($list[1]), 'Taco\Domains\ExprIs');
	}



	/**
	 *	Jak vypadá, bez nastavení.
	 */
	function testEmpty()
	{
		$a = new Criteria('Domains\Album');
		$this->assertEquals('Domains\Album', $a->getTypeName());
		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(NULL, $a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení sloupcu.
	 */
	function testSetWith()
	{
		$a = new Criteria('Domains\Album');
		$a->with('id')			//	Chceme sloupecek id
			->with('*')			//	Vsechny prvky, bez asociaci
			->with('title')		//	Chceme title
			->with('discuss.id')//	Chceme hodnoty asociace discus, ale z ni jen id
			->with('vote.*');	//	Chceme vsechny hodnoty asociace vote

		$this->assertEquals(array('id','*','title','discuss.id','vote.*'), $a->getWith());
		$this->assertNull($a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení podmínek.
	 */
	function testSetWhere()
	{
		$a = Criteria::create('Domains\Album')
			->where('id =', 5)
			->where('id IN', array(1, 3, 4))
			->where('title LIKE', 'text')
			->where('(vote = ? OR vote = ?)', 1, 6);

		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
		$where = $a->getWhere();
		$this->assertEquals('Domains\Album[(id = 5 AND id IN [1, 3, 4] AND title LIKE "text" AND (vote = 1 OR vote = 6))]', Printer::format($a));
	}



	function testWhereExpresion()
	{
		$a = Criteria::create('Domains\Album')
			->where(new ExprIs('id', 555));

		$list = $a->getWhere()->expresions();

		$this->assertEquals(1, count($list));
		$this->assertInstanceOf(ExprIs::class, $list[0]);
		$this->assertEquals('id', $list[0]->prop());
		$this->assertEquals(555, $list[0]->value());
	}



	/**
	 *	Jak vypadá, po nastavení řazení
	 */
	function testSetSort()
	{
		$a = Criteria::create('Domains\Album')
			->orderByAsc('id')
			->orderByDesc('title');

		$this->assertEquals(array(), $a->getWith());
		$this->assertNull($a->getWhere());
		$this->assertEquals(array('id' => 'ASC', 'title' => 'DESC'), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení Rozsahu.
	 */
	function testSetRange()
	{
		$a = Criteria::create('Domains\Album')
			->limit(10)
			->offset(8);

		$this->assertEquals(array(), $a->getWith());
		$this->assertNull($a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertEquals(10, $a->getLimit());
		$this->assertEquals(8, $a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení Rozsahu.
	 */
	function testFreeze()
	{
		$this->setExpectedException(InvalidStateException::class, 'Cannot modify a frozen object Taco\Domains\Criteria.');
		$a = Criteria::create('Domains\Album')
			->where('id', 5)
			->where('id IN', array(1, 3, 4))
			->where('title LIKE', 'text')
			//->where('(vote = %i OR vote = %i)', 1, 6)
			->limit(10)
			->offset(8)
			->freeze();
		$this->assertTrue($a->isFrozen());
		$a->where('count', 6);
	}

}
