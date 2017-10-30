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
$Migration=new Basic\Migration($dbConfig);
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
	$Migration->dropAll();

## Migrar todas as tabelas
	$Migration->migrateAll();

## Esvaziar todas as tabelas
	$Migration->truncateAll();
