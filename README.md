# migration
:pencil: Sistema básico de migration

## Composer
	composer require basic/migration

## Instalação
```
<?php
require 'vendor/autoload.php';
$dbConfig=[
	'db_server'=>'localhost',
	'db_name'=>'test',
	'db_user'=>'root',
	'db_password'=>''
];
$BasicMigration=new Basic\Migration($dbConfig);
$BasicMigration->migrate();
```
## Exemplo de tabela
O nome do arquivo de texto é o nome da tabela. As tabelas devem ficar armazenadas no diretório /table um nivel acima do diretório /vendor.

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
	$BasicMigration->dropAll();

## Migrar todas as tabelas
	$BasicMigration->migrateAll();

## Esvaziar todas as tabelas
	$BasicMigration->truncateAll();
