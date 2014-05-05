<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;
$product = $_REQUEST['product'];
 $sponser_qry = "select distinct(tg_database) as tg_database1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and inventory_product = '".$_REQUEST['product_type']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='inventory_database' id='inventory_database' onchange='showInventoryActive(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$tg_database = $row['tg_database1'];
	echo "<option>$tg_database</option>"; 
	}
	echo "</select>";
?> 