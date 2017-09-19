# plain2sql
:pencil: Converte arquivos de texto para tabelas SQL

## Composer
	composer require plain2sql/plain2sql
	
## Instalação
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
$Plain2SQL->migrate();
```
## Exemplo de tabela
O nome do arquivo é o nome da tabela

### tables/user
```
id
name
email
passoword
token
token_expiration
```

## Apagar todas as tabelas
	$Plain2SQL->dropAll();

## Migrar todas as tabelas
	$Pain2SQL->migrateAll();

## Esvaziar todas as tabelas
	$Plain2SQL->truncateAll();
