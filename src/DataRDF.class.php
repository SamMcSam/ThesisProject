<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 9/12/2014
* last update : 9/12/2014
*
* Generates a city file in XML that can be uploaded to a triple store (from a GML file)
*/
class DataRDF {

	private $filePath;
	private $fileName;
	
	private $dataFile;
	private $rdfFile;

	function __construct($fileName, $filePath) 
	{
		$this->filePath = $filePath;
		$this->fileName = $fileName;
		
		$this->dataFile = null;
		$this->rdfFile = null;
		$this->filePath = null;

		//loads the file in a string
		//$this->load();

		//generates a string with the rdf data to upload 
		$this->generateRDF();
	}

	private function generateRDF()
	{
		//define structure

		//for each line

		/*
		$c = '
		PREFIX data:<http://test.com/>
		INSERT DATA
			{
			  GRAPH <http://graphData>
			  { 
				data:x data:tag "three" . 
				data:y data:tag "four" . 
			  }
			}
		';
		*/	
	}

	//------------------------------------------------------------------------------

	public function getFile()
	{
		if ($this->rdfFile != null) {
				return $this->rdfFile;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}
}