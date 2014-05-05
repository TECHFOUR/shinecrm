<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");

global $adb;
$product = $_REQUEST['product'];
$inventory_product = $_REQUEST['inventory_product'];
 $sponser_qry = "select distinct(inventory_active) as inventory_active1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and inventory_product = '".$inventory_product."' and tg_database = '".$_REQUEST['tg_database']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='inventory_active' id='inventory_active' onchange='showInventoryPrices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$inventory_active = $row['inventory_active1'];
	echo "<option>$inventory_active</option>"; 
	}
	echo "</select>";
?> 