<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('SMARTMATCH');
$product = $_REQUEST['product'];
$smartmatch_jobs = $_REQUEST['smartmatch_jobs'];

$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and no_of_jobs = '".$smartmatch_jobs."' and smartmatch_month = '".$_REQUEST['smartmatch_duration']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$smartmatch_bottom_price = $row['product_bottom_price'];
	$smartmatch_mrp = $row['product_mrp'];
	}
	echo "$smartmatch_bottom_price*$smartmatch_mrp*$service_tax";
?> 