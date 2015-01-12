<?php
/* 
* Thesis project
* @author Samuel Constantino
* created update : 9/12/2014
* last update : 11/12/2014
*
* Generates an insert query from a data file 
*/

//require_once('DataClass.class.php');

class DataInsert {
				// extends DataClass {

	const DATA_URI = "http://data.graph/";
	const DATA_PREFIX = "data";
	const DATA_PREFIX_URI = "http://master.thesis/project/data/";
	const VISU_PREFIX = "visu";
	const VISU_PREFIX_URI = "http://master.thesis/project/visualization/";

	private $dataStructureName;
	private $dataStructure;

	private $fileName;
	private $filePath;

	private $dataArray;
	private $queryString; 

	function __construct($dataType, $fileName, $filePath) 
	{
		//load data type list
		$jsonString = file_get_contents("../config/dataTypes.json");
		$listTypes = json_decode($jsonString, true);

		if (array_key_exists($dataType, $listTypes)){
			$this->dataStructureName = $dataType;
			$this->dataStructure = $listTypes[$dataType];
		}
		else
			throw new Exception("Data type has not been defined.", 1);

		$this->fileName = $fileName;
		$this->filePath = $filePath;
		
		$this->dataArray = null;
		$this->queryString = null;

		//loads the file in a structure
		$this->loadData();

		//generates a string with the rdf INSERT query
		$this->generateQuery();
	}

	private function loadData()
	{
		$file = fopen($this->filePath, "r");
	    if (! $file) 
	        throw new Exception("Could not open the file!");
	   	else {
	   		//line by line
	   		$i = 0;
	   		while (($line = fgets($file)) !== false) {
	   			//if starts with the special character '#', don't read the file
   				if (!preg_match("/^ *#/", $line)){

   					$content = explode (" ", $line, count($this->dataStructure));
   					
   					$this->dataArray[$i] = array();
   					for ($j=0; $j < count($this->dataStructure) ; $j++){
   						$this->dataArray[$i][$this->dataStructure[$j]] = rtrim( $content[$j] ); //also removes break char
   					}
   					$i++;
   				}
		    }
	   	}

		fclose($file);
		//var_dump($this->dataArray);
	}

	private function generateQuery()
	{
		$nameGraph = DataClass::DATA_URI . date("Y-m-d_H-i-s") ."/" . $this->fileName;

		$this->queryString = "
			PREFIX ". DataClass::DATA_PREFIX .":<". DataClass::DATA_PREFIX_URI .">
			INSERT DATA
			{
				GRAPH <$nameGraph>
				{ 
		";		

		$i = 1;
		foreach ($this->dataArray as $data){

			$nameData = DataClass::DATA_PREFIX . ":Data" . $i;
			$this->queryString .= $nameData . " a " . DataClass::DATA_PREFIX . ":" . $this->dataStructureName . ". ";

			foreach ($data as $key=>$value){
				$this->queryString .= $nameData . " " . DataClass::DATA_PREFIX . ":" . $key . " ";
				if (is_numeric($value))
					$this->queryString .= $value; //a number
				else
					$this->queryString .= '"'.$value.'"'; //a string between quotation marks
				$this->queryString .= ". ";
			}
			$i++;

		}

		$this->queryString .= "
				}
			}
		";
	}

	//------------------------------------------------------------------------------

	public function getQuery()
	{
		if ($this->queryString != null) {
				return $this->queryString;
		}	
		else 
			throw new Exception ("Query could not be retrieved.");
	}
}