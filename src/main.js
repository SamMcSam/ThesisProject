/* 
* Thesis project
* @author Samuel Constantino
* created : 10/11/2014
* last update : 12/12/2014
*
* AJAX queries et al.
*/

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
	//var type_file
	var uploadcity_complete = document.getElementById("uploadcity_complete").value;
	var uploadcity_texture = document.getElementById("uploadcity_texture").checked;
	var formData = null;

	if (!refresh) {
		if (uploadcity_file.length < 1){
			afficheMessage("city", "Please select a file to upload.");
			return;
		}

		formData = new FormData();
    	formData.append('uploadcity_file', uploadcity_file[0], uploadcity_file[0].name);
    	formData.append('uploadcity_complete', uploadcity_complete);
    	formData.append('uploadcity_texture', uploadcity_texture);
    }
	
    xhrHTML("menuCity", "menuCity.php", formData);
}

function loadData(refresh)
{
	//get all the data from this form
	var uploaddata_file = document.getElementById("uploaddata_file").files;
	var selectRepo = document.getElementById("uploaddata_repo");
	var repoNumber = selectRepo.options[selectRepo.selectedIndex].value;
	var selectType = document.getElementById("uploaddata_type");
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
    	formData.append('uploaddata_repo', repoNumber);
    	formData.append('uploaddata_type', dataType);
	}

    xhrHTML("menuData", "menuData.php", formData);
}

function loadTechnique(refresh)
{
	//get all the data from this form
	var uploadtechnique_file = document.getElementById("uploadtechnique_file").files;
	var uploadtechnique_lang = document.getElementById("uploadtechnique_lang").value;
	//var uploadtechnique_typeupload_file = document.getElementById("uploadtechnique_typeupload_file").checked;

	var formData = null;

	if (!refresh){
		if (uploadtechnique_file.length < 1){
			afficheMessage("data", "No technique submitted.");
			return;
		}

		formData = new FormData();
    	formData.append('uploadtechnique_file', uploadtechnique_file[0], uploadtechnique_file[0].name);
    	formData.append('uploadtechnique_lang', uploadtechnique_lang);
    	//formData.append('uploadtechnique_typeupload_file', dataType);
	}

    xhrHTML("menuTechnique", "menuTechnique.php", formData);
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

	    case "menuTechnique":
	    	loadEnrichment(true);
    }	
	
}

//--------------------------------------------------

function afficheMessage(div, message)
{
	document.getElementById(div + "_message").className = "error";
		document.getElementById(div + "_message").innerHTML = message;
}

// -----------------------------------------------

function verifyPercent(element)
{
	var content = parseInt(element.value);

	if (isNaN(content))
		element.value = 100;
	else 
	{
    	if (content > 100)
    		element.value = 100;
    	else if (content < 0)
    		element.value = 0;
	  }	
}
