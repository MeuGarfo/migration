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
password
token
token_expiration
```

## Apagar todas as tabelas
	$BasicMigration->drop_all();

## Migrar todas as tabelas
	$BasicMigration->migrate_all();

## Esvaziar todas as tabelas
	$BasicMigration->truncate_all();
