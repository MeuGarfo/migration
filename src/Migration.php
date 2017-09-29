<?php
/**
* User: Anderson Ismael
* Date: 18/set/2017
*/

namespace Basic;

use Medoo\Medoo;

class Migration{
    public $dir;
    public $db;
    function __construct($db){
        $dir=ROOT.'table';
        $this->set_dir($dir);
        $this->set_db($db);
    }
    //private
    private function set_dir($dir){
        if(substr($dir, -1)<>'/'){
            $dir=$dir.'/';
        }
        $this->dir=$dir;
    }
    private function set_db($db){
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
    //public
    public function drop_all(){
        $tables=$this->tables();
        if($tables){
            foreach($tables as $table){
                $sql="DROP TABLE $table;";
                $this->query($sql);
            }
            return true;
        }else{
            return false;
        }
    }
    public function migrate_all(){
        $tablesRAW=$this->my_scan_dir($this->dir);
        $tables=null;
        foreach($tablesRAW as $key=>$value){
            if($this->valid_column($value)){
                $content=file_get_contents($this->dir.$value);
                $content=explode(PHP_EOL,$content);
                foreach ($content as $contentKey => $contentValue) {
                    if(!$this->valid_column($contentValue)){
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
                    $this->delete_table($tableName);
                }
            }
            //exclusão de colunas
            foreach ($this->tables() as $keyTableInDB=>$tableName) {
                //le as colunas que já existe na tabelas
                $columnsInDB=$this->columns($tableName);
                //apaga as colunas que estão sobrando
                foreach($columnsInDB as $keyColumnsInDB=>$valueColumnInDB){
                    if(!in_array($valueColumnInDB,$tables[$tableName])){
                        $this->delete_column($tableName,$valueColumnInDB);
                    }
                }
            }
        }
        //criação de colunas
        foreach ($tables as $tableKey => $tableValues) {
            $tableName=$tableKey;
            if(!$this->table_exists($tableName)){
                $this->create_table($tableName);
            }
            $this->create_column($tableName,'id');
            foreach($tableValues as $columnName){
                $this->create_column($tableName,$columnName);
            }
        }
        return true;
    }
    private function seed_tables(){
        //todo semear dados
    }
    public function truncate_all(){
        $tables=$this->tables();
        foreach($tables as $table){
            $sql="TRUNCATE $table;";
            $this->query($sql);
        }
        return true;
    }
    //protectes
    protected function column_exists($tableName,$columnName){
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
        if(!$this->table_exists($tableName)){
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
    protected function create_column($tableName,$columnName){
        $tableName=trim($tableName);
        $columnName=trim($columnName);
        if(!$this->column_exists($tableName,$columnName)){
            $sql='ALTER TABLE `'.$tableName.'` ADD ';
            if($columnName=='id'){
                $sql=$sql.'`'.$columnName.'` serial;';
            }else{
                // ALTER TABLE `user` ADD `email` LONGTEXT NOT NULL ;
                $sql=$sql.'`'.$columnName.'` LONGTEXT;';
            }
            if(!$this->column_exists($tableName,$columnName)){
                return $this->query($sql);
            }
        }
    }
    protected function create_table($tableName){
        $tableName=trim($tableName);
        $sql='CREATE TABLE IF NOT EXISTS `'.$tableName.'`(id serial) ENGINE=INNODB;';
        $return=$this->query($sql);
        return $return;
    }
    protected function delete_column($tableName,$columnName){
        if($columnName!='id'){
            $tableName=trim($tableName);
            $columnName=trim($columnName);
            $sql='ALTER TABLE '.$tableName.' DROP COLUMN '.$columnName;
            return $this->query($sql);
        }
    }
    protected function delete_table($tableName){
        $tableName=trim($tableName);
        $sql='DROP TABLE IF EXISTS '.$tableName;
        return $this->query($sql);
    }
    protected function my_scan_dir($dir) {
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
    protected function query($sql){
        return $this->db->query($sql)->fetchAll();
    }
    protected function rename_column($tableName,$oldColumnName,$create_columnName){
        $tableName=trim($tableName);
        $oldColumnName=trim($oldColumnName);
        $create_columnName=trim($create_columnName);
        if(!$this->table_exists($tableName)){
            return false;
        }
        if($this->column_exists($tableName,$oldColumnName)){
            $sql='ALTER TABLE `'.$tableName.'` CHANGE ';
            $sql=$sql.'`'.$oldColumnName.'` `'.$create_columnName.'` longtext';
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
    protected function table_exists($tableName){
        $tableName=trim($tableName);
        $tables=$this->tables();
        if(@in_array($tableName, $tables)){
            return true;
        }else{
            return false;
        }
    }
    protected function valid_column($columnName){
        $columnName=trim($columnName);
        $allowed = array("_");
        if (ctype_alpha(str_replace($allowed, '', $columnName))){
            return $columnName;
        } else {
            return false;
        }
    }
}
