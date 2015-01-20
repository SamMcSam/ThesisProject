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

require_once('CityRDF.class.php');

//----------------------------------------------------------------

$msg = "<div id='enrichment_message' class='error'></div>";

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
$listDataHuman = null; //for human-readable display
$techName = "";

if (isset($_POST["enrichment_repoId"])){
	$repoNumber = $_POST["enrichment_repoId"];
	$repoName = $listRepo[$repoNumber]["id"];

	// get all data context names
	$sesame->setRepository($repoName);
	$listData = $sesame->getListContexts();

	//remove city context
	$listData = CityRDF::getListDataContexts($listData, $listDataHuman);

	//var_dump($listData);
	//var_dump($listDataHuman);
}

if (isset($_POST["enrichment_dataName"]))
	$dataName = $_POST["enrichment_dataName"];

if (isset($_POST["enrichment_techName"]))
	$techName = $_POST["enrichment_techName"];

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
					echo "<input type='hidden' id='enrichment_repoId' name='enrichment_repoId' value='$repoNumber' />";
				}
				// a selection of all repositories
				else 
				{
					echo "<select id='enrichment_repoId' name='enrichment_repoId' size='1' onchange='loadEnrichment(false);'><option value='-1'>";

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
				if ($repoNumber < 1)
				{
					echo "<input type='text' id='enrichment_dataName' name='enrichment_dataName' value='$dataName' readonly/>";
				}
				// a selection of all data in this repository
				else 
				{
					echo "<select id='enrichment_data' name='enrichment_data' size='1'><option value='-1'>";

					for($i = 0 ; $i < count ($listData) ; $i++){
						echo "<option value='".$listData[$i]."'";
						if ($dataName == $listData[$i])
							echo " selected";
						echo ">" . $listDataHuman[$i];
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

		<?php
			if ($repoNumber > 0) {
				echo "<table>
					<tr>
						<td> <button class='champs' type='button' onclick='loadEnrichment(true);'>Back</button> </td>
						<td> <button class='champs' type='button' onclick='loadGeneration(false);'>Next</button> </td>
					</tr>
				</table>
				";
			}
		?>
		
	</form>
	<?php echo $msg;?>

</fieldset>