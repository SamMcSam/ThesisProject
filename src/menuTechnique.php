<?php
/* 
* Thesis project
* @author Samuel Constantino
* last update : 6/12/2014
*
* Menu to upload visualization techniques
* analyses varaibles and layout managers to see if correct
*/
?>

<a name="technique"></a>
<fieldset> <legend>Upload visualization techniques</legend> 
	<form>

<?php
	// menu confirmation
	echo "
		<p>
			Query language : 
			<input id='uploadtechnique_lang' type='radio' name='uploadtechnique_lang' value='sparql1.0' readonly checked/> SPARQL 1.0
		</p>
		<p>
			Upload by : 
			<input id='uploadtechnique_typeupload_file' type='radio' name='uploadtechnique_typeupload' value='file' checked onclick='switchTechniqueUploadType();'/> File
			<input id='uploadtechnique_typeupload_text' type='radio' name='uploadtechnique_typeupload' value='text'onclick='switchTechniqueUploadType();'/> Text input
		</p>
		<p>
			<input id='uploadtechnique_file' type='file' name='uploadtechnique_file'/>
			<textarea id='uploadtechnique_text' name='uploadtechnique_text' style='display:none;'></textarea>
		</p>
	";
?>	
		<button class='champs' type="button" onclick="loadTechnique(false);">Upload</button>
	</form>
	<p>
		<a href='#technique'>Read me more on technique definition format</a>
	</p>
	<?php echo $msg;?>
</fieldset>