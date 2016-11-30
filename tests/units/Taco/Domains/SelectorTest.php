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
require_once __dir__ . '/../../../../libs/Taco/Domains/Selector.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Parser.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Expr.php';
require_once __dir__ . '/../../../../libs/Taco/Domains/Cond.php';


use Taco\Domains;


/**
 *
 * @call phpunit SelectorTest.php tests_libs_taco_dhe_SelectorTest
 */
class tests_libs_taco_dhe_SelectorTest extends PHPUnit_Framework_TestCase
{


	/**
	 *	Získání informací.
	 */
	public function testSample()
	{
		$a = new Domains\Criteria('Domains\Album');
		$a->with('id');
		$a->with('title');
		$a->where('id', 5);

		$sel = new Domains\Selector($a);

		$this->assertTrue($sel->with('id'));
		$this->assertTrue($sel->with('title'));
		$this->assertFalse($sel->with('ident'));
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

		$sel = new Domains\Selector($a);

		$this->assertTrue($sel->with('id'));
		$this->assertTrue($sel->with('title'));
		$this->assertFalse($sel->with('ident'));
	}



	/**
	 *	Získání informací.
	 */
	public function testWhere()
	{
		$a = new Domains\Criteria('Domains\Album');
		$a->with('id');
		$a->with('title');
		$a->where('id', 5);

		$sel = new Domains\Selector($a);

		$this->assertTrue($sel->where('id'));
		$this->assertFalse($sel->where('ident'));
	}



	/**
	 *	Získání informací v zanořeném
	 */
	public function testWhereTree()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->with('id')
			->with('title')
			->where('id', 5)
			->where(new Domains\CondOr(
					Domains\Expr::like('title', 'lorem'),
					Domains\Expr::like('perex', 'lorem'),
					Domains\Expr::like('description', 'lorem')
					)
					);

		$sel = new Domains\Selector($a);

		$this->assertTrue($sel->where('description', True));
		$this->assertFalse($sel->where('description'));
		$this->assertFalse($sel->where('ident'));
	}



	/**
	 *	Získání informací.
	 */
	public function testIndexWhere()
	{
		$a = new Domains\Criteria('Domains\Album');
		$a->with('id');
		$a->with('title');
		$a->where('id', 5);

		$sel = new Domains\Selector($a);

		$this->assertEquals($sel->whereValue('id', 0), 5);
		try {
			$this->assertFalse($sel->whereValue('ident', 0));
			$this->fail();
		}
		catch (\InvalidArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'Clause [ident] is not found.');
		}
		try {
			$this->assertFalse($sel->whereValue('id', 1));
			$this->fail();
		}
		catch (\InvalidArgumentException $e) {
			$this->assertEquals($e->getMessage(), 'Clause [id] value with index [1] is not found.');
		}
	}




	/**
	 *	Získání informací.
	 */
	public function testOrderBy()
	{
		$a = new Domains\Criteria('Domains\Album');
		$a->with('id');
		$a->with('title');
		$a->where('id', 5);
		$a->orderByDesc('name');

		$sel = new Domains\Selector($a);

		$this->assertTrue($sel->orderBy('name'));
		$this->assertFalse($sel->orderBy('id'));
	}



	/**
	 *	Získání informací v zanořeném
	 */
	public function testWhereTree2()
	{
		$a = Domains\Criteria::create('Domains\Album')
			->with('id')
			->with('title')
			->where('id', 5)
			->where('title LIKe', 'abc')
			->where(new Domains\CondOr(
					Domains\Expr::like('title', 'lorem'),
					Domains\Expr::like('perex', 'lorem'),
					Domains\Expr::like('description', 'lorem')
					)
					)
			->where('title LIKe', 'cde');

		$sel = new Domains\Selector($a);

		$c = $sel->whereExpr('title');
		$this->assertCount(2, $c);
		$this->assertInstanceOf('Taco\Domains\ExprLike', $c[0]);
		$this->assertInstanceOf('Taco\Domains\ExprLike', $c[1]);
	}




}
