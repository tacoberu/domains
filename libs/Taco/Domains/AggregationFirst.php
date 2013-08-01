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
 * Agregace, která vrací první záznam odpovídající podmínce.
 *
 * @author     Martin Takáč (taco@taco-beru.name)
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
	 *	Posunutí výpisu.
	 *	@return int
	 */
	public function getOffset()
	{
		return 0;
	}



	/**
	 *	Omezení počtu výpisu.
	 *	@return int
	 */
	public function getLimit()
	{
		return 1;
	}

}



