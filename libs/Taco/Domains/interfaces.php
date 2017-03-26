<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;


/**
 * Omezujeme položky rozsahu.
 * @author Martin Takáč <martin@takac.name>
 */
interface Filterable
{
	/**
	 * Zvoli omezení na sloupce.
	 *
	 * @example:
	 * $model->where('id = ?', "56")	//	Podle validace zkontroluje, ze tam ma byt int.
	 *     ->where('id IS NOT NULL')
	 *     ->where('title = ? OR title = ?', 'foo', 'boo')
	 *     ->where('discuss.id = ?', 6)
	 *     ->where('vote.create BETWEEN ? AND ?', time(), time()+100)
	 *     ->where('role IN (?)', array(1, 4, 6))
	 *
	 * @param more
	 *
	 * @return self
	 */
	function where($args);


	/**
	 * @return array
	 */
	function getWhere();

}



/**
 * Řadíme položky.
 * @author Martin Takáč <martin@takac.name>
 */
interface Sortable
{
	/**
	 *	Řazení.
	 *
	 *	@example:
	 *	$model->sort('key', DESC)
	 *		->sort('title', DESC)
	 *		->sort('title = "cs"', DESC)
	 *
	 *	@param string
	 *	@param string
	 *
	 *	@return self
	 */
	function sort($by, $dir);


	/**
	 * Získání výsledků.
	 * @return array of OrderItem
	 */
	function getOrders();

}



/**
 * Rozsah položek.
 * @author Martin Takáč <martin@takac.name>
 */
interface Range extends Filterable, Sortable
{

	/**
	 *	@param integer
	 *
	 *	@return self
	 */
	function limit($value);



	/**
	 *	@param integer
	 *
	 *	@return self
	 */
	function offset($value);

}



/**
 * Unikátní identifikace právě jedné položky.
 * @author Martin Takáč <martin@takac.name>
 */
interface Identification
{

	/**
	 * Representation ident as string.
	 * @return string
	 */
	function getIdentKey();

}



/**
 * Instance collecting change of values of entry.
 * @author Martin Takáč <martin@takac.name>
 */
interface Changes
{

	/**
	 * Změny na jednom, nebo množině záznamů.
	 * @return Identification|Range
	 */
	function getFilter();



	/**
	 * Results
	 * @return array
	 */
	function changes();

}



/**
 * Příkaz pro smazání nějakého záznamu, nebo záznamů.
 * @author Martin Takáč <martin@takac.name>
 */
interface Remove
{

	/**
	 * Smazat jeden, nebo více záznamů.
	 * @return Identification|Range
	 */
	function getFilter();

}
