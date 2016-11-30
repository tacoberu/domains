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
 * Condition - podmínka,
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class Cond implements IExpr
{

	const TYPE_AND = 'AND';
	const TYPE_OR = 'OR';
	const TYPE_NOT = 'NOT';


	/**
	 * Seznam podmínek.
	 */
	private $list = array();



	/**
	 * Operand.
	 */
	protected $type;



	/**
	 * Typ výrazu.
	 */
	function type()
	{
		return $this->type;
	}


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
 * Condition - podmínka,
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class CondOr extends Cond
{


	/**
	 * Podmínka jsoucnosti: (id = 1 OR id = 2 OR id = 5)
	 * @param Objekt.
	 */
	public function __construct()
	{
		$this->type = self::TYPE_OR;
		foreach (Parser::formatWhere(func_get_args()) as $expr) {
			$this->add($expr);
		}
	}



}


/**
 * Condition - podmínka,
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class CondAnd extends Cond
{


	/**
	 * Podmínka jsoucnosti: (id = 1 AND id = 2 AND id = 5)
	 * @param Objekt.
	 */
	public function __construct()
	{
		$this->type = self::TYPE_AND;
		foreach (Parser::formatWhere(func_get_args()) as $expr) {
			$this->add($expr);
		}
	}



}



/**
 * Condition - podmínka,
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class _FuncNot extends Cond
{


	/**
	 * Negace: NOT (id = 1 AND id = 2 AND id = 5)
	 * Negace: NOT (id = 1 AND id = 2 AND id = 5)
	 * @param Objekt.
	 */
	public function __construct()
	{
		$this->type = self::TYPE_NOT;
		foreach (Parser::formatWhere(func_get_args()) as $expr) {
			$this->add($expr);
		}
	}



}
