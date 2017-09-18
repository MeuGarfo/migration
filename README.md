# plain2sql
:pencil: Plain text to SQL

## composer
	composer require aicoutos/plain2sql
	
## use
```
$options=[
	'dir'=>'/your-dir',
	'mysql'=>[
		'db'
	]
];
$Plain2SQL=new Plain2SQL\Plain2SQL($options);
```