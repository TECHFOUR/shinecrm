<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

  $sponser_qry = "select distinct(no_of_jobs) as no_of_jobs1 from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$_REQUEST['month']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='smartmatch_jobs' id='smartmatch_jobs' onchange='show_Smartmatch_Duraion(this.value);' >";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$no_of_jobs = $row['no_of_jobs1'];
	echo "<option>$no_of_jobs</option>"; 
	}
	echo "</select>";
?> 