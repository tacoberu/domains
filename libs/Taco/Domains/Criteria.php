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
 * Criteria
 *
 * Pozadavek dat.
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class Criteria implements ICriteria
{


	/**
	 * Typ požadovaného prvku.
	 */
	protected $typeName;



	/**
	 * Jmeno prvku. Bud je to string, nebo instance.
	 */
	private $with = array();



	/**
	 * Podmínka je v podobě stromu. Kořenem bývá nejčastěji AND
	 * @var Cond
	 */
	private $filter = array();



	/**
	 * Setrideno.
	 */
	private $orderBy = array();



	/**
	 * Posunutí.
	 */
	private $offset = NULL;



	/**
	 * Omezení počtu.
	 */
	private $limit = NULL;



	/**
	 * Is the object unmodifiable?
	 * @var bool
	 */
	private $frozen = FALSE;



	/**
	 * @param Objekt.
	 */
	static function create($type)
	{
		return new self($type);
	}



	/**
	 * @param Objekt.
	 */
	static function range($type, $limit = 40, $offset = 0)
	{
		return self::create($type)
				->limit($limit)
				->offset($offset);
	}



	/**
	 * @param Objekt.
	 */
	static function first($type, $limit = 40, $offset = 0)
	{
		return new AgregationFirst(self::create($type));
	}



	/**
	 * @param Objekt.
	 */
	function __construct($type)
	{
		if (empty($type)) {
			throw new \InvalidArgumentException('Type object of criteria not found.');
		}

		if (is_object($type)) {
			$type = get_class($type);
		}

		$this->typeName = $type;
	}




	/**
	 * @params string
	 *
	 * @return Criteria
	 */
	function what($name)
	{
		$this->updating();
		$this->what = $name;
		return $this;
	}



	/**
	 * Zvoli, ktere sloupce chcem.
	 *
	 * @example:
	 * $model->with('id')    // Chceme sloupecek id
	 *  ->with('*')          // Vsechny prvky, bez asociaci
	 *  ->with('title')      // Chceme title
	 *  ->with('discuss.id') // Chceme hodnoty asociace discus, ale z ni jen id
	 *  ->with('vote.*')     // Chceme vsechny hodnoty asociace vote
	 *
	 * @params string
	 *
	 * @return Criteria
	 */
	function with($name, $filter = Null)
	{
		$this->updating();
		if (in_array($name, $this->with)) {
			return $this;
		}
		if ($filter) {
			$name = (object) array(
					'name' => $name,
					'filter' => $filter
				);
		}
		$this->with[] = $name;
		return $this;
	}



	/**
	 * Zruší požadavky.
	 * @return self
	 */
	function resetWith()
	{
		$this->updating();
		$this->with = array();
		return $this;
	}



	/**
	 * Nastavi podminku do Criteria
	 * @param mixed
	 * @example
	 * $criteria->where('code LIKE', $code);
	 *
	 * @return ICriteria
	 */
	function where($expresion)
	{
		$args = func_get_args();

		if (empty($this->filter)) {
			$list = new CondAnd();
			$this->filter = $list;
		}
		else {
			$list = $this->filter;
		}
		foreach (Parser::formatWhere($args) as $expr) {
			$this->filter->add($expr);
		}

		$this->updating();
		return $this;
	}



	/**
	 * Razeni.
	 *
	 * @example:
	 * $model->sort('id')
	 *  ->sort('title DESC')
	 *  ->sort('title = "cs"')
	 *
	 * @params string
	 *
	 * @return Criteria
	 */
	function orderByAsc($by)
	{
		$this->updating();
		$this->orderBy[$by] = 'ASC';
		return $this;
	}



	/**
	 * Razeni.
	 *
	 * @example:
	 * $model->sort('id')
	 *  ->sort('title DESC')
	 *  ->sort('title = "cs"')
	 *
	 * @params string
	 *
	 * @return Criteria
	 */
	function orderByDesc($by)
	{
		$this->updating();
		$this->orderBy[$by] = 'DESC';
		return $this;
	}



	/**
	 * Zruší řazení.
	 * @return self
	 */
	function resetOrderBy()
	{
		$this->updating();
		$this->orderBy = array();
		return $this;
	}



	/**
	 * Omezit počet.
	 * @param  int   Počet záznamů.
	 * @return self
	 */
	function limit($value)
	{
		$this->updating();
		$this->limit = $value;
		return $this;
	}



	/**
	 * Posunout na offset.
	 * @param  int   Posun o.
	 * @return self
	 */
	function offset($value)
	{
		$this->updating();
		$this->offset = $value;
		return $this;
	}



	/** Gettery *******************************************************/



	/**
	 * Seznam sloupců.
	 */
	function getTypeName()
	{
		return $this->typeName;
	}



	/**
	 * Seznam sloupců.
	 */
	function getWith()
	{
		return $this->with;
	}



	/**
	 * Seznam omezení filtrace.
	 * @return Cond
	 */
	function getWhere()
	{
		return $this->filter;
	}



	/**
	 * Seznam řazení.
	 */
	function getOrderBy()
	{
		return $this->orderBy;
	}



	/**
	 * Posunutí výpisu.
	 * @return int
	 */
	function getOffset()
	{
		return $this->offset;
	}



	/**
	 * Omezení počtu výpisu.
	 * @return int
	 */
	function getLimit()
	{
		return $this->limit;
	}



	/**
	 * Výpočet hashe pro tento dotaz.
	 * @return string
	 */
	function hash()
	{
		$s = $this->getTypeName() . '|';

		if (count($this->getWith())) {
			$a = $this->getWith();
			sort($a);
			$s .= 'select:[' . implode(';', $a) . ']';
		}

		if (count($this->getWhere())) {
			$a = array();
			foreach ($this->getWhere() as $row) {
				$tmp = array();
				foreach ($row->args as $arg) {
					if (is_object($arg)) {
						$tmp[] = get_class($arg);
					}
					else if (is_integer($arg)) {
						$tmp[] = 'INT';
					}
					else if (is_string($arg)) {
						$tmp[] = 'STRING';
					}
					else {
						$tmp[] = gettype($arg);
					}
				}
				$a[] = $row->expr . '=' . implode(',', $tmp);
			}
			sort($a);
			$s .= 'where:[' . implode(';', $a) . ']';
		}

		if (count($this->getOrderBy())) {
			$a = array();
			foreach ($this->getOrderBy() as $name => $dir) {
				$a[] = "$name:$dir";
			}
			$s .= 'orderby:[' . implode(';', $a) . ']';
		}
		return md5($s);
	}



	/** freezable *****************************************************/



	/**
	 * Makes the object unmodifiable.
	 * @return void
	 */
	function freeze()
	{
		$this->frozen = TRUE;
		return $this;
	}



	/**
	 * Is the object unmodifiable?
	 * @return bool
	 */
	final function isFrozen()
	{
		return $this->frozen;
	}



	/**
	 * Creates a modifiable clone of the object.
	 * @return void
	 */
	function __clone()
	{
		$this->frozen = FALSE;
	}



	/**
	 * @return void
	 */
	protected function updating()
	{
		if ($this->frozen) {
			$class = get_class($this);
			throw new InvalidStateException("Cannot modify a frozen object $class.", 1);
		}
	}


}
