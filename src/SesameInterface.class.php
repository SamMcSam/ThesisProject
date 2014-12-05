<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 2/12/2014
*
* Interface with Sesame repository 
* This is a re-writting of phpSesame using cURL instead
* phpSesame source : https://github.com/alexlatchford/phpSesame 
*
* Needs cURL enabled on the server
*/	
 
//------------------------------------------------------
// 
//------------------------------------------------------

class SesameInterface
{
	// Return MIME types
	const SPARQL_XML = 'application/sparql-results+xml';
	//const SPARQL_POST = 'application/x-www-form-urlencoded'; //if query sent with post
	
	// Input MIME Types
	const RDFXML = 'application/rdf+xml';
	const NTRIPLES = 'text/plain';
	const TURTLE = 'application/x-turtle';
	const N3 = 'text/rdf+n3';
	const TRIX = 'application/trix';
	const TRIG = 'application/x-trig';
	
	private $server;
	private $repository;
	
	function __construct($sesameUrl = 'http://localhost:8080/openrdf-sesame', $repository = null)
	{
		$this->setServer($sesameUrl);
		if ($repository != null)
			$this->setRepository($repository);
		else 
			$this->repository = null;
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
		$request->setHeader("Accept: " . self::SPARQL_XML); //ATTENTION : Header CANNOT have space between Accept and the colon!!!!!!
		
		$request->send("GET");
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($request->getResponse());
		
		//finds if the named repository is in the list of repositories
		$analyseXml = $xmlDoc->documentElement->getElementsByTagName("result");
		foreach($analyseXml as $repositories){			
			$bindings = $repositories->getElementsByTagName('binding');
			$literal = $bindings->item(0)->getElementsByTagName('literal');
			//echo $literal->item(0)->nodeValue;

			if ($literal->item(0)->nodeValue == $rep){
				return true;		 
				echo "OK";}
		}
		
		return false; //hasn't found the repository
	}
	
	private function checkRepository()
	{
		if (empty($this->repository) || $this->repository == '')
		{
			throw new Exception ('No repository has been selected.');
		}
	}
	
	private function checkContext(&$context)
	{
		if($context != 'null')
		{
			$context = (substr($context, 0, 1) != '<' || substr($context, strlen($context) - 1, 1) != '>') ? "<$context>" : $context;
			$context = urlencode($context);
		}
	}
	
	private function checkInputFormat($format)
	{
		if ($format != self::RDFXML && $format != self::N3 && $format != self::NTRIPLES
				&& $format != self::TRIG && $format != self::TRIX && $format != self::TURTLE)
		{
			throw new Exception ('Please supply a valid input format.');
		}
	}
	
	//------------------------------------------------------
	// 
	//------------------------------------------------------
	
	public function createRepository($name){
		if (!$this->existsRepository($name)){
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
	}
	
	public function append($data, $context = 'null', $inputFormat = self::RDFXML)
	{
		$this->checkRepository();
		$this->checkContext($context);
		$this->checkInputFormat($inputFormat);
		
		$request = new HttpRequest($this->server . '/repositories/' . $this->repository . '/statements?context=' . $context);
		$request->setHeader('Content-type: ' . $inputFormat);
		$request->setData($data);
		$response = $request->send();
		if($request->getStatus() != 204)
		{
			throw new Exception ('Failed to append data to the repository, HTTP response error: ' . $request->getStatus());
		}
	}
	
	
	public function appendFile($filePath, $context = 'null', $inputFormat = self::RDFXML)
	{
		if(empty($filePath) || $filePath == '')
		{
			throw new Exception('Please supply a filepath.');
		}
		$data = file_get_contents($filePath);

		if ($context == 'null')
			$context = "<file://fakepath/$filePath>";
			
		$this->append($data, $context, $inputFormat);
	}
	

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
	
	function __construct($address, $header=array(), $data=array())
	{
		$this->setAddress($address);
		$this->header = array();
		$this->setHeader($header);
		$this->data = array();
		$this->setData($data);
		$this->status = null;
		$this->response = null;
	}
	
	public function send($type="POST")
	{
		// initialisation curl
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->address);
			
		curl_setopt($c, CURLOPT_HTTPHEADER, $this->header);
		
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
		return $this->response;
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
		$this->header = array($value);
    }

    public function setData ($value){
		if (is_array($value))
			$this->data = http_build_query($value); 
		else
			$this->data = $value;
    }

	public function getStatus (){
        return $this->status;
    }

	// A REFAIRE
	public function getResponse (){		
		return $this->response;
    }
	
}