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
 * Výraz
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
interface IExpr
{
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
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class Expr implements IExpr
{


	/**
	 *	Levá strana výrazu.
	 */
	protected $lft;



	/**
	 *	Pravá strana výrazu.
	 */
	protected $rgt;



	/**
	 *	Operand.
	 */
	protected $op;



	/**
	 *	@param Objekt.
	 */
	public function __construct($op, $lft, $rgt)
	{
		$this->op = strtoupper($op);
		$this->lft = $lft;
		$this->rgt = $rgt;
	}



	/**
	 * Typ výrazu.
	 */
	public function type()
	{
		return $this->op;
	}



	/**
	 * Oprátor
	 */
	public function op()
	{
		return $this->op;
	}



	/**
	 * Název property.
	 */
	public function prop()
	{
		return $this->lft;
	}



	/**
	 * Název property.
	 */
	public function id()
	{
		return $this->prop();
	}



	/**
	 * Hodnota
	 */
	public function value()
	{
		return $this->rgt;
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	public function __toString()
	{
		return "{$this->prop()} {$this->type()} {$this->value()}";
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	public static function is($prop, $value)
	{
		return new ExprIs($prop, $value);
	}



	/**
	 * Podmínka jsoucnosti: id IN(1,3,5)
	 */
	public static function in($prop, array $values)
	{
		return new ExprIn($prop, $values);
	}



	/**
	 * Podmínka jsoucnosti: title LIKE 'ahoj'
	 */
	public static function like($prop, $text)
	{
		return new ExprLike($prop, $text);
	}



}



/**
 * Rovnost.
 */
class ExprIs extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft, $rgt)
	{
		parent::__construct('=', $lft, $rgt);
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
	 *	@param Objekt.
	 */
	public function __construct($rgt)
	{
		parent::__construct(self::MASK, Null, $rgt);
	}


}



/**
 * Nerovnost
 */
class ExprIsNot extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft, $rgt)
	{
		parent::__construct('!=', $lft, $rgt);
	}


}




/**
 * Negace
 */
class ExprNot extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft)
	{
		parent::__construct('NOT', Null, $lft);
	}


}




/**
 * Rovnost na NULL.
 */
class ExprIsNull extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft)
	{
		parent::__construct('ISNULL', $lft, Null);
	}


}




/**
 * Nerovnost na NULL.
 */
class ExprIsNotNull extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft)
	{
		parent::__construct('ISNOTNULL', $lft, Null);
	}


}




/**
 * Obsaženost.
 */
class ExprIn extends Expr
{

	/**
	 *	@param Objekt.
	 */
	public function __construct($lft, array $rgt)
	{
		parent::__construct('in', $lft, $rgt);
	}



	/**
	 * Hodnota
	 */
	public function value()
	{
		return (array)parent::value();
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	public function __toString()
	{
		$values = implode(', ', $this->value());
		return "{$this->prop()} {$this->type()} ({$values})";
	}


}





/**
 * Rovnost.
 */
class ExprLike extends Expr
{


	/**
	 *	@param Objekt.
	 */
	public function __construct($lft, $rgt)
	{
		parent::__construct('like', $lft, $rgt);
	}



	/**
	 * Podmínka jsoucnosti: id = 1
	 */
	public function __toString()
	{
		return "{$this->prop()} {$this->type()} '{$this->value()}'";
	}



}



