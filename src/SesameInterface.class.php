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

require_once('HttpRequest.class.php');

class SesameInterface
{
	// Return MIME types
	const SPARQL_XML = 'application/sparql-results+xml';
	const SPARQL_JSON = 'application/sparql-results+json';
	const SPARQL_POST = 'application/x-www-form-urlencoded'; //if query sent with post
	
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
		
		$request->send(HttpRequest::METHOD_GET);
		
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
	
	private function checkQueryLang($queryLang)
	{
		if ($queryLang != 'sparql' && $queryLang != 'serql')
		{
			throw new Exception ('Please supply a valid query language, SPARQL or SeRQL supported.');
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
	
	//equivalent 
	public function update($data, $inputFormat = self::SPARQL_POST)
	{
		$this->checkRepository();
		$this->checkContext($context);
		//$this->checkInputFormat($inputFormat);
		
		$request = new HttpRequest($this->server . '/repositories/' . $this->repository . '/statements');
		$request->setHeader('Content-type: ' . $inputFormat);
		
		$data = array(
			'update' => $data
		);
		$request->setData($data);
		
		$response = $request->send();
		if($request->getStatus() != 204)
		{
			throw new Exception ('Failed to append data to the repository, HTTP response error: ' . $request->getStatus());
		}
	}
	
	public function append($data, $context = 'null', $inputFormat = self::RDFXML)
	{
		$this->checkRepository();
		$this->checkContext($context);
		//$this->checkInputFormat($inputFormat);
		
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
	
	
	public function query($query, $header, $queryLang = 'sparql', $infer = true)
	{
		$this->checkRepository();
		$this->checkQueryLang($queryLang);
		
		$request = new HttpRequest($this->server . '/repositories/' . $this->repository);
		
		/*
		$header = array(
			'Accept: ' . self::SPARQL_XML, //for simple query
			'Accept: ' . self::RDFXML //for construct
		);
		*/
		
		$request->setHeader($header);
		
		$data = array (
			"query" => $query
			//"queryLn" => $queryLang,
			//"infer" => $infer
		);
		$request->setData($data);		
		
		$request->send();
		
		if($request->getStatus() != 200)
		{
			throw new Exception ('Failed to run query, HTTP response error: ' . $request->getStatus());
		}
		
		return $request->getResponse();
	}
	
 }