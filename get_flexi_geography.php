<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

 $sponser_qry = "select distinct(flexi_geography) as flexi_geography1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and flexi_product_type = '".$_REQUEST['product_type']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='flexi_geography' id='flexi_geography' onchange='show_flexi_access(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$flexi_geography = $row['flexi_geography1'];
	echo "<option>$flexi_geography</option>"; 
	}
	echo "</select>";
?> 