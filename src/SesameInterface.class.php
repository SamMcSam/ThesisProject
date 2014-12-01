<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 1/12/2014
*
* Interface with Sesame repository 
* based on : https://github.com/alexlatchford/phpSesame (using cURL instead)
*
* Needs cURL enabled on the server
*/	

class SesameInterface
{
	const SPARQL_XML = 'application/sparql-results+xml';
	const SPARQL_POST = 'application/x-www-form-urlencoded'; //if query sent with post
	const RDFXML = 'application/rdf+xml';
	const NTRIPLES = 'text/plain';
	
	private $server;
	private $repository;
	
	function __construct($sesameUrl = 'http://localhost:8080/openrdf-sesame', $repository = null)
	{
		$this->server = $sesameUrl;
		$this->setRepository($repository);
	}
	
	public function setRepository($rep)
	{
		$this->repository = $rep;
	}
	
	//------------------------------------------------------
	// 
	//------------------------------------------------------
	
	public function createRepository($name){
	
		//creating a repository is only possible from the workbench, weirdly...
		$serverWorkbench = str_replace("/openrdf-sesame", "/openrdf-workbench", $this->server);  
		
		//form data
		$data = array(
			'type' => "memory-rdfs" ,
			'Repository ID' => $name ,
			'Repository title' => "Repository for the city '" . $name . "' created on " . date("Y-m-d H:i:s"),
			'Persist' => 'true' ,
			'Sync delay' => '0' 
		);
		
		$this->httpRequest($data, $serverWorkbench . '/repositories/NONE/create');
	}
	
	//public function 
	
	//------------------------------------------------------
	// 
	//------------------------------------------------------
	
	private function httpRequest($data, $address, $inputFormat=0)
	{
		// initialisation curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $address);
		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, $inputFormat);
		
		$requete = http_build_query($data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requete);
				
		$timeout = 5;
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		//execution
		$reponse = curl_exec($ch);
		
		//close connection
		curl_close($ch);
		
		return $reponse;
		
		/*
		//output
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($reponse);
		//afficher xml
		echo $xmlDoc->saveXML();
		*/
	}
	
	//file_get_contents($filePath);
	
}