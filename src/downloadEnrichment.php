<?php
/* 
* Thesis project
* @author Samuel Constantin
* created : 12/2/2015
* last update : 12/2/2015
*
* for downloading the enrichment file
*/

header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="enrichment.x3d"');

echo $output;

?>