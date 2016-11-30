<?php
/**
 * This file is part of the Domains (https://github.com/tacoberu/domains)
 *
 * Copyright (c) 2004 Martin Takáč (http://martin.takac.name)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Taco\Domains;


/**
 * Criteria
 *
 * Pozadavek dat. Cast pro verejne pouziti programatora.
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
interface ICriteria
{



	/**
	 * Zvoli, ktere atributy entity chcem.
	 *
	 * @example:
	 * $model->with('id') // Chceme sloupecek id
	 *  ->with('*')       // Vsechny prvky, bez asociaci
	 *  ->with('title')   // Chceme title
	 *  ->with('discuss.id') // Chceme hodnoty asociace discus, ale z ni jen id
	 *  ->with('vote.*') // Chceme vsechny hodnoty asociace vote
	 *
	 * @params string
	 *
	 * @return self
	 */
	function with($name, $filter = Null);



	/**
	 * Zvoli, na ktere sloupce budem filtrovat.
	 *
	 * @example:
	 * $model->where('id = ?', "56") // Podle validace zkontroluje, ze tam ma byt int.
	 *  ->where('id IS NOT NULL')
	 *  ->where('title = ? OR title = ?', 'foo', 'boo')
	 *  ->where('discuss.id = ?', 6)
	 *  ->where('vote.create BETWEEN ? AND ?', time(), time()+100)
	 *  ->where('role IN (?)', array(1, 4, 6))
	 *
	 * @params more
	 *
	 * @return self
	 */
	function where($args);



	/**
	 * Razeni.
	 *
	 * @example:
	 * $model->orderByDesc('id')
	 *  ->orderByDesc('title')
	 *  ->orderByDesc('title = "cs"')
	 *
	 * @params string
	 *
	 * @return self
	 */
	function orderByDesc($by);



	/**
	 * Razeni.
	 *
	 * @see orderByDesc
	 * @params string
	 *
	 * @return self
	 */
	function orderByAsc($by);



	/**
	 * Omezení.
	 *
	 * @params integer
	 *
	 * @return self
	 */
	function limit($value);



	/**
	 * Omezení.
	 *
	 * @params integer
	 *
	 * @return self
	 */
	function offset($value);



}
