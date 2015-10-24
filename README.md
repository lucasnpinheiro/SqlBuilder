# JBZoo SqlBuilder  [![Build Status](https://travis-ci.org/JBZoo/SqlBuilder.svg?branch=master)](https://travis-ci.org/JBZoo/SqlBuilder)   [![Coverage Status](https://coveralls.io/repos/JBZoo/SqlBuilder/badge.svg?branch=master&service=github)](https://coveralls.io/github/JBZoo/SqlBuilder?branch=master)

#### PHP library description

[![License](https://poser.pugx.org/JBZoo/SqlBuilder/license)](https://packagist.org/packages/JBZoo/SqlBuilder)
[![Latest Stable Version](https://poser.pugx.org/JBZoo/SqlBuilder/v/stable)](https://packagist.org/packages/JBZoo/SqlBuilder) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/JBZoo/SqlBuilder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/JBZoo/SqlBuilder/?branch=master)

### Init builder
```php
use JBZoo\SqlBuilder\SqlBuilder;

// composer autoload.php
require_once './vendor/autoload.php';

// register database database type, connection and table prefix (if exists)
$connection = mysqli_connect('127.0.0.1', 'root', '', 'sqlbuilder', '3306');
SqlBuilder::set('mysqli', $connection, 'prefix_');
```

### SELECT queries
```php
use JBZoo\SqlBuilder\Query\Select;
echo (new Select('table', 'tTable')); // or __toString() method
```
```sql
SELECT * FROM `table` AS `tTable`
```

### Simple SELECT query
```php
$select = (new Select('#__items', 'tItem'))
    ->select('tItem.item_id', 'id')
    ->leftJoin(['#__categories', 'tCategory'], 'tCategory.id = tItem.category_id')
    ->where(['tItem.name', ' = ?s'], "O'Realy")
    ->where('tItem.name IN ?a', ['42', '43', '44'])
    ->group('tItem.author')
    ->order('tItem', 'desc')
    ->limit(5, 10);
```
```sql
SELECT 
    `tItem`.`item_id` 
FROM 
    `prefix_items` AS `tItem` 
    LEFT JOIN `prefix_categories` AS `tCategory` ON (tCategory.id = tItem.category_id) 
WHERE 
    `tItem`.`name` = 'O\'Realy' 
    AND tItem.name IN ('42', '43', '44') 
GROUP BY 
    `tItem`.`author` 
ORDER BY 
    `tItem` DESC 
LIMIT 
    10, 5
```

### Flexible and safe WHERE conditions
```php
$select = (new Select(['items', 'tAlias']))
    ->where("prop = 'O\\'Realy'") // old school
    ->where('prop = ?s', "O'Realy") // escape string
    ->where(['prop', '= ?s'], "O'Realy", 'or') // quote field
    ->whereOR(['tAlias.prop', '=', '?s'], "O'Realy"); // more syntax suger :)
```
```sql
SELECT * FROM `items`AS `tAlias`
WHERE 
    prop = 'O\'Realy' 
    AND prop = 'O\'Realy' 
    OR `prop` = 'O\'Realy' 
    OR `tAlias`.`prop` = 'O\'Realy'
```
#### Escaping other variable types
```php
$select = (new Select(['items', 'tAlias']))
    ->where('prop <> ?e', 'tAlias.col') // entities
    ->where('prop < ?i', ' -10,56 ') // integer
    ->where('prop > ?f', ' -10,56 ') // float
    ->where('prop = ?b', 1) // bool TRUE
    ->where('prop <> ?b', 0) // and FALSE
    ->where('prop ?n', 0) // IS [NOT] NULL
    ->where('prop IN ?a', [null, '1', "'qwerty'"]); // array
```
```sql
SELECT * FROM `items` AS `tAlias`
WHERE 
    prop <> `tAlias`.`col` 
    AND prop < -10 
    AND prop > -10.56 
    AND prop = TRUE 
    AND prop <> FALSE 
    AND prop IS NULL 
    AND prop IN (NULL, '1', '\'qwerty\'')
```
#### WHERE groups
```php
$select = (new Select(['items', 'tAlias']))
    ->whereGroup([
        ['tAlias.prop > ?i', 42],
        [['tAlias.prop', '< ?i'], 4242],
    ])
    ->whereGroup([
        ['prop NOT IN ?a', [0, 1, null, 'Some string']],
        ['prop ?n', 0],
    ], 'OR');
```
```sql
SELECT * FROM `items` AS `tAlias` 
WHERE 
    (
        tAlias.prop > 42 
        AND `tAlias`.`prop` < 4242
    ) 
    OR (
        prop NOT IN ('0', '1', NULL, 'Some string') 
        AND prop IS NULL
    )
```

### Other queries
  * Delete
  * Insert
  * Replace
  * Union
  * Update

## License

MIT
