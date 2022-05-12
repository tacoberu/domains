<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;


/**
 * Výraz
 *
 * @author Martin Takáč <martin@takac.name>
 */
interface IExpr
{

	/**
	 * Operátor, název výrazu.
	 * @return string
	 */
	function type();

}



/**
 * Výraz
 *
 * a is b
 * a = b
 * a != b
 * a < b
 * a > b
 * a <= b
 * a >= b
 *
 * @author Martin Takáč <martin@takac.name>
 */
abstract class Expr implements IExpr
{

	/**
	 * Levá strana výrazu.
	 * @var string
	 */
	private $prop;



	/**
	 * Pravá strana výrazu.
	 * @var mixed
	 */
	private $value;


	/**
	 * @param string|null $prop
	 * @param mixed $value
	 */
	function __construct($prop, $value)
	{
		$this->prop = $prop;
		$this->value = $value;
	}



	/**
	 * Name of property.
	 * @return string
	 */
	function prop()
	{
		return $this->prop;
	}



	/**
	 * @return mixed
	 */
	function value()
	{
		return $this->value;
	}



	/**
	 * @return string
	 */
	function __toString()
	{
		$val = self::formatValue($this->value());
		return "{$this->prop()} {$this->type()} {$val}";
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 * @param string $prop
	 * @param mixed $value
	 */
	static function is($prop, $value)
	{
		return new ExprIs($prop, $value);
	}



	/**
	 * Podmínka jsoucnosti: id IN(1,3,5)
	 * @param string $prop
	 * @param array<mixed> $values
	 */
	static function in($prop, array $values)
	{
		return new ExprIn($prop, $values);
	}



	/**
	 * Podmínka jsoucnosti: id NOTIN(1,3,5)
	 * @param string $prop
	 * @param array<mixed> $values
	 */
	static function notin($prop, array $values)
	{
		return new ExprNotIn($prop, $values);
	}



	/**
	 * Podmínka jsoucnosti: title LIKE 'ahoj'
	 * @param string $prop
	 * @param mixed $text
	 */
	static function like($prop, $text)
	{
		return new ExprLike($prop, $text);
	}



	/**
	 * Podmínka jsoucnosti: title NOTLIKE 'ahoj'
	 * @param string $prop
	 * @param mixed $text
	 */
	static function notlike($prop, $text)
	{
		return new ExprNotLike($prop, $text);
	}



	/**
	 * @param mixed $val
	 * @return string
	 */
	private static function formatValue($val)
	{
		if ($val === Null) {
			return 'Null';
		}
		if ($val === True) {
			return 'True';
		}
		if ($val === False) {
			return 'False';
		}
		if ($val === '') {
			return '""';
		}
		if (is_string($val)) {
			$val = var_export($val, True);
			$val = trim($val, '\'');
			return "\"{$val}\"";
		}
		return var_export($val, True);
	}

}



/**
 * Rovnost, nebo ekvivalence.
 * a = 3
 */
class ExprIs extends Expr
{

	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return '=';
	}

}



/**
 * equals b
 * equals(b)
 */
class ExprThisIsEquals extends Expr
{

	const MASK = '@';


	/**
	 * @param mixed $rgt
	 */
	function __construct($rgt)
	{
		parent::__construct(Null, $rgt);
	}


	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return self::MASK;
	}

}



/**
 * Nerovnost
 */
class ExprIsNot extends Expr
{

	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return '!=';
	}

}



/**
 * Větší než.
 */
class ExprGreaterThan extends Expr
{

	/**
	 * @return string
	 */
	function type()
	{
		return '>';
	}

}



/**
 * Menší než.
 */
class ExprLessThan extends Expr
{

	/**
	 * @return string
	 */
	function type()
	{
		return '<';
	}

}



/**
 * Větší než nebo rovno.
 */
class ExprGreaterOrEqualThan extends Expr
{

	/**
	 * @return string
	 */
	function type()
	{
		return '>=';
	}

}



/**
 * Menší než nebo rovno.
 */
class ExprLessOrEqualThan extends Expr
{

	/**
	 * @return string
	 */
	function type()
	{
		return '<=';
	}

}



/**
 * Rovnost na NULL.
 */
class ExprIsNull extends Expr
{


	/**
	 * @param string $lft
	 */
	function __construct($lft)
	{
		parent::__construct($lft, Null);
	}


	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return 'ISNULL';
	}

}



/**
 * Nerovnost na NULL.
 */
class ExprIsNotNull extends Expr
{


	/**
	 * @param string $lft
	 */
	function __construct($lft)
	{
		parent::__construct($lft, Null);
	}


	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return 'ISNOTNULL';
	}

}



/**
 * Obsaženost.
 */
class ExprIn extends Expr
{

	/**
	 * @param string $lft
	 * @param array<mixed> $rgt
	 */
	function __construct($lft, array $rgt)
	{
		parent::__construct($lft, $rgt);
	}


	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return 'IN';
	}



	/**
	 * @return array<mixed>
	 */
	function value()
	{
		return (array)parent::value();
	}



	function __toString()
	{
		$values = implode(', ', $this->value());
		return "{$this->prop()} {$this->type()} ({$values})";
	}

}



/**
 * Nevýskyt v množině.
 */
class ExprNotIn extends Expr
{

	/**
	 * @param string $lft
	 * @param array<mixed> $rgt
	 */
	function __construct($lft, array $rgt)
	{
		parent::__construct($lft, $rgt);
	}



	/**
	 * @return string
	 */
	function type()
	{
		return 'NOTIN';
	}



	/**
	 * @return array<mixed>
	 */
	function value()
	{
		return (array)parent::value();
	}



	function __toString()
	{
		$values = implode(', ', $this->value());
		return "{$this->prop()} {$this->type()} ({$values})";
	}

}



/**
 * Odpovídá masce.
 */
class ExprLike extends Expr
{


	/**
	 * @return string
	 */
	function type()
	{
		return 'LIKE';
	}



	function __toString()
	{
		return "{$this->prop()} {$this->type()} '{$this->value()}'";
	}

}



/**
 * Neodpovídá masce.
 */
class ExprNotLike extends Expr
{


	/**
	 * @return string
	 */
	function type()
	{
		return 'NOTLIKE';
	}



	function __toString()
	{
		return "{$this->prop()} {$this->type()} '{$this->value()}'";
	}

}



/**
 * Negace
 */
class ExprNot extends Expr
{

	/**
	 * @param mixed $lft
	 */
	function __construct($lft)
	{
		parent::__construct(Null, $lft);
	}



	/**
	 * Operátor
	 * @return string
	 */
	function type()
	{
		return 'NOT';
	}



	/**
	 * @return string
	 */
	function __toString()
	{
		return "{$this->type()} {$this->value()}";
	}

}



class ExprInSubquery extends Expr
{

	/**
	 * @return string
	 */
	function type()
	{
		return 'in';
	}

}
