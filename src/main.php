<?php
/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 6/12/2014
*
* main menu composed of all different menu
* each menu is called dynamically called with AJAX queries
*/
?>

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='fr' lang='fr'>
	<head>
		<title>PROTOTYPE</title>
		<meta charset='UTF-8'>
		<link rel="stylesheet" href="style.css">
	</head>

	<body>
		<div id='mainBlock'>

			<!-- MENU TO UPLOAD CITY -->
			<div id='menuCity'>
				<?php include("menuCity.php");?>
			</div>
			<div id='menuCity_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
			
			<!-- MENU TO UPLOAD DATA -->
			<div id='menuData'>
				<?php include("menuData.php");?>
			</div>
			<div id='menuData_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
				
			<!-- MENU TO CREATE TECHNIQUE -->
			<div id='menuTechnique'>
				<?php include("menuTechnique.php");?>
			</div>
			<div id='menuTechnique_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>

			<!-- MENU TO GENERATE ENRICHED MODEL -->
			<div id='menuEnrichment'>
				<?php include("menuEnrichment.php");?>
			</div>
			<div id='menuEnrichment_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
			
		</div>
	</body>

</html>

<script type="text/javascript">

    //--------------------------------------------------
    //Fonctions Ajax
    //--------------------------------------------------
    
	function getXhr() 
	{
		var xhr = null;

		if (window.XMLHttpRequest){
			xhr = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject('Msxml2.XMLHTTP');
			} catch (e) {
				xhr = new ActiveXObject('Microsoft.XMLHTTP');
			}       
		}
		else {
			alert('Votre navigateur ne supporte pas les objets XMLHTTPRequest...'); 
			xhr = false; 
		}
		return xhr;
	}
    
    function xhrHTML(div, url, data) 
	{
		document.getElementById(div + "_loading").style.display = '';
		document.getElementById(div).style.display = 'none';

	
		var xhr = null;
		var xhr = getXhr();
		
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) {
				document.getElementById(div).innerHTML = xhr.responseText;
				document.getElementById(div).style.display = '';
				document.getElementById(div + "_loading").style.display = 'none';
			}
		}
		
		xhr.open("POST",url,true);
		//xhr.setRequestHeader("Content-type", false); //DOES NOT WORK IF USE A FORMDATA
		xhr.send(data);
	}
    
    //--------------------------------------------------
    //Fonctions pour chaque bouton
    //--------------------------------------------------

    function loadCity()
    {
    	//get all the data from this form
    	var uploadcity_file = document.getElementById("uploadcity_file").files;
    	var complete_upload = document.getElementById("complete_upload").value;
    	var remove_texture = document.getElementById("remove_texture").checked;

    	if (uploadcity_file.length > 0) {
	    	var formData = new FormData();
	    	formData.append('uploadcity_file', uploadcity_file[0], uploadcity_file[0].name);
	    	formData.append('complete_upload', complete_upload);
	    	formData.append('remove_texture', remove_texture);

	    	xhrHTML("menuCity", "menuCity.php", formData);
   		}
   		else {
   			document.getElementById("city_message").className = "error";
   			document.getElementById("city_message").innerHTML = "Please select a file to upload.";
   		}
    }

    function loadData()
    {
    	//get all the data from this form
    	var uploaddata_file = document.getElementById("uploaddata_file").files;
    	var select = document.getElementById("repo_name");
		var repoNumber = select.options[select.selectedIndex].value;

		if (repoNumber < 1){
			document.getElementById("data_message").className  = "error";
			document.getElementById("data_message").innerHTML  = "Please select a repository";
			return;
		}

    	if (uploaddata_file.length > 0) {
	    	var formData = new FormData();
	    	formData.append('uploaddata_file', uploaddata_file[0], uploaddata_file[0].name);
	    	formData.append('repo_name', repoNumber);

	    	xhrHTML("menuData", "menuData.php", formData);
   		}
   		else {
   			document.getElementById("data_message").className = "error";
   			document.getElementById("data_message").innerHTML = "Please select a file to upload.";
   		}
    }

    function loadEnrichment()
    {
    	var data = "";

    	//get all the data from this form

    	xhrHTML("menuEnrichment", "menuEnrichment.php", data);
    }

</script>