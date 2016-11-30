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
 * Agregace, která vrací počet záznamů odpovídající podmínce.
 *
 * @author Martin Takáč (taco@taco-beru.name)
 */
class AgregationCount extends Agregation
{


	/**
	 * Standardní metoda readeru, která získává data.
	 * @return string
	 */
	function getReadMethod()
	{
		return 'readCount';
	}

}
