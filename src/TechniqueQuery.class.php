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

require_once('../config/constantsPath.php');

class TechniqueQuery {

	const TECHNIQUE_DIRECTORY = "../sys/VisualizationTechniques/";
	const TECHNIQUE_EXT = ".tech";

	const LAYOUT_URI = "<http://unige.ch/masterThesis/layoutmanagers/>";
	const LAYOUT_NAME = "layout";

	private $parametersValid;
	private $layoutsValid;

	private $listManagers;

	private $parameters;
	private $modelContext;
	private $dataContext;

	private $fileName;
	private $queryString;

	function __construct($fileName, $path = TechniqueQuery::TECHNIQUE_DIRECTORY) 
	{
		$this->parametersValid = false;
		$this->layoutsValid = false;

		$this->listManagers = array();

		$this->parameters = array();
		$this->modelContext = "";
		$this->dataContext = "";

		$this->fileName = $fileName;
		$this->queryString = "";

		//loads the technique file
		$this->loadFile($path);

		//creates the list of customizable parameters
		$this->generateParameters();

		//creates the list of layout managers
		$this->generateManagers();
		$this->validateLayoutManagers();
	}

	private function loadFile($path)
	{
		$file = fopen($path . $this->fileName . TechniqueQuery::TECHNIQUE_EXT, "r");

	    if (! $file) 
	        throw new Exception("Could not open the file!");
	   	else {
	   		while (($line = fgets($file)) !== false) {
	   			 $this->queryString .= $line;
		    }
	   	}

	   	//$this->queryString = preg_replace( "/\r|\n/", "", $this->queryString );

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

	private function generateManagers()
	{
		preg_match_all("/layout:(.*)\./", $this->queryString, $retour);

		foreach ($retour[1] as $value) {
			$this->listManagers[] = htmlspecialchars($value);
		}

		//skip the first ; ie the PREFIX layout: <some uri>
		unset($this->listManagers[0]); 

		/*
		echo "<pre>";
		print_r($retour);
		print_r($this->listManagers);
		echo "</pre>";
		*/
	}

	//-------------------------------------------------------

	//load values from array that should have at least the same parameter keys found in technique file
	public function loadParameterValues($parameterValues)
	{
		foreach ($this->parameters as $key => $value) {
			$this->parameters[$key] = $parameterValues[$key];
		}

		//print_r($this->parameters);
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
		$this->parametersValid = true;

		foreach ($this->parameters as $key => $value) {
			if (empty($value))
				$this->parametersValid = false;
		}

		//echo $this->parametersValid;
		//print_r($this->parameters);
		return $this->parametersValid;
	}

	private function validateLayoutManagers()
	{
		$this->layoutsValid = true;
		$listLayout = TechniqueQuery::getLayoutsSupported();

		foreach ($this->listManagers as $manager) {
			if (! in_array ($manager , $listLayout))
				$this->layoutsValid = false;
		}

		return $this->layoutsValid;
	}


	//-------------------------------------------------------

	public function generateQuery()
	{
		// ADDS 'FROM' statements

		$pos = strrpos($this->queryString , "WHERE");

		//$fromModel = "FROM " . htmlspecialchars($this->modelContext) . " ";
		//$fromData = "FROM " . htmlspecialchars($this->dataContext) . " ";
		$fromModel = "FROM " . ($this->modelContext) . " ";
		// + multipart
		$fromData = "FROM " . ($this->dataContext) . " ";

		$this->queryString = substr_replace($this->queryString, $fromModel . $fromData, $pos, 0);

		// REPLACES parameter with custom values

		foreach ($this->parameters as $key => $value) {
			$this->queryString = preg_replace("/\#(".$key.".*)\#/", $value, $this->queryString);
		}
	}

	//-------------------------------------------------------

	public function isValidTechnique(){
		return $this->layoutsValid;
	}

	public function getLayoutNames()
	{
		return $this->listManagers;
	}

	public function getParameterNames()
	{
		return array_keys($this->parameters);
	}

	public function getParameterWithValues()
	{
		return $this->parameters;
	}

	public function getQuery()
	{
		if (!$this->parametersValid)
			throw new  Exception("Incomplete parameters list (some values are missing)", 1);
		if (!$this->layoutsValid)
			throw new  Exception("One or more layout manager requested isn't present in the system.", 1);
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

	//-------------------------------------------------------

	//gives array with names of Layouts in the layout directory
	public static function getLayoutsSupported()
	{
		$list = scandir(PATH_LAYOUTMANAGERS);
		$layoutManagers = null;

		foreach ($list as $el)
		{
			if(is_file(PATH_LAYOUTMANAGERS.$el))
				$layoutManagers[] = explode(".", $el)[0];
		}

		return $layoutManagers;
	}

	//gives array with names of techniques in the techniques directory
	public static function getTechniquesSupported()
	{
		$list = scandir(PATH_TECHNIQUES);
		$techniques = null;

		foreach ($list as $el)
		{
			if(is_file(PATH_TECHNIQUES.$el))
				$techniques[] = explode(".", $el)[0];
		}

		return $techniques;
	}

}