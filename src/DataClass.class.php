<?php
/* 
* Thesis project
* @author Samuel Constantino
* created update : 11/12/2014
* last update : 11/12/2014
*
* Class 
* TODO : generalise type of data into db? should it be customisable?
*/

class DataClass {

	const DATA_URI = "http://data.graph/";
	const DATA_PREFIX = "data";
	const DATA_PREFIX_URI = "http://master.thesis/project/data/";
	const VISU_PREFIX = "visu";
	const VISU_PREFIX_URI = "http://master.thesis/project/visualization/";

	protected $dataStructureName;
	protected $dataStructure;

	function __construct($dataType) 
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
	}
}