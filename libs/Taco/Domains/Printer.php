<?php
/**
 * Copyright (c) since 2004 Martin Takáč (http://martin.takac.name)
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace Taco\Domains;

use LogicException;


/**
 * Tools pro formátování objektu do lidsky čitelné podoby.
 * @author Martin Takáč <martin@takac.name>
 */
class Printer
{

	/**
	 * @return string
	 */
	static function formatWhere(IExpr $expr = Null)
	{
		if (empty($expr)) {
			return '';
		}

		switch (True) {
			case $expr instanceof CondAnd:
				$ops = [];
				foreach ($expr->expresions() as $expr2) {
					$ops[] = self::formatWhere($expr2);
				}

				return '(' . implode(' AND ', $ops) . ')';
			case $expr instanceof CondOr:
				$ops = [];
				foreach ($expr->expresions() as $expr2) {
					$ops[] = self::formatWhere($expr2);
				}

				return '(' . implode(' OR ', $ops) . ')';
			case $expr instanceof ExprIs:
			case $expr instanceof ExprIsNot:
			case $expr instanceof ExprGreaterThan:
			case $expr instanceof ExprGreaterOrEqualThan:
			case $expr instanceof ExprLessThan:
			case $expr instanceof ExprLessOrEqualThan:
			case $expr instanceof ExprLike:
			case $expr instanceof ExprNotLike:
			case $expr instanceof ExprIn:
			case $expr instanceof ExprNotIn:
				return "{$expr->prop()} {$expr->type()} " . self::escape($expr->value());
			case $expr instanceof ExprIsNull:
			case $expr instanceof ExprIsNotNull:
				return "{$expr->prop()} {$expr->type()}";
			default:
				throw new LogicException("Unsupported `" . get_class($expr) . "' in expresion: `{$expr}'.");
		}
	}



	private static function formatLimitOffset(Criteria $criteria)
	{
		if ($criteria->getLimit() || $criteria->getOffset()) {
			return [ $criteria->getOffset(), $criteria->getLimit() ];
		}
	}



	private static function formatWiths(Criteria $criteria)
	{
		return $criteria->getWith();
	}



	private static function escape($val)
	{
		switch(True) {
			case is_bool($val):
				return $val ? 'true' : 'false';
			case is_numeric($val):
				return $val;
			case is_string($val):
				return '"' . $val . '"';
			case is_array($val):
				return '[' . implode(', ', array_map('self::escape', $val)) . ']';
			default:
				throw new LogicException("Unsupported type of value:`" . gettype($val) . "'.");
		}
		return $criteria->getWith();
	}

}
