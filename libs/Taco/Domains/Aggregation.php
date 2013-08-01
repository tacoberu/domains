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

namespace Taco\Domains;




/**
 * Většina agragací jen kopíruje
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
abstract class Agregation implements IAgregation, ICriteria
{

	/**
	 * Dekorator instance, na kterou se převádí funkce.
	 */
	private $criteria;


	/**
	 * Dekorator pro Criteria.
	 * @param criteria.
	 */
	public function __construct(ICriteria $criteria)
	{
		$this->criteria = $criteria;
	}



	/**
	 * @see ICriteria::getTypeName()
	 */
	public function getTypeName()
	{
		return $this->criteria->getTypeName();
	}



	/**
	 * Standardní metoda readeru, která získává data.
	 * @return string
	 */
	public abstract function getReadMethod();



	/**
	 * Zvoli, ktere atributy entity chcem.
	 *
	 * @example:
	 * $model->with('id')	//	Chceme sloupecek id
	 * 	->with('*')		//	Vsechny prvky, bez asociaci
	 * 	->with('title')	//	Chceme title
	 * 	->with('discuss.id')	//	Chceme hodnoty asociace discus, ale z ni jen id
	 * 	->with('vote.*')	//	Chceme vsechny hodnoty asociace vote
	 *
	 * @params string
	 * @see ICriteria::with()
	 *
	 * @return self
	 */
	function with($name, $filter = Null)
	{
		$this->criteria->with($name, $filter);
		return $this;
	}



	/**
	 * Zvoli, na ktere sloupce budem filtrovat.
	 *
	 * @example:
	 * $model->where('id = ?', "56")	//	Podle validace zkontroluje, ze tam ma byt int.
	 * 	->where('id IS NOT NULL')
	 * 	->where('title = ? OR title = ?', 'foo', 'boo')
	 * 	->where('discuss.id = ?', 6)
	 * 	->where('vote.create BETWEEN ? AND ?', time(), time()+100)
	 * 	->where('role IN (?)', array(1, 4, 6))
	 *
	 * @see ICriteria::where()
	 * @params more
	 *
	 * @return self
	 */
	function where($expresion)
	{
		$args = func_get_args();
		call_user_func_array(array($this->criteria, 'where'), $args);
		return $this;
	}



	/**
	 * Razeni.
	 *
	 * @example:
	 * $model->orderByDesc('id')
	 * 	->orderByDesc('title')
	 * 	->orderByDesc('title = "cs"')
	 *
	 * @see ICriteria::orderByDesc()
	 * @params string
	 *
	 * @return self
	 */
	function orderByDesc($by)
	{
		return $this->criteria->orderByDesc($by);
	}



	/**
	 * Razeni.
	 *
	 * @see ICriteria::orderByDesc()
	 * @params string
	 *
	 * @return self
	 */
	function orderByAsc($by)
	{
		return $this->criteria->orderByAsc($by);
	}



	/**
	 * Omezení.
	 *
	 * @see ICriteria::limit()
	 * @params integer
	 *
	 * @return self
	 */
	function limit($value)
	{
		return $this->criteria->limit($value);
	}



	/**
	 * Omezení.
	 *
	 * @see ICriteria::offset()
	 * @params integer
	 *
	 * @return self
	 */
	function offset($value)
	{
		return $this->criteria->offset($value);
	}



	/** freezable *****************************************************/



	/**
	 * Makes the object unmodifiable.
	 * @return void
	 */
	public function __call($method, $args)
	{
		return call_user_func_array(array($this->criteria, $method), $args);
	}



	/**
	 * Creates a modifiable clone of the object.
	 * @return void
	 */
	public function __clone()
	{
		clone $this->criteria;
	}


}
