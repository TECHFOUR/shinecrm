<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");

global $adb;

$product = $_REQUEST['product'];
$database_geography = $_REQUEST['database_geography'];
$database_it = $_REQUEST['database_it'];		 


 $sponser_qry = "select distinct(database_month) as database_month1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' 
				and geography = '".$database_geography."' and database_it = '".$database_it."' and database_limit = '".$_REQUEST['database_limit']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='database_month' id='database_month' onchange='show_Database_Prices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$database_month = $row['database_month1'];
	echo "<option>$database_month</option>"; 
	}
	echo "</select>";
?> 