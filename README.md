# basicmigration
:pencil: Converte arquivos de texto para tabelas SQL

## Composer
	composer require basic/migration

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
$BasicMigration=new Basic\Migration($dir,$db);
$BasicMigration->migrate();
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
	$BasicMigration->dropAll();

## Migrar todas as tabelas
	$Pain2SQL->migrateAll();

## Esvaziar todas as tabelas
	$BasicMigration->truncateAll();
