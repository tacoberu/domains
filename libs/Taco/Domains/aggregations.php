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

	private $lft;

	/**
	 * @param mixed $lft
	 */
	function __construct($lft)
	{
		$this->lft = $lft;
	}

}
