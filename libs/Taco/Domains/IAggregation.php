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
 * Condition - podmínka,
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
interface IAgregation
{

	/**
	 * Standardní metoda readeru, která získává data.
	 * @return string
	 */
	function getReadMethod();

}

