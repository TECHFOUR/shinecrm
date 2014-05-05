<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

$product = $_REQUEST['product'];
$smart_flexi_type = $_REQUEST['smart_flexi_type'];

$sponser_qry = "select distinct(flexi_access) as flexi_access1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and flexi_product_type = '".$smart_flexi_type."' and flexi_geography = '".$_REQUEST['geography']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='flexi_access' id='flexi_access' onchange='show_flexi_duration(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$flexi_access = $row['flexi_access1'];
	echo "<option>$flexi_access</option>"; 
	}
	echo "</select>";
?> 