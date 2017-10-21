<?php
/**
* Basic
* Micro framework em PHP
*/

namespace Basic;

use Medoo\Medoo;

/**
* Classe Migration
*/
class Migration
{
    /**
    * Instância da classe Medoo
    * @var object
    */
    public $db;
    /**
    * Configurações SQL
    * @param array $db Dados do servidor SQL
    */
    public function __construct(array $db)
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => $db['name'],
            'server' => $db['server'],
            'username' => $db['user'],
            'password' => $db['password'],
            'charset' => 'utf8',
            'port' => 3306
        ]);
    }
    /**
    * Apaga todas as tabelas
    * @return bool Retorna true ou false
    */
    public function dropAll():bool
    {
        $tables=$this->tables();
        if ($tables) {
            foreach ($tables as $table) {
                $sql="DROP TABLE $table;";
                $this->query($sql);
            }
            return true;
        } else {
            return false;
        }
    }
    /**
    * Migra todas as tabelas
    * @return bool Retorna true
    */
    public function migrateAll():bool
    {
        $dir=ROOT.'table/';
        $tablesRAW=$this->myScanDir($dir);
        $tables=null;
        foreach ($tablesRAW as $key=>$value) {
            if ($this->validColumn($value)) {
                $content=file_get_contents($dir.$value);
                $content=explode(PHP_EOL, $content);
                foreach ($content as $contentKey => $contentValue) {
                    if (!$this->validColumn($contentValue)) {
                        unset($content[$contentKey]);
                    }
                }
                $content=array_filter($content);
                $content=array_values($content);
                $tables[$value]=$content;
            }
        }
        if ($this->tables()) {
            //exclusão de tabelas
            foreach ($this->tables() as $key => $tableName) {
                if (!isset($tables[$tableName])) {
                    $this->deleteTable($tableName);
                }
            }
            //exclusão de colunas
            foreach ($this->tables() as $keyTableInDB=>$tableName) {
                //le as colunas que já existe na tabelas
                $columnsInDB=$this->columns($tableName);
                //apaga as colunas que estão sobrando
                foreach ($columnsInDB as $keyColumnsInDB=>$valueColumnInDB) {
                    if (!in_array($valueColumnInDB, $tables[$tableName])) {
                        $this->deleteColumn($tableName, $valueColumnInDB);
                    }
                }
            }
        }
        //criação de colunas
        foreach ($tables as $tableKey => $tableValues) {
            $tableName=$tableKey;
            if (!$this->tableExists($tableName)) {
                $this->createTable($tableName);
            }
            $this->createColumn($tableName, 'id');
            foreach ($tableValues as $columnName) {
                $this->createColumn($tableName, $columnName);
            }
        }
        return true;
    }
    /**
    * Apagar os dados de todas as tabelas
    * @return bool Retorna true
    */
    public function truncateAll():bool
    {
        $tables=$this->tables();
        foreach ($tables as $table) {
            $sql="TRUNCATE $table;";
            $this->query($sql);
        }
        return true;
    }
    /**
    * Verifica se a coluna existe
    * @param  string $tableName  Nome da tabela
    * @param  string $columnName Nome da coluna
    * @return bool               Retorna true ou false
    */
    public function columnExists(string $tableName, string $columnName):bool
    {
        $tableName=trim($tableName);
        $columnName=trim($columnName);
        $columns=$this->columns($tableName);
        if (@in_array($columnName, $columns)) {
            return true;
        } else {
            return false;
        }
    }
    /**
    * Lista de colunas
    * @param  string $tableName Nome da tabela
    * @return mixed             Lista de colunas
    */
    public function columns(string $tableName)
    {
        $tableName=trim($tableName);
        if (!$this->tableExists($tableName)) {
            return false;
        }
        $sql='SHOW COLUMNS FROM '.$tableName;
        $result=$this->query($sql);
        if (is_array($result)) {
            $array=null;
            foreach ($result as $key=>$value) {
                $array[]=$value['Field'];
            }
            return $array;
        } else {
            return false;
        }
    }
    /**
    * Cria coluna
    * @param  string $tableName  Nome da tabela
    * @param  string $columnName Nome da coluna
    * @return mixed              Retorna true ou false
    */
    public function createColumn(string $tableName, string $columnName)
    {
        $tableName=trim($tableName);
        $columnName=trim($columnName);
        if (!$this->columnExists($tableName, $columnName)) {
            $sql='ALTER TABLE `'.$tableName.'` ADD ';
            if ($columnName=='id') {
                $sql=$sql.'`'.$columnName.'` serial;';
            } else {
                // ALTER TABLE `user` ADD `email` LONGTEXT NOT NULL ;
                $sql=$sql.'`'.$columnName.'` LONGTEXT;';
            }
            if (!$this->columnExists($tableName, $columnName)) {
                return $this->query($sql);
            }
        }
    }
    /**
    * Criar tabela
    * @param  string $tableName Nome da tabela
    * @return bool              Retorna true ou false
    */
    public function createTable(string $tableName):bool
    {
        $tableName=trim($tableName);
        $sql='CREATE TABLE IF NOT EXISTS `'.$tableName.'`(id serial) ENGINE=INNODB;';
        $return=$this->query($sql);
        return $return;
    }
    /**
    * Apagar coluna
    * @param  string $tableName  Nome da tabela
    * @param  string $columnName Nome da coluna
    * @return bool               Retorna true ou false
    */
    public function deleteColumn(string $tableName, string $columnName):bool
    {
        if ($columnName!='id') {
            $tableName=trim($tableName);
            $columnName=trim($columnName);
            $sql='ALTER TABLE '.$tableName.' DROP COLUMN '.$columnName;
            return $this->query($sql);
        }
    }
    /**
    * Apagar tabela
    * @param  string $tableName Nome da tabela
    * @return bool              Retorna true ou false
    */
    public function deleteTable(string $tableName):bool
    {
        $tableName=trim($tableName);
        $sql='DROP TABLE IF EXISTS '.$tableName;
        return $this->query($sql);
    }
    /**
    * Lista de arquivos
    * @param  string $dir Diretório
    * @return array       Lista de arquivos
    */
    public function myScanDir(string $dir):array
    {
        $ignored = array('.', '..', '.svn', '.htaccess');
        $files = array();
        foreach (scandir($dir) as $file) {
            if (in_array($file, $ignored)) {
                continue;
            }
            $files[$file] = filemtime($dir.$file);
        }
        arsort($files);
        $files = array_keys($files);
        return ($files) ? $files : false;
    }
    /**
    * Requisição SQL RAW
    * @param  string $sql Código SQL RAW
    * @return mixed       Resposta RAW
    */
    public function query(string $sql)
    {
        return $this->db->query($sql)->fetchAll();
    }
    /**
    * Renomear coluna
    * @param  string $tableName         Nome da tabela
    * @param  string $oldColumnName     Nome antigo da coluna
    * @param  string $create_columnName Novo nome da coluna
    * @return bool                      Retorna true ou false
    */
    public function renameColumn(string $tableName, string $oldColumnName, string $create_columnName):bool
    {
        $tableName=trim($tableName);
        $oldColumnName=trim($oldColumnName);
        $create_columnName=trim($create_columnName);
        if (!$this->tableExists($tableName)) {
            return false;
        }
        if ($this->columnExists($tableName, $oldColumnName)) {
            $sql='ALTER TABLE `'.$tableName.'` CHANGE ';
            $sql=$sql.'`'.$oldColumnName.'` `'.$create_columnName.'` longtext';
            return $this->query($sql);
        } else {
            return false;
        }
    }
    /**
    * Lista de tabelas
    * @return array Lista de tabelas
    */
    public function tables():array
    {
        $sql='SHOW TABLES';
        $result=$this->query($sql);
        if (is_array($result)) {
            $array=null;
            foreach ($result as $key=>$value) {
                $array[]=array_values($value)[0];
            }
            return $array;
        } else {
            return false;
        }
    }
    /**
    * Verifica se a tabela existe
    * @param  string $tableName Nome da tabela
    * @return bool              True ou false
    */
    public function tableExists(string $tableName):bool
    {
        $tableName=trim($tableName);
        $tables=$this->tables();
        if (@in_array($tableName, $tables)) {
            return true;
        } else {
            return false;
        }
    }
    /**
    * Valida o nome da coluna
    * @param  string $columnName Nome da coluna
    * @return string             Retorna o nome formatado ou false
    */
    public function validColumn(string $columnName):string
    {
        $columnName=trim($columnName);
        $allowed = array("_");
        if (ctype_alpha(str_replace($allowed, '', $columnName))) {
            return $columnName;
        } else {
            return false;
        }
    }
}
