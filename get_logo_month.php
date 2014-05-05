<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

  $sponser_qry = "select distinct(logo_month) as logo_month1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and logo_product = '".$_REQUEST['logo_product']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='logo_month' id='logo_month' onchange='show_Logo_Prices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$logo_month = $row['logo_month1'];
	echo "<option>$logo_month</option>"; 
	}
	echo "</select>";
?> 