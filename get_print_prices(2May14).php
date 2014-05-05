<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;
$service_tax = getTaxValue('PRINT');

$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and print_product = '".$_REQUEST['product_type']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$print_month = $row['event_month'];
	$print_bottom_price = $row['print_bottom_price'];
	$print_mrp = $row['product_mrp'];
	$print_size = $row['product_bottom_price'];
	}
	echo "$print_month*$print_bottom_price*$print_mrp*$print_size*$service_tax";
?> 