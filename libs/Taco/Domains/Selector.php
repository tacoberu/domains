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
 *	Výběr
 *
 *	@author     Martin Takáč (taco@taco-beru.name)
 */
class Selector
{

	private $source;


	/**
	 *	V jakém zdroji to budem vyhledávat.
	 */
	public function __construct($source)
	{
		$this->source = $source;
	}



	/**
	 *	Zjistí, zda je přítomna požadovaná klausule. V případě složeného výrazu,
	 *	vrací celý výraz.
	 */
	public function with($clause)
	{
		foreach ($this->source->getWith() as $item) {
			if (is_string($item)) {
				if ($item == $clause) {
					return True;
				}
			}
			else if (is_object($item)) {
				if ($item->name == $clause) {
					return $item->filter;
				}
			}
			else {
				throw new \LogicException('Struktura criteria je nějaká rozbitá.');
			}
		}
		return False;
	}



	/**
	 *	Existuje klausule?
	 */
	public function where($clause, $deep = False)
	{
		return (boolean) self::findWhere($this->source->getWhere(), $clause, $deep);
	}



	/**
	 *	Existuje klausule?
	 */
	public function whereExpr($clause, $deep = False)
	{
		return self::findWhere($this->source->getWhere(), $clause, $deep);
	}



	/**
	 *	Existuje klausule?
	 */
	public function whereValue($clause, $index)
	{
		$c = self::findWhere($this->source->getWhere(), $clause, False);
		if (! $c) {
			throw new \InvalidArgumentException("Clause [$clause] is not found.", 3);
		}
		if (count($c) > 1) {
			throw new \InvalidArgumentException("Clause [$clause] is multiple [" . count($c) . "].", 4);
		}
		$c = $c[0];
		
		if ($index > 0) {
			$values = $c->value();
			if (! isset($values[$index])) {
				throw new \InvalidArgumentException("Clause [$clause] value with index [$index] is not found.", 1);
			}
			return $values;
		}
		else if (! $c->value()) {
			throw new \InvalidArgumentException("Clause [$clause] value with index [$index] is not found.", 2);
		}

		return $c->value();
	}



	/**
	 *	Existuje klausule?
	 */
	public function orderBy($clause)
	{
		$c = $this->source->getOrderBy();
		return isset($c[$clause]);
	}



	/**
	 *	Prohledávání do hloubky.
	 */
	private static function findWhere($where, $clause, $deep)
	{
		$ret = array();
		foreach ($where->expresions() as $c) {
			if ($c instanceof Cond) {
				if ($deep && ($cs = self::findWhere($c, $clause, $deep))) {
					$ret[] = array_merge($ret, $cs);
				}
			}
			elseif ($c->prop() == $clause) {
				$ret[] = $c;
			}
		}
		return $ret;
	}



}
