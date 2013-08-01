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
 *	Naformátování Criteria do Dibi meta SQL kodu.
 *
 *	@author     Martin Takáč (taco@taco-beru.name)
 */
class DibiFormater
{


	/**
	 *	Existuje klausule?
	 */
	function format(ICriteria $criteria)
	{
		$code = array();
		
		
		// -- Vygenerování dotazu.
		//	Generovani restrikce téměř samostatná. Rozhoduje se podle filter.
		//	Nevybavuji se, zda by bylo potřeba jinak. Proto je prvni.
		$where = $this->formatWhereSection($criteria);

		//	Generovani razeni podle toho, podle čeho chceme řadit, může přidávat
		//	hodnoty do všeho.
		$orderby = self::section();
#		$builder->buildOrderBy($command);

		$having = self::section();

		//	Generovani skupin ovlivňuje vše.
		$groupby = self::section();
#		$builder->buildGroupBy($command);

		//	Generovani sloupců musi vedet, zda se maji obalit do agregačních funkcí.
		$select = $this->formatSelectSection($criteria);

		//	Generovani FROM
		$from = $this->formatFromSection($criteria);

		//	Generovani JOIN podle obsažených sloupců a restrikcí přidává patřičné tabulky.
#		$builder->buildJoins($command);
		$joins = self::section();

		$limit = self::section();

		$offset = self::section();

		return self::section(
				trim(implode(' ', array(
						$select->code, 
						$from->code, 
						$joins->code, 
						$where->code, 
						$groupby->code, 
						$orderby->code, 
						$having->code, 
						$limit->code,
						$offset->code
						))),
				array_merge(
						$select->args,
						$from->args,
						$joins->args,
						$where->args,
						$groupby->args,
						$orderby->args,
						$having->args,
						$limit->args,
						$offset->args
						)
				);
	}


	/**
	 * Zpracuje FROM sekci dotazu SQL.
	 *
	 * @return string
	 */
	protected function formatSelectSection(ICriteria $criteria)
	{
		$code = array();
		$args = array();

		if (!count($criteria->getWith())) {
			$code[] = '[a].*';
		}
		else {
			foreach ($criteria->getWith() as $column) {
#				$filter = Null;
#				$name = $column;
#				if (is_object($column)) {
#					$name = $column->name;
#					$filter = $column->filter;
#				}

#				if ($tmp = $this->applySelect($name, $filter)) {
#					$command->select($tmp);
#				}
			}
		}

#		foreach ($this->columns as $column) {
#			$command->select($column);
#		}

		return self::section('SELECT ' . implode(', ', $code), $args);
	}



	/**
	 * Zpracuje FROM sekci dotazu SQL.
	 *
	 * @return string
	 */
	protected function formatFromSection(ICriteria $criteria)
	{
		return self::section('FROM [' . $this->formatSource($criteria) . '] AS [a]');
	}



	/**
	 * Zpracuje WHERE sekci dotazu SQL.
	 *
	 * @return string
	 */
	protected function formatWhereSection(ICriteria $criteria)
	{
		$cond = $criteria->getWhere();
		if (empty($cond)) {
			return self::section();
		}

		$list = $cond->expresions();
		if (empty($list)) {
			return self::section();
		}

		$code = array();
		$args = array();
		foreach ($list as $row) {
			if ($row instanceof ExprIs) {
				$code[] = '[a].[' . $row->prop() . '] ' . $row->op() . ' ' . $this->formatArgValue($row->value());
				$args[] = $row->value();
			}
			else {
				throw new \Exception('Zatím nepodporavaný výraz [' . get_class($row) . ']');
			}
		}

		return self::section('WHERE ' . implode(' AND ', $code), $args);
	}



	/**
	 * @param $value
	 * @return ...
	 */
	protected function formatArgValue($value)
	{
		if (is_array($value)) {
			return '%in';
		}
		elseif (is_bool($value)) {
			return '%b';
		}
		elseif (is_integer($value)) {
			return '%i';
		}
		elseif (is_string($value)) {
			return '%s';
		}
		else {
			throw new \InvalidArgumentException("Unknow criteria value for: [" . print_r($value, True) . '].');
		}
	}


	/**
	 * Znění tabulky, ze které čteme.
	 *
	 * @return string
	 */
	protected function formatSource(ICriteria $criteria)
	{
		$name = $criteria->getTypeName();
		if ($i = strrpos($name, '\\')) {
			$name = substr($name, $i + 1);
		}
		return strtolower($name);
	}



	/**
	 * @param 
	 * @return ...
	 */
	protected static function section($code = Null, array $args = array())
	{
		return (object) array(
				'code' => $code,
				'args' => $args,
				);
	}


}
