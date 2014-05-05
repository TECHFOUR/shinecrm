<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

 $sponser_qry = "select distinct(smartmatch_month) as smartmatch_month1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and no_of_jobs = '".$_REQUEST['duration']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='smartmatch_month' id='smartmatch_month' onchange='show_Smartmatch_Prices(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$smartmatch_month = $row['smartmatch_month1'];
	echo "<option>$smartmatch_month</option>"; 
	}
	echo "</select>";
?> 