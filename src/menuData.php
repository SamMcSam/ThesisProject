<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 6/12/2014
*
* Menu to upload data
*/

require_once('SesameInterface.class.php');

$sesame = new SesameInterface('http://localhost:8080/openrdf-sesame');
$listRepo = $sesame->getListRepositories();

$msg = "<div id='data_message' class='error'></div>";

if (isset($_FILES["uploaddata_file"])) {
	try 
	{
		$nameFile = $_FILES["uploaddata_file"]["name"];
		$tempFile = $_FILES["uploaddata_file"]["tmp_name"];
		$repoName = $listRepo[$_POST["repo_name"]]["id"];

		if ($_FILES["uploaddata_file"]["error"] > 0)
			throw new Exception("Upload error n°".$_FILES["uploaddata_file"]["error"]);

		//set repository
		if (!$sesame->setRepository($repoName))
			throw new Exception("Repository not found.");

		//if ($_FILES["uploadcity_file"]["type"] != "text/xml" || $_FILES["uploadcity_file"]["type"] != ".gml")
		//	throw new Exception ("File type must be either xml or gml");§

		// GENERATE data graph
		$data = new DataRDF($nameFile, $tempFile);

		//upload
		//$query = $sesame->update($city->getGraph());

		//	throw new Exception("A repository for this file already exists.");
		
		$msg = "<div id='data_message' class='confirmed'>Data graph was created for the file '$nameFile' in the '$repoName' repository!</div>";
	}
	catch (Exception $e){
		$msg = "<div id='data_message' class='error'>". $e->getMessage() ."</div>";
	}
	
}

?>

<div class='titre'>Data graph</div>
<fieldset> <legend>Charger les données dans un graphe</legend> 
	<form>
		<p>
			Lier les données au modèle : 
			<select id="repo_name" name="repo_name" size="1">
				<option value="0"> 
				<?php
				$i = 1;
				foreach($listRepo as $repo){
					echo "<option value='$i'>" . $repo["id"] . " - " . $repo["title"] ;
					$i++;
				}
				?>
			</select>
		</p>
		<p>
			<input id='uploaddata_file' type='file' name='uploaddata_file' />
		</p>
		<button class='champs' type="button" onclick="loadData();">Upload</button>
	</form>
	<?php echo $msg;?>
</fieldset>