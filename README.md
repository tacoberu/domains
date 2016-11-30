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



Selector
--------
Helper for selectin attribs in criteriam


Validator
---------
Slouží k validaci criteria. Omezujeme tím prvky, nebo podmínky které může clinent uvádět.


Formater
--------
Překládá kriterium na jiný formát. Například pro dibi, pro Doctrine a podobně.
