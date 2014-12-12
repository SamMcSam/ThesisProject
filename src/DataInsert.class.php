<?php
/* 
* Thesis project
* @author Samuel Constantino
* created update : 9/12/2014
* last update : 11/12/2014
*
* Generates an insert query from a data file
*/

require_once('DataClass.class.php');

class DataInsert extends DataClass {

	//herited
	//protected $dataStructureName;
	//protected $dataStructure;

	private $fileName;
	private $filePath;

	private $dataArray;
	private $queryString; 

	function __construct($dataType, $fileName, $filePath) 
	{
		//creates structure in $dataStructure
		parent::__construct($dataType);

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
   						$this->dataArray[$i][$this->dataStructure[$j]] = $content[$j];
   					}
   					$i++;
   				}
		    }
	   	}

		fclose($file);
		var_dump($this->dataArray);
	}

	private function generateQuery()
	{
		$this->queryString = "
			PREFIX data:<http://master.thesis/project/>
				INSERT DATA
				{
					GRAPH <http://data.graph/25112014/Munich_DataProto_type1a.txt>
					{ 
					data:x data:tag "three" . 
					data:y data:tag "four" . 
				}
			}
		";

		foreach ($this->dataArray as $data){
			foreach ($data as $key=>$value){

			}
		}
	}

	//------------------------------------------------------------------------------

	public function getQuery()
	{
		if ($this->queryString != null) {
				return $this->queryString;
		}	
		else 
			throw new Exception ("Saving the file failed.");
	}
}