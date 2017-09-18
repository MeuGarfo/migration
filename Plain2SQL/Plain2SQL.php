<?php
/**
 * User: Anderson Ismael
 * Date: 18/set/2017
 */

namespace Plain2SQL;

use Medoo\Medoo;

class Plain2SQL{
    public $dir;
    public $db;
	function __construct($dir,$db){
	    $this->setDir($dir);
	    $this->setDb($db);
	}
	//private
	private function setDir($dir){
	    $this->dir=$dir;
	}
	private function setDb($db){
	    $this->db = new Medoo([
	        // required
	        'database_type' => 'mysql',
	        'database_name' => $db['name'],
	        'server' => $db['server'],
	        'username' => $db['user'],
	        'password' => $db['password'],	        
	        // [optional]
	        'charset' => 'utf8',
	        'port' => 3306
	    ]);
	}
	private function sql($sql){
	    return $this->db->query($sql)->fetchAll();
	}
	//public
	public function dropTables(){
	    $tables=$this->tables();
	    foreach($tables as $table){
	        $sql="DROP TABLE $table;";
	        $this->query($sql);
	    }
	}
	public function migrate(){
	    $tablesRAW=$this->myScanDir($this->dir);
	    $tables=null;
	    foreach($tablesRAW as $key=>$value){
	        if($this->validColumn($value)){
	            $content=file_get_contents($dir.$value);
	            $content=explode(PHP_EOL,$content);
	            foreach ($content as $contentKey => $contentValue) {
	                if(!$this->validColumn($contentValue)){
	                    unset($content[$contentKey]);
	                }
	            }
	            $content=array_filter($content);
	            $content=array_values($content);
	            $tables[$value]=$content;
	        }
	    }
	    if($this->tables()){
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
	            foreach($columnsInDB as $keyColumnsInDB=>$valueColumnInDB){
	                if(!in_array($valueColumnInDB,$tables[$tableName])){
	                    $this->deleteColumn($tableName,$valueColumnInDB);
	                }
	            }
	        }
	    }
	    //criação de colunas
	    foreach ($tables as $tableKey => $tableValues) {
	        $tableName=$tableKey;
	        if(!$this->tableExists($tableName)){
	            $this->createTable($tableName);
	        }
	        $this->createColumn($tableName,'id');
	        foreach($tableValues as $columnName){
	            $this->createColumn($tableName,$columnName);
	        }
	    }
	}
	private function seedTables(){
	    //todo semear dados
	}
	public function truncateTables(){
	    $tables=$this->tables();
	    foreach($tables as $table){
	        $sql="TRUNCATE $table;";
	        $this->query($sql);
	    }
	}
	//protectes
	protected function columnExists($tableName,$columnName){
	    $tableName=trim($tableName);
	    $columnName=trim($columnName);
	    $columns=$this->columns($tableName);
	    if(@in_array($columnName, $columns)){
	        return true;
	    }else{
	        return false;
	    }
	}
	protected function columns($tableName){
	    $tableName=trim($tableName);
	    if(!$this->tableExists($tableName)){
	        return false;
	    }
	    $sql='SHOW COLUMNS FROM '.$tableName;
	    $result=$this->query($sql);
	    if(is_array($result)){
	        $array=null;
	        foreach($result as $key=>$value){
	            $array[]=$value['Field'];
	        }
	        return $array;
	    }else{
	        return false;
	    }
	}
	protected function createColumn($tableName,$columnName){
	    $tableName=trim($tableName);
	    $columnName=trim($columnName);
	    if(!$this->columnExists($tableName,$columnName)){
	        $sql='ALTER TABLE `'.$tableName.'` ADD ';
	        if($columnName=='id'){
	            $sql=$sql.'`'.$columnName.'` serial;';
	        }else{
	            // ALTER TABLE `user` ADD `email` LONGTEXT NOT NULL ;
	            $sql=$sql.'`'.$columnName.'` LONGTEXT;';
	        }
	        if(!$this->columnExists($tableName,$columnName)){
	            return $this->query($sql);
	        }
	    }
	}
	protected function createTable($tableName){
	    $tableName=trim($tableName);
	    $sql='CREATE TABLE IF NOT EXISTS `'.$tableName.'`(id serial) ENGINE=INNODB;';
	    $return=$this->query($sql);
	    return $return;
	}
	protected function deleteColumn($tableName,$columnName){
	    if($columnName!='id'){
	        $tableName=trim($tableName);
	        $columnName=trim($columnName);
	        $sql='ALTER TABLE '.$tableName.' DROP COLUMN '.$columnName;
	        return $this->query($sql);
	    }
	}
	protected function deleteTable($tableName){
	    $tableName=trim($tableName);
	    $sql='DROP TABLE IF EXISTS '.$tableName;
	    return $this->query($sql);
	}
	protected function myScanDir($dir) {
	    $ignored = array('.', '..', '.svn', '.htaccess');
	    $files = array();
	    foreach (scandir($dir) as $file) {
	        if (in_array($file, $ignored)) continue;
	        $files[$file] = filemtime($dir . '/' . $file);
	    }
	    arsort($files);
	    $files = array_keys($files);
	    return ($files) ? $files : false;
	}
	protected function renameColumn($tableName,$oldColumnName,$createColumnName){
	    $tableName=trim($tableName);
	    $oldColumnName=trim($oldColumnName);
	    $createColumnName=trim($createColumnName);
	    if(!$this->tableExists($tableName)){
	        return false;
	    }
	    if($this->columnExists($tableName,$oldColumnName)){
	        $sql='ALTER TABLE `'.$tableName.'` CHANGE ';
	        $sql=$sql.'`'.$oldColumnName.'` `'.$createColumnName.'` longtext';
	        return $this->query($sql);
	    }else{
	        return false;
	    }
	}
	protected function tables(){
	    $sql='SHOW TABLES';
	    $result=$this->query($sql);
	    if(is_array($result)){
	        $array=null;
	        foreach($result as $key=>$value){
	            $array[]=array_values($value)[0];
	        }
	        return $array;
	    }else{
	        return false;
	    }
	}
	protected function tableExists($tableName){
	    $tableName=trim($tableName);
	    $tables=$this->tables();
	    if(@in_array($tableName, $tables)){
	        return true;
	    }else{
	        return false;
	    }
	}
	protected function validColumn($columnName){
	    $columnName=trim($columnName);
	    $allowed = array("_");
	    if (ctype_alpha(str_replace($allowed, '', $columnName))){
	        return $columnName;
	    } else {
	        return false;
	    }
	}
}