<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;


/**
 * Převádí textový zápis na objekty.
 *
 * @author Martin Takáč <martin@takac.name>
 */
class Parser
{

	/**
	 * Vytvoří instanci třídy Filter na základě textu. Umí i nabindovat volitelné argumenty.
	 *
	 * @param string $expr Like '[id = ?]'
	 * @param array $args Values for ? in $expr.
	 * @return Filter|Null
	 */
	static function parseFilter($expr, array $args = [])
	{
		// where - v případě filter je to jednodužší, páč to nic dalšího neumí.
		if ($i = strpos($expr, '[')) {
			$whereexpr = substr(trim(substr($expr, 7)), 1, -1);
			return new Filter(substr($expr, 0, 7), self::parseWhere($whereexpr));
		}
		return Null;
	}



	/**
	 * Nastavi podminku do Criteria
	 * @param string $expr
	 * @param array<mixed> $args
	 * example
	 * 	$criteria->parseWhere('code LIKE', ['code']);
	 * 	$criteria->parseWhere('id =', [5]);
	 * 	$criteria->parseWhere('id = ?', [5]);
	 *
	 * @return IExpr
	 */
	static function parseWhere($expr, array $args = [])
	{
		if (empty($expr)) {
			throw new InvalidArgumentException("Empty where-conds.");
		}
		$tok = new Tokenizer($args);
		if ($expr[0] === '(') {
			list($expr, $tail) = $tok->parseConds($expr);
		}
		else {
			list($expr, $tail) = $tok->parseExpr($expr);
		}

		if ($tail) {
			throw new InvalidArgumentException("Unsuported where-conds: `{$tail}'.");
		}

		return $expr;
	}

}
