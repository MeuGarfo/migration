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
	function __construct($options){
        $this->db=new Medoo($options['db']);
        $this->dir=$options['dir'];
	}
	function dropTables(){
	    //todo apagar tabelas
	}
	function seedTables(){
	    //todo semear dados
	}
	function truncateTables(){
	    //todo limpar tabelas
	}
	private function sql($sql){
	    return $this->db->query($sql)->fetchAll();
	}
}