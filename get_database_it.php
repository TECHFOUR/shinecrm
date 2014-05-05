<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");

global $adb;
$product = $_REQUEST['product'];
 $sponser_qry = "select distinct(database_it) as database_it1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and geography = '".$_REQUEST['database_geography']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='database_it' id='database_it' onchange='show_database_limit(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$database_it = $row['database_it1'];
	echo "<option>$database_it</option>"; 
	}
	echo "</select>";
?> 