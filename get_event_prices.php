<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$serice_tax = getTaxValue('Events');

$product = $_REQUEST['product'];
$product_type = $_REQUEST['product_type'];

$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and event_product = '".$product_type."' and event_sponsorship = '".$_REQUEST['sponser']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$month = $row['vendorname'];
	$bp = $row['product_bottom_price'];
	$mrp = $row['product_mrp'];
	}
	echo "$month*$bp*$mrp*$serice_tax";
?> 