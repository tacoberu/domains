<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use InvalidArgumentException;


/**
 * @author Martin Takáč <martin@takac.name>
 */
class Tokenizer
{

	/**
	 * @var array
	 */
	private $binds;

	/**
	 * @var int
	 */
	private $index = 0;


	function __construct(array $binds = array())
	{
		$this->binds = $binds;
	}



	/**
	 * Expression is composing from modificator, object, operator and arguments.
	 * @param string $source Like "a = 1", "NOT b = 1", "b IN (1,2,3)"
	 * @return array<Expr|string> LIke [Expr, string]
	 */
	function parseExpr($source)
	{
		if (empty($source)) {
			throw new InvalidArgumentException("Empty expression.");
		}
		switch (true) {
			// "NOT abc = 42", "NOT abc = ?"
			case preg_match('~^(NOT\s+)?(\w+)\s+([=!<>]+)\s+(.*)~s', $source, $matches):
				list($arg, $tail) = $this->parseArgs($matches[4]);
				$expr = self::buildExpr($matches[1], $matches[2], $matches[3], $arg);
				return array($expr, ltrim($tail));
			// "NOT abc =", [42]
			case preg_match('~^(NOT\s+)?(\w+)\s+([=!<>]+\s*)$~s', $source, $matches):
				list($arg, $_) = $this->parseArgs('?');
				$expr = self::buildExpr($matches[1], $matches[2], $matches[3], $arg);
				return array($expr, '');
			// IN, NOTIN
			case preg_match('~^(\w+)\s+(NOTIN|IN)\s*(.*)?~s', $source, $matches):
				if (strlen($matches[3])) {
					list($arg, $tail) = $this->parseArgs($matches[3]);
				}
				else {
					list($arg, $_) = $this->parseArgs('?');
					$tail = '';
				}
				$expr = self::buildExpr(null, $matches[1], strtoupper($matches[2]), $arg);
				return array($expr, ltrim($tail));
			// LIKE, NOTLIKE
			case preg_match('~^(\w+)\s+(LIKE|NOTLIKE)\s*(.*)?~s', $source, $matches):
				if (strlen($matches[3])) {
					list($arg, $tail) = $this->parseArgs($matches[3]);
				}
				else {
					list($arg, $_) = $this->parseArgs('?');
					$tail = '';
				}
				$expr = self::buildExpr(null, $matches[1], $matches[2], $arg);
				return array($expr, ltrim($tail));
			// ISNULL, ISNOTNULL
			case preg_match('~^(\w+)\s+(ISNULL|NULL|ISNOTNULL)(.*)~s', $source, $matches):
				$expr = self::buildExpr(null, $matches[1], $matches[2], null);
				return array($expr, ltrim($matches[3]));
			// "NOT abc", [42]
			case preg_match('~^(NOT\s+)?(\w+)\s*(.*)$~s', $source, $matches):
				list($arg, $_) = $this->parseArgs('?');
				$expr = self::buildExpr($matches[1], $matches[2], '=', $arg);
				return array($expr, '');
			default:
				throw new InvalidArgumentException("Unsuported expression: `{$source}'.");
		}
	}



	/**
	 * Composition with bracket: "(a AND b OR (c AND e))"
	 * @param string $source
	 * @return array<Cond|string> Konkrétně [Cond, string]
	 */
	function parseConds($source)
	{
		if (empty($source)) {
			throw new InvalidArgumentException("Empty expression.");
		}
		$res = array();
		$op = null;
		while(true) {
			switch (true) {
				case $source[0] === '(':
					list($expr, $tail) = $this->parseExpr(substr($source, 1));
					$res[] = $expr;
					$source = ltrim($tail);
					break;
				case $source[0] === ')':
					return array(
						($op === 'OR') ? new CondOr($res) : new CondAnd($res),
						ltrim(substr($source, 1))
					);
				case substr($source, 0, 3) === 'AND':
					if ($op == 'OR') {
						throw new InvalidArgumentException("Unconsistenci operators. Must be only one type of op (OR or AND) with in one brackets pair.");
					}
					$op = 'AND';
					$source = ltrim(substr($source, 3));
					if ($source[0] === '(') {
						list($expr, $tail) = $this->parseConds($source);
						$res[] = $expr;
						$source = (string)$tail;
					}
					else {
						$source = '(' . $source;
					}
					break;
				case substr($source, 0, 2) === 'OR':
					if ($op == 'AND') {
						throw new InvalidArgumentException("Unconsistenci operators. Must be only one type of op (OR or AND) with in one brackets pair.");
					}
					$op = 'OR';
					$source = ltrim(substr($source, 2));
					if ($source[0] === '(') {
						list($expr, $tail) = $this->parseConds($source);
						$res[] = $expr;
						$source = (string)$tail;
					}
					else {
						$source = '(' . $source;
					}
					break;
				default:
					throw new InvalidArgumentException("Unsuported conditions: `{$source}'.");
			}
		}
	}



	private function parseArgs($source)
	{
		switch (true) {
			// bind to value
			case $source[0] == '?':
				$xs = array_values($this->binds);
				if ( ! array_key_exists($this->index, $xs)) {
					throw new InvalidArgumentException("Unexcepted bound of arguments. Require {$this->index}s index.");
				}
				$x = $xs[$this->index];
				$this->index++;
				return array(self::buildArgs('!bind', $x), substr($source, 1));
			// bind to value (named)
			case $source[0] == '%':
				$i = strpos($source, '}');
				$key = substr($source, 2, $i-2);
				$x = $this->binds[$key];
				return array(self::buildArgs('!bind', $x), substr($source, $i + 1));
			case $source[0] == '(':
				return self::parseTuple(substr(ltrim($source), 1), ')');
			case $source[0] == '[':
				return self::parseTuple(substr(ltrim($source), 1), ']');
			// string
			case $source[0] == '"':
				return self::parseText(substr($source, 1), '"');
			// numeric
			case preg_match('~^(\d+)(.*)~s', $source, $matches):
				return array(self::buildArgs('numeric', $matches[1]), $matches[2]);
			// bool
			case strtolower(substr($source, 0, 4)) == 'true':
				return array(self::buildArgs('bool', true), substr($source, 4));
			case strtolower(substr($source, 0, 5)) == 'false':
				return array(self::buildArgs('bool', false), substr($source, 5));
			default:
				throw new InvalidArgumentException("Unsuported args: `{$source}'.");
		}
	}



	private static function parseArgs2($source)
	{
		switch (true) {
			// string
			case $source[0] == '"':
				return self::parseText(substr($source, 1), '"');
			// numeric
			case preg_match('~^(\d+)(.*)~s', $source, $matches):
				return array(self::buildArgs('numeric', $matches[1]), $matches[2]);
			// bool
			case strtolower(substr($source, 0, 4)) == 'true':
				return array(self::buildArgs('bool', true), substr($source, 4));
			case strtolower(substr($source, 0, 5)) == 'false':
				return array(self::buildArgs('bool', false), substr($source, 5));
			default:
				throw new InvalidArgumentException("Unsuported args: `{$source}'.");
		}
	}



	private static function parseText($str, $quote)
	{
		// Prázdný řetězec.
		if ($str[0] == $quote) {
			//~ return '';
			return array(
				self::buildArgs('string', ''),
				substr($str, 1) ?: ''
			);
		}
		// @TODO Escapování
		// ...
		$i = strpos($str, $quote);
		return array(
			self::buildArgs('string', substr($str, 0, $i)),
			substr($str, $i + 1) ?: ''
		);
	}



	/**
	 * Výraz v závorkách oddělený čárkama.
	 */
	private static function parseTuple($str, $bracket, $delimiter = ',')
	{
		// Prázdný řetězec.
		if ($str[0] == $bracket) {
			return '';
		}
		// @TODO Escapování
		// @TODO Zanoření
		// ...
		$i = strpos($str, $bracket);
		return array(
			self::buildArgs('tuple', array_map(function($x) {
				return self::castValue(self::parseArgs2(trim($x))[0]);
			}, explode($delimiter, substr($str, 0, $i)))),
			substr($str, $i + 1) ?: ''
		);
	}



	private static function buildExpr($mod, $col, $op, $arg)
	{
		switch ($op) {
			case '=':
				return new ExprIs($col, self::castValue($arg));
			case '!=':
			case '<>':
				return new ExprIsNot($col, self::castValue($arg));
			case '>':
				return new ExprGreaterThan($col, self::castValue($arg));
			case '<':
				return new ExprLessThan($col, self::castValue($arg));
			case '>=':
				return new ExprGreaterOrEqualThan($col, self::castValue($arg));
			case '<=':
				return new ExprLessOrEqualThan($col, self::castValue($arg));
			case 'IN':
				return new ExprIn($col, self::castValue($arg));
			case 'NOTIN':
				return new ExprNotIn($col, self::castValue($arg));
			case 'LIKE':
				return new ExprLike($col, self::castValue($arg));
			case 'NOTLIKE':
				return new ExprNotLike($col, self::castValue($arg));
			case 'ISNULL':
			case 'NULL':
				return new ExprIsNull($col);
			case 'ISNOTNULL':
				return new ExprIsNotNull($col);
			default:
				throw new InvalidArgumentException("Unsuported expr operator: `{$op}'.");
		}
	}



	private static function buildArgs($type, $val)
	{
		return array('type' => $type, 'val' => $val);
	}



	private static function castValue($expr)
	{
		switch ($expr['type']) {
			case '!bind':
				return $expr['val'];
			case 'numeric':
				return (int) $expr['val'];
			case 'string':
				return (string) $expr['val'];
			case 'bool':
				return (bool) $expr['val'];
			case 'tuple':
				return (array) $expr['val'];
			default:
				throw new InvalidArgumentException("Unsuported typecast: `{$expr['type']}'.");
		}
	}

}
