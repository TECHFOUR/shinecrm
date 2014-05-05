<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");

global $adb;

$service_tax = getTaxValue('ELITE MIND & SHINE VERIFIED');
$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and elite_product_type = '".$_REQUEST['elite_product']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$elite_no_wlakin = $row['elite_no_wlakin'];
	$elite_req_details = $row['elite_req_details'];
	$elite_mrp = $row['product_mrp'];
	$elite_bp = $row['product_bottom_price'];
	}
	echo "$elite_no_wlakin*$elite_req_details*$elite_mrp*$elite_bp*$service_tax";
?> 