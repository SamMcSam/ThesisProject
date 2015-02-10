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
	
	public function getRepository()
	{
		return $this->repository;
	}
	
	//------------------------------------------------------
	// Tests on Sesame
	//------------------------------------------------------
	
	public function getListRepositories(){
		$listRepo = array();

		$request = new HttpRequest($this->server . '/repositories');
		$request->setHeader("Accept: " . self::SPARQL_XML); //ATTENTION : Header CANNOT have space between Accept and the colon!!!!!!
		
		$request->send(HttpRequest::METHOD_GET);
		
		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($request->getResponse());
		
		//finds if the named repository is in the list of repositories
		$analyseXml = $xmlDoc->documentElement->getElementsByTagName("result");
		$i = 0;
		foreach($analyseXml as $repositories){		
			$listRepo[$i] = array();

			$bindings = $repositories->getElementsByTagName('binding');
			foreach ($bindings as $info){
				$key = $info->getAttribute("name");
				$value = $info->childNodes->item(1)->nodeValue;
				//echo"$key<br>";
				//echo"$value<br>";
				//echo"<br>";
				$listRepo[$i][$key] = $value;
			}
			$i++;
		}
		return $listRepo;
	}

	public function getListContexts(){
		$contextList = null;

		$this->checkRepository();

		$request = new HttpRequest($this->server . '/repositories/' . $this->repository . '/contexts');
		$request->setHeader('Accept: ' . self::SPARQL_XML);

		$response = $request->send(HttpRequest::METHOD_GET);

		$xmlDoc = new DOMDocument();
		$xmlDoc->loadXML($response);

		$analyseXml = $xmlDoc->documentElement->getElementsByTagName("uri");
		foreach($analyseXml as $contexts){	
			$contextList[] = $contexts->nodeValue;
		}
		
		return $contextList;
	}

	public function existsRepository($rep) {
		$listRepo = $this->getListRepositories();

		foreach($listRepo as $repositories){			
			if ($repositories["id"] == $rep){
				return true;		 
			}
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
	// Repository and context functions
	//------------------------------------------------------
	
	//function only works on OpenWorkbench
	public function deleteRepository($name){
		$serverWorkbench = str_replace("/openrdf-sesame", "/openrdf-workbench", $this->server);  

		//form data
		$data = array(
			'id' => $name
		);

		$request = new HttpRequest($serverWorkbench . '/repositories/NONE/delete', 0, $data);
		$request->send();
	}

	//function only works on OpenWorkbench
	public function createRepository($name, $description){
		if (!$this->existsRepository($name)){
			//creating a repository is only possible from the workbench, weirdly...
			$serverWorkbench = str_replace("/openrdf-sesame", "/openrdf-workbench", $this->server);  
			
			//form data
			$data = array(
				'type' => "memory-rdfs" ,
				'Repository ID' => $name ,
				'Repository title' => $description,
				'Persist' => 'true' ,
				'Sync delay' => '0' 
			);
			
			$request = new HttpRequest($serverWorkbench . '/repositories/NONE/create', 0, $data);
			$request->send();
		}
	}

	//TODO
	public function deleteContext(){
		//...
	}

	//------------------------------------------------------
	// Query functions
	//------------------------------------------------------
	
	//equivalent to Update on OpenWorkbench
	public function update($data, $inputFormat = self::SPARQL_POST)
	{
		$this->checkRepository();
		//$this->checkContext($context);
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