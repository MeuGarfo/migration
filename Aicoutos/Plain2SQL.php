<?php
/**
 * User: Anderson Ismael
 * Date: 18/set/2017
 */

namespace Aicoutos;


class Plain2SQL{
	function __construct(){
        //TODO definir diretório
        //@todo jadsjsad
        //todo ddd
        //fixme dsadas
        //FIXME sadsad
	}

	function Create($path,$closure){
		$this->Router->post($path,$closure);
	}

	function Read($path,$closure){
		$this->Router->get($path,$closure);
	}

	function Update($path,$closure){
		$this->Router->put($path,$closure);
	}

	function Delete($path,$closure){
		$this->Router->delete($path,$closure);
	}
}