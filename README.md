# plain2sql
:pencil: Plain text to SQL

## composer
	composer require plain2sql/plain2sql
	
## plain text (the table name is the filename)
```
id
name
email
```
	
## config
```
<?php
require 'vendor/autoload.php';
$dir='tables';
$db=[
	'server'=>'localhost',
	'name'=>'test',
	'user'=>'root',
	'password'=>''
];
$p2s=new Plain2SQL\Plain2SQL($dir,$db);
```

## dropAll
	$p2s->dropTables();

## migrateAll
	$p2s->migrateAll();

## truncateAll
	$p2s->truncateTables();

