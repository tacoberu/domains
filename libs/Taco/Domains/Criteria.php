<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;


/**
 * Komplexní požadavek na nějaká data. Obsahuje filtrování, řazení, omezení na stránky.
 *
 * @author Martin Takáč <martin@takac.name>
 */
class Criteria
{

	/**
	 * Jmeno prvku. Bud je to string, nebo instance.
	 * @var mixed
	 */
	private $what;


	/**
	 * @var array
	 */
	private $with = array();


	/**
	 * Podmínka je v podobě stromu. Kořenem bývá nejčastěji AND
	 * @var Filter
	 */
	private $filter;


	/**
	 * Setrideno.
	 * @var array<string, 'ASC'|'DESC'>
	 */
	private $orderBy = array();


	/**
	 * Posunutí.
	 * @var int|Null
	 */
	private $offset = NULL;


	/**
	 * Omezení počtu.
	 * @var int|Null
	 */
	private $limit = NULL;


	/**
	 * Is the object unmodifiable?
	 * @var bool
	 */
	private $frozen = FALSE;


	/**
	 * @param object|string $type
	 * @return self
	 */
	static function create($type)
	{
		return new self($type);
	}



	/**
	 * @param object|string $type
	 * @param int $limit
	 * @param int $offset
	 * @return self
	 */
	static function range($type, $limit = 40, $offset = 0)
	{
		return self::create($type)
				->limit($limit)
				->offset($offset);
	}



	/**
	 * @param object|string $type
	 * @return self
	 */
	static function first($type)
	{
		return self::create($type)
				->limit(1)
				->offset(0);
	}



	/**
	 * @param object|string $type
	 * @return Aggregations\Count
	 */
	static function count($type)
	{
		return new Aggregations\Count(self::create($type));
	}



	/**
	 * @param object|string $type
	 */
	function __construct($type)
	{
		if (empty($type)) {
			throw new InvalidArgumentException('Type object of criteria not found.');
		}

		if (is_object($type)) {
			$type = get_class($type);
		}

		$this->filter = new Filter($type);
	}




	/**
	 * @params string $name
	 *
	 * @return self
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
	 * example:
	 * $model->with('id')    // Chceme sloupecek id
	 *  ->with('*')          // Vsechny prvky, bez asociaci
	 *  ->with('title')      // Chceme title
	 *  ->with('discuss.id') // Chceme hodnoty asociace discus, ale z ni jen id
	 *  ->with('vote.*')     // Chceme vsechny hodnoty asociace vote
	 *
	 * @params string $name
	 * @params string $filter
	 *
	 * @return self
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
	 * @param mixed $expression
	 * example
	 * $criteria->where('code LIKE', $code);
	 * $criteria->where(new ExprLike('code', $code));
	 *
	 * @return self
	 */
	function where($expression)
	{
		$args = func_get_args();
		call_user_func_array([$this->filter, 'where'], $args);
		$this->updating();
		return $this;
	}



	/**
	 * Razeni.
	 *
	 * example:
	 * $model->sororderByAsct('id')
	 *  ->orderByAsc('title')
	 *  ->orderByAsc('title = "cs"')
	 *
	 * @params string $by
	 *
	 * @return self
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
	 * $model->orderByDesc('id')
	 *  ->orderByDesc('title')
	 *  ->orderByDesc('title = "cs"')
	 *
	 * @params string $by
	 *
	 * @return self
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
	 * @param  int $value Počet záznamů.
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
	 * @param  int $value Posun o.
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
	 * @return string
	 */
	function getTypeName()
	{
		return $this->filter->getTypeName();
	}



	/**
	 * @return array<mixed>
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
		return $this->filter->getWhere();
	}



	/**
	 * Seznam řazení.
	 * @return array<string, 'ASC'|'DESC'>
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



	/** freezable *****************************************************/



	/**
	 * Makes the object unmodifiable.
	 * @return self
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
