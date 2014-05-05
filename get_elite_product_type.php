<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

if($_REQUEST['product'] == 'ELITE MIND ')
$_REQUEST['product'] = 'ELITE MIND & SHINE VERIFIED';


$sponser_qry = "select distinct(elite_product_type) as elite_product_type1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$_REQUEST['product']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='elite_product_type' id='elite_product_type' onchange='showElitePrices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$elite_product_type = $row['elite_product_type1'];
	echo "<option>$elite_product_type</option>"; 
	}
	echo "</select>";
?> 