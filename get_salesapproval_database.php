<?php
$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;
$product_id = $_REQUEST['p_record'];


$contact_qry1 = $adb->query("SELECT cf_805 FROM vtiger_contactscf
WHERE cf_805 <> '' AND vtiger_contactscf.contactid = ".$product_id."");
			$activitystatus = $adb->num_rows($contact_qry1);
			
				if($activitystatus >0)
				$post_call_update = 'yes';
				else
				$post_call_update = 'no';
				
			
			$leadcheck = $adb->query("SELECT cf_913 FROM vtiger_contactscf 
INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.crmid = vtiger_contactscf.contactid
INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_crmentityrel.relcrmid WHERE relmodule = 'Leads' AND vtiger_leadscf.cf_913 = 'Deal Won – 100%' AND contactid =".$product_id."");

			$leadstatus = $adb->num_rows($leadcheck);
				if($leadstatus >0)
				$lead_won = 'yes';
				else
				$lead_won = 'no';
				
		$todaydate = date("Y-m-d");	
		$date_validation = 'yes';
			
		$datecheck = $adb->query("SELECT * FROM vtiger_contactscf 
INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.crmid = vtiger_contactscf.contactid
INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_crmentityrel.relcrmid WHERE relmodule = 'Leads' AND vtiger_leadscf.cf_913 = 'Deal Won – 100%' AND contactid =".$product_id."");
		
			if($adb->num_rows($datecheck) > 0) {
        	    while($row = $adb->fetch_array($datecheck)){
					$lead_date = $row['cf_911'];
					
					if($lead_date < $todaydate){
						$date_validation = 'no';
					break;	
					}
					
            	}	
			}				
					
$str = $post_call_update.'*'.$lead_won.'*'.$date_validation;
echo $str;
?> 