<?php

class Resource{

	private $_client;
	private $_functions;

	function Resource($client, $config){
		
		$this->_client = $client;
		$this->_functions = $config['functions'];

	}

	function call($messageName, $args){
		
		return $this->_client->soapCall($this->_functions[$messageName]['methodApiName'], $args);
	}
}
?>