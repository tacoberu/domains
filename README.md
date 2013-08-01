Criteria
========

PHP DSL for quering between app and business layers.


Inspirated by SQL, LINQ, or Dibi.
Used for quering to database, file, filesystem, xml, anything.

with - o jaká data máme zájem.
where - podmínka, který musí být splněna na požadovaných datech.
limit - kolik toho chceme,
offset - posunutí okna
orderByDesc, orderByAsc - seřazení výsledků.


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
Helper for selectin attribs in criteriaum





Validator
---------
Slouží k validaci criteria, když chceme omezit které klíče to má ověřovat.




Formater
--------
Překldádá criterium na jiný formát. Například pro dibi, pro Doctrine a podobně.


