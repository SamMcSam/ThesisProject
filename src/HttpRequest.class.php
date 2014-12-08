<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 2/12/2014
*
* Needs cURL enabled on the server
*/	

class HttpRequest {
	
	const METHOD_POST = "Post";
	const METHOD_GET = "Get";
	
	private $address; 
	private $header; 
	private $data; 
	private $status; 
	private $response; 
	
	function __construct($address, $header=array(), $data=array())
	{
		$this->setAddress($address);
		$this->setHeader($header);
		$this->data = array();
		$this->setData($data);
		$this->status = null;
		$this->response = null;
	}
	
	public function send($type=self::METHOD_POST)
	{
		// initialisation curl
		$c = curl_init();
		curl_setopt($c, CURLOPT_URL, $this->address);
		
		$timeout = 5;
		curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				
		if ($type==self::METHOD_POST){
			curl_setopt($c, CURLOPT_POST, 1);
			curl_setopt($c, CURLOPT_POSTFIELDS, $this->data);
			
			$this->header[] = 'Content-type:' . SesameInterface::SPARQL_POST;
		}
		
		curl_setopt($c, CURLOPT_HTTPHEADER, $this->header);
		//curl_setopt($c, CURLINFO_HEADER_OUT,true);
		
		//execution
		$reponse = curl_exec($c); 
		$this->status = curl_getinfo($c, CURLINFO_HTTP_CODE);
		
		//echo "<p>";
		//var_dump(curl_getinfo($c));
		//echo "</p>";
		
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
		if (is_array($value))
			$this->header = $value;
		else
			$this->header = array($value);
    }

    public function setData ($value){
		if (is_array($value))
			$this->data = http_build_query($value); 
		else
			$this->data = $value;
    
		//echo $this->data;
	}

	public function getStatus (){
        return $this->status;
    }

	// A REFAIRE
	public function getResponse (){		
		return $this->response;
    }
	
}