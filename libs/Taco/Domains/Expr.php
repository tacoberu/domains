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
	 */
	private $prop;



	/**
	 * Pravá strana výrazu.
	 */
	private $value;


	/**
	 * @param string
	 * @param mixin
	 * @param mixin
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
	 * @return mixin
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
		return "{$this->prop()} {$this->type()} {$this->value()}";
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	static function is($prop, $value)
	{
		return new ExprIs($prop, $value);
	}



	/**
	 * Podmínka jsoucnosti: id IN(1,3,5)
	 */
	static function in($prop, array $values)
	{
		return new ExprIn($prop, $values);
	}



	/**
	 * Podmínka jsoucnosti: id NOTIN(1,3,5)
	 */
	static function notin($prop, array $values)
	{
		return new ExprNotIn($prop, $values);
	}



	/**
	 * Podmínka jsoucnosti: title LIKE 'ahoj'
	 */
	static function like($prop, $text)
	{
		return new ExprLike($prop, $text);
	}



	/**
	 * Podmínka jsoucnosti: title NOTLIKE 'ahoj'
	 */
	static function notlike($prop, $text)
	{
		return new ExprNotLike($prop, $text);
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
	 * @param Objekt.
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
	 * @param Objekt.
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
	 * @param Objekt.
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
	 * @param Objekt.
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
	 * Hodnota
	 */
	function value()
	{
		return (array)parent::value();
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
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
 * Odpovídá pasce.
 */
class ExprLike extends Expr
{


	/**
	 * @param Objekt.
	 */
	function __construct($lft, $rgt)
	{
		parent::__construct($lft, $rgt);
	}



	/**
	 * @return string
	 */
	function type()
	{
		return 'LIKE';
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
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
	 * @param Objekt.
	 */
	function __construct($lft, $rgt)
	{
		parent::__construct($lft, $rgt);
	}



	/**
	 * @return string
	 */
	function type()
	{
		return 'NOTLIKE';
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
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
	 * @param Objekt.
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
