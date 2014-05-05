<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('DATABASE');
$product = $_REQUEST['product'];
$database_geography = $_REQUEST['database_geography'];
$database_it = $_REQUEST['database_it'];
$database_limit = $_REQUEST['database_limit'];	

$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' 
				and geography = '".$database_geography."' and database_it = '".$database_it."' and database_limit = '".$database_limit."'
				and database_month = '".$_REQUEST['database_duration']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$database_bottom_price = $row['product_bottom_price'];
	$database_mrp = $row['product_mrp'];
	$upsell_excell = $row['upsell_excell'];
	$upsell_word = $row['upsell_word'];
	$upsell_emailer = $row['upsell_emailer'];
	$downsell_word = $row['downsell_word'];
	$downsell_excel = $row['downsell_excel'];
	$downsell_emailer = $row['downsell_emailer'];
	$upsell_login = $row['upsell_login'];
	}
	echo "$database_bottom_price*$database_mrp*$upsell_word*$upsell_excell*$upsell_emailer*$downsell_word*$downsell_excel*$downsell_emailer*$service_tax*$upsell_login";
?> 