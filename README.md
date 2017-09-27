# migration
:pencil: Sistema básico de migration

## Composer
	composer require basic/migration

## Instalação
```
<?php
require 'vendor/autoload.php';
$dbConfig=[
	'server'=>'localhost',
	'name'=>'test',
	'user'=>'root',
	'password'=>''
];
$BasicMigration=new Basic\Migration($dbConfig);
$BasicMigration->migrate();
```
## Exemplo de tabela
O nome do arquivo de texto é o nome da tabela. As tabelas devem ficar armazenadas no diretório /table um nivel acima do /vendor.

### table/user
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
