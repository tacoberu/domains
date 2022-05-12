<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;


/**
 * $f = (new Filter('Article'))
 *       ->where('active', True)
 *       ->where('name LIKE', 'SciFi%');
 *
 * @author Martin Takáč <martin@takac.name>
 */
class Filter implements Filterable
{

	/**
	 * Typ požadovaného prvku.
	 * @var string
	 */
	private $typeName;


	/**
	 * Podmínka je v podobě stromu. Kořenem bývá defaultně AND.
	 * @var Cond|Null
	 */
	private $condition = Null;


	/**
	 * Is the object unmodifiable?
	 * @var bool
	 */
	private $frozen = FALSE;



	/**
	 * @param string|object $type
	 */
	function __construct($type, Cond $cond = null)
	{
		if (empty($type)) {
			throw new InvalidArgumentException('Type object of filter not found.');
		}

		if (is_object($type)) {
			$type = get_class($type);
		}

		$this->typeName = $type;
		$this->condition = $cond;
	}



	/**
	 * @param mixed $expresion
	 * example
	 *   $criteria->where('code', $code);
	 *   $criteria->where('code =', $code);
	 *   $criteria->where('code LIKE', $code);
	 *   $criteria->where('code IN', 1, 2, 3);
	 *
	 * @return self
	 */
	function where($expresion)
	{
		$args = func_get_args();
		$acum = array();

		// Začátek může obsahovat vysloveně instance. Ty nebude parsovat. Jako výraz následovaný argumenty může být až jako poslední.
		while ($expr = array_shift($args)) {
			if ($expr instanceof IExpr) {
				$acum[] = $expr;
			}
			else {
				$acum[] = Parser::parseWhere($expr, $args);
				break;
			}
		}

		// Naplníme filtr
		foreach ($acum as $expr) {
			if (empty($this->condition)) {
				if ($expr instanceof Cond) {
					$this->condition = $expr;
					continue;
				}
				else {
					$this->condition = new CondAnd([]);
				}
			}
			$this->condition->add($expr);
		}

		$this->updating();
		return $this;
	}



	/**
	 * Seznam sloupců.
	 * @return string
	 */
	function getTypeName()
	{
		return $this->typeName;
	}



	/**
	 * Seznam omezení filtrace.
	 * @return Cond
	 */
	function getWhere()
	{
		return $this->condition;
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
