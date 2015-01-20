<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 20/1/2015
*
* Menu to  generate a enriched 3DCM part 1 - selects the city, data, technique
* then calls menuGeneration to specify the technique's parameters
*/

require_once('../config/constantsPath.php');

require_once('SesameInterface.class.php');
require_once('TechniqueQuery.class.php');

require_once('DataInsert.class.php');

//----------------------------------------------------------------

$msg = "<div id='data_message' class='error'></div>";

// Load repository list
$sesame = new SesameInterface(URL_SESAME);
$listRepo = $sesame->getListRepositories();

//load technique list
$listTech = TechniqueQuery::getTechniquesSupported();

// parameters
$repoName = "";
$repoNumber = 0;
$dataName = "";
$listData = array();
$techName = "";

if (isset($_POST["enrichment_repo"])){
	$repoNumber = $_POST["enrichment_repo"];
	$repoName = $listRepo[$repoNumber]["id"];

	// get all data context names
	//$listData = array();
	/*
	catch (Exception $e){
		$msg = "<div id='enrichment_message' class='error'>". $e->getMessage() ."</div>";
	}
	*/
}

if (isset($_POST["enrichment_dataName"]))
	$dataName = $_POST["enrichment_dataName"];

if (isset($_POST["enrichment_techName"]))
	$techName = $_POST["enrichment_techName"];

//if ?
//	$msg = "<div class='error'>Data couldn't be uploaded. </div>";
//else {
//	$msg = "<div class='confirmed'>The data '' has been uploaded to the '' repository!</div>";
//}


?>

<a name="enrichment"></a>
<fieldset> <legend>Generate an enriched 3DCM</legend> 
	<form>
		<p>
			1 - Select a 3D model : 
			<?php
				// if already selected
				if ($repoNumber > 0)
				{
					echo "<input type='text' id='enrichment_repoName' name='enrichment_repoName' value='$repoName' readonly />";
					echo "<input type='hidden' id='enrichment_repo' name='enrichment_repo' value='$repoNumber' />";
				}
				// a selection of all repositories
				else 
				{
					echo "<select id='enrichment_repo' name='enrichment_repo' size='1' onchange='loadEnrichment(false);'><option value='-1'>";

					for($i = 1 ; $i < count ($listRepo) ; $i++){
						echo "<option value='$i'>" . $listRepo[$i]["id"] . " - " . $listRepo[$i]["title"] ;
					}
						
					echo "</select>";
				}
			
			?>
		</p>
		<p>
			2 - Select a data set : 
			<?php
				// if already selected, or repo hasn't been selected yet
				if (!empty($dataName) || $repoNumber < 1)
				{
					echo "$dataName";
					echo "<input type='hidden' id='enrichment_dataName' name='enrichment_dataName' value='$dataName' />";
				}
				// a selection of all data in this repository
				else 
				{
					echo "<select id='enrichment_data' name='enrichment_data' size='1'><option value='-1'>";

					for($i = 0 ; $i < count ($listData) ; $i++){
						echo "<option value='$i'>" . $listData[$i];
					}
						
					echo "</select>";
				}
			
			?>
		</p>
		<p>
			3 - Select a visualization technique : 
			<?php
				echo "<select id='enrichment_techName' name='enrichment_techName' size='1'><option value='-1'>";

				for($i = 0 ; $i < count ($listTech) ; $i++){
					echo "<option value='".$listTech[$i]."'";
					if ($techName == $listTech[$i])
						echo " selected";
					echo ">" . $listTech[$i];
				}
					
				echo "</select>";			
			?>
		</p>

		<table>
			<tr>
				<td> <button class='champs' type="button" onclick="loadEnrichment(true);">Back</button> </td>
				<td> <button class='champs' type="button" onclick="loadEnrichment(false);">Next</button> </td>
			</tr>
		</table>
		
	</form>
	<?php echo $msg;?>

</fieldset>