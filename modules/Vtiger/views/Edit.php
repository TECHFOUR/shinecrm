<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Class Vtiger_Edit_View extends Vtiger_Index_View {
    protected $record = false;
    function __construct() {
        parent::__construct();
    }

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $record = $request->get('record');

        $recordPermission = Users_Privileges_Model::isPermitted($moduleName, 'EditView', $record);

        if(!$recordPermission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request) {

        /*Start code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/
        global $adb,$current_user;
        $Branch = 	$current_user->department;
        $Account_Manager_Email = $current_user->email1;
        $Account_Manager_Contact = $current_user->phone_mobile;
        $BTL =	$current_user->phone_home;
        $BSM =	$current_user->phone_other;
        $TEAM = $current_user->title;

        $viewer = $this->getViewer ($request);

        $viewer->assign('Branch', $Branch);
        $viewer->assign('Account_Manager_Email', $Account_Manager_Email);
        $viewer->assign('Account_Manager_Contact', $Account_Manager_Contact);
        $viewer->assign('BTL', $BTL);
        $viewer->assign('BSM', $BSM);
        $viewer->assign('TEAM', $TEAM);

        /*End code to find the current user details to show in left tab by jitendra singh on 21 March 2014*/


        $moduleName = $request->getModule();
        $record = $request->get('record');
        if(!empty($record) && $request->get('isDuplicate') == true) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('MODE', '');
        }else if(!empty($record)) {
            $recordModel = $this->record?$this->record:Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }
        if(!$this->record){
            $this->record = $recordModel;
        }

        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAll(), $fieldList);

        foreach($requestFieldList as $fieldName=>$fieldValue){
            $fieldModel = $fieldList[$fieldName];
            $specialField = false;
            // We collate date and time part together in the EditView UI handling
            // so a bit of special treatment is required if we come from QuickCreate
            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'time_start' && !empty($fieldValue)) {
                $specialField = true;
                // Convert the incoming user-picked time to GMT time
                // which will get re-translated based on user-time zone on EditForm
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");

            }

            if ($moduleName == 'Calendar' && empty($record) && $fieldName == 'date_start' && !empty($fieldValue)) {
                $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList['time_start']);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue." ".$startTime);
                list($startDate, $startTime) = explode(' ', $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);

        $viewer->assign('PICKIST_DEPENDENCY_DATASOURCE',Zend_Json::encode($picklistDependencyDatasource));
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('CURRENTDATE', date('Y-n-j'));
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());

        $isRelationOperation = $request->get('relationOperation');


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


        //if it is relation edit
        $viewer->assign('IS_RELATION_OPERATION', $isRelationOperation);
        if($isRelationOperation) {
            $viewer->assign('SOURCE_MODULE', $request->get('sourceModule'));
            $viewer->assign('SOURCE_RECORD', $request->get('sourceRecord'));
        }

        if($moduleName == "Potentials") {
            $contactid = $request->get('sourceRecord');
            if($contactid == "") {
                $contact_qry = $adb->query("SELECT contactid  FROM vtiger_contactdetails
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.crmid = vtiger_contactdetails.contactid AND vtiger_crmentityrel.relmodule = 'Potentials' )
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentityrel.relcrmid = $record");
                if($adb->num_rows($contact_qry) > 0) {
                    $contact_res = $adb->fetch_array($contact_qry);
                    $contactid = $contact_res['contactid'];
                }
            }

            $unique_Products = array();
            $RELATED_DATABASE_PRODUCTS = array();
            $RELATED_EDUCATION_PRODUCTS = array();
            $RELATED_LOGO_PRODUCTS = array();
            $RELATED_PRINT_PRODUCTS = array();
            $RELATED_INVENTORY_PRODUCTS = array();
            $RELATED_EVENTS_PRODUCTS = array();
            $RELATED_SMARTMATCH_PRODUCTS = array();
            $RELATED_MYPARICHAY_PRODUCTS = array();
            $RELATED_EMSHINEVERIFIED_PRODUCTS = array();
            $RELATED_FLEXIHIRE_PRODUCTS = array();

            $EVENTS_PRODUCTS = array();
            $LOGO_PRODUCTS = array();
            $EDUCATION_PRODUCTS = array();
            $DATABASE_PRODUCTS = array();
            $INVENTORY_PRODUCTS = array();
            $PRINT_PRODUCTS = array();
            $FLEXIHIRE_PRODUCTS = array();
            $EMSHINEVERIFIED_PRODUCTS = array();
            $SMARTMATCH_PRODUCTS = array();

            if($record == "")
                $sub_query = "AND vtiger_leadscf.converted = '0' ";
            else{
                $leadid = '';
                $packagesod_qry = $adb->query("SELECT pref_leads FROM vtiger_campaignscf
                                          INNER JOIN vtiger_crmentityrel
                                            ON (
                                            vtiger_crmentityrel.relcrmid = vtiger_campaignscf.campaignid
                                            AND vtiger_crmentityrel.relmodule = 'Campaigns'
                                            )
                                          INNER JOIN vtiger_campaign ON vtiger_campaign.campaignid = vtiger_campaignscf.campaignid
                                          INNER JOIN vtiger_crmentity
                                            ON vtiger_crmentity.crmid = vtiger_campaignscf.campaignid
                                            WHERE vtiger_crmentity.deleted = 0 AND campaigntype <> 'SMART JOBS' AND vtiger_crmentityrel.crmid = $record");
                if($adb->num_rows($packagesod_qry) > 0) {
                    $i = 1;
                    while($row = $adb->fetch_array($packagesod_qry)){
                        $pref_leads = $row['pref_leads'];
                        if($i == 1)
                            $leadid = $pref_leads;
                        else
                            $leadid .= ', '.$pref_leads;
                        $i++;
                    }
                }
                $sub_query = " AND (vtiger_leadscf.leadid IN($leadid) OR vtiger_leadscf.converted = '0') ";
            }
            $lead_qry = $adb->query("SELECT lead_no, vtiger_leaddetails.leadid as leadid, cf_907, cf_909, cf_911, cf_913, cf_915 FROM vtiger_leaddetails
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_leaddetails.leadid AND vtiger_crmentityrel.relmodule = 'Leads' )
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
                            INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_leaddetails.leadid
                            WHERE vtiger_crmentity.deleted = 0 $sub_query AND vtiger_crmentityrel.crmid = $contactid AND cf_913 IN('Deal Won – 100%') ORDER BY cf_907");
            if($adb->num_rows($lead_qry) > 0) {
                $i = 1;
                while($row = $adb->fetch_array($lead_qry)){
                    $value = $row['lead_no'].'--'.$row['cf_909'].'--'.$row['cf_907'];
                    //$index = $row['leadid'].'__'.$row['lead_no'];
                    $index = $row['leadid'];
                    $product = strtolower($row['cf_907']);
                    if($product == 'database'){
                        $RELATED_DATABASE_PRODUCTS[$i]['product'] = $value;
                        $RELATED_DATABASE_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'education'){
                        $RELATED_EDUCATION_PRODUCTS[$i]['product'] = $value;
                        $RELATED_EDUCATION_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'logo'){
                        $RELATED_LOGO_PRODUCTS[$i]['product'] = $value;
                        $RELATED_LOGO_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'inventory'){
                        $RELATED_INVENTORY_PRODUCTS[$i]['product'] = $value;
                        $RELATED_INVENTORY_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'events'){
                        $RELATED_EVENTS_PRODUCTS[$i]['product'] = $value;
                        $RELATED_EVENTS_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'print'){
                        $RELATED_PRINT_PRODUCTS[$i]['product'] = $value;
                        $RELATED_PRINT_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'smartmatch'){
                        $RELATED_SMARTMATCH_PRODUCTS[$i]['product'] = $value;
                        $RELATED_SMARTMATCH_PRODUCTS[$i]['leadno'] = $index;
                    }

                    if($product == 'myparichay'){
                        $RELATED_MYPARICHAY_PRODUCTS[$i]['product'] = $value;
                        $RELATED_MYPARICHAY_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'flexihire'){
                        $RELATED_FLEXIHIRE_PRODUCTS[$i]['product'] = $value;
                        $RELATED_FLEXIHIRE_PRODUCTS[$i]['leadno'] = $index;
                    }
                    if($product == 'emshineverified'){
                        $RELATED_EMSHINEVERIFIED_PRODUCTS[$i]['product'] = $value;
                        $RELATED_EMSHINEVERIFIED_PRODUCTS[$i]['leadno'] = $index;
                    }

                    $unique_Products[] = $row['cf_907'];
                    $i++;
                }
                // Start fetch data from package master
                $package_master_qry = $adb->query("SELECT vtiger_vendor.vendorid as venid, package_master, logo_product,
                            education_product, event_product, geography, inventory_product, print_product, flexi_geography,
                             elite_product_type, smart_jproduct_type, smartmatch_month FROM vtiger_vendor
                            INNER JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
                            WHERE vtiger_crmentity.deleted = 0");
                if($adb->num_rows($package_master_qry) > 0) {
                    $i = 0;
                    while($row = $adb->fetch_array($package_master_qry)) {
                        $package_master = $row['package_master'];
                        $vendorid = $row['venid'];
                        if($package_master == "LOGO")
                            $LOGO_PRODUCTS[] = $row['logo_product'];
                        if($package_master == "EDUCATION")
                            $EDUCATION_PRODUCTS[] = $row['education_product'].'__'.$vendorid;
                        if($package_master == "EVENTS")
                            $EVENTS_PRODUCTS[] = $row['event_product'];
                        if($package_master == "DATABASE")
                            $DATABASE_PRODUCTS[] = $row['geography'];
                        if($package_master == "INVENTORY")
                            $INVENTORY_PRODUCTS[] = $row['inventory_product'];
                        if($package_master == "PRINT")
                            $PRINT_PRODUCTS[] = $row['print_product'].'__'.$vendorid;
                        if($package_master == "FLEXIHIRE")
                            $FLEXIHIRE_PRODUCTS[] = $row['flexi_geography'];
                        if($package_master == "EMSHINEVERIFIED")
                            $EMSHINEVERIFIED_PRODUCTS[] = $row['elite_product_type'].'__'.$vendorid;
                        if($package_master == "SMARTMATCH")
                            $SMARTMATCH_PRODUCTS[] = $row['smartmatch_month'];
                        $i++;
                    }
                }

                // End fetch data from package master
                //echo "<pre>";print_r($SMARTJOBS_PRODUCTS);
                $viewer->assign('UNIQUE_PRODUCTS',array_unique($unique_Products));
                $viewer->assign('RELATED_DATABASE_PRODUCTS',$RELATED_DATABASE_PRODUCTS);
                $viewer->assign('RELATED_EDUCATION_PRODUCTS',$RELATED_EDUCATION_PRODUCTS);
                $viewer->assign('RELATED_LOGO_PRODUCTS',$RELATED_LOGO_PRODUCTS);
                $viewer->assign('RELATED_INVENTORY_PRODUCTS',$RELATED_INVENTORY_PRODUCTS);
                $viewer->assign('RELATED_EVENTS_PRODUCTS',$RELATED_EVENTS_PRODUCTS);
                $viewer->assign('RELATED_PRINT_PRODUCTS',$RELATED_PRINT_PRODUCTS);
                $viewer->assign('RELATED_SMARTMATCH_PRODUCTS',$RELATED_SMARTMATCH_PRODUCTS);
                $viewer->assign('RELATED_MYPARICHAY_PRODUCTS',$RELATED_MYPARICHAY_PRODUCTS);
                $viewer->assign('RELATED_EMSHINEVERIFIED_PRODUCTS',$RELATED_EMSHINEVERIFIED_PRODUCTS);
                $viewer->assign('RELATED_FLEXIHIRE_PRODUCTS',$RELATED_FLEXIHIRE_PRODUCTS);

                $viewer->assign('EVENTS_PRODUCTS',array_unique($EVENTS_PRODUCTS));
                $viewer->assign('LOGO_PRODUCTS',array_unique($LOGO_PRODUCTS));
                $viewer->assign('EDUCATION_PRODUCTS',array_unique($EDUCATION_PRODUCTS));
                $viewer->assign('DATABASE_PRODUCTS',array_unique($DATABASE_PRODUCTS));
                $viewer->assign('INVENTORY_PRODUCTS',array_unique($INVENTORY_PRODUCTS));
                $viewer->assign('PRINT_PRODUCTS',array_unique($PRINT_PRODUCTS));
                $viewer->assign('FLEXIHIRE_PRODUCTS',array_unique($FLEXIHIRE_PRODUCTS));
                $viewer->assign('EMSHINEVERIFIED_PRODUCTS',$EMSHINEVERIFIED_PRODUCTS);
                $viewer->assign('SMARTMATCH_PRODUCTS',array_unique($SMARTMATCH_PRODUCTS));
            }

            $DRAWEE_BANK_LIST = array();
            $bank_qry = $adb->query("SELECT bank_name  FROM vtiger_bank_name WHERE presence = 1 ORDER BY bank_name ");
            if($adb->num_rows($bank_qry) > 0) {
                while($row = $adb->fetch_array($bank_qry)) {
                    $DRAWEE_BANK_LIST[$row['bank_name']] = $row['bank_name'];
                }
            }

            $PAYMENT_MODE_LIST = array();
            $paymode_qry = $adb->query("SELECT payment_mode  FROM vtiger_payment_mode WHERE presence = 1 ORDER BY payment_mode ");
            if($adb->num_rows($paymode_qry) > 0) {
                while($row = $adb->fetch_array($paymode_qry)) {
                    $PAYMENT_MODE_LIST[$row['payment_mode']] = $row['payment_mode'];
                }
            }

// start for packagesold ***********************************
            $recordid = $_REQUEST['record'];
            $counter_education_assign = 1;
            $EDUCATION_LIST = array();
            $EDUCATION_LIST[0] = '';
            $EDUCATION_LIST[1] = '';

            $counter_logo_assign = 1;
            $LOGO_LIST = array();
            $LOGO_LIST[0] = '';
            $LOGO_LIST[1] = '';

            $counter_event_assign = 1;
            $EVENT_LIST = array();
            $EVENT_LIST[0] = '';
            $EVENT_LIST[1] = '';

            $counter_database_assign = 1;
            $DATABASE_LIST = array();
            $DATABASE_LIST[0] = '';
            $DATABASE_LIST[1] = '';

            $counter_inventory_assign = 1;
            $INVENTORY_LIST = array();
            $INVENTORY_LIST[0] = '';
            $INVENTORY_LIST[1] = '';

            $counter_print_assign = 1;
            $PRINT_LIST = array();
            $PRINT_LIST[0] = '';
            $PRINT_LIST[1] = '';

            $counter_flexihire_assign = 1;
            $FLEXIHIRE_LIST = array();
            $FLEXIHIRE_LIST[0] = '';
            $FLEXIHIRE_LIST[1] = '';

            $counter_emshineverified_assign = 1;
            $EMSHINEVERIFIED_LIST = array();
            $EMSHINEVERIFIED_LIST[0] = '';
            $EMSHINEVERIFIED_LIST[1] = '';

            $counter_smartjobs_assign = 1;
            $SMARTJOBS_LIST = array();
            $SMARTJOBS_LIST[0] = '';
            $SMARTJOBS_LIST[1] = '';

            $counter_smartmatch_assign = 1;
            $SMARTMATCH_LIST = array();
            $SMARTMATCH_LIST[0] = '';
            $SMARTMATCH_LIST[1] = '';

            $packagesold_qry = $adb->query("SELECT vtiger_crmentityrel.relcrmid as relentityid, vtiger_campaign.*, vtiger_campaignscf.* FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordid ");
            if($adb->num_rows($packagesold_qry) > 0) {
                $education_i = 1;
                $event_i = 1;
                $logo_i = 1;
                $database_i = 1;
                $inventory_i = 1;
                $print_i = 1;
                $flexihire_i = 1;
                $emshineverified_i = 1;
                $smartjobs_i = 1;
                $smartmatch_i = 1;
                while($row = $adb->fetch_array($packagesold_qry)) {
                    $package_master = $row['campaigntype'];
                    if($package_master == 'DATABASE'){
                        $DATABASE_LIST[$database_i]['databaseid'] = $row['relentityid'];
                        $DATABASE_LIST[$database_i]['product'] = $row['pref_leads'];//Product
                        $DATABASE_LIST[$database_i]['geography_database'] = $row['db_geography'];//Product Type
                        $DATABASE_LIST[$database_i]['database_it'] = $row['db_non_it'];//Sponsorship
                        $DATABASE_LIST[$database_i]['database_limit'] = $row['db_limits']; //Month
                        $DATABASE_LIST[$database_i]['database_month'] = $row['db_dur_months']; //MRP
                        $DATABASE_LIST[$database_i]['db_upsell_exl'] = $row['db_upsell_exl']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['db_upsell_word'] = $row['db_upsell_word'];
                        $DATABASE_LIST[$database_i]['db_upsell_email'] = $row['db_upsell_email'];
                        $DATABASE_LIST[$database_i]['db_upsell_login'] = $row['db_upsell_login'];
                        $DATABASE_LIST[$database_i]['db_dwnsell_exl'] = $row['db_dwnsell_exl'];
                        $DATABASE_LIST[$database_i]['db_dwnsell_word'] = $row['db_dwnsell_word'];
                        $DATABASE_LIST[$database_i]['db_dwnsell_email'] = $row['db_dwnsell_email'];
                        $DATABASE_LIST[$database_i]['up_excel_amount'] = $row['up_excel_amount']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['up_word_amount'] = $row['up_word_amount'];
                        $DATABASE_LIST[$database_i]['up_email_amount'] = $row['up_email_amount']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['up_login_amount'] = $row['up_login_amount']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['down_excel_amount'] = $row['down_excel_amount'];
                        $DATABASE_LIST[$database_i]['down_word_amount'] = $row['down_word_amount']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['down_email_amount'] = $row['down_email_amount'];
                        $DATABASE_LIST[$database_i]['up_excel_amount_bp'] = $row['up_excel_amount_bp']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['up_word_amount_bp'] = $row['up_word_amount_bp'];
                        $DATABASE_LIST[$database_i]['up_email_amount_bp'] = $row['up_email_amount_bp']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['up_login_amount_bp'] = $row['up_login_amount_bp']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['down_excel_amount_bp'] = $row['down_excel_amount_bp'];
                        $DATABASE_LIST[$database_i]['down_word_amount_bp'] = $row['down_word_amount_bp']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['down_email_amount_bp'] = $row['down_email_amount_bp'];
                        $DATABASE_LIST[$database_i]['mrp'] = $row['product_mrp']; //MRP
                        $DATABASE_LIST[$database_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $DATABASE_LIST[$database_i]['ps_discount'] = $row['ps_discount'];
                        $DATABASE_LIST[$database_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $DATABASE_LIST[$database_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $DATABASE_LIST[$database_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $DATABASE_LIST[$database_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $database_i++;
                    }

                    if($package_master == 'EVENTS'){
                        $EVENT_LIST[$event_i]['eventid'] = $row['relentityid'];
                        $EVENT_LIST[$event_i]['product'] = $row['pref_leads'];//Product
                        $EVENT_LIST[$event_i]['comment_product_type'] = $row['comment_product_type'];//Product Type
                        $EVENT_LIST[$event_i]['campaignstatus'] = $row['campaignstatus'];//Sponsorship
                        $EVENT_LIST[$event_i]['mrp'] = $row['product_mrp']; //MRP
                        $EVENT_LIST[$event_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $EVENT_LIST[$event_i]['month'] = $row['budgetcost']; //Month
                        $EVENT_LIST[$event_i]['ps_discount'] = $row['ps_discount'];
                        $EVENT_LIST[$event_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $EVENT_LIST[$event_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $EVENT_LIST[$event_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $EVENT_LIST[$event_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $event_i++;
                    }

                    if($package_master == 'EMSHINEVERIFIED'){
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['emshineverifiedid'] = $row['relentityid'];
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['product'] = $row['pref_leads'];//Product
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['elite_mproduct_type'] = $row['elite_mproduct_type'];//Product Type
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['elite_no_walk'] = $row['elite_no_walk'];//Sponsorship
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['elite_re_details'] = $row['elite_re_details']; //Month
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['mrp'] = $row['product_mrp']; //MRP
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['ps_discount'] = $row['ps_discount'];
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $EMSHINEVERIFIED_LIST[$emshineverified_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $emshineverified_i++;
                    }

                    if($package_master == 'INVENTORY'){
                        $INVENTORY_LIST[$inventory_i]['inventoryid'] = $row['relentityid'];
                        $INVENTORY_LIST[$inventory_i]['product'] = $row['pref_leads'];//Product
                        $INVENTORY_LIST[$inventory_i]['exp_product_type'] = $row['exp_product_type'];//Product Type
                        $INVENTORY_LIST[$inventory_i]['exp_tg_db'] = $row['exp_tg_db'];//Sponsorship
                        $INVENTORY_LIST[$inventory_i]['exp_active'] = $row['exp_active'];//Sponsorship
                        $INVENTORY_LIST[$inventory_i]['noofemailer'] = $row['campaignname']; //MRP
                        $INVENTORY_LIST[$inventory_i]['mrp'] = $row['product_mrp']; //MRP
                        $INVENTORY_LIST[$inventory_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $INVENTORY_LIST[$inventory_i]['ps_discount'] = $row['ps_discount'];
                        $INVENTORY_LIST[$inventory_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $INVENTORY_LIST[$inventory_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $INVENTORY_LIST[$inventory_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $INVENTORY_LIST[$inventory_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $inventory_i++;
                    }

                    if($package_master == 'LOGO'){
                        $LOGO_LIST[$logo_i]['logoid'] = $row['relentityid'];
                        $LOGO_LIST[$logo_i]['product'] = $row['pref_leads'];//Product
                        $LOGO_LIST[$logo_i]['logo_product_type'] = $row['logo_product_type'];//Product Type
                        $LOGO_LIST[$logo_i]['month'] = $row['logo_month']; //Month
                        $LOGO_LIST[$logo_i]['mrp'] = $row['product_mrp']; //MRP
                        $LOGO_LIST[$logo_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $LOGO_LIST[$logo_i]['ps_discount'] = $row['ps_discount'];
                        $LOGO_LIST[$logo_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $LOGO_LIST[$logo_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $LOGO_LIST[$logo_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $LOGO_LIST[$logo_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $logo_i++;
                    }

                    if($package_master == 'SMART JOBS'){
                        $SMARTJOBS_LIST[$smartjobs_i]['smartjobsid'] = $row['relentityid'];
                        $SMARTJOBS_LIST[$smartjobs_i]['product'] = $row['pref_leads'];//Product
                        $SMARTJOBS_LIST[$smartjobs_i]['sjobs_product_type'] = $row['sjobs_product_type'];//Product Type
                        $SMARTJOBS_LIST[$smartjobs_i]['sjobs_no_jobs'] = $row['sjobs_no_jobs']; //Month
                        $SMARTJOBS_LIST[$smartjobs_i]['mrp'] = $row['product_mrp']; //MRP
                        $SMARTJOBS_LIST[$smartjobs_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $smartjobs_i++;
                    }

                    if($package_master == 'SMARTMATCH'){
                        $SMARTMATCH_LIST[$smartmatch_i]['smartmatchid'] = $row['relentityid'];
                        $SMARTMATCH_LIST[$smartmatch_i]['product'] = $row['pref_leads'];//Product
                        $SMARTMATCH_LIST[$smartmatch_i]['match_dura_month'] = $row['match_dura_month'];//Product Type
                        $SMARTMATCH_LIST[$smartmatch_i]['match_no_jobs'] = $row['match_no_jobs']; //Month
                        $SMARTMATCH_LIST[$smartmatch_i]['mrp'] = $row['product_mrp']; //MRP
                        $SMARTMATCH_LIST[$smartmatch_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $SMARTMATCH_LIST[$smartmatch_i]['ps_discount'] = $row['ps_discount'];
                        $SMARTMATCH_LIST[$smartmatch_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $SMARTMATCH_LIST[$smartmatch_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $SMARTMATCH_LIST[$smartmatch_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $SMARTMATCH_LIST[$smartmatch_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $smartmatch_i++;
                    }

                    if($package_master == 'PRINT'){
                        $PRINT_LIST[$print_i]['printid'] = $row['relentityid'];
                        $PRINT_LIST[$print_i]['product'] = $row['pref_leads'];//Product
                        $PRINT_LIST[$print_i]['print_product_type'] = $row['print_product_type'];//Product Type
                        $PRINT_LIST[$print_i]['print_size'] = $row['print_size']; //Month
                        $PRINT_LIST[$print_i]['mrp'] = $row['product_mrp']; //MRP
                        $PRINT_LIST[$print_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $PRINT_LIST[$print_i]['ps_discount'] = $row['ps_discount'];
                        $PRINT_LIST[$print_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $PRINT_LIST[$print_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $PRINT_LIST[$print_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $PRINT_LIST[$print_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $print_i++;
                    }


                    if($package_master == 'EDUCATION'){
                        $EDUCATION_LIST[$education_i]['educationid'] = $row['relentityid'];
                        $EDUCATION_LIST[$education_i]['product'] = $row['pref_leads'];//Product
                        $EDUCATION_LIST[$education_i]['edu_product_type'] = $row['edu_product_type'];//Product Type
                        $EDUCATION_LIST[$education_i]['edu_no_cmp'] = $row['edu_no_cmp'];//No Of Company
                        $EDUCATION_LIST[$education_i]['bottom_price'] = $row['product_bottom_price']; //Rest of India BP
                        $EDUCATION_LIST[$education_i]['mrp'] = $row['product_mrp']; //MRP
                        $EDUCATION_LIST[$education_i]['ps_discount'] = $row['ps_discount'];
                        $EDUCATION_LIST[$education_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $EDUCATION_LIST[$education_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $EDUCATION_LIST[$education_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $EDUCATION_LIST[$education_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $education_i++;
                    }

                    if($package_master == 'FLEXIHIRE'){
                        $FLEXIHIRE_LIST[$flexihire_i]['flexihireid'] = $row['relentityid'];
                        $FLEXIHIRE_LIST[$flexihire_i]['product'] = $row['pref_leads'];//Product
                        $FLEXIHIRE_LIST[$flexihire_i]['elite_mgeography'] = $row['elite_mgeography'];//Product Type
                        $FLEXIHIRE_LIST[$flexihire_i]['elite_maccess'] = $row['elite_maccess'];//Sponsorship
                        $FLEXIHIRE_LIST[$flexihire_i]['elite_mduration'] = $row['elite_mduration']; //Month
                        $FLEXIHIRE_LIST[$flexihire_i]['mrp'] = $row['product_bottom_price']; //MRP
                        $FLEXIHIRE_LIST[$flexihire_i]['bottom_price'] = $row['product_mrp']; //Rest of India BP
                        $FLEXIHIRE_LIST[$flexihire_i]['ps_discount'] = $row['ps_discount'];
                        $FLEXIHIRE_LIST[$flexihire_i]['ps_discount_amount'] = $row['ps_discount_amount'];
                        $FLEXIHIRE_LIST[$flexihire_i]['ps_offered_amount'] = $row['ps_offered_amount'];
                        $FLEXIHIRE_LIST[$flexihire_i]['ps_service_tax_amount'] = $row['ps_service_tax_amount'];
                        $FLEXIHIRE_LIST[$flexihire_i]['ps_total_amount'] = $row['ps_total_amount'];
                        $flexihire_i++;
                    }
                }


                if($education_i > 1)
                    $counter_education_assign = $education_i -1 ;
                if($event_i > 1)
                    $counter_event_assign = $event_i -1 ;
                if($logo_i > 1)
                    $counter_logo_assign = $logo_i -1 ;
                if($database_i > 1)
                    $counter_database_assign = $database_i -1 ;
                if($inventory_i > 1)
                    $counter_inventory_assign = $inventory_i -1 ;
                if($print_i > 1)
                    $counter_print_assign = $print_i -1 ;
                if($emshineverified_i > 1)
                    $counter_emshineverified_assign = $emshineverified_i -1 ;
                if($smartjobs_i > 1)
                    $counter_smartjobs_assign = $smartjobs_i -1 ;
                if($smartmatch_i > 1)
                    $counter_smartmatch_assign = $smartmatch_i -1 ;
            }
            //echo "<pre>";print_r($EDUCATION_LIST);
            $viewer->assign('COUNTER_EDUCATION_ASSIGN',$counter_education_assign);
            $viewer->assign('EDUCATION_LIST',$EDUCATION_LIST);
            $viewer->assign('COUNTER_EVENT_ASSIGN',$counter_event_assign);
            $viewer->assign('EVENT_LIST',$EVENT_LIST);
            $viewer->assign('COUNTER_LOGO_ASSIGN',$counter_logo_assign);
            $viewer->assign('LOGO_LIST',$LOGO_LIST);
            $viewer->assign('COUNTER_DATABASE_ASSIGN',$counter_database_assign);
            $viewer->assign('DATABASE_LIST',$DATABASE_LIST);
            $viewer->assign('COUNTER_INVENTORY_ASSIGN',$counter_inventory_assign);
            $viewer->assign('INVENTORY_LIST',$INVENTORY_LIST);
            $viewer->assign('COUNTER_PRINT_ASSIGN',$counter_print_assign);
            $viewer->assign('PRINT_LIST',$PRINT_LIST);
            $viewer->assign('COUNTER_FLEXIHIRE_ASSIGN',$counter_flexihire_assign);
            $viewer->assign('FLEXIHIRE_LIST',$FLEXIHIRE_LIST);
            $viewer->assign('COUNTER_EMSHINEVERIFIED_ASSIGN',$counter_emshineverified_assign);
            $viewer->assign('EMSHINEVERIFIED_LIST',$EMSHINEVERIFIED_LIST);
            $viewer->assign('COUNTER_SMARTJOBS_ASSIGN',$counter_smartjobs_assign);
            $viewer->assign('SMARTJOBS_LIST',$SMARTJOBS_LIST);
            $viewer->assign('COUNTER_SMARTMATCH_ASSIGN',$counter_smartmatch_assign);
            $viewer->assign('SMARTMATCH_LIST',$SMARTMATCH_LIST);
// end for packagesold ***********************************

// start for payment ***********************************
            $counter_payment_assign = 1;
            $PAYMENT_LIST = array();
            $PAYMENT_LIST[0]= '';
            $PAYMENT_LIST[1] = '';
            $payment_qry = $adb->query("SELECT vtiger_project.projectid as paymentid, payment_mode,checkdate,checkno,ro_available,bank_name,amount,onlinemode,
							slip_number,tan_no,cts FROM vtiger_project
                            INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_project.projectid AND vtiger_crmentityrel.relmodule = 'Project')
                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $recordid ");
            if($adb->num_rows($payment_qry) > 0) {
                $i = 1;
                while($row = $adb->fetch_array($payment_qry)) {
                    $PAYMENT_LIST[$i]['paymentid'] = $row['paymentid'];
                    $PAYMENT_LIST[$i]['payment_mode'] = $row['payment_mode'];
                    $PAYMENT_LIST[$i]['checkdate'] = $row['checkdate'];
                    $PAYMENT_LIST[$i]['checkno'] = $row['checkno'];
                    $PAYMENT_LIST[$i]['ro_available'] = $row['ro_available'];
                    $PAYMENT_LIST[$i]['bank_name'] = $row['bank_name'];
                    $PAYMENT_LIST[$i]['amount'] = number_format($row['amount'],2);

                    $PAYMENT_LIST[$i]['onlinemode'] = $row['onlinemode'];
                    $PAYMENT_LIST[$i]['slip_number'] = $row['slip_number'];
                    $PAYMENT_LIST[$i]['tan_no'] = $row['tan_no'];
                    $PAYMENT_LIST[$i]['cts'] = $row['cts'];
                    $i++;
                }
                $counter_payment_assign = $i -1 ;
            }
// end for payment ***********************************
            // start for sales Approval edit condition ***********************************
            $edit_permission = 0;
            $approval_qry = $adb->query("SELECT final_approval, potcrmentity.smownerid as ownerid, vtiger_crmentity.smownerid as lastactionuserid, approval_status, u2.id as reportto_id FROM vtiger_servicecontractscf
                                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontractscf.servicecontractsid
                                                INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_servicecontractscf.servicecontractsid AND vtiger_crmentityrel.relmodule = 'ServiceContracts')
                                                INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                                INNER JOIN vtiger_crmentity as potcrmentity ON potcrmentity.crmid = vtiger_potentialscf.potentialid
                                                INNER JOIN vtiger_users  on vtiger_users.id = vtiger_crmentity.smownerid
                                                INNER JOIN vtiger_users AS u2 ON u2.id = vtiger_users.reports_to_id
                                                WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $record
                                                ORDER BY vtiger_crmentity.crmid DESC LIMIT 1");
            if($adb->num_rows($approval_qry) > 0) {
                $row = $adb->fetchByAssoc($approval_qry);
                $approvestatus = $row['approval_status'];
                $ownerid = $row['ownerid'];
                $final_approval = $row['final_approval'];
                $last_action_userid = $row['lastactionuserid'];
                $reportto_id = $row['reportto_id'];
                if(($current_user->id == $reportto_id) && $approvestatus == 'Approved')
                    $edit_permission = 1;
                if($current_user->id == $ownerid && $approvestatus == 'Rejected')
                    $edit_permission = 1;
            }else{
                $sourcsRecord = $_REQUEST['sourceRecord'];
                $record = $_REQUEST['record'];
                if($sourcsRecord != ""){
                    $sql = "SELECT smownerid FROM vtiger_contactscf
                                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
                                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_contactscf.contactid = $sourcsRecord ";
                }else{
                    $sql = "SELECT smownerid FROM vtiger_potentialscf
                                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potentialscf.potentialid
                                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $record ";
                }
                $pot_qry = $adb->query($sql);
                if($adb->num_rows($pot_qry) > 0) {
                    $row = $adb->fetchByAssoc($pot_qry);
                    $smownerid = $row['smownerid'];
                    if($smownerid == $current_user->id)
                        $edit_permission = 1;
                }
            }
            if($final_approval == 'Yes')
                $edit_permission = 0;
            $viewer->assign('APPROVAL_EDIT_PERMISSION',$edit_permission);
// end for sales Approval edit condition ***********************************
// start for upsell and dowmsell option valuse
            $value_excel = array();
            $value_word = array();
            $value_emailer = array();
            for($i =1; $i<=3; $i++){
                $value_excel[] = (3*$i).'k';
                $value_word[] = (2*$i).'k';
                $value_emailer[] = (5*$i).'k';
            }
            $viewer->assign('VALUEEXCEL',$value_excel);
            $viewer->assign('VALUEWORD',$value_word);
            $viewer->assign('VALUEEMAILER',$value_emailer);
            $value_noofjob = array();
            for($i =1; $i<=10; $i++){
                $value_noofjob[] = (50*$i);
            }
            $viewer->assign('SMARTJOBS_PRODUCTS',$value_noofjob);
// start for upsell and dowmsell option valuse
            $viewer->assign('COUNTER_PAYMENT_ASSIGN',$counter_payment_assign);
            $viewer->assign('DRAWEE_BANK_LIST',$DRAWEE_BANK_LIST);
            $viewer->assign('PAYMENT_MODE_LIST',$PAYMENT_MODE_LIST);
            $viewer->assign('PAYMENT_LIST',$PAYMENT_LIST);
        }// end Potentials

        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));
        $viewer->view('EditView.tpl', $moduleName);
    }
}