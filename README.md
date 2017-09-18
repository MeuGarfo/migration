# plain2sql
:pencil: Plain text to SQL

## composer
	composer require plain2sql/plain2sql
	
## table
```
id
name
email
```
	
## use
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
$Plain2SQL=new Plain2SQL\Plain2SQL($dir,$db);
```