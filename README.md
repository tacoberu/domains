Criteria
========

[![Build Status](https://travis-ci.org/tacoberu/domains.svg?branch=master)](https://travis-ci.org/tacoberu/domains)

PHP DSL for quering between app and business layers.

Inspirated by SQL, LINQ, or Dibi.
Used for quering to database, file, filesystem, xml, anything.

- with - o jaká data máme zájem.
- where - podmínka, který musí být splněna na požadovaných datech.
- limit - kolik toho chceme,
- offset - posunutí okna
- orderByDesc, orderByAsc - seřazení výsledků.


Example
-------

Příklad dotazu na soubor - dvacet souborů menších jak 400B včetně obsahu
a informace o souboru:


	Criteria::range('IO/File', 20)
		->with('fileInfo')
		->with('content')
		->where('fileInfo.size <', 400);


	Criteria::range('Article', 20)
		->with('fileInfo')
		->with('content')
		->where('fileInfo.size <', 400)
		->orderByDesc('fileInfo.size');


	Criteria::count('Article')
		->with('fileInfo')
		->with('content')
		->where('fileInfo.size <', 400);


	Criteria::first('Article')
		->with('fileInfo')
		->with('content')
		->where('fileInfo.size <', 400);



	$f = (new Filter('Album'))
		->where('created <', new DateTime())
		->where('author.name LIKE', 'Sinead%');
	$f = Parser::parseFilter('Album WHERE created < ? AND name LIKE ?', new DateTime(), 'Sinead%');
	$repository->range($f);

	function range(Filterable $filter = Null, Sortable $sort = Null, $limit = Null, $offset = Null)
	{

		(new Validation($schema))->assertFilter($filter);
		$con = $this->dibi;

		foreach ($filter as $name as $args) {
			switch($name) {
				'author':
					$con->leftJoin(...);
					break;
			}
		}
		foreach ($filter as $name as $args) {
			if ($args instanceof Cond) {
				...
			}
			else {
				switch($name) {
					'author.name':
						$con->where('b.name = ?', $args);
						break;
					default:
						$con->where("a.{$name} = ?", $args);
				}
			}
		}
	}


Pseudocode:

	-- Posledních pět článků konkrétního uživatele.
	article[user.id = 42|limit 5]{*}

	-- Všechny kočky, mající více jak 2 černé koťata. Přičemž nás zajímá celkový počet koček, jméno kočky, a barva kočky.
	cats[children[color = black]{count} > 2]{children.count, name, color}

	-- Patnáct uživatelů z mariánek. U každého chceme krom základních atributů také ulici a město z adresy.
	-- Dále chceme pro každého pět (povolených) posledních článků. Z každého článku chceme jen jméno a popis.
	users[address.city = "marianky"|limit 15]{*, address{street, city}, article[enabled = true|sort-desc-by date|limit 5]{name, description}}


PHP Code:

	// Posledních pět článků konkrétního uživatele.
	Criteria::range('article', [
		Condition::is('user.id', 42),
		Range::limit(5)
	],
	['*']);

	// Všechny kočky, mající více jak 2 černé koťata. Přičemž nás zajímá celkový počet koček, jméno kočky, a barva kočky.
	// cats[children[color = black]{count} > 2]{children.count, name, color}
	// cats[children{count} > 2]{children.count, name, color}
	// [children[color = black]{count} > 2]
	// [children{count} > 2]
	Criteria::range('cat', [
		Condition::largerThan(
			Criteria::range('cat', []
		, 2),
	],
	['children.count', 'name', 'color']);


	// AND
	Criteria::range('article', [
		Condition::like('user.name', 'Pepa'),
		Condition::like('user.name', 'Pavel'),
	],
	['*']);


	// OR
	Criteria::range('article', new ConditionOr([
		Condition::like('user.name', 'Pepa'),
		Condition::like('user.name', 'Pavel'),
	]),
	['*']);


Selector
--------
Helper for selectin attribs in criteriam


Validator
---------
Slouží k validaci criteria. Omezujeme tím prvky, nebo podmínky které může klient uvádět.


Formater
--------
Překládá kriterium na jiný formát. Například pro dibi, pro Doctrine a podobně.


TODO
----

- refactoring názvů
- zanořený dotazy
- print, deserialize tak aby to vrátilo hodnotu
- oprava pojmenovaných argumnentů
- validace na elementy
