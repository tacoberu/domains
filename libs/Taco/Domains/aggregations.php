<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains\Aggregations;


/**
 * Výraz
 *
 * @author Martin Takáč <martin@takac.name>
 */
interface Aggregation
{

}



class Count implements Aggregation
{

	function __construct($lft)
	{
	}

}
