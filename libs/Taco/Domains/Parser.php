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
 * Převádí textový zápis na objekty.
 *
 * @author     Martin Takáč (taco@taco-beru.name)
 */
class Parser
{

	/**
	 * Označení pro sebe sama.
	 */
	const EQUALS = '@';


	/**
	 * Nastavi podminku do Criteria
	 * @param mixed
	 * @example
	 * $criteria->where('code LIKE', $code);
	 *
	 * @return ICriteria
	 */
	public static function formatWhere(array $args)
	{
		$ret = array();

		while ($expr = array_shift($args)) {
			//	Dokavad jsou to ciste vyrazy, tak je skladame,
			if ($expr instanceof IExpr) {
				$ret[] = $expr;
			}
			//	Jakmile jsou prerusene něčím jiným, zkusíme to znova od začátku.
			else {
				break;
			}
		}

		if (empty($expr) && !count($args)) {
			return $ret;
		}

		$prop = trim($expr);
		if (preg_match('~^[a-zA-Z\.\-\_]+$~', $prop, $matches)) {
			if (count($args) > 1) {
				throw new \InvalidArgumentException('Prilis mnoho parametru: ' . count($args));
			}
			$ret[] = Expr::is($matches[0], $args[0]);
		}
		else if (preg_match('~([a-zA-Z\.\-\_]+)\s?(.+)~', $prop, $matches)) {
			switch (strtoupper($matches[2])) {
				case '=':
					if (count($args) > 1) {
						throw new \InvalidArgumentException('Prilis mnoho parametru: ' . count($args));
					}
					$ret[] = Expr::is($matches[1], $args[0]);
					break;

				case '!=':
				case '<>':
					if (count($args) > 1) {
						throw new \InvalidArgumentException('Prilis mnoho parametru: ' . count($args));
					}
					$ret[] = new Expr('!=', $matches[1], $args[0]);
					break;

				case 'IN':
				case 'LIKE':
					if (count($args) > 1) {
						throw new \InvalidArgumentException('Prilis mnoho parametru: ' . count($args));
					}
					$fce = strtolower($matches[2]);
					$ret[] = Expr::$fce($matches[1], $args[0]);
					break;

				case '<':
				case '>':
				case '>=':
				case '<=':
					$ret[] = new Expr($matches[2], $matches[1], $args[0]);
					break;

				case 'NULL':
				case 'ISNULL':
					$ret[] = new ExprIsNull($matches[1]);
					break;

				case 'NOTNULL':
				case 'ISNOTNULL':
					$ret[] = new ExprIsNotNull($matches[1]);
					break;

				case 'HAS':
					$ret[] = new Expr('HAS', $matches[1], $args[0]);
					break;

				default:
					throw new \InvalidArgumentException('Unknow type operator: expr[' . $expr . '] -> args[ '. print_r($args, true) . '].');
			}
		}
		elseif($prop == self::EQUALS) {
			$ret[] = new ExprThisIsEquals($args[0]);
		}
		else {
			throw new \InvalidArgumentException($prop);
		}

		return $ret;
	}


}
