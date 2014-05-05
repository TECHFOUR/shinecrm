<?php

$root_dir = getcwd();
$root_directory = $root_dir.'/';
require_once("$root_directory/modules/Emails/mail.php");
include("$root_directory/config.php");
global $adb;
$recordId = $_REQUEST['approval_id'];

 $total_payment_amount = 0.0;

            $total_package_sold_amount = 0.00;
            
            $packagesold_qry = $adb->query("SELECT ps_total_amount FROM vtiger_campaign
    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
    INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
    INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
    INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_campaignscf.pref_leads
    WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordId
    ORDER BY campaigntype, vtiger_crmentity.crmid ");
            if($adb->num_rows($packagesold_qry) > 0) {
                while($row = $adb->fetchByAssoc($packagesold_qry)) {
                    $total_package_sold_amount = $total_package_sold_amount + str_replace(",","",$row['ps_total_amount']);
                }
            }

            $payment_qry = $adb->query("SELECT payment_mode, amount FROM vtiger_project
    INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
    INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_project.projectid AND vtiger_crmentityrel.relmodule = 'Project')
    INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
    WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordId ");
          if($adb->num_rows($payment_qry) > 0) {
                while($row = $adb->fetchByAssoc($payment_qry)) {
                    $total_payment_amount = $total_payment_amount + str_replace(",","",$row['amount']);
					//Add by Raghvender Singh on 01052014
					if($row['payment_mode'] == 'TDS')
					{
					$total_payment_tds_amount = $total_payment_tds_amount + str_replace(",","",$row['amount']);
					}
					//Add by Raghvender Singh on 01052014
                }
            }
		$percentage = $adb->query("SELECT percentage FROM vtiger_inventorytaxinfo WHERE taxlabel = 'TDS'");
				$percentage_TDS = $adb->num_rows($percentage); 
	
		$total_package_per_sold_amount = (($total_package_sold_amount*$percentage_TDS)/100);
		if($total_payment_tds_amount <= $total_package_per_sold_amount){
				$tds_amount_match = 'yes';				
			}
			else{
				$tds_amount_match = 'no';
			}
		
		$total_package_sold_amount = floor($total_package_sold_amount);
			$total_payment_amount = floor($total_payment_amount);
			$difference_amount = '';
			if($total_package_sold_amount == $total_payment_amount){
				$difference_amount = 'yes';
				}
			else{
				$difference_amount = 'no';
			}	
			
	$app_qry = $adb->query("SELECT payment_mode_app FROM vtiger_potentialscf WHERE potentialid = ".$recordId);

				if($adb->num_rows($app_qry) > 0) {
					while($row = $adb->fetch_array($app_qry)) {
						$payment_mode_app = $row['payment_mode_app'];
					}
				}			
			if($payment_mode_app == 'Credit'){
				$payment_mode_app = 'no';
				}
			else{
				$payment_mode_app = 'Yes';
			}
			
			
			//End Add by Raghvender Singh on 30042014
        
					
$str = $difference_amount.'*'.$payment_mode_app.'*'.$tds_amount_match;
echo $str;
?> 