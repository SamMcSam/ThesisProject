<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/1/2015
* last update : 12/1/2015
*
* loads a query from a file, change its graph references and adds the custom parameters
* returns an usable query string on triple store
*/

class TechniqueQuery {

	const TECHNIQUE_DIRECTORY = "../VisualizationTechniques/";
	const TECHNIQUE_EXT = ".tech";

	private $exportable;

	private $parameters;
	private $modelContext;
	private $dataContext;

	private $fileName;
	private $queryString;

	function __construct($fileName, $modelContext = "", $dataContext = "") 
	{
		$this->exportable = false;

		$this->parameters = array();
		$this->modelContext = $modelContext;
		$this->dataContext = $dataContext;

		$this->fileName = $fileName;
		$this->queryString = "";

		//loads the technique file
		$this->loadFile();

		//creates the list of customizable parameters
		$this->generateParameters();
	}

	private function loadFile()
	{
		$file = fopen(TechniqueQuery::TECHNIQUE_DIRECTORY . $this->fileName . TechniqueQuery::TECHNIQUE_EXT, "r");

	    if (! $file) 
	        throw new Exception("Could not open the file!");
	   	else {
	   		while (($line = fgets($file)) !== false) {
	   			 $this->queryString .= $line;
		    }
	   	}

		fclose($file);

		/*
		echo "<pre>";
		echo htmlspecialchars($this->queryString);
		echo "</pre>";
		*/
	}

	private function generateParameters()
	{
		// parameters are between two hash symbols. Ex : #parameter#
		// Also supports default parameters with syntax : #parameter=default#
		preg_match_all("/\#(.*)\#/", $this->queryString, $retour);

		foreach ($retour[1] as $value) {

			$para = explode("=", $value);
			$this->parameters[$para[0]] = ( sizeof($para)> 1 ) ? $para[1] : "";

			//echo $para[0] . " = " . $this->parameters[$para[0]] . "<br>";
		}

		//print_r($this->parameters);

		$this->validateParameters();
	}

	//-------------------------------------------------------

	public function getParameterNames()
	{
		return array_keys($this->parameters);
	}

	//load values from array that should have at least the same parameter keys found in technique file
	public function loadParameterValues($parameterValues)
	{
		foreach ($this->parameters as $key => $value) {
			$this->parameters[$key] = $parameterValues[$key];
		}

		//print_r($this->parameters);

		$this->validateParameters();
	}

	public function setModelGraph($modelContext)
	{
		$this->modelContext = $modelContext;
	}

	public function setDataGraph($dataContext)
	{
		$this->dataContext = $dataContext;
	}

	//-------------------------------------------------------

	private function validateParameters()
	{
		$this->exportable = true;

		foreach ($this->parameters as $key => $value) {
			if (empty($value))
				$this->exportable = false;
		}

		//echo $this->exportable;
		//print_r($this->parameters);
	}

	//-------------------------------------------------------

	public function generateQuery()
	{
		// ADDS 'FROM' statements

		$pos = strrpos($this->queryString , "WHERE");

		$fromModel = "FROM " . htmlspecialchars($this->modelContext) . " ";
		$fromData = "FROM " . htmlspecialchars($this->dataContext) . " ";

		$this->queryString = substr_replace($this->queryString, $fromModel . $fromData, $pos, 0);

		// REPLACES parameter with custom values

		foreach ($this->parameters as $key => $value) {
			$this->queryString = preg_replace("/\#(".$key.".*)\#/", $value, $this->queryString);
		}

		echo "<pre>";
		echo ($this->queryString);
		echo "</pre>";
	}

	public function getQuery()
	{
		if (!$this->exportable)
			throw new  Exception("Incomplete parameters list (some values are missing)", 1);
		elseif (empty($this->modelContext))
			throw new  Exception("Missing model graph URI", 1);
		elseif (empty($this->dataContext))
			throw new  Exception("Missing data graph URI", 1);
		else
		{
			$this->generateQuery();

			return $this->queryString;
		}
	}

}