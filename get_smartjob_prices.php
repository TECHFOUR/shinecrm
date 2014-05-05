<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
include("get_service_tax.php");
global $adb;

$service_tax = getTaxValue('SMART JOBS');
$sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and smart_jproduct_type = '".$_REQUEST['smartjob_product']."'";
		
	$query = $adb->query($sponser_qry);
	while($row = $adb->fetch_array($query)) {	
	$smart_jno_jobs = $row['smart_jno_jobs'];
	$smart_jbp = $row['product_bottom_price'];
	$smart_jmrp = $row['product_mrp'];
	}
	echo "$smart_jno_jobs*$smart_jbp*$smart_jmrp*$service_tax";
?> 