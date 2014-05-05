<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('EDUCATION');
$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and education_product = '".$_REQUEST['education_product']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$no_of_company = $row['no_of_company'];
	$education_bottom_price = $row['product_bottom_price'];
	$education_mrp = $row['product_mrp'];
	}
	echo "$no_of_company*$education_bottom_price*$education_mrp*$service_tax";
?> 