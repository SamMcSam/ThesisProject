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
			<div class='titre'>City graph</div>
			<div id='menuCity'>
				<?php include("menuCity.php");?>
			</div>
			<div id='menuCity_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
			
			<!-- MENU TO UPLOAD DATA -->
			<div class='titre'>Data graph</div>
			<div id='menuData'>
				<?php include("menuData.php");?>
			</div>
			<div id='menuData_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>
				
			<!-- MENU TO CREATE TECHNIQUE -->
			<div class='titre'>Abstract visualization techniques</div>
			<div id='menuTechnique'>
				<?php include("menuTechnique.php");?>
			</div>
			<div id='menuTechnique_loading' style="display:none;">
				<img class ="imgLoading" src="../img/loading.gif"> LOADING...
			</div>

			<!-- MENU TO GENERATE ENRICHED MODEL -->
			<div class='titre'>Visualization technique</div>
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
				refreshSection(div);
			}
		}
		
		xhr.open("POST",url,true);
		//xhr.setRequestHeader("Content-type", false); //DOES NOT WORK IF USE A FORMDATA
		xhr.send(data);
	}
    
    //--------------------------------------------------
    //Fonctions pour chaque bouton
    //--------------------------------------------------

    function loadCity(refresh)
    {
    	//get all the data from this form
    	var uploadcity_file = document.getElementById("uploadcity_file").files;
    	var complete_upload = document.getElementById("complete_upload").value;
    	var remove_texture = document.getElementById("remove_texture").checked;
    	var formData = null;

    	if (!refresh) {
    		if (uploadcity_file.length < 1){
				afficheMessage("city", "Please select a file to upload.");
				return;
			}

    		formData = new FormData();
	    	formData.append('uploadcity_file', uploadcity_file[0], uploadcity_file[0].name);
	    	formData.append('complete_upload', complete_upload);
	    	formData.append('remove_texture', remove_texture);
	    }
    	
	    xhrHTML("menuCity", "menuCity.php", formData);
    }

    function loadData(refresh)
    {
    	//get all the data from this form
    	var uploaddata_file = document.getElementById("uploaddata_file").files;
    	var selectRepo = document.getElementById("repo_name");
		var repoNumber = selectRepo.options[selectRepo.selectedIndex].value;
    	var selectType = document.getElementById("data_type");
		var dataType = selectType.options[selectType.selectedIndex].value;
		var formData = null;

		if (!refresh){
			if (repoNumber < 0){
				afficheMessage("data", "Please select a repository.");
				return;
			}

			if (uploaddata_file.length < 1){
				afficheMessage("data", "Please select a file to upload.");
				return;
			}

			formData = new FormData();
	    	formData.append('uploaddata_file', uploaddata_file[0], uploaddata_file[0].name);
	    	formData.append('repo_name', repoNumber);
	    	formData.append('data_type', dataType);
		}

	    xhrHTML("menuData", "menuData.php", formData);
    }

    function loadEnrichment(refresh)
    {
    	var formData = null;

    	//get all the data from this form
    	if (!refresh){
			formData = new FormData();
	    	//formData.append('uploaddata_file', uploaddata_file[0], uploaddata_file[0].name);
	    	//formData.append('repo_name', repoNumber);
		}

    	xhrHTML("menuEnrichment", "menuEnrichment.php", formData);
    }

    //--------------------------------------------------

    function afficheMessage(div, message)
    {
    	document.getElementById(div + "_message").className = "error";
   		document.getElementById(div + "_message").innerHTML = message;
    }

    //NOT FINISHED
    function refreshSection(div)
    {
    	switch (div) {
		    case "menuCity":
		        //loadCityDelete(true);
    			loadData(true);
    			//loadDataDelete(true);
		        break;
		    //ETC!
	    }	
    	
    }

</script>