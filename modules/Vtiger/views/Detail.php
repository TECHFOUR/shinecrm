<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_Detail_View extends Vtiger_Index_View {
    protected $record = false;

    function __construct() {
        parent::__construct();
        $this->exposeMethod('showDetailViewByMode');
        $this->exposeMethod('showModuleDetailView');
        $this->exposeMethod('showModuleSummaryView');
        $this->exposeMethod('showModuleBasicView');
        $this->exposeMethod('showRecentActivities');
        $this->exposeMethod('showRecentComments');
        $this->exposeMethod('showRelatedList');
        $this->exposeMethod('showChildComments');
        $this->exposeMethod('showAllComments');
        $this->exposeMethod('getActivities');
    }

    function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');

        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordId);
        if(!$recordPermission) {
            throw new AppException('LBL_PERMISSION_DENIED');
        }
        return true;
    }

    function preProcess(Vtiger_Request $request, $display=true) {
        parent::preProcess($request, false);

        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $summaryInfo = array();
        // Take first block information as summary information
        $stucturedValues = $recordStrucure->getStructure();
        foreach($stucturedValues as $blockLabel=>$fieldList) {
            $summaryInfo[$blockLabel] = $fieldList;
            break;
        }

        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);

        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $navigationInfo = ListViewSession::getListViewNavigation($recordId);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('NAVIGATION', $navigationInfo);

        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $found = false;
        if ($navigationInfo) {
            foreach($navigationInfo as $page=>$pageInfo) {
                foreach($pageInfo as $index=>$record) {
                    //If record found then next record in the interation
                    //will be next record
                    if($found) {
                        $nextRecordId = $record;
                        break;
                    }
                    if($record == $recordId) {
                        $found = true;
                    }
                    //If record not found then we are assiging previousRecordId
                    //assuming next record will get matched
                    if(!$found) {
                        $prevRecordId = $record;
                    }
                }
                //if record is found and next record is not calculated we need to perform iteration
                if($found && !empty($nextRecordId)) {
                    break;
                }
            }
        }

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if(!empty($prevRecordId)) {
            $viewer->assign('PREVIOUS_RECORD_URL', $moduleModel->getDetailViewUrl($prevRecordId));
        }
        if(!empty($nextRecordId)) {
            $viewer->assign('NEXT_RECORD_URL', $moduleModel->getDetailViewUrl($nextRecordId));
        }

        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('LINK_RECORD', $_REQUEST['record']);





        $viewer->assign('IS_EDITABLE', $this->record->getRecord()->isEditable($moduleName));
        $viewer->assign('IS_DELETABLE', $this->record->getRecord()->isDeletable($moduleName));

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
        $linkModels = $this->record->getSideBarLinks($linkParams);
        $viewer->assign('QUICK_LINKS', $linkModels);

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $viewer->assign('DEFAULT_RECORD_VIEW', $currentUserModel->get('default_record_view'));

        if($display) {
            $this->preProcessDisplay($request);
        }
    }

    function preProcessTplName(Vtiger_Request $request) {
        global $adb;

        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        if($moduleName == "Potentials") {
            $total_payment_amount = 0.0;

            $total_package_sold_amount = 0.00;
            $packagesold_qry = $adb->query("SELECT ps_total_amount FROM vtiger_campaign
    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
    INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
    INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
    INNER JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_campaignscf.pref_leads
    WHERE vtiger_crmentity.deleted = 0 AND campaigntype != 'SMART JOBS' AND vtiger_potentialscf.potentialid = $recordId
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
//Add by Raghvender Singh on 03052014
		$related_module = $_REQUEST['relatedModule'];
		$related_mode = $_REQUEST['mode'];
	
		//End Add by Raghvender Singh on 03052014
		//End Add by Raghvender Singh on 03052014
		$viewer->assign('RELATED_MODULE', $related_module);
		$viewer->assign('RELATED_MODE', $related_mode);
		//End Add by Raghvender Singh on 03052014			

            $viewer->assign('TOTAL_PACKAGE_SOLD_AMOUNT',number_format($total_package_sold_amount,2));
            $viewer->assign('TOTAL_PAYMENT_AMOUNT',number_format($total_payment_amount,2));
			$percentage = $adb->query("SELECT percentage FROM vtiger_inventorytaxinfo WHERE taxlabel = 'TDS'");
				$percentage_TDS = $adb->num_rows($percentage); 
	
		$total_package_per_sold_amount = (($total_package_sold_amount*$percentage_TDS)/100);
		if($total_payment_tds_amount <= $total_package_per_sold_amount){
				$tds_amount_match = 'yes';				
			}
			else{
				$tds_amount_match = 'no';
			}
			
			// Code added by jitendra singh on 2 May 2014
			
			$document_qry = $adb->query("SELECT * FROM vtiger_potential
			INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
			INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.crmid = vtiger_potential.potentialid 
			INNER JOIN vtiger_notes ON vtiger_notes.notesid = vtiger_senotesrel.notesid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.potentialid =".$recordId);
			
			$payment_qry = $adb->query("SELECT payment_mode_app FROM vtiger_potentialscf WHERE potentialid = ".$recordId);

				if($adb->num_rows($payment_qry) > 0) {
					while($row = $adb->fetch_array($payment_qry)) {
						$payment_mode_val = $row['payment_mode_app'];
					}
				}
				
			if($adb->num_rows($document_qry) > 0 || $payment_mode_val != 'Credit'){
				$payment_mode_app = 'yes';
				}
			else{
				$payment_mode_app = 'no';
			}
			//End
			
		
			//Add by Raghvender Singh on 01052014
			/*			$app_qry = $adb->query("SELECT payment_mode_app FROM vtiger_potentialscf WHERE potentialid = ".$recordId);

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
			}*/
			
			//Add by Raghvender Singh on 01052014
			
			$total_package_sold_amount = floor(number_format($total_package_sold_amount,2));
			$total_payment_amount = floor(number_format($total_payment_amount,2));
			$difference_amount = '';
			if($total_package_sold_amount == $total_payment_amount){
				$difference_amount = 'yes';
				}
			else{
				$difference_amount = 'no';
			}	
			
			$viewer->assign('TDS_AMOUNT_MATCH',$tds_amount_match);
			$viewer->assign('PAYMENT_MODE_APPROVAL',$payment_mode_app);
			$viewer->assign('DIFFERENCE_AMOUNT',$difference_amount);
			$viewer->assign('APP_RECORD_ID',$recordId);
			
			$add_sales_approval = 'Add Sales Approval'; 
			$viewer->assign('ADD_SALES_APPROVAL',$add_sales_approval);
			
			
			//End Add by Raghvender Singh on 01052014
        }
        return 'DetailViewPreProcess.tpl';
    }

    function process(Vtiger_Request $request) {

        /*Start code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/
        global $adb,$current_user;
        $Branch = 	$current_user->department;
        $Account_Manager_Email = $current_user->email1;
        $Account_Manager_Contact = $current_user->phone_mobile;
        $BTL =	$current_user->phone_home;
        $BSM =	$current_user->phone_other;
        $TEAM = $current_user->title;

        /*Start to find the contact detail to show in left tab by jitendra sigh on 27 march 2014*/

        $contact_qry = $adb->query("SELECT * FROM vtiger_crmentityrel
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity .crmid = vtiger_crmentityrel .crmid
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentityrel .crmid
                        INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
                        WHERE relcrmid = ".$_REQUEST['record']."");
        if($adb->num_rows($contact_qry) > 0) {
            while($row = $adb->fetch_array($contact_qry)){
                $client_id = $row['contact_no'];
                $name_of_client = $row['cf_927'];
                $type_of_client = $row['leadsource'];
                $contct_person = $row['cf_815'];
                $mobile_no = $row['cf_823'];
                $city = $row['cf_835'];
                $contact_id = $row['contactsubscriptionid'];
            }
        }
        /*Start to find the contact detail to show in left tab by jitendra sigh on 27 march 2014*/
		
		
					// Start Query for branch
		$branch_qry = $adb->query("SELECT branch_city
                        from vtiger_users 
                        INNER JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_users.branch where vtiger_users.id = ".$current_user->id."");
						
		 if($adb->num_rows($branch_qry) > 0) {
           while($row = $adb->fetch_array($branch_qry)){
                $Branch = $row['branch_city'];
		   }
		 }
		//End
		
		
		// Start Query for BTL 
		$btl_qry = $adb->query("SELECT b.first_name as fname,b.last_name as lname ,b.id as userid
                        from vtiger_users as a
                        INNER JOIN vtiger_users as b ON a.reports_to_id = b.id where a.id = '".$current_user->id."'");
						
		 if($adb->num_rows($btl_qry) > 0) {
           while($row = $adb->fetch_array($btl_qry)){
                $BTL = $row['fname'].' '.$row['lname'];
				$userid = $row['userid'];
		   }
		 }
		//End
		
		// Start Query for BSM 
		$bsb_qry = $adb->query("SELECT c.first_name as fname,c.last_name as lname
                        from vtiger_users as a
                        INNER JOIN vtiger_users as b ON a.reports_to_id = b.id 
						LEFT JOIN vtiger_users as c ON b.reports_to_id = c.id 
						where b.id = '".$userid."'");
						
		 if($adb->num_rows($bsb_qry) > 0) {
           while($row = $adb->fetch_array($bsb_qry)){
                $BSM = $row['fname'].' '.$row['lname'];
		   }
		 }
		//End

        $viewer = $this->getViewer($request);

        $viewer->assign('Branch', $Branch);
        $viewer->assign('Account_Manager_Email', $Account_Manager_Email);
        $viewer->assign('Account_Manager_Contact', $Account_Manager_Contact);
        $viewer->assign('BTL', $BTL);
        $viewer->assign('BSM', $BSM);
        $viewer->assign('TEAM', $TEAM);
		
		// Start for post call update msg ..when page uploded by Raghvender Singh on 02052014
		$product_id = $_REQUEST['record'];
		$activestatus_new = $adb->query("SELECT cf_805, eventstatus FROM vtiger_contactdetails
		 INNER JOIN  vtiger_crmentityrel ON vtiger_crmentityrel.crmid = vtiger_contactdetails.contactid 
  		INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_crmentityrel.relcrmid
  		INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
  		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
		WHERE eventstatus = 'Held' AND vtiger_crmentity.deleted = 0 AND vtiger_contactdetails.contactid = ".$product_id);
 			$count2 = 0;
			if($adb->num_rows($activestatus_new) > 0) {
        	    while($row = $adb->fetch_array($activestatus_new)){
					$activitystatus_check = $row['cf_805'];
					$eventstatus_check = $row['eventstatus'];
					$count2++;
            	}	
			}
			$viewer->assign('ACTIVITYSTATUS_CHECK',$activitystatus_check);
			$viewer->assign('EVENTSTATUS_CHECK',$eventstatus_check);
			$viewer->assign('EVENTSTATUS_COUNT',$count2);
		// End Start for post call update msg ..when page uploded by Raghvender Singh on 02052014
	
	// Start for fetching activity status by Raghvender Singh on 01052014
		$product_id = $_REQUEST['record'];
		$contact_qry1 = $adb->query("SELECT cf_805 FROM vtiger_contactscf
WHERE cf_805 <> '' AND vtiger_contactscf.contactid = ".$product_id);
			$activitystatus = $adb->num_rows($contact_qry1);
			
				if($activitystatus >0)
				$post_call_update = 'yes';
				else
				$post_call_update = 'no';
				
			
			$leadcheck = $adb->query("SELECT cf_913 FROM vtiger_contactscf 
INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.crmid = vtiger_contactscf.contactid
INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_crmentityrel.relcrmid WHERE relmodule = 'Leads' AND vtiger_leadscf.cf_913 = 'Deal Won – 100%' AND contactid =".$product_id);

			$leadstatus = $adb->num_rows($leadcheck);
				if($leadstatus >0)
				$lead_won = 'yes';
				else
				$lead_won = 'no';
				
		$todaydate = date("Y-m-d");	
		$date_validation = 'yes';
			
		$datecheck = $adb->query("SELECT * FROM vtiger_contactscf 
INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.crmid = vtiger_contactscf.contactid
INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_crmentityrel.relcrmid WHERE relmodule = 'Leads' AND vtiger_leadscf.cf_913 = 'Deal Won – 100%' AND contactid =".$product_id);
		
			if($adb->num_rows($datecheck) > 0) {
        	    while($row = $adb->fetch_array($datecheck)){
					$lead_date = $row['cf_911'];
					if($lead_date < $todaydate){
						$date_validation = 'no';
					break;	
					}
					
            	}	
			}
			
			$viewer->assign('RECORDID',$product_id);
			$viewer->assign('post_call_update',$post_call_update );
			$viewer->assign('lead_won',$lead_won );
			$viewer->assign('date_validation',$date_validation );
		// End Starts for sales approvel check by Raghvender singh on 01052014
        // Customer related data
        $viewer->assign('client_id',$client_id );
        $viewer->assign('name_of_client',$name_of_client );
        $viewer->assign('type_of_client',$type_of_client );
        $viewer->assign('contct_person',$contct_person );
        $viewer->assign('mobile_no',$mobile_no );
        $viewer->assign('city',$city );
        $viewer->assign('contact_id',$contact_id );

// start for fetching Packagesold and Payment details .....................
        if($_REQUEST['module'] == 'Potentials'){
            $RELATED_PACKAGESOLD = array();
            $RELATED_PAYMENTS = array();
            $recordid = $_REQUEST['record'];
            $total_package_sold_amount = 0.00;
            $packagesold_qry = $adb->query("SELECT vtiger_leaddetails.lead_no as leadno , vtiger_campaign.*, vtiger_campaignscf.* FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                            LEFT JOIN vtiger_leaddetails ON vtiger_leaddetails.leadid = vtiger_campaignscf.pref_leads
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordid
                            ORDER BY campaigntype, vtiger_crmentity.crmid ");
            if($adb->num_rows($packagesold_qry) > 0) {
                $i = 1;
                while($row = $adb->fetchByAssoc($packagesold_qry)) {
                    $RELATED_PACKAGESOLD[$i]['package_master'] = $row['campaigntype'];
                    $RELATED_PACKAGESOLD[$i]['eventid'] = $row['relentityid'];
                    $RELATED_PACKAGESOLD[$i]['product_event'] = $row['leadno'];//Product

                    /*start Education ********/
                    $RELATED_PACKAGESOLD[$i]['edu_product_type_education'] = $row['edu_product_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['edu_no_cmp_education'] = $row['edu_no_cmp']; //Month
                    /*end Education ********/
                    /*start Database ********/
                    $RELATED_PACKAGESOLD[$i]['geography_database'] = $row['db_geography'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['database_it'] = $row['db_non_it'];//Sponsorship
                    $RELATED_PACKAGESOLD[$i]['database_limit'] = $row['db_limits']; //Month
                    $RELATED_PACKAGESOLD[$i]['database_month'] = $row['db_dur_months']; //MRP
                    $RELATED_PACKAGESOLD[$i]['db_upsell_exl'] = $row['db_upsell_exl']; //Rest of India BP
                    $RELATED_PACKAGESOLD[$i]['db_upsell_word'] = $row['db_upsell_word'];
                    $RELATED_PACKAGESOLD[$i]['db_upsell_email'] = $row['db_upsell_email'];
                    $RELATED_PACKAGESOLD[$i]['db_upsell_login'] = $row['db_upsell_login'];
                    $RELATED_PACKAGESOLD[$i]['db_dwnsell_exl'] = $row['db_dwnsell_exl'];
                    $RELATED_PACKAGESOLD[$i]['db_dwnsell_word'] = $row['db_dwnsell_word'];
                    $RELATED_PACKAGESOLD[$i]['db_dwnsell_email'] = $row['db_dwnsell_email'];
                    /*end Database ********/
                    /*start FLEXIHIRE ********/
                    $RELATED_PACKAGESOLD[$i]['elite_mgeography'] = $row['elite_mgeography'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['elite_maccess'] = $row['elite_maccess'];//Sponsorship
                    $RELATED_PACKAGESOLD[$i]['elite_mduration'] = $row['elite_mduration']; //Month
                    /*end FLEXIHIRE ********/

                    /*start Inventory ********/
                    $RELATED_PACKAGESOLD[$i]['exp_product_type'] = $row['exp_product_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['exp_tg_db'] = $row['exp_tg_db'];//Sponsorship
                    $RELATED_PACKAGESOLD[$i]['exp_active'] = $row['exp_active'];//Sponsorship
                    $RELATED_PACKAGESOLD[$i]['noofemailer'] = $row['campaignname']; //MRP
                    /*end Inventory ********/
					
					/*start Print ********/
					$RELATED_PACKAGESOLD[$i]['print_product_type'] = $row['print_product_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['print_size'] = $row['print_size']; //Month
					/*end Print ********/
					
					/*start logo ********/
					$RELATED_PACKAGESOLD[$i]['logo_product_type'] = $row['logo_product_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['month'] = $row['logo_month']; //Month
                	/*end logo ********/
					
					
					/*start EMSHINEVERIFIED *****/
					$RELATED_PACKAGESOLD[$i]['elite_mproduct_type'] = $row['elite_mproduct_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['elite_no_walk'] = $row['elite_no_walk'];//Sponsorship
                    $RELATED_PACKAGESOLD[$i]['elite_re_details'] = $row['elite_re_details']; //Month
					/*end EMSHINEVERIFIED *****/
					
					/*smartmatch start ******/
					
					$RELATED_PACKAGESOLD[$i]['smartmatchid'] = $row['relentityid'];
                    $RELATED_PACKAGESOLD[$i]['product'] = $row['pref_leads'];//Product
                    $RELATED_PACKAGESOLD[$i]['match_dura_month'] = $row['match_dura_month'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['match_no_jobs'] = $row['match_no_jobs']; //Month
                   /*smartmatch end *******/

                   /*smartjob start ******/
                   $RELATED_PACKAGESOLD[$i]['smartjobsid'] = $row['relentityid'];                  
                   $RELATED_PACKAGESOLD[$i]['sjobs_product_type'] = $row['sjobs_product_type'];//Product Type
                   $RELATED_PACKAGESOLD[$i]['sjobs_no_jobs'] = $row['sjobs_no_jobs']; //Month
                   /*smartjob end ******/
                    
					/*start Events ********/
                    $RELATED_PACKAGESOLD[$i]['comment_product_type_event'] = $row['comment_product_type'];//Product Type
                    $RELATED_PACKAGESOLD[$i]['month_event'] = $row['budgetcost']; //Month
                    $RELATED_PACKAGESOLD[$i]['campaignstatus_event'] = $row['campaignstatus'];//Sponsorship
                    /*end Events ********/

                    $RELATED_PACKAGESOLD[$i]['product_bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                    $RELATED_PACKAGESOLD[$i]['product_mrp'] = $row['product_mrp']; //MRP
                    $RELATED_PACKAGESOLD[$i]['ps_discount'] = $row['ps_discount'];
                    $RELATED_PACKAGESOLD[$i]['ps_discount_amount'] = $row['ps_discount_amount'];
                    $RELATED_PACKAGESOLD[$i]['ps_offered_amount'] = $row['ps_offered_amount'];
                    $RELATED_PACKAGESOLD[$i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                    $RELATED_PACKAGESOLD[$i]['ps_total_amount'] = $row['ps_total_amount'];
                    $total_package_sold_amount = $total_package_sold_amount + str_replace(",","",$row['ps_total_amount']);
                    $i++;
                }
            }

            $total_payment_amount = 0.0;
            $payment_qry = $adb->query("SELECT payment_mode,checkdate,checkno,ro_available,bank_name,amount,onlinemode,
							slip_number,tan_no,cts FROM vtiger_project
                            INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_project.projectid AND vtiger_crmentityrel.relmodule = 'Project')
                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordid ");
            if($adb->num_rows($payment_qry) > 0) {
                $i = 1;
                while($row = $adb->fetchByAssoc($payment_qry)) {

                    $RELATED_PAYMENTS[$i]['payment_mode'] = $row['payment_mode'];
                    $RELATED_PAYMENTS[$i]['checkdate'] = $row['checkdate'];
                    $RELATED_PAYMENTS[$i]['checkno'] = $row['checkno'];
                    $RELATED_PAYMENTS[$i]['ro_available'] = $row['ro_available'];
                    $RELATED_PAYMENTS[$i]['bank_name'] = $row['bank_name'];
                    $RELATED_PAYMENTS[$i]['amount'] = number_format($row['amount'],2);

                    $RELATED_PAYMENTS[$i]['onlinemode'] = $row['onlinemode'];
                    $RELATED_PAYMENTS[$i]['slip_number'] = $row['slip_number'];
                    $RELATED_PAYMENTS[$i]['tan_no'] = $row['tan_no'];
                    $RELATED_PAYMENTS[$i]['cts'] = $row['cts'];
                    $total_payment_amount = $total_payment_amount + str_replace(",","",$row['amount']);
                    $i++;
                }
            }
            //echo "<pre>";print_r($RELATED_PACKAGESOLD);die;
            $viewer->assign('TOTAL_PACKAGE_SOLD_AMOUNT',number_format($total_package_sold_amount,2));
            $viewer->assign('TOTAL_PAYMENT_AMOUNT',number_format($total_payment_amount,2));
            $viewer->assign('RELATED_PACKAGESOLD',$RELATED_PACKAGESOLD);
            $viewer->assign('RELATED_PAYMENTS',$RELATED_PAYMENTS);
        }
// end for fetching Packagesold and Payment details .....................

        /*End code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/


        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            return;
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        if ($currentUserModel->get('default_record_view') === 'Summary') {
            echo $this->showModuleBasicView($request);
        } else {
            echo $this->showModuleDetailView($request);
        }


    }

    public function postProcess(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

        $selectedTabLabel = $request->get('tab_label');

        if(empty($selectedTabLabel)) {
            if($currentUserModel->get('default_record_view') === 'Detail') {
                $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
            } else{
                if($moduleModel->isSummaryViewSupported()) {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_SUMMARY', $moduleName);
                } else {
                    $selectedTabLabel = vtranslate('SINGLE_'.$moduleName, $moduleName).' '. vtranslate('LBL_DETAILS', $moduleName);
                }
            }
        }

        $viewer = $this->getViewer($request);

        $viewer->assign('SELECTED_TAB_LABEL', $selectedTabLabel);
        $viewer->assign('MODULE_MODEL', $this->record->getModule());
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

        //$viewer->view('DetailViewPostProcess.tpl', $moduleName);
// Add by Raghvender Singh on 03052014
		if($moduleName == 'Leads'){			
			}
		else{
		   $viewer->view('DetailViewPostProcess.tpl', $moduleName);
		}
//End Add by Raghvender Singh on 03052014	
        parent::postProcess($request);
    }


    public function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();

        $jsFileNames = array(
            'modules.Vtiger.resources.Detail',
            "modules.$moduleName.resources.Detail",
            'modules.Vtiger.resources.RelatedList',
            "modules.$moduleName.resources.RelatedList",
            'libraries.jquery.jquery_windowmsg',
            "libraries.jquery.ckeditor.ckeditor",
            "libraries.jquery.ckeditor.adapters.jquery",
            "modules.Emails.resources.MassEdit",
            "modules.Vtiger.resources.CkEditor",
        );

        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    function showDetailViewByMode($request) {
        $requestMode = $request->get('requestMode');
        if($requestMode == 'full') {
            return $this->showModuleDetailView($request);
        }
        return $this->showModuleBasicView($request);
    }

    /**
     * Function shows the entire detail for the record
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showModuleDetailView(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));

        return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
    }

    function showModuleSummaryView($request) {
        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);

        $moduleModel = $recordModel->getModule();
        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('RELATED_ACTIVITIES', $this->getActivities($request));

        return $viewer->view('ModuleSummaryView.tpl', $moduleName, true);
    }

    /**
     * Function shows basic detail for the record
     * @param <type> $request
     */
    function showModuleBasicView($request) {

        $recordId = $request->get('record');
        $moduleName = $request->getModule();

        if(!$this->record){
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();

        $detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_SUMMARY', $this->showModuleSummaryView($request));

        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
        $viewer->assign('MODULE_NAME', $moduleName);

        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
        $structuredValues = $recordStrucure->getStructure();

        $moduleModel = $recordModel->getModule();

        $viewer->assign('RECORD_STRUCTURE', $structuredValues);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());

        echo $viewer->view('DetailViewSummaryContents.tpl', $moduleName, true);
    }

    /**
     * Function returns recent changes made on the record
     * @param Vtiger_Request $request
     */
    function showRecentActivities (Vtiger_Request $request) {
        $parentRecordId = $request->get('record');
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();

        if(empty($pageNumber)) {
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel);
        $pagingModel->calculatePageRange($recentActivities);

        $viewer = $this->getViewer($request);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        echo $viewer->view('RecentActivities.tpl', $moduleName, 'true');
    }

    /**
     * Function returns latest comments
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showRecentComments(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();

        if(empty($pageNumber)) {
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentComments = ModComments_Record_Model::getRecentComments($parentId, $pagingModel);
        $pagingModel->calculatePageRange($recentComments);
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $viewer = $this->getViewer($request);
        $viewer->assign('COMMENTS', $recentComments);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('RecentComments.tpl', $moduleName, 'true');
    }

    /**
     * Function returns related records
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showRelatedList(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $targetControllerClass = null;

        // Added to support related list view from the related module, rather than the base module.
        try {
            $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'In'.$moduleName.'Relation', $relatedModuleName);
        }catch(AppException $e) {
            try {
                // If any module wants to have same view for all the relation, then invoke this.
                $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'InRelation', $relatedModuleName);
            }catch(AppException $e) {
                // Default related list
                $targetControllerClass = Vtiger_Loader::getComponentClassName('View', 'RelatedList', $moduleName);
            }
        }
        if($targetControllerClass) {
            $targetController = new $targetControllerClass();
            return $targetController->process($request);
        }
    }

    /**
     * Function sends the child comments for a comment
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showChildComments(Vtiger_Request $request) {
        $parentCommentId = $request->get('commentid');
        $parentCommentModel = ModComments_Record_Model::getInstanceById($parentCommentId);
        $childComments = $parentCommentModel->getChildComments();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $viewer = $this->getViewer($request);
        $viewer->assign('PARENT_COMMENTS', $childComments);
        $viewer->assign('CURRENTUSER', $currentUserModel);

        return $viewer->view('CommentsList.tpl', $moduleName, 'true');
    }

    /**
     * Function sends all the comments for a parent(Accounts, Contacts etc)
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showAllComments(Vtiger_Request $request) {
        $parentRecordId = $request->get('record');
        $commentRecordId = $request->get('commentid');
        $moduleName = $request->getModule();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();

        $parentCommentModels = ModComments_Record_Model::getAllParentComments($parentRecordId);

        if(!empty($commentRecordId)) {
            $currentCommentModel = ModComments_Record_Model::getInstanceById($commentRecordId);
        }

        $viewer = $this->getViewer($request);
        $viewer->assign('CURRENTUSER', $currentUserModel);
        $viewer->assign('PARENT_COMMENTS', $parentCommentModels);
        $viewer->assign('CURRENT_COMMENT', $currentCommentModel);

        return $viewer->view('ShowAllComments.tpl', $moduleName, 'true');
    }
    /**
     * Function to get Ajax is enabled or not
     * @param Vtiger_Record_Model record model
     * @return <boolean> true/false
     */
    function isAjaxEnabled($recordModel) {
        return $recordModel->isEditable();
    }

    /**
     * Function to get activities
     * @param Vtiger_Request $request
     * @return <List of activity models>
     */
    public function getActivities(Vtiger_Request $request) {
        return '';
    }


    /**
     * Function returns related records based on related moduleName
     * @param Vtiger_Request $request
     * @return <type>
     */
    function showRelatedRecords(Vtiger_Request $request) {
        $parentId = $request->get('record');
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $relatedModuleName = $request->get('relatedModule');
        $moduleName = $request->getModule();

        if(empty($pageNumber)) {
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if(!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName);
        $models = $relationListView->getEntries($pagingModel);
        $header = $relationListView->getHeaders();

        $viewer = $this->getViewer($request);
        $viewer->assign('MODULE' , $moduleName);
        $viewer->assign('RELATED_RECORDS' , $models);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE' , $relatedModuleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);

        return $viewer->view('SummaryWidgets.tpl', $moduleName, 'true');
    }
}