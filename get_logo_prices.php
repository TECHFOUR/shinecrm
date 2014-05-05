<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('LOGO');
$product = $_REQUEST['product'];
$logo_product = $_REQUEST['logo_product'];


$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' 
				and logo_product = '".$logo_product."' and logo_month = '".$_REQUEST['logo_duration']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$logo_bottom_price = $row['product_bottom_price'];
	$logo_mrp = $row['product_mrp'];
	}
	echo "$logo_bottom_price*$logo_mrp*$service_tax";
?> 