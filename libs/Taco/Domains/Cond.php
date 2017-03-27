<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;


/**
 * Condition - podmínka. Podmínka existuje sama o sobě. Je ji možné použít
 * v případě WHERE u SELECTu, ale třeba i u DELETE, UPDATE. Stejně tak jako u
 * v případě JOIN - když bychom Criteria chápali jako SQL.
 *
 * @author Martin Takáč <martin@takac.name>
 */
abstract class Cond implements IExpr
{

	const TYPE_AND = 'AND';
	const TYPE_OR = 'OR';
	const TYPE_NOT = 'NOT';


	/**
	 * Seznam podmínek.
	 */
	private $list = array();


	/**
	 * Seznam výrazů.
	 */
	function expresions()
	{
		return $this->list;
	}



	/**
	 * Přiřadit podmínku.
	 */
	function add(IExpr $expr)
	{
		$this->list[] = $expr;
		return $this;
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	function __toString()
	{
		if (!$this->expresions()) {
			return '';
		}
		return '(' . implode(' ' . $this->type() . ' ', $this->expresions()) . ')';
	}

}



/**
 * Condition - výrazy s OR.
 *
 * @author     Martin Takáč <martin@takac.name>
 */
class CondOr extends Cond
{

	/**
	 * Podmínka jsoucnosti: (id = 1 OR id = 2 OR id = 5)
	 */
	function __construct(array $args)
	{
		foreach ($args as $expr) {
			$this->add($expr);
		}
	}



	function type()
	{
		return self::TYPE_OR;
	}

}



/**
 * Condition - podmínka slučovací.
 *
 * @author     Martin Takáč <martin@takac.name>
 */
class CondAnd extends Cond
{

	/**
	 * Podmínka jsoucnosti: (id = 1 AND id = 2 AND id = 5)
	 * @param Objekt.
	 */
	function __construct(array $args)
	{
		foreach ($args as $expr) {
			$this->add($expr);
		}
	}



	function type()
	{
		return self::TYPE_AND;
	}

}



/**
 * Condition - podmínka,
 *
 * @author     Martin Takáč (martin@takac.name>
 */
class _FuncNot extends Cond
{


	/**
	 * Negace: NOT (id = 1 AND id = 2 AND id = 5)
	 * Negace: NOT (id = 1 AND id = 2 AND id = 5)
	 * @param Objekt.
	 */
	function __construct()
	{
		foreach (Parser::parseWhere(func_get_args()) as $expr) {
			$this->add($expr);
		}
	}



	function type()
	{
		return self::TYPE_NOT;
	}

}
