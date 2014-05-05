<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");


global $adb;
$product = $_REQUEST['product'];
$geography = $_REQUEST['geography']; 

$sponser_qry = "select distinct(database_limit) as database_limit1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and geography = '".$geography."' and database_it = '".$_REQUEST['database_it']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='database_limit' id='database_limit' onchange='show_database_duration(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$database_limit = $row['database_limit1'];
	echo "<option>$database_limit</option>"; 
	}
	echo "</select>";
?> 