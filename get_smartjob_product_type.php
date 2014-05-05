<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

$sponser_qry = "select distinct(smart_jproduct_type) as smart_jproduct_type1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$_REQUEST['product']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='smart_jproduct_type' id='smart_jproduct_type' onchange='showSmartjobPrices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$smart_jproduct_type = $row['smart_jproduct_type1'];
	echo "<option>$smart_jproduct_type</option>"; 
	}
	echo "</select>";
?> 