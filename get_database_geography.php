<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");

global $adb;
$sponser_qry = "select distinct(geography) as geography1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$_REQUEST['geography']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='database_geography' id='database_geography' onchange='show_Database_it(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$geography = $row['geography1'];
	echo "<option>$geography</option>"; 
	}
	echo "</select>";
?> 