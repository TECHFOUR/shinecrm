<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Calendar_Edit_View extends Vtiger_Edit_View {

	function __construct() {
		parent::__construct();
		$this->exposeMethod('Events');
		$this->exposeMethod('Calendar');
	}

	function process(Vtiger_Request $request) {
		$mode = $request->getMode();

		$recordId = $request->get('record');
		if(!empty($recordId)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($recordId);
			$mode = $recordModel->getType();
		}

		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request, $mode);
			return;
		}
		$this->Calendar($request, 'Calendar');
	}

	function Events($request, $moduleName) {


        global $adb;
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
		$viewer = $this->getViewer ($request);
		$record = $request->get('record');

		 if(!empty($record) && $request->get('isDuplicate') == true) {
			$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', '');
		}else if(!empty($record)) {
			$recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
			$viewer->assign('MODE', 'edit');
			$viewer->assign('RECORD_ID', $record);
		} else {
			$recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
			$viewer->assign('MODE', '');
		}
		$eventModule = Vtiger_Module_Model::getInstance($moduleName);
		$recordModel->setModuleFromInstance($eventModule);

		$moduleModel = $recordModel->getModule();
		$fieldList = $moduleModel->getFields();
		$requestFieldList = array_intersect_key($request->getAll(), $fieldList);

		foreach($requestFieldList as $fieldName=>$fieldValue){
			$fieldModel = $fieldList[$fieldName];
			$specialField = false;
			// We collate date and time part together in the EditView UI handling 
			// so a bit of special treatment is required if we come from QuickCreate 
			if (empty($record) && ($fieldName == 'time_start' || $fieldName == 'time_end') && !empty($fieldValue)) { 
				$specialField = true; 
				// Convert the incoming user-picked time to GMT time 
				// which will get re-translated based on user-time zone on EditForm 
				$fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i"); 
			} 
            if (empty($record) && ($fieldName == 'date_start' || $fieldName == 'due_date') && !empty($fieldValue)) { 
                if($fieldName == 'date_start'){
                    $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                    $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                    list($startDate, $startTime) = explode(' ', $startDateTime);
                    $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
                }else{
                    $endTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_end']);
                    $endDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$endTime);
                    list($endDate, $endTime) = explode(' ', $endDateTime);
                    $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($endDate);
                }
            }
            
			if($fieldModel->isEditable() || $specialField) { 
				$recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
			}
		}
		$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel,
									Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);

		$viewMode = $request->get('view_mode');
		if(!empty($viewMode)) {
			$viewer->assign('VIEW_MODE', $viewMode);
		}
		
		//If followup value is passed from request to process the value and sent to client
		$requestFollowUpDate = $request->get('followup_date_start');
		$requestFollowUpTime = $request->get('followup_time_start');
		$followUpStatus = $request->get('followup');
		$eventStatus = $request->get('eventstatus');
		
		if(!empty($requestFollowUpDate)){
			$followUpDate = $requestFollowUpDate;
		}
		if(!empty($requestFollowUpTime)){
			$followUpTime = $requestFollowUpTime;
		}
		if($followUpStatus == 'on'){
			$viewer->assign('FOLLOW_UP_STATUS',TRUE);
		}
		if($eventStatus == 'Held'){
			$viewer->assign('SHOW_FOLLOW_UP',TRUE);
		}else{
			$viewer->assign('SHOW_FOLLOW_UP',FALSE);
		}

        /*Start code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/
        global $adb,$current_user;
        $Branch = 	$current_user->department;
        $Account_Manager_Email = $current_user->email1;
        $Account_Manager_Contact = $current_user->phone_mobile;
        $BTL =	$current_user->phone_home;
        $BSM =	$current_user->phone_other;
        $TEAM = $current_user->title;

        /*Start to find the contact detail to show in left tab by jitendra sigh on 27 march 2014*/

       if($_REQUEST['sourceRecord'] != ''){

        $contact_qry = $adb->query("SELECT *
                        from vtiger_contactdetails
                        INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
                        WHERE vtiger_contactdetails.contactid = ".$_REQUEST['sourceRecord']."");
        }else{
            $contact_qry = $adb->query("SELECT * FROM vtiger_crmentityrel
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity .crmid = vtiger_crmentityrel .crmid
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentityrel .crmid
                        INNER JOIN vtiger_contactsubdetails ON vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
                        WHERE relcrmid = ".$_REQUEST['record']."");

        }
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

        $viewer->assign('Branch', $Branch);
        $viewer->assign('Account_Manager_Email', $Account_Manager_Email);
        $viewer->assign('Account_Manager_Contact', $Account_Manager_Contact);
        $viewer->assign('BTL', $BTL);
        $viewer->assign('BSM', $BSM);
        $viewer->assign('TEAM', $TEAM);

        // Customer related data
        $viewer->assign('client_id',$client_id );
        $viewer->assign('name_of_client',$name_of_client );
        $viewer->assign('type_of_client',$type_of_client );
        $viewer->assign('contct_person',$contct_person );
        $viewer->assign('mobile_no',$mobile_no );
        $viewer->assign('city',$city );
        $viewer->assign('contact_id',$contact_id );




        /*End code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/



        $viewer->assign('FOLLOW_UP_DATE',$followUpDate);
		$viewer->assign('FOLLOW_UP_TIME',$followUpTime);
		$viewer->assign('RECURRING_INFORMATION', $recordModel->getRecurrenceInformation());
		$viewer->assign('TOMORROWDATE', Vtiger_Date_UIType::getDisplayDateValue(date('Y-m-d', time()+86400)));
		
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
		$viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());

		$viewer->assign('MODULE', $moduleName);
		$viewer->assign('CURRENTDATE', date('Y-n-j'));
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$existingRelatedContacts = $recordModel->getRelatedContactInfo();

		//To add contact ids that is there in the request . Happens in gotoFull form mode of quick create
		$requestContactIdValue = $request->get('contact_id');
		if(!empty($requestContactIdValue)) {
			$existingRelatedContacts[] = array('name' => Vtiger_Util_Helper::getRecordName($requestContactIdValue) ,'id' => $requestContactIdValue);
		}
		
        $viewer->assign('RELATED_CONTACTS', $existingRelatedContacts);

		$isRelationOperation = $request->get('relationOperation');
        // start added by ajay for dynamic lead form in edit form
            if($moduleName == "Events" && $existingRelatedContacts[0]['id'] != "") {
                $RELATED_PRODUCTS = array();
                $contactid = $existingRelatedContacts[0]['id'];
                $lead_qry = $adb->query("SELECT vtiger_leaddetails.leadid as leadid, cf_907, cf_909, cf_911, cf_913, cf_915 FROM vtiger_leaddetails
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_leaddetails.leadid AND vtiger_crmentityrel.relmodule = 'Leads' )
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
                            INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_leaddetails.leadid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentityrel.crmid = $contactid AND cf_913 NOT IN('Deal Won – 100%', 'Deal Lost – 0%')");
                if($adb->num_rows($lead_qry) > 0) {
                    $i = 1;
                    while($row = $adb->fetch_array($lead_qry)){
                        $RELATED_PRODUCTS[$i]['product'] = $row['cf_907'];
                        $RELATED_PRODUCTS[$i]['expected_revenue'] = $row['cf_909'];
                        $RELATED_PRODUCTS[$i]['expected_closure_date'] = $row['cf_911'];
                        $RELATED_PRODUCTS[$i]['lead_stage'] = $row['cf_913'];
                        $RELATED_PRODUCTS[$i]['remarks'] = $row['cf_915'];
                        $RELATED_PRODUCTS[$i]['leadid'] = $row['leadid'];
                        $i++;
                    }
                }

                $lead_stage_qry = $adb->query("SELECT cf_901 FROM vtiger_cf_901 WHERE presence = 1 ORDER BY cf_901 ");
                $LEAD_PRODUCTS = array();
                if($adb->num_rows($lead_stage_qry) > 0) {
                    while($row = $adb->fetch_array($lead_stage_qry)){
                        $LEAD_PRODUCTS[$row['cf_901']] = $row['cf_901'];
                    }
                }

                $lead_stage_qry = $adb->query("SELECT cf_913 FROM vtiger_cf_913 WHERE presence = 1 ORDER BY cf_913 ");
                $LEAD_STAGE = array();
                if($adb->num_rows($lead_stage_qry) > 0) {
                    while($row = $adb->fetch_array($lead_stage_qry)){
                        $LEAD_STAGE[$row['cf_913']] = $row['cf_913'];
                    }
                }

                $viewer->assign('LEAD_PRODUCTS', $LEAD_PRODUCTS);
                $viewer->assign('LEAD_STAGE', $LEAD_STAGE);
                $viewer->assign('RELATED_PRODUCTS', $RELATED_PRODUCTS);
                $viewer->assign('PRODUCT_PERMISSION', 1);
            }
        // end added by ajay for dynamic lead form in edit form

		//if it is relation edit
		$viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
		if($isRelationOperation) {
			$viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
			$viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
		}
		$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $accessibleUsers = $currentUser->getAccessibleUsers();
		
		$viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
		$viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
        $viewer->assign('INVITIES_SELECTED', $recordModel->getInvities());
        $viewer->assign('CURRENT_USER', $currentUser);
		$viewer->view('EditView.tpl', $moduleName);
	}

	function Calendar($request, $moduleName) {
		parent::process($request);
	}
}