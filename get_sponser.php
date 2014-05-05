<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;

$product = $_REQUEST['product'];
 $sponser_qry = "select * from vtiger_vendor 
				inner join vtiger_vendorcf on vtiger_vendor.vendorid = vtiger_vendorcf.vendorid
				inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_vendor.vendorid
				where vtiger_crmentity.deleted = 0 and package_master = '".$product."' and event_product = '".$_REQUEST['producttype']."'";
		
	$query = $adb->query($sponser_qry);
	echo "<select name='event_sponsorship' id='event_sponsorship' onchange='showEventPrices(this.value);'>";
	echo "<option>Select an Option</option>";
	while($row = $adb->fetch_array($query)) {	
	$sponsorship = $row['event_sponsorship'];
	echo "<option>$sponsorship</option>"; 
	}
	echo "</select>";
?> 