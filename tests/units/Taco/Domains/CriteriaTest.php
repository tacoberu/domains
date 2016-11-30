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


require_once __dir__ . '/../../../../libs/Taco/Domains/ICriteria.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Criteria.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Parser.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Expr.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Cond.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/exceptions.php';



use Taco\Domains;


/**
 *
 * @call phpunit CriteriaTest.php tests_libs_taco_dhe_CriteriaTest
 */
class tests_libs_taco_dhe_CriteriaTest extends PHPUnit_Framework_TestCase
{


	/**
	 *	Je třeba určit jaký typ objektu získáme.
	 */
	public function testFail()
	{
		try {
			$a = new Domains\Criteria(Null);
			$this->fail('Not throwed exception.');
		}
		catch (\InvalidArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'Type object of criteria not found.');
		}
		catch (\Exception $e) {
			throw new $e;
		}
	}



	/**
	 *	Získání informací.
	 */
	public function testReflection()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');

		$a = new Domains\Criteria('Domains\Album');
		$a->where('id=%i', 5)
			->where('id IN %in', array(1, 3, 4))
			->where('title LIKE %s', 'text')
			->where('(vote = %i OR vote = %i)', 1, 6);

dump($a);
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testCondDuplicate()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->where('id', 5)
			->where('id', 8)
			->where('id', 4);
		$where = $a->getWhere();
		$this->assertEquals($where->type(), 'AND');
		$list = $where->expresions();

		$this->assertEquals(count($list), 3);
		$this->assertEquals($list[0]->prop(), 'id');
		$this->assertEquals($list[0]->value(), 5);
		$this->assertEquals(get_class($list[0]), 'Taco\Domains\ExprIs');
		$this->assertEquals($list[1]->prop(), 'id');
		$this->assertEquals($list[1]->value(), 8);
		$this->assertEquals(get_class($list[1]), 'Taco\Domains\ExprIs');
		$this->assertEquals($list[2]->prop(), 'id');
		$this->assertEquals($list[2]->value(), 4);
		$this->assertEquals(get_class($list[2]), 'Taco\Domains\ExprIs');
	}



	/**
	 *	Různé podmínky na stejný sloupce.
	 */
	public function testCondDuplicateFail()
	{
		try {
			$a = Domains\Criteria::create('Domains\Album')
				->where('id', 4, 7);
			$this->fail('exception');
		}
		catch (\InvalidArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'Prilis mnoho parametru: 2');
		}
	}



	/**
	 *	Získání informací.
	 */
	public function testCondIs()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->where(Domains\Expr::is('id', 5))
			->where(Domains\Expr::in('id', array(1, 3, 4)))
			->where(Domains\Expr::like('title', 'text'))
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
	public function testCondOr()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->where(Domains\Expr::is('id', 5))
			->where(new Domains\CondOr(Domains\Expr::is('vote', 11), Domains\Expr::is('vote', 6)))
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
	public function testEmpty()
	{
		$a = new Domains\Criteria('Domains\Album');
		$this->assertEquals('Domains\Album', $a->getTypeName());
		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(array(), $a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení sloupcu.
	 */
	public function testSetWith()
	{
		$a = new Domains\Criteria('Domains\Album');
		$a->with('id')			//	Chceme sloupecek id
			->with('*')			//	Vsechny prvky, bez asociaci
			->with('title')		//	Chceme title
			->with('discuss.id')//	Chceme hodnoty asociace discus, ale z ni jen id
			->with('vote.*');	//	Chceme vsechny hodnoty asociace vote

		$this->assertEquals(array('id','*','title','discuss.id','vote.*'), $a->getWith());
		$this->assertEquals(array(), $a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení podmínek.
	 */
	public function testWhereBySelf()
	{
		$album = new \stdClass;
		$album->id = 8;

		$a = Domains\Criteria::create('Domains\Album')
			->where('@', $album);

		$where = $a->getWhere();
		$list = $where->expresions();
		$this->assertInstanceOf('Taco\Domains\ExprThisIsEquals', $list[0]);
		$this->assertEquals((object)array('id' => 8), $list[0]->value());
	}



	/**
	 *	Jak vypadá, po nastavení podmínek.
	 */
	public function testSetWhere()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');

		$a = Domains\Criteria::create('Domains\Album')
			->where('id=%i', 5)
			->where('id IN %in', array(1, 3, 4))
			->where('title LIKE %s', 'text')
			->where('(vote = %i OR vote = %i)', 1, 6);

		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
		$where = $a->getWhere();
		$this->assertEquals(4, count($where));
// dump($where);
		$this->assertEquals(
				(object) array (
						"expr" => "id=%i",
						"args" => array(5)
					),
				$where['id=%i']
			);
		$this->assertEquals(
				(object) array (
						"expr" => "id IN %in",
						"args" => array(
								array(1, 3, 4)
							)
					),
				$where["id IN %in"]
			);
		$this->assertEquals(
				(object) array (
						"expr" => "title LIKE %s",
						"args" => array('text')
					),
				$where["title LIKE %s"]
			);
		$this->assertEquals(
				(object) array (
						"expr" => "(vote = %i OR vote = %i)",
						"args" => array(1, 6)
					),
				$where["(vote = %i OR vote = %i)"]
			);
	}



	/**
	 *	Jak vypadá, po nastavení řazení
	 */
	public function testSetSort()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->orderByAsc('id')
			->orderByDesc('title');

		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(array(), $a->getWhere());
		$this->assertEquals(array('id' => 'ASC', 'title' => 'DESC'), $a->getOrderBy());
		$this->assertNull($a->getLimit());
		$this->assertNull($a->getOffset());
	}



	/**
	 *	Jak vypadá, po nastavení Rozsahu.
	 */
	public function testSetRange()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->limit(10)
			->offset(8);

		$this->assertEquals(array(), $a->getWith());
		$this->assertEquals(array(), $a->getWhere());
		$this->assertEquals(array(), $a->getOrderBy());
		$this->assertEquals(10, $a->getLimit());
		$this->assertEquals(8, $a->getOffset());
	}




	/**
	 *	Jak vypadá, po nastavení Rozsahu.
	 */
	public function testFreeze()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->where('id', 5)
			->where('id IN', array(1, 3, 4))
			->where('title LIKE', 'text')
			//->where('(vote = %i OR vote = %i)', 1, 6)
			->limit(10)
			->offset(8)
			->freeze();
		$this->assertTrue($a->isFrozen());
		try {
			$a->where('count', 6);
			$this->fail('Objekt nesmí jít dále upravovat.');
		}
		catch (Domains\InvalidStateException $e) {
			$this->assertEquals($e->getMessage(), 'Cannot modify a frozen object Taco\Domains\Criteria.');
			$this->assertEquals($e->getCode(), 1);
		}
	}




}
