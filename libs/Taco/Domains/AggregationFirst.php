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
 * Agregace, která vrací první záznam odpovídající podmínce.
 *
 * @author Martin Takáč (taco@taco-beru.name)
 */
class AgregationFirst extends Agregation
{


	/**
	 * Standardní metoda readeru, která získává data.
	 * @return string
	 */
	function getReadMethod()
	{
		return 'readFirst';
	}



	/**
	 * Posunutí výpisu.
	 * @return int
	 */
	function getOffset()
	{
		return 0;
	}



	/**
	 * Omezení počtu výpisu.
	 * @return int
	 */
	function getLimit()
	{
		return 1;
	}

}
