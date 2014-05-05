<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('FLEXI HIRE');
$product = $_REQUEST['product'];
$smart_flexi_type = $_REQUEST['smart_flexi_type'];
$flexi_geography = $_REQUEST['flexi_geography'];
$flexi_access = $_REQUEST['flexi_access'];

$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and flexi_product_type = '".$smart_flexi_type."' 
				and flexi_geography = '".$flexi_geography."' and flexi_access = '".$flexi_access."' and flexi_duration = '".$_REQUEST['flexi_duration']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$flexi_bp = $row['product_bottom_price'];
	$flexi_mrp = $row['product_mrp'];
	}
	echo "$flexi_bp*$flexi_mrp*$service_tax";
?> 