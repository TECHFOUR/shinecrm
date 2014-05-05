<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;


$sponser_qry = "select distinct(event_product) as event_product1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$_REQUEST['product']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='prodct_type' id='prodct_type' onchange='showProductType(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$prodct_type = $row['event_product1'];
	echo "<option>$prodct_type</option>"; 
	}
	echo "</select>";
?> 