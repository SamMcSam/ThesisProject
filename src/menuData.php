<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 6/12/2014
*
* Menu to upload data
*/

require_once('SesameInterface.class.php');
require_once('DataInsert.class.php');

$msg = "<div id='data_message' class='error'></div>";

// Load repository list
$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');
$listRepo = $sesame->getListRepositories();

//load data type list
$jsonString = file_get_contents("../config/dataTypes.json");
$listTypes = json_decode($jsonString, true);

//IF called by AJAX with POST request
if (isset($_FILES["uploaddata_file"])) {
	try 
	{
		$nameFile = $_FILES["uploaddata_file"]["name"];
		$tempFile = $_FILES["uploaddata_file"]["tmp_name"];
		$repoName = $listRepo[$_POST["uploaddata_repo"]]["id"];
		$dataType = $_POST["uploaddata_type"];

		if ($_FILES["uploaddata_file"]["error"] > 0)
			throw new Exception("Upload error n°".$_FILES["uploaddata_file"]["error"]);

		//set repository
		if (!$sesame->setRepository($repoName))
			throw new Exception("Repository not found.");

		//if ($_FILES["uploadcity_file"]["type"] != "text/xml" || $_FILES["uploadcity_file"]["type"] != ".gml")
		//	throw new Exception ("File type must be either xml or gml");§

		// GENERATE data graph
		$data = new DataInsert($dataType, $nameFile, $tempFile);

		//upload
		$query = $sesame->update($data->getQuery());

		//	throw new Exception("A repository for this file already exists.");
		
		$msg = "<div id='data_message' class='confirmed'>Data graph was created for the file '$nameFile' in the '$repoName' repository!</div>";
	}
	catch (Exception $e){
		$msg = "<div id='data_message' class='error'>". $e->getMessage() ."</div>";
	}
	
}

?>

<fieldset> <legend>Load data in triple store</legend> 
	<form>
		<p>
			Link data to the city repository : 
			<select id="uploaddata_repo" name="uploaddata_repo" size="1">
				<option value="-1"> 
				<?php
					for($i = 1 ; $i < count ($listRepo) ; $i++){
						echo "<option value='$i'>" . $listRepo[$i]["id"] . " - " . $listRepo[$i]["title"] ;
					}
				?>
			</select>
		</p>
		<p>
			<input id='uploaddata_file' type='file' name='uploaddata_file' />
			Data type : <select id="uploaddata_type" name="uploaddata_type" size="1">
				<?php
					$i = 1;
					foreach($listTypes as $key => $val){
						echo "<option value='$key'> Type $i, defined as : " ;
						foreach($val as $info){
							echo "".$info." ";
						}
						$i++;
					}
				?>
			</select>
		</p>
		<button class='champs' type="button" onclick="loadData(false);">Upload</button>
	</form>
	<?php echo $msg;?>
</fieldset>