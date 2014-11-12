<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 10/11/2014
*
* Interface with Sesame repository 
* Modified from : https://github.com/alexlatchford/phpSesame
*
* Needs cURL enabled on the server
*/	

class SesameInterface
{
	private $server_adress;
	private $repository;
	
	function __construct($sesameUrl = 'http://localhost:8080/openrdf-sesame', $repository = null)
	{
		$this->server_adress = $sesameUrl;
		$this->setRepository($repository);
	}
	
	public function setRepository($rep)
	{
		$this->repository = $rep;
	}
	
	public function query($query)
	{
		// initialisation curl
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->server_adress . '/repositories/' . $this->repository . '/statements/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$timeout = 5;
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		
		//query
		$data = array('update'=>$query);
		$requete = http_build_query($data);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requete); 
		
		//execution
		$reponse = curl_exec($ch);
		//echo $reponse; //debug
		
		//close connection
		curl_close($ch);
		
		/*
		//output
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($reponse);
		//afficher xml
		echo $xmlDoc->saveXML();
		*/
	}
	
}