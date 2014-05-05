<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('INVENTORY');
$product = $_REQUEST['product'];
$inventory_product = $_REQUEST['inventory_product'];
$inventory_database = $_REQUEST['inventory_database'];

 $sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and tg_database = '".$inventory_database."' and inventory_product = '".$inventory_product."'  and inventory_active = '".$_REQUEST['active']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$no_of_emailer = $row['no_of_emailer'];
	$inventory_bottom_price = $row['product_bottom_price'];
	$inventory_mrp = $row['product_mrp'];
	}
	echo "$no_of_emailer*$inventory_bottom_price*$inventory_mrp*$service_tax";
?> 