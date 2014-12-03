<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 2/12/2014
*
* Interface with Sesame repository 
* based on : https://github.com/alexlatchford/phpSesame (using cURL instead)
*
* Needs cURL enabled on the server
*/	
 
//------------------------------------------------------
// 
//------------------------------------------------------

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
		$this->setServer($sesameUrl);
		$this->setRepository($repository);
	}
	
	public function setServer($serv)
	{
		$this->server = $serv;
	}
	
	public function setRepository($rep)
	{
		if ($this->existsRepository($rep)) {
			$this->repository = $rep;
			return true;
		}
		else {
			$this->repository = null;
			return false;
		}
	}
	
	//------------------------------------------------------
	// Tests on Sesame
	//------------------------------------------------------
	
	public function existsRepository($rep) {
		$request = new HttpRequest($this->server . '/repositories');
		$request->setHeader("Accept: " . self::SPARQL_XML);
		
		$request->send("GET");
		
		if (strpos($request->getResponse(),$rep) !== false)
			return true;
		else
			return false;
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
		
		$request = new HttpRequest($serverWorkbench . '/repositories/NONE/create', 0, $data);
		$request->send();
	}
	
	//upload xml file
		//file_get_contents($filePath) 
	//upload rdf file
	//do a query
	
 }
 
//------------------------------------------------------
// 
//------------------------------------------------------
 
class HttpRequest {
	
	private $address; 
	private $header; 
	private $data; 
	private $status; 
	private $response; 
	
	function __construct($address, $header=0, $data=array())
	{
		$this->setAddress($address);
		$this->setHeader($header);
		$this->setData($data);
		$this->status = null;
		$this->response = null;
	}
	
	public function send($type="POST")
	{
		// initialisation curl
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->address);
			
		curl_setopt($c, CURLOPT_HEADER, $this->header);
		
		$timeout = 5;
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				
		if ($type=="POST"){
			curl_setopt($c, CURLOPT_POST, 1);
			curl_setopt($c, CURLOPT_POSTFIELDS, $this->data);
		}
		//elsif put...
		//...
		
		//execution
		$reponse = curl_exec($c); 
		$this->status = curl_getinfo($c, CURLINFO_HTTP_CODE);	
		
		curl_close($c); //close connection
		
		//save output
		$this->response = $reponse;		
	}
	
	/*
	//output
	$xmlDoc = new DOMDocument();
	$xmlDoc->loadXML($reponse);
	//afficher xml
	echo $xmlDoc->saveXML();
	*/
	
	//file_get_contents($filePath);
	
	//---------------------------------------------------------

    public function setAddress($value){
        $this->address = $value;
    }

    public function setHeader($value ){
        $this->header = $value;
    }

    public function setData ($value ){
		if (is_array($value)){
			$this->data = http_build_query($value);
			return true;
		}
		else
			return false;
    }

	public function getStatus (){
        return $this->status;
    }

	// A REFAIRE
	public function getResponse (){		
		return $this->response;
    }
	
}