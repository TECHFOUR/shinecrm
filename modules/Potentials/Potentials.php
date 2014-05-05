<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Potentials/Potentials.php,v 1.65 2005/04/28 08:08:27 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('modules/Contacts/Contacts.php');
require_once('modules/Calendar/Activity.php');
require_once('modules/Documents/Documents.php');
require_once('modules/Emails/Emails.php');
require_once('include/utils/utils.php');
require_once('user_privileges/default_module_view.php');

// vtiger_potential is used to store customer information.
class Potentials extends CRMEntity {
    var $log;
    var $db;

    var $module_name="Potentials";
    var $table_name = "vtiger_potential";
    var $table_index= 'potentialid';

    var $tab_name = Array('vtiger_crmentity','vtiger_potential','vtiger_potentialscf');
    var $tab_name_index = Array('vtiger_crmentity'=>'crmid','vtiger_potential'=>'potentialid','vtiger_potentialscf'=>'potentialid');
    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_potentialscf', 'potentialid');

    var $column_fields = Array();

    var $sortby_fields = Array('potentialname','amount','closingdate','smownerid','accountname');

    // This is the list of vtiger_fields that are in the lists.
    var $list_fields = Array(
        'Potential'=>Array('potential'=>'potentialname'),
        'Organization Name'=>Array('potential'=>'related_to'),
        'Contact Name'=>Array('potential'=>'contact_id'),
        'Sales Stage'=>Array('potential'=>'sales_stage'),
        'Amount'=>Array('potential'=>'amount'),
        'Expected Close Date'=>Array('potential'=>'closingdate'),
        'Assigned To'=>Array('crmentity','smownerid')
    );

    var $list_fields_name = Array(
        'Potential'=>'potentialname',
        'Organization Name'=>'related_to',
        'Contact Name'=>'contact_id',
        'Sales Stage'=>'sales_stage',
        'Amount'=>'amount',
        'Expected Close Date'=>'closingdate',
        'Assigned To'=>'assigned_user_id');

    var $list_link_field= 'potentialname';

    var $search_fields = Array(
        'Potential'=>Array('potential'=>'potentialname'),
        'Related To'=>Array('potential'=>'related_to'),
        'Expected Close Date'=>Array('potential'=>'closedate')
    );

    var $search_fields_name = Array(
        'Potential'=>'potentialname',
        'Related To'=>'related_to',
        'Expected Close Date'=>'closingdate'
    );

    var $required_fields =  array();

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('assigned_user_id', 'createdtime', 'modifiedtime', 'potentialname');

    //Added these variables which are used as default order by and sortorder in ListView
    var $default_order_by = 'potentialname';
    var $default_sort_order = 'ASC';

    // For Alphabetical search
    var $def_basicsearch_col = 'potentialname';

    //var $groupTable = Array('vtiger_potentialgrouprelation','potentialid');
    function Potentials() {
        $this->log = LoggerManager::getLogger('potential');
        $this->db = PearDatabase::getInstance();
        $this->column_fields = getColumnFields('Potentials');
    }

    function save_module($module)
    {   global $adb, $current_user;

        if(isset($_REQUEST['currentid']) && $_REQUEST['currentid'] != "" && $_REQUEST['sourceModule'] == "Contacts" && $_REQUEST['action'] == 'Save') {
            $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
            $adb->pquery($sql, array($_REQUEST['sourceRecord'],'Contacts',$_REQUEST['currentid'],'Potentials'));
        }

        if($_REQUEST['action'] == 'Save') {
            $this->addPackageSold();
            $this->addPayment();

            $total_payment_amount = "";
            $payment_qry = $adb->query("SELECT vtiger_projectcf.amount as amt FROM vtiger_project
                                        INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                                        INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_project.projectid AND vtiger_crmentityrel.relmodule = 'Project')
                                        INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $this->id ");
            if($adb->num_rows($payment_qry) > 0) {
                while($row = $adb->fetchByAssoc($payment_qry)) {
                    $total_payment_amount = $total_payment_amount + str_replace(",","",$row['amt']);
                }
            }


            $total_sale_amount = 0.00;

            $packagesold_qry = $adb->query("SELECT ps_total_amount FROM vtiger_campaign
                                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
                                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                            INNER JOIN vtiger_crmentity as crmentity_pot ON crmentity_pot.crmid = vtiger_potentialscf.potentialid
                                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $this->id
                                             AND campaigntype != 'SMART JOBS' ORDER BY vtiger_crmentity.crmid ");
            if($adb->num_rows($packagesold_qry) > 0) {
                while($row = $adb->fetchByAssoc($packagesold_qry)) {
                    $total_sale_amount = $total_sale_amount + str_replace(",","",$row['ps_total_amount']);
                }
            }
            if(floor($total_sale_amount) === floor($total_payment_amount))
                $adb->query("UPDATE vtiger_potentialscf SET cf_807 = 'Completed' where vtiger_potentialscf.potentialid = $this->id ");
            else
                $adb->query("UPDATE vtiger_potentialscf SET cf_807 = 'Incompleted' where vtiger_potentialscf.potentialid = $this->id ");
        }
    }

    function  addPackageSold(){
        $record_mode_education_count = count($_REQUEST['record_mode_education']);
        $record_mode_event_count = count($_REQUEST['record_mode_event']);
        $record_mode_logo_count = count($_REQUEST['record_mode_logo']);
        $record_mode_database_count = count($_REQUEST['record_mode_database']);
        $record_mode_inventory_count = count($_REQUEST['record_mode_inventory']);
        $record_mode_print_count = count($_REQUEST['record_mode_print']);
        $record_mode_flexihire = count($_REQUEST['record_mode_flexihire']);
        $record_mode_emshineverified = count($_REQUEST['record_mode_emshineverified']);
        $record_mode_smartjobs = count($_REQUEST['record_mode_smartjobs']);
        $record_mode_smartmatch = count($_REQUEST['record_mode_smartmatch']);
        if($record_mode_education_count > 1)
            $this->addPackageSoldEducation($record_mode_education_count);
        if($record_mode_event_count > 1 )
            $this->addPackageSoldEvents($record_mode_event_count);
        if($record_mode_logo_count > 1 )
            $this->addPackageSoldLogo($record_mode_logo_count);
        if($record_mode_database_count > 1 )
            $this->addPackageSoldDatabase($record_mode_database_count);
        if($record_mode_inventory_count > 1 )
            $this->addPackageSoldInventory($record_mode_inventory_count);
        if($record_mode_print_count > 1 )
            $this->addPackageSoldPrint($record_mode_print_count);
        if($record_mode_flexihire > 1 )
            $this->addPackageSoldFlexiHire($record_mode_flexihire);
        if($record_mode_emshineverified > 1 )
            $this->addPackageSoldEMShineVerified($record_mode_emshineverified);
        if($record_mode_smartjobs > 1 )
            $this->addPackageSoldSmartJobs($record_mode_smartjobs);
        if($record_mode_smartmatch > 1 )
            $this->addPackageSoldSmartMatch($record_mode_smartmatch);
    }

    function addPackageSoldSmartMatch($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'SMARTMATCH';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_smartmatch'][$i];
            $product_events = $_REQUEST['product_smartmatch'][$i];
            $product_type_events = $_REQUEST['product_type_smartmatch'][$i];
            list($noofcompany_education, $y) = explode("__", $_REQUEST['noofjob_smartmatch'][$i]);
            $bottom_price_events = $_REQUEST['bottom_price_smartmatch'][$i];
            $mrp_events = $_REQUEST['mrp_smartmatch'][$i];
            $discount_evevts = $_REQUEST['discount_smartmatch'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_smartmatch'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_smartmatch'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_smartmatch'][$i];
            $total_amount_events = $_REQUEST['total_amount_smartmatch'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'match_dura_month'=>$product_type_events,
                'match_no_jobs'=>$noofcompany_education,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, match_dura_month, match_no_jobs, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$noofcompany_education."','".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldSmertMatch($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldSmertMatch($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                                        $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                        $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, match_dura_month, match_no_jobs, product_bottom_price , product_mrp, ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', match_dura_month = '$product_type_events',
                                    match_no_jobs = '$noofcompany_education',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldSmartJobs($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'SMART JOBS';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_smartjobs'][$i];
            $product_events = $_REQUEST['product_smartjobs'][$i];
            $product_type_events = $_REQUEST['product_type_smartjobs'][$i];
            $mrp_events = $_REQUEST['price_per_job'][$i];
            $bottom_price_events = $_REQUEST['bottom_price_smartjobs'][$i];

            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'sjobs_product_type'=>$product_type_events,
                'sjobs_no_jobs'=>$product_type_events,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price, product_mrp )
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$product_bottom_price."', '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, sjobs_product_type, sjobs_no_jobs)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$product_type_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldSmartJobs($eventid, $packagesold_values, $product_events, $product_type_events,
                    $bottom_price_events, $mrp_events);
            }
        } // end for loop
    }

    function updatePckageSoldSmartJobs($eventid, $packagesold_values, $product_events, $product_type_events,
                                       $bottom_price_events, $mrp_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, sjobs_product_type, sjobs_no_jobs, product_bottom_price , product_mrp
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', sjobs_product_type = '$product_type_events',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' ,
                                     modifiedby = ".$current_user->id.", sjobs_no_jobs = '$product_type_events' ,
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }

    function addPackageSoldEMShineVerified($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'EMSHINEVERIFIED';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_emshineverified'][$i];
            $product_events = $_REQUEST['product_emshineverified'][$i];
            list($product_type_events, $y) = explode("__", $_REQUEST['product_type_emshineverified'][$i]);
            $month_events = $_REQUEST['walkin_emshineverified'][$i];
            $sponsorship_events = $_REQUEST['rdetail_emshineverified'][$i];
            $bottom_price_events = $_REQUEST['bottom_price_emshineverified'][$i];
            $mrp_events = $_REQUEST['mrp_emshineverified'][$i];
            $discount_evevts = $_REQUEST['discount_emshineverified'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_emshineverified'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_emshineverified'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_emshineverified'][$i];
            $total_amount_events = $_REQUEST['total_amount_emshineverified'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'elite_mproduct_type'=>$product_type_events,
                'elite_no_walk'=>$sponsorship_events,
                'elite_re_details'=>$month_events,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid,$module, $currentdatetime, $currentdatetime,$packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount,
                            elite_mproduct_type, elite_no_walk, elite_re_details)
                             VALUES(".$crmid.", '".$product_events."', '".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."',
                             '".$product_type_events."', '".$month_events."', '".$sponsorship_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldEMShineVerified($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldEMShineVerified($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                                             $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                             $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, elite_mproduct_type, elite_no_walk, elite_re_details, product_bottom_price , product_mrp,ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', elite_mproduct_type = '$product_type_events',
                                    elite_no_walk = '$sponsorship_events', elite_re_details = '$month_events',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldFlexiHire($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'FLEXIHIRE';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_flexihire'][$i];
            $product_events = $_REQUEST['product_flexihire'][$i];
            $product_type_events = $_REQUEST['product_type_flexihire'][$i];
            $month_events = $_REQUEST['access_flexihire'][$i];
            list($sponsorship_events, $y) = explode("__", $_REQUEST['duration_flexihire'][$i]);
            $bottom_price_events = $_REQUEST['bottom_price_flexihire'][$i];
            $mrp_events = $_REQUEST['mrp_flexihire'][$i];
            $discount_evevts = $_REQUEST['discount_flexihire'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_flexihire'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_flexihire'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_flexihire'][$i];
            $total_amount_events = $_REQUEST['total_amount_flexihire'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'elite_mgeography'=>$product_type_events,
                'elite_maccess'=>$sponsorship_events,
                'elite_mduration'=>$month_events,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid,$module, $currentdatetime, $currentdatetime,$packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount,
                            elite_mgeography, elite_maccess, elite_mduration, elite_mbp, elite_mmrp)
                             VALUES(".$crmid.", '".$product_events."', '".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."',
                             '".$product_type_events."', '".$month_events."', '".$sponsorship_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldFlexiHire($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldFlexiHire($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                                       $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                       $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, elite_mgeography, elite_maccess, elite_mduration,product_bottom_price , product_mrp,ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', elite_mgeography = '$product_type_events',
                                    elite_maccess = '$sponsorship_events', elite_mduration = '$month_events',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldPrint($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'PRINT';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_print'][$i];
            $product_events = $_REQUEST['product_print'][$i];
            list($product_type_events, $y) = explode("__", $_REQUEST['product_type_print'][$i]);
            $noofcompany_education = $_REQUEST['size_sqcm_print'][$i];
            $bottom_price_events = $_REQUEST['bottom_price_print'][$i];
            $mrp_events = $_REQUEST['mrp_print'][$i];
            $discount_evevts = $_REQUEST['discount_print'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_print'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_print'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_print'][$i];
            $total_amount_events = $_REQUEST['total_amount_print'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'print_product_type'=>$product_type_events,
                'print_size'=>$noofcompany_education,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, print_size, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$noofcompany_education."', '".$bottom_price_events."', '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, print_product_type, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."','".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldPrint($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldPrint($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                                   $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                   $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, print_product_type, print_size, product_bottom_price , product_mrp, ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', print_product_type = '$product_type_events',
                                    print_size = '$noofcompany_education',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldInventory($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'INVENTORY';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_inventory'][$i];
            $product_events = $_REQUEST['product_inventory'][$i];
            $product_type_events = $_REQUEST['product_type_inventory'][$i];
            list($tgdatabase_inventory, $y) = explode("__", $_REQUEST['tgdatabase_inventory'][$i]);
            list($active_inventory, $y) = explode("__", $_REQUEST['active_inventory'][$i]);
            $noofemailer_inventory = $_REQUEST['noofemailer_inventory'][$i];
            $bottom_price_events = $_REQUEST['bottom_price_inventory'][$i];
            $mrp_events = $_REQUEST['mrp_inventory'][$i];
            $discount_evevts = $_REQUEST['discount_inventory'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_inventory'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_inventory'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_inventory'][$i];
            $total_amount_events = $_REQUEST['total_amount_inventory'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'exp_product_type'=>$product_type_events,
                'exp_tg_db'=>$tgdatabase_inventory,
                'exp_active'=>$active_inventory,
                'campaignname'=>$noofemailer_inventory,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid,$module, $currentdatetime, $currentdatetime,$packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, campaignname, product_bottom_price, product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$noofemailer_inventory."', '".$bottom_price_events."', '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads,exp_product_type,exp_tg_db,exp_active,
                            ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$tgdatabase_inventory."', '".$active_inventory."'
                             , '".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldInventory($eventid, $packagesold_values, $product_events, $product_type_events, $tgdatabase_inventory, $active_inventory, $noofemailer_inventory,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldInventory($eventid, $packagesold_values, $product_events, $product_type_events, $tgdatabase_inventory, $active_inventory, $noofemailer_inventory,
                                       $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                       $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, exp_product_type, exp_tg_db, exp_active, campaignname, product_bottom_price, product_mrp,ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', exp_product_type = '$product_type_events',
                                    exp_tg_db = '$tgdatabase_inventory', exp_active = '$active_inventory',campaignname = '$noofemailer_inventory',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldDatabase($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'DATABASE';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_database'][$i];
            $product_events = $_REQUEST['product_database'][$i];
            $product_type_events = $_REQUEST['geography_database'][$i];
            $it_nonit_database = $_REQUEST['it_nonit_database'][$i];
            $limits_database = $_REQUEST['limits_database'][$i];
            list($duration_database, $y) = explode("__",$_REQUEST['duration_database'][$i]);
            $upsell_excel_database = $_REQUEST['upsell_excel_database'][$i];
            $upsell_word_database = $_REQUEST['upsell_word_database'][$i];
            $upsell_emailer_database = $_REQUEST['upsell_emailer_database'][$i];
            $upsell_login_database = $_REQUEST['upsell_login_database'][$i];
            $downsell_excel_database = $_REQUEST['downsell_excel_database'][$i];
            $downsell_word_database = $_REQUEST['downsell_word_database'][$i];
            $downsell_emailer_database = $_REQUEST['downsell_emailer_database'][$i];

            $upsell_excell_amount = $_REQUEST['upsell_excell_amount'][$i];
            $upsell_word_amount = $_REQUEST['upsell_word_amount'][$i];
            $upsell_emailer_amount = $_REQUEST['upsell_emailer_amount'][$i];
            $upsell_login_amount = $_REQUEST['upsell_login_amount'][$i];
            $downsell_excel_amount = $_REQUEST['downsell_excel_amount'][$i];
            $downsell_word_amount = $_REQUEST['downsell_word_amount'][$i];
            $downsell_emailer_amount = $_REQUEST['downsell_emailer_amount'][$i];

            $upsell_excell_amount_bp = $_REQUEST['upsell_excell_amount_bp'][$i];
            $upsell_word_amount_bp = $_REQUEST['upsell_word_amount_bp'][$i];
            $upsell_emailer_amount_bp = $_REQUEST['upsell_emailer_amount_bp'][$i];
            $upsell_login_amount_bp = $_REQUEST['upsell_login_amount_bp'][$i];
            $downsell_excel_amount_bp = $_REQUEST['downsell_excel_amount_bp'][$i];
            $downsell_word_amount_bp = $_REQUEST['downsell_word_amount_bp'][$i];
            $downsell_emailer_amount_bp = $_REQUEST['downsell_emailer_amount_bp'][$i];

            $bottom_price_events = str_replace(",","",$_REQUEST['bottom_price_database'][$i]);
            $mrp_events = str_replace(",","",$_REQUEST['mrp_database'][$i]);
            $discount_evevts = str_replace(",","",$_REQUEST['discount_database'][$i]);
            $discount_amount_events = str_replace(",","",$_REQUEST['discount_amount_database'][$i]);
            $offered_amount_events = str_replace(",","",$_REQUEST['offered_amount_database'][$i]);
            $service_tax_amount_events = str_replace(",","",$_REQUEST['service_tax_amount_database'][$i]);
            $total_amount_events = str_replace(",","",$_REQUEST['total_amount_database'][$i]);
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'db_geography'=>$product_type_events,
                'db_non_it'=>$it_nonit_database,
                'db_limits'=>$limits_database,
                'db_dur_months'=>$duration_database,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'db_upsell_exl'=>$upsell_excel_database,
                'db_upsell_word'=>$upsell_word_database,
                'db_upsell_email'=>$upsell_emailer_database,
                'db_upsell_login'=>$upsell_login_database,
                'db_dwnsell_exl'=>$downsell_excel_database,
                'db_dwnsell_word'=>$downsell_word_database,
                'db_dwnsell_email'=>$downsell_emailer_database,
                'up_excel_amount'=>$upsell_excell_amount,
                'up_word_amount'=>$upsell_word_amount,
                'up_email_amount'=>$upsell_emailer_amount,
                'up_login_amount'=>$upsell_login_amount,
                'down_excel_amount'=>$downsell_excel_amount,
                'down_word_amount'=>$downsell_word_amount,
                'down_email_amount'=>$downsell_emailer_amount,
                'up_excel_amount_bp'=>$upsell_excell_amount_bp,
                'up_word_amount_bp'=>$upsell_word_amount_bp,
                'up_email_amount_bp'=>$upsell_emailer_amount_bp,
                'up_login_amount_bp'=>$upsell_login_amount_bp,
                'down_excel_amount_bp'=>$downsell_excel_amount_bp,
                'down_word_amount_bp'=>$downsell_word_amount_bp,
                'down_email_amount_bp'=>$downsell_emailer_amount_bp,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no,product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."', '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, db_geography, db_non_it, db_limits,
                            db_dur_months, db_upsell_exl, db_upsell_word, db_upsell_email,
                            db_dwnsell_exl, db_dwnsell_word, db_dwnsell_email,
                            ps_discount, ps_discount_amount,ps_offered_amount, ps_service_tax_amount, ps_total_amount, up_excel_amount,
                            up_word_amount,up_email_amount,down_excel_amount,down_word_amount, down_email_amount, up_excel_amount_bp,
up_word_amount_bp,up_email_amount_bp,down_excel_amount_bp,down_word_amount_bp, down_email_amount_bp, db_upsell_login, up_login_amount, up_login_amount_bp)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$it_nonit_database."', '".$limits_database."' , '".$duration_database."',
                                    '".$upsell_excel_database."' , '".$upsell_word_database."',
                                    '".$upsell_emailer_database."', '".$downsell_excel_database."', '".$downsell_word_database."' , '".$downsell_emailer_database."',
                             '".$discount_evevts."', '".$discount_amount_events."','".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."'
                            , '".$upsell_excell_amount."', '".$upsell_word_amount."', '".$upsell_emailer_amount."'
                             , '".$downsell_excel_amount."', '".$downsell_word_amount."', '".$downsell_emailer_amount."'
                             , '".$upsell_excell_amount_bp."', '".$upsell_word_amount_bp."', '".$upsell_emailer_amount_bp."'
                             , '".$downsell_excel_amount_bp."', '".$downsell_word_amount_bp."', '".$downsell_emailer_amount_bp."'
                             , '".$upsell_login_database."', '".$upsell_login_amount."', '".$upsell_login_amount_bp."'
                             )");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldDatabase($eventid, $packagesold_values, $product_events, $product_type_events, $it_nonit_database,
                    $limits_database,$duration_database,$upsell_excel_database,$upsell_word_database,$upsell_emailer_database,
                    $downsell_excel_database,$downsell_word_database,$downsell_emailer_database,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events, $upsell_excell_amount, $upsell_word_amount,
                    $upsell_emailer_amount,$downsell_excel_amount,$downsell_word_amount,$downsell_emailer_amount, $upsell_excell_amount_bp, $upsell_word_amount_bp,
                    $upsell_emailer_amount_bp,$downsell_excel_amount_bp,$downsell_word_amount_bp,$downsell_emailer_amount_bp,
                    $upsell_login_database, $upsell_login_amount,$upsell_login_amount_bp);
            }
        } // end for loop
    }

    function updatePckageSoldDatabase($eventid, $packagesold_values, $product_events, $product_type_events, $it_nonit_database,
                                      $limits_database,$duration_database,$upsell_excel_database,$upsell_word_database,$upsell_emailer_database,
                                      $downsell_excel_database,$downsell_word_database,$downsell_emailer_database,
                                      $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                      $service_tax_amount_events, $total_amount_events, $upsell_excell_amount, $upsell_word_amount,
                                      $upsell_emailer_amount,$downsell_excel_amount,$downsell_word_amount,$downsell_emailer_amount, $upsell_excell_amount_bp, $upsell_word_amount_bp,
                                      $upsell_emailer_amount_bp,$downsell_excel_amount_bp,$downsell_word_amount_bp,$downsell_emailer_amount_bp,
                                      $upsell_login_database, $upsell_login_amount,$upsell_login_amount_bp) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, db_geography, db_limits, db_dur_months, product_bottom_price , product_mrp, db_upsell_exl,
                            db_upsell_word, db_upsell_email, db_dwnsell_exl, db_dwnsell_word, db_dwnsell_email, ps_discount,
                            ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', db_geography = '$product_type_events',
                                    db_non_it = '$it_nonit_database',db_limits = '$limits_database', db_dur_months = '$duration_database' , db_upsell_exl = '$upsell_excel_database',
                                    db_upsell_word = '$upsell_word_database',db_upsell_email = '$upsell_emailer_database', db_dwnsell_exl = '$downsell_excel_database' , db_dwnsell_word = '$downsell_word_database',
                                    db_dwnsell_email = '$downsell_emailer_database', product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                    modifiedby = ".$current_user->id.",modifiedtime = '".date('Y-m-d H:i:s')."',up_excel_amount = '$upsell_excell_amount'
                                    ,up_word_amount = '$upsell_word_amount',up_email_amount = '$upsell_emailer_amount',down_excel_amount = '$downsell_excel_amount'
                                    ,down_word_amount = '$downsell_word_amount',down_email_amount = '$downsell_emailer_amount'
                                    ,up_excel_amount_bp = '$upsell_excell_amount_bp'
                                    ,up_word_amount_bp = '$upsell_word_amount_bp',up_email_amount_bp = '$upsell_emailer_amount_bp',down_excel_amount_bp = '$downsell_excel_amount_bp'
                                    ,down_word_amount_bp = '$downsell_word_amount_bp',down_email_amount_bp = '$downsell_emailer_amount_bp'
                                    ,db_upsell_login = '$upsell_login_database',up_login_amount = '$upsell_login_amount',up_login_amount_bp = '$upsell_login_amount_bp'
                                     where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldLogo($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'LOGO';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_logo'][$i];
            $product_events = $_REQUEST['product_logo'][$i];
            $product_type_events = $_REQUEST['product_type_logo'][$i];
            list($duration_logo, $y) = explode("__",$_REQUEST['duration_logo'][$i]);
            $bottom_price_events = $_REQUEST['bottom_price_logo'][$i];
            $mrp_events = $_REQUEST['mrp_logo'][$i];
            $discount_evevts = $_REQUEST['discount_logo'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_logo'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_logo'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_logo'][$i];
            $total_amount_events = $_REQUEST['total_amount_logo'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'logo_product_type'=>$product_type_events,
                'logo_month'=>$duration_logo,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, logo_product_type, logo_month,ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$duration_logo."','".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldLogo($eventid, $packagesold_values, $product_events, $product_type_events, $duration_logo,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldLogo($eventid, $packagesold_values, $product_events, $product_type_events, $duration_logo,
                                  $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                  $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, logo_product_type, logo_month, product_bottom_price , product_mrp, ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', logo_product_type = '$product_type_events',
                                    logo_month = '$duration_logo',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }


    function addPackageSoldEducation($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'EDUCATION';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_education'][$i];
            $product_events = $_REQUEST['product_education'][$i];
            list($product_type_events, $y) = explode("__", $_REQUEST['product_type_education'][$i]);
            $noofcompany_education = $_REQUEST['noofcompany_education'][$i];
            $bottom_price_events = $_REQUEST['bottom_price_education'][$i];
            $mrp_events = $_REQUEST['mrp_education'][$i];
            $discount_evevts = $_REQUEST['discount_education'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_education'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_education'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_education'][$i];
            $total_amount_events = $_REQUEST['total_amount_education'][$i];
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'edu_product_type'=>$product_type_events,
                'edu_no_cmp'=>$noofcompany_education,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );

            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid, $module, $currentdatetime, $currentdatetime, $packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, edu_product_type, edu_no_cmp, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$product_type_events."', '".$noofcompany_education."','".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldEducation($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldEducation($eventid, $packagesold_values, $product_events, $product_type_events, $noofcompany_education,
                                       $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                       $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, edu_product_type, edu_no_cmp, product_bottom_price , product_mrp, ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', edu_product_type = '$product_type_events',
                                    edu_no_cmp = '$noofcompany_education',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }

    function addPackageSoldEvents($count_num){
        global $adb, $current_user;
        $module = 'Campaigns';
        $product = 'EVENTS';
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];

        for($i = 1; $i < $count_num; $i++) {
            $eventid = $_REQUEST['record_mode_event'][$i];
            $product_events = $_REQUEST['product_events'][$i];
            $product_type_events = $_REQUEST['product_type_events'][$i];
            $month_events = $_REQUEST['month_events'][$i];
            list($sponsorship_events, $y) = explode("__", $_REQUEST['sponsorship_events'][$i]);
            $bottom_price_events = $_REQUEST['bottom_price_events'][$i];
            $mrp_events = $_REQUEST['mrp_events'][$i];
            $discount_evevts = $_REQUEST['discount_events'][$i];
            $discount_amount_events = $_REQUEST['discount_amount_events'][$i];
            $offered_amount_events = $_REQUEST['offered_amount_events'][$i];
            $service_tax_amount_events = $_REQUEST['service_tax_amount_events'][$i];
            $total_amount_events = $_REQUEST['total_amount_events'][$i];

            $packagesold_values = array(
                'pref_leads'=>$product_events,
                'comment_product_type'=>$product_type_events,
                'campaignstatus'=>$sponsorship_events,
                'budgetcost'=>$month_events,
                'product_bottom_price'=>$bottom_price_events,
                'product_mrp'=>$mrp_events,
                'ps_discount'=>$discount_evevts,
                'ps_discount_amount'=>$discount_amount_events,
                'ps_offered_amount'=>$offered_amount_events,
                'ps_service_tax_amount'=>$service_tax_amount_events,
                'ps_total_amount'=>$total_amount_events
            );
            $adb->query("UPDATE vtiger_leadscf SET converted = '1' where leadid = $product_events ");
            if($eventid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $packagesold_num = $this->getEntityNum($module);

                $entity_values = array(
                    'campaign_no'=> $packagesold_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>$module
                );

                $all_values = array_merge($packagesold_values,  $entity_values);
                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid,$module, $currentdatetime, $currentdatetime,$packagesold_num));

                $adb->query("INSERT INTO vtiger_campaign (campaignid, campaigntype, campaign_no, comment_product_type, budgetcost, campaignstatus, product_bottom_price , product_mrp)
                            VALUES(".$crmid.", '$product', '".$packagesold_num."', '".$product_type_events."', '".$month_events."', '".$sponsorship_events."' , '".$bottom_price_events."' , '".$mrp_events."')");

                $adb->query("INSERT INTO vtiger_campaignscf (campaignid, pref_leads, ps_discount, ps_discount_amount,
                            ps_offered_amount, ps_service_tax_amount, ps_total_amount)
                             VALUES(".$crmid.", '".$product_events."', '".$discount_evevts."', '".$discount_amount_events."',
                             '".$offered_amount_events."', '".$service_tax_amount_events."', '".$total_amount_events."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid, $module));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, $module,$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {
                $this->updatePckageSoldEvents($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                    $service_tax_amount_events, $total_amount_events);
            }
        } // end for loop
    }

    function updatePckageSoldEvents($eventid, $packagesold_values, $product_events, $product_type_events, $sponsorship_events, $month_events,
                                    $bottom_price_events, $mrp_events, $discount_evevts, $discount_amount_events, $offered_amount_events,
                                    $service_tax_amount_events, $total_amount_events) {
        global $adb, $current_user;
        $module = 'Campaigns';
        $packagesold_event_qry = $adb->query("SELECT pref_leads, comment_product_type, campaignstatus, budgetcost, product_bottom_price , product_mrp,ps_discount
                            , ps_discount_amount, ps_offered_amount, ps_service_tax_amount, ps_total_amount
                            FROM vtiger_campaign
                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_campaign.campaignid = $eventid ");
        if($adb->num_rows($packagesold_event_qry) > 0) {
            $eventrow = $adb->fetchByAssoc($packagesold_event_qry);
            $counter = 0;
            foreach($packagesold_values as $key=>$row) {
                if(vtlib_purify($eventrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_campaign
                                    INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                    SET pref_leads = '$product_events', comment_product_type = '$product_type_events',
                                    campaignstatus = '$sponsorship_events', budgetcost = '$month_events',
                                    product_bottom_price = '$bottom_price_events', product_mrp = '$mrp_events' , ps_discount = '$discount_evevts',
                                    ps_discount_amount = '$discount_amount_events', ps_offered_amount = '$offered_amount_events' ,
                                    ps_service_tax_amount = '$service_tax_amount_events',ps_total_amount = '$total_amount_events',
                                     modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_campaign.campaignid = $eventid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, $module, $current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $eventrow[$key], $row));
                    $counter++;
                }
            }
        }
    }

    function  addPayment(){
        global $adb, $current_user;
        $date_format = $current_user->date_format;
        $assignedid = $_REQUEST['assigned_user_id'];
        $payment_mode = $_REQUEST['record_mode_payment'];
        $count_num = count($payment_mode);

        for($i = 1; $i < $count_num; $i++) {
            $paymentid = $_REQUEST['record_mode_payment'][$i];
            $payment_mode = $_REQUEST['payment_mode'][$i];
            $cheque_date = $_REQUEST['check_date'][$i];
            $cheque_date = DateTimeField::__convertToDBFormat($cheque_date, $date_format);
            $cheque_number = $_REQUEST['check_number'][$i];
            $ro_available = $_REQUEST['payble_at_location'][$i];
            $bank_name = $_REQUEST['bank_name'][$i];
            $amount = $_REQUEST['total_amount_payment'][$i];

            $online_mode = $_REQUEST['total_amount_payment'][$i];
            $slip_no = $_REQUEST['offered_amount_payment'][$i];
            $tan_no = $_REQUEST['service_tax_amount_payment'][$i];
            $cts = $_REQUEST['discount_amount_payment'][$i];
            $payment_values = array(
                'payment_mode'=>$payment_mode,
                'checkdate'=>$cheque_date,
                'checkno'=>$cheque_number,
                'ro_available'=>$ro_available,
                'bank_name'=>$bank_name,
                'amount'=>$amount,
                'onlinemode'=>$online_mode,
                'slip_number'=>$slip_no,
                'tan_no'=>$tan_no,
                'cts'=>$cts
            );

            if($paymentid == 0) { // Create Payment
                $crmid = $adb->getUniqueID("vtiger_crmentity");
                $createrid = $current_user->id;
                $currentdatetime = date("Y-m-d H:i:s");
                $patment_num = $this->getEntityNum('Project');

                $entity_values = array(
                    'project_no'=> $patment_num,
                    'assigned_user_id'=>$assignedid,
                    'createdtime'=>$currentdatetime,
                    'modifiedby'=>$createrid,
                    'record_id'=>$crmid,
                    'record_module'=>'Project'
                );

                $all_values = array_merge($payment_values,  $entity_values);

                $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
                $adb->pquery($query, array($crmid, $createrid, $assignedid,"Project", $currentdatetime, $currentdatetime,$patment_num));

                $adb->query("INSERT INTO vtiger_project (projectid, project_no, checkno, checkdate)
                VALUES(".$crmid.", '".$patment_num."', '".$cheque_number."' , '".$cheque_date."')");

                $adb->query("INSERT INTO vtiger_projectcf (projectid, payment_mode, ro_available, bank_name, amount, onlinemode, slip_number, tan_no, cts)
                VALUES(".$crmid.", '".$payment_mode."', '".$ro_available."', '".$adb->sql_escape_string($bank_name)."', '".$amount."', '".$online_mode."', '".$slip_no."', '".$tan_no."', '".$cts."')");

                $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
                $adb->pquery($sql, array($this->id,'Potentials',$crmid,'Project'));

                // Start Save in Modtracker table ******************
                $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
                                    VALUES(?,?,?,?,?,?)', Array($thisid, $crmid, 'Project',$current_user->id, date('Y-m-d H:i:s',time()), 2));

                foreach($all_values as $key=>$row) {
                    if($row != "")	{
                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,postvalue) VALUES(?,?,?)',
                            Array($thisid, $key, $row));
                    }
                }
            }
            else {

                $this->updatePayment($paymentid, $payment_values,$payment_mode,$cheque_date,$cheque_number,$ro_available,$bank_name,$amount,$online_mode,$slip_no,$tan_no,$cts);
                //$this->updatePayment($paymentid, $payment_values, $payment_mode, $cheque_date, $payble_at_location, $cheque_number, $bank_name, $amount);
            }
        } // end for loop
        // echo $counter;die;
    }

    function updatePayment($paymentid, $payment_values,$payment_mode,$cheque_date,$cheque_number,$ro_available,$bank_name,$amount,$online_mode,$slip_no,$tan_no,$cts) {
        global $adb, $current_user;
        $payment_qry = $adb->query("SELECT payment_mode,checkdate,checkno,ro_available,bank_name,amount,onlinemode,slip_number,tan_no,cts
                            FROM vtiger_project
                            INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_project.projectid = $paymentid ");
        if($adb->num_rows($payment_qry) > 0) {
            $paymentrow = $adb->fetchByAssoc($payment_qry);
            $counter = 0;
            foreach($payment_values as $key=>$row) {
                if(vtlib_purify($paymentrow[$key]) != $row)	{
                    if($counter == 0) {
                        $adb->query("UPDATE vtiger_project
                                    INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                                   	SET payment_mode = '$payment_mode', checkdate = '$cheque_date', checkno='$cheque_number', ro_available='$ro_available',
									bank_name='$bank_name', amount='$amount',onlinemode='$online_mode', slip_number='$slip_no',tan_no='$tan_no',cts='$cts',
									modifiedby = ".$current_user->id.",
                                    modifiedtime = '".date('Y-m-d H:i:s')."' where vtiger_projectcf.projectid = $paymentid "
                        );

                        $thisid = $adb->getUniqueId('vtiger_modtracker_basic');
                        $adb->pquery('INSERT INTO vtiger_modtracker_basic(id, crmid, module, whodid, changedon, status)
					                  VALUES(?,?,?,?,?,?)', Array($thisid, $paymentid, 'Project',$current_user->id, date('Y-m-d H:i:s',time()), 0));

                        $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                            Array($thisid, modifiedby, 0, $current_user->id));
                    }
                    $adb->pquery('INSERT INTO vtiger_modtracker_detail(id,fieldname,prevalue, postvalue) VALUES(?,?,?,?)',
                        Array($thisid, $key, $paymentrow[$key], $row));
                    $counter++;
                }
            }
        }
    }

    function getEntityNum($module) {
        global $adb;
        $query_num = $adb->query("select prefix, cur_id from vtiger_modentity_num where semodule='$module' and active = 1");
        $result_num = $adb->fetch_array($query_num);
        $prefix = $result_num['prefix'];
        $cur_id = $result_num['cur_id'];
        $entity_num = $prefix.$cur_id;
        $next_curr_id = $cur_id + 1;
        $adb->query("update vtiger_modentity_num set cur_id = ".$next_curr_id." where semodule='$module' and active = 1");
        return $entity_num;
    }

    /** Function to create list query
     * @param reference variable - where condition is passed when the query is executed
     * Returns Query.
     */
    function create_list_query($order_by, $where)
    {
        global $log,$current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');
        $tab_id = getTabid("Potentials");
        $log->debug("Entering create_list_query(".$order_by.",". $where.") method ...");
        // Determine if the vtiger_account name is present in the where clause.
        $account_required = preg_match("/accounts\.name/", $where);

        if($account_required)
        {
            $query = "SELECT vtiger_potential.potentialid,  vtiger_potential.potentialname, vtiger_potential.dateclosed FROM vtiger_potential, vtiger_account ";
            $where_auto = "account.accountid = vtiger_potential.related_to AND vtiger_crmentity.deleted=0 ";
        }
        else
        {
            $query = 'SELECT vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_crmentity.smcreatorid, vtiger_potential.closingdate FROM vtiger_potential inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid LEFT JOIN vtiger_groups on vtiger_groups.groupid = vtiger_crmentity.smownerid left join vtiger_users on vtiger_users.id = vtiger_crmentity.smownerid ';
            $where_auto = ' AND vtiger_crmentity.deleted=0';
        }

        $query .= $this->getNonAdminAccessControlQuery('Potentials',$current_user);
        if($where != "")
            $query .= " where $where ".$where_auto;
        else
            $query .= " where ".$where_auto;
        if($order_by != "")
            $query .= " ORDER BY $order_by";

        $log->debug("Exiting create_list_query method ...");
        return $query;
    }

    /** Function to export the Opportunities records in CSV Format
     * @param reference variable - order by is passed when the query is executed
     * @param reference variable - where condition is passed when the query is executed
     * Returns Export Potentials Query.
     */
    function create_export_query($where)
    {
        global $log;
        global $current_user;
        $log->debug("Entering create_export_query(". $where.") method ...");

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery("Potentials", "detail_view");
        $fields_list = getFieldsListFromQuery($sql);

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT $fields_list,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				FROM vtiger_potential
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_potential.potentialid
				LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid=vtiger_users.id
				LEFT JOIN vtiger_account on vtiger_potential.related_to=vtiger_account.accountid
				LEFT JOIN vtiger_contactdetails on vtiger_potential.contact_id=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_potentialscf on vtiger_potentialscf.potentialid=vtiger_potential.potentialid
                LEFT JOIN vtiger_groups
        	        ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_campaign
					ON vtiger_campaign.campaignid = vtiger_potential.campaignid";

        $query .= $this->getNonAdminAccessControlQuery('Potentials',$current_user);
        $where_auto = "  vtiger_crmentity.deleted = 0 ";

        if($where != "")
            $query .= "  WHERE ($where) AND ".$where_auto;
        else
            $query .= "  WHERE ".$where_auto;

        $log->debug("Exiting create_export_query method ...");
        return $query;

    }



    /** Returns a list of the associated contacts
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function get_contacts($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_contacts(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        $accountid = $this->column_fields['related_to'];
        $search_string = "&fromPotential=true&acc_id=$accountid";

        if($actions) {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab$search_string','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = 'select case when (vtiger_users.user_name not like "") then '.$userNameSql.' else vtiger_groups.groupname end as user_name,
					vtiger_contactdetails.accountid,vtiger_potential.potentialid, vtiger_potential.potentialname, vtiger_contactdetails.contactid,
					vtiger_contactdetails.lastname, vtiger_contactdetails.firstname, vtiger_contactdetails.title, vtiger_contactdetails.department,
					vtiger_contactdetails.email, vtiger_contactdetails.phone, vtiger_crmentity.crmid, vtiger_crmentity.smownerid,
					vtiger_crmentity.modifiedtime , vtiger_account.accountname from vtiger_potential
					inner join vtiger_contpotentialrel on vtiger_contpotentialrel.potentialid = vtiger_potential.potentialid
					inner join vtiger_contactdetails on vtiger_contpotentialrel.contactid = vtiger_contactdetails.contactid
					INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
					INNER JOIN vtiger_contactsubdetails ON vtiger_contactdetails.contactid = vtiger_contactsubdetails.contactsubscriptionid
					INNER JOIN vtiger_customerdetails ON vtiger_contactdetails.contactid = vtiger_customerdetails.customerid
					INNER JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid
					inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
					left join vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left join vtiger_users on vtiger_crmentity.smownerid=vtiger_users.id
					where vtiger_potential.potentialid = '.$id.' and vtiger_crmentity.deleted=0';

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_contacts method ...");
        return $return_value;
    }

    /** Returns a list of the associated calls
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc..
     * All Rights Reserved..
     * Contributor(s): ______________________________________..
     */
    function get_activities($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_activities(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/Activity.php");
        $other = new Activity();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        $button .= '<input type="hidden" name="activity_mode">';

        if($actions) {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Task\";' type='submit' name='button'" .
                        " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
                }
                if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id', 'readwrite') == '0') {
                    $button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create'" .
                        " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\";this.form.return_module.value=\"$this_module\";this.form.activity_mode.value=\"Events\";' type='submit' name='button'" .
                        " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
                }
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_activity.activityid as 'tmp_activity_id',vtiger_activity.*,vtiger_seactivityrel.crmid as parent_id, vtiger_contactdetails.lastname,vtiger_contactdetails.firstname,
					vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_crmentity.modifiedtime,
					case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_recurringevents.recurringtype from vtiger_activity
					inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
					left join vtiger_cntactivityrel on vtiger_cntactivityrel.activityid = vtiger_activity.activityid
					left join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_cntactivityrel.contactid
					inner join vtiger_potential on vtiger_potential.potentialid=vtiger_seactivityrel.crmid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left outer join vtiger_recurringevents on vtiger_recurringevents.activityid=vtiger_activity.activityid
					where vtiger_seactivityrel.crmid=".$id." and vtiger_crmentity.deleted=0
					and ((vtiger_activity.activitytype='Task' and vtiger_activity.status not in ('Completed','Deferred'))
					or (vtiger_activity.activitytype NOT in ('Emails','Task') and  vtiger_activity.eventstatus not in ('','Held'))) ";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_activities method ...");
        return $return_value;
    }

    /**
     * Function to get Contact related Products
     * @param  integer   $id  - contactid
     * returns related Products record in array format
     */
    function get_products($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_products(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        if($actions) {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $query = "SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.productcode,
				vtiger_products.commissionrate, vtiger_products.qty_per_unit, vtiger_products.unit_price,
				vtiger_crmentity.crmid, vtiger_crmentity.smownerid
				FROM vtiger_products
				INNER JOIN vtiger_seproductsrel ON vtiger_products.productid = vtiger_seproductsrel.productid and vtiger_seproductsrel.setype = 'Potentials'
				INNER JOIN vtiger_productcf
				ON vtiger_products.productid = vtiger_productcf.productid 
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_products.productid
				INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_seproductsrel.crmid
				LEFT JOIN vtiger_users
					ON vtiger_users.id=vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups
					ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				WHERE vtiger_crmentity.deleted = 0 AND vtiger_potential.potentialid = $id";

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_products method ...");
        return $return_value;
    }

    /**	Function used to get the Sales Stage history of the Potential
     *	@param $id - potentialid
     *	return $return_data - array with header and the entries in format Array('header'=>$header,'entries'=>$entries_list) where as $header and $entries_list are array which contains all the column values of an row
     */
    function get_stage_history($id)
    {
        global $log;
        $log->debug("Entering get_stage_history(".$id.") method ...");

        global $adb;
        global $mod_strings;
        global $app_strings;

        $query = 'select vtiger_potstagehistory.*, vtiger_potential.potentialname from vtiger_potstagehistory inner join vtiger_potential on vtiger_potential.potentialid = vtiger_potstagehistory.potentialid inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_potential.potentialid where vtiger_crmentity.deleted = 0 and vtiger_potential.potentialid = ?';
        $result=$adb->pquery($query, array($id));
        $noofrows = $adb->num_rows($result);

        $header[] = $app_strings['LBL_AMOUNT'];
        $header[] = $app_strings['LBL_SALES_STAGE'];
        $header[] = $app_strings['LBL_PROBABILITY'];
        $header[] = $app_strings['LBL_CLOSE_DATE'];
        $header[] = $app_strings['LBL_LAST_MODIFIED'];

        //Getting the field permission for the current user. 1 - Not Accessible, 0 - Accessible
        //Sales Stage, Expected Close Dates are mandatory fields. So no need to do security check to these fields.
        global $current_user;

        //If field is accessible then getFieldVisibilityPermission function will return 0 else return 1
        $amount_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'amount') != '0')? 1 : 0;
        $probability_access = (getFieldVisibilityPermission('Potentials', $current_user->id, 'probability') != '0')? 1 : 0;
        $picklistarray = getAccessPickListValues('Potentials');

        $potential_stage_array = $picklistarray['sales_stage'];
        //- ==> picklist field is not permitted in profile
        //Not Accessible - picklist is permitted in profile but picklist value is not permitted
        $error_msg = 'Not Accessible';

        while($row = $adb->fetch_array($result))
        {
            $entries = Array();

            $entries[] = ($amount_access != 1)? $row['amount'] : 0;
            $entries[] = (in_array($row['stage'], $potential_stage_array))? $row['stage']: $error_msg;
            $entries[] = ($probability_access != 1) ? $row['probability'] : 0;
            $entries[] = DateTimeField::convertToUserFormat($row['closedate']);
            $date = new DateTimeField($row['lastmodified']);
            $entries[] = $date->getDisplayDate();

            $entries_list[] = $entries;
        }

        $return_data = Array('header'=>$header,'entries'=>$entries_list);

        $log->debug("Exiting get_stage_history method ...");

        return $return_data;
    }

    /**
     * Function to get Potential related Task & Event which have activity type Held, Completed or Deferred.
     * @param  integer   $id
     * returns related Task or Event record in array format
     */
    function get_history($id)
    {
        global $log;
        $log->debug("Entering get_history(".$id.") method ...");
        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "SELECT vtiger_activity.activityid, vtiger_activity.subject, vtiger_activity.status,
		vtiger_activity.eventstatus, vtiger_activity.activitytype,vtiger_activity.date_start,
		vtiger_activity.due_date, vtiger_activity.time_start,vtiger_activity.time_end,
		vtiger_crmentity.modifiedtime, vtiger_crmentity.createdtime,
		vtiger_crmentity.description,case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
				from vtiger_activity
				inner join vtiger_seactivityrel on vtiger_seactivityrel.activityid=vtiger_activity.activityid
				inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_activity.activityid
				left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
				left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
				where (vtiger_activity.activitytype != 'Emails')
				and (vtiger_activity.status = 'Completed' or vtiger_activity.status = 'Deferred' or (vtiger_activity.eventstatus = 'Held' and vtiger_activity.eventstatus != ''))
				and vtiger_seactivityrel.crmid=".$id."
                                and vtiger_crmentity.deleted = 0";
        //Don't add order by, because, for security, one more condition will be added with this query in include/RelatedListView.php

        $log->debug("Exiting get_history method ...");
        return getHistory('Potentials',$query,$id);
    }


    /**
     * Function to get Potential related Quotes
     * @param  integer   $id  - potentialid
     * returns related Quotes record in array format
     */
    function get_quotes($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_quotes(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "select case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name,
					vtiger_account.accountname, vtiger_crmentity.*, vtiger_quotes.*, vtiger_potential.potentialname from vtiger_quotes
					inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_quotes.quoteid
					left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_quotes.potentialid
					left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
					left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
					inner join vtiger_account on vtiger_account.accountid=vtiger_quotes.accountid
					where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid=".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_quotes method ...");
        return $return_value;
    }

    /**
     * Function to get Potential related SalesOrder
     * @param  integer   $id  - potentialid
     * returns related SalesOrder record in array format
     */
    function get_salesorder($id, $cur_tab_id, $rel_tab_id, $actions=false) {
        global $log, $singlepane_view,$currentModule,$current_user;
        $log->debug("Entering get_salesorder(".$id.") method ...");
        $this_module = $currentModule;

        $related_module = vtlib_getModuleNameById($rel_tab_id);
        require_once("modules/$related_module/$related_module.php");
        $other = new $related_module();
        vtlib_setup_modulevars($related_module, $other);
        $singular_modname = vtlib_toSingular($related_module);

        $parenttab = getParentTab();

        if($singlepane_view == 'true')
            $returnset = '&return_module='.$this_module.'&return_action=DetailView&return_id='.$id;
        else
            $returnset = '&return_module='.$this_module.'&return_action=CallRelatedList&return_id='.$id;

        $button = '';

        if($actions && getFieldVisibilityPermission($related_module, $current_user->id, 'potential_id', 'readwrite') == '0') {
            if(is_string($actions)) $actions = explode(',', strtoupper($actions));
            if(in_array('SELECT', $actions) && isPermitted($related_module,4, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_SELECT')." ". getTranslatedString($related_module). "' class='crmbutton small edit' type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\" value='". getTranslatedString('LBL_SELECT'). " " . getTranslatedString($related_module) ."'>&nbsp;";
            }
            if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
                $button .= "<input title='".getTranslatedString('LBL_ADD_NEW'). " ". getTranslatedString($singular_modname) ."' class='crmbutton small create'" .
                    " onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
                    " value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString($singular_modname) ."'>&nbsp;";
            }
        }

        $userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>
            'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');
        $query = "select vtiger_crmentity.*, vtiger_salesorder.*, vtiger_quotes.subject as quotename
			, vtiger_account.accountname, vtiger_potential.potentialname,case when
			(vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname
			end as user_name from vtiger_salesorder
			inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_salesorder.salesorderid
			left outer join vtiger_quotes on vtiger_quotes.quoteid=vtiger_salesorder.quoteid
			left outer join vtiger_account on vtiger_account.accountid=vtiger_salesorder.accountid
			left outer join vtiger_potential on vtiger_potential.potentialid=vtiger_salesorder.potentialid
			left join vtiger_groups on vtiger_groups.groupid=vtiger_crmentity.smownerid
			left join vtiger_users on vtiger_users.id=vtiger_crmentity.smownerid
			 where vtiger_crmentity.deleted=0 and vtiger_potential.potentialid = ".$id;

        $return_value = GetRelatedList($this_module, $related_module, $other, $query, $button, $returnset);

        if($return_value == null) $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        $log->debug("Exiting get_salesorder method ...");
        return $return_value;
    }

    /**
     * Move the related records of the specified list of id's to the given record.
     * @param String This module name
     * @param Array List of Entity Id's from which related records need to be transfered
     * @param Integer Id of the the Record to which the related records are to be moved
     */
    function transferRelatedRecords($module, $transferEntityIds, $entityId) {
        global $adb,$log;
        $log->debug("Entering function transferRelatedRecords ($module, $transferEntityIds, $entityId)");

        $rel_table_arr = Array("Activities"=>"vtiger_seactivityrel","Contacts"=>"vtiger_contpotentialrel","Products"=>"vtiger_seproductsrel",
            "Attachments"=>"vtiger_seattachmentsrel","Quotes"=>"vtiger_quotes","SalesOrder"=>"vtiger_salesorder",
            "Documents"=>"vtiger_senotesrel");

        $tbl_field_arr = Array("vtiger_seactivityrel"=>"activityid","vtiger_contpotentialrel"=>"contactid","vtiger_seproductsrel"=>"productid",
            "vtiger_seattachmentsrel"=>"attachmentsid","vtiger_quotes"=>"quoteid","vtiger_salesorder"=>"salesorderid",
            "vtiger_senotesrel"=>"notesid");

        $entity_tbl_field_arr = Array("vtiger_seactivityrel"=>"crmid","vtiger_contpotentialrel"=>"potentialid","vtiger_seproductsrel"=>"crmid",
            "vtiger_seattachmentsrel"=>"crmid","vtiger_quotes"=>"potentialid","vtiger_salesorder"=>"potentialid",
            "vtiger_senotesrel"=>"crmid");

        foreach($transferEntityIds as $transferId) {
            foreach($rel_table_arr as $rel_module=>$rel_table) {
                $id_field = $tbl_field_arr[$rel_table];
                $entity_id_field = $entity_tbl_field_arr[$rel_table];
                // IN clause to avoid duplicate entries
                $sel_result =  $adb->pquery("select $id_field from $rel_table where $entity_id_field=? " .
                    " and $id_field not in (select $id_field from $rel_table where $entity_id_field=?)",
                    array($transferId,$entityId));
                $res_cnt = $adb->num_rows($sel_result);
                if($res_cnt > 0) {
                    for($i=0;$i<$res_cnt;$i++) {
                        $id_field_value = $adb->query_result($sel_result,$i,$id_field);
                        $adb->pquery("update $rel_table set $entity_id_field=? where $entity_id_field=? and $id_field=?",
                            array($entityId,$transferId,$id_field_value));
                    }
                }
            }
        }
        parent::transferRelatedRecords($module, $transferEntityIds, $entityId);
        $log->debug("Exiting transferRelatedRecords...");
    }

    /*
     * Function to get the secondary query part of a report
     * @param - $module primary module name
     * @param - $secmodule secondary module name
     * returns the query string formed on fetching the related data for report for secondary module
     */
    function generateReportsSecQuery($module,$secmodule,$queryplanner){
        $matrix = $queryplanner->newDependencyMatrix();
        $matrix->setDependency('vtiger_crmentityPotentials',array('vtiger_groupsPotentials','vtiger_usersPotentials','vtiger_lastModifiedByPotentials'));
        $matrix->setDependency('vtiger_potential', array('vtiger_crmentityPotentials','vtiger_accountPotentials',
            'vtiger_contactdetailsPotentials','vtiger_campaignPotentials','vtiger_potentialscf'));


        if (!$queryplanner->requireTable("vtiger_potential",$matrix)){
            return '';
        }

        $query = $this->getRelationQuery($module,$secmodule,"vtiger_potential","potentialid", $queryplanner);

        if ($queryplanner->requireTable("vtiger_crmentityPotentials",$matrix)){
            $query .= " left join vtiger_crmentity as vtiger_crmentityPotentials on vtiger_crmentityPotentials.crmid=vtiger_potential.potentialid and vtiger_crmentityPotentials.deleted=0";
        }
        if ($queryplanner->requireTable("vtiger_accountPotentials")){
            $query .= " left join vtiger_account as vtiger_accountPotentials on vtiger_potential.related_to = vtiger_accountPotentials.accountid";
        }
        if ($queryplanner->requireTable("vtiger_contactdetailsPotentials")){
            $query .= " left join vtiger_contactdetails as vtiger_contactdetailsPotentials on vtiger_potential.contact_id = vtiger_contactdetailsPotentials.contactid";
        }
        if ($queryplanner->requireTable("vtiger_potentialscf")){
            $query .= " left join vtiger_potentialscf on vtiger_potentialscf.potentialid = vtiger_potential.potentialid";
        }
        if ($queryplanner->requireTable("vtiger_groupsPotentials")){
            $query .= " left join vtiger_groups vtiger_groupsPotentials on vtiger_groupsPotentials.groupid = vtiger_crmentityPotentials.smownerid";
        }
        if ($queryplanner->requireTable("vtiger_usersPotentials")){
            $query .= " left join vtiger_users as vtiger_usersPotentials on vtiger_usersPotentials.id = vtiger_crmentityPotentials.smownerid";
        }
        if ($queryplanner->requireTable("vtiger_campaignPotentials")){
            $query .= " left join vtiger_campaign as vtiger_campaignPotentials on vtiger_potential.campaignid = vtiger_campaignPotentials.campaignid";
        }
        if ($queryplanner->requireTable("vtiger_lastModifiedByPotentials")){
            $query .= " left join vtiger_users as vtiger_lastModifiedByPotentials on vtiger_lastModifiedByPotentials.id = vtiger_crmentityPotentials.modifiedby ";
        }
        return $query;
    }

    /*
     * Function to get the relation tables for related modules
     * @param - $secmodule secondary module name
     * returns the array with table names and fieldnames storing relations between module and this module
     */
    function setRelationTables($secmodule){
        $rel_tables = array (
            "Calendar" => array("vtiger_seactivityrel"=>array("crmid","activityid"),"vtiger_potential"=>"potentialid"),
            "Products" => array("vtiger_seproductsrel"=>array("crmid","productid"),"vtiger_potential"=>"potentialid"),
            "Quotes" => array("vtiger_quotes"=>array("potentialid","quoteid"),"vtiger_potential"=>"potentialid"),
            "SalesOrder" => array("vtiger_salesorder"=>array("potentialid","salesorderid"),"vtiger_potential"=>"potentialid"),
            "Documents" => array("vtiger_senotesrel"=>array("crmid","notesid"),"vtiger_potential"=>"potentialid"),
            "Accounts" => array("vtiger_potential"=>array("potentialid","related_to")),
            "Contacts" => array("vtiger_potential"=>array("potentialid","contact_id")),
        );
        return $rel_tables[$secmodule];
    }

    // Function to unlink all the dependent entities of the given Entity by Id
    function unlinkDependencies($module, $id) {
        global $log;
        /*//Backup Activity-Potentials Relation
        $act_q = "select activityid from vtiger_seactivityrel where crmid = ?";
        $act_res = $this->db->pquery($act_q, array($id));
        if ($this->db->num_rows($act_res) > 0) {
            for($k=0;$k < $this->db->num_rows($act_res);$k++)
            {
                $act_id = $this->db->query_result($act_res,$k,"activityid");
                $params = array($id, RB_RECORD_DELETED, 'vtiger_seactivityrel', 'crmid', 'activityid', $act_id);
                $this->db->pquery("insert into vtiger_relatedlists_rb values (?,?,?,?,?,?)", $params);
            }
        }
        $sql = 'delete from vtiger_seactivityrel where crmid = ?';
        $this->db->pquery($sql, array($id));*/

        parent::unlinkDependencies($module, $id);
    }

    // Function to unlink an entity with given Id from another entity
    function unlinkRelationship($id, $return_module, $return_id) {
        global $log;
        if(empty($return_module) || empty($return_id)) return;

        if($return_module == 'Accounts') {
            $this->trash($this->module_name, $id);
        } elseif($return_module == 'Campaigns') {
            $sql = 'UPDATE vtiger_potential SET campaignid = ? WHERE potentialid = ?';
            $this->db->pquery($sql, array(null, $id));
        } elseif($return_module == 'Products') {
            $sql = 'DELETE FROM vtiger_seproductsrel WHERE crmid=? AND productid=?';
            $this->db->pquery($sql, array($id, $return_id));
        } elseif($return_module == 'Contacts') {
            $sql = 'DELETE FROM vtiger_contpotentialrel WHERE potentialid=? AND contactid=?';
            $this->db->pquery($sql, array($id, $return_id));

            // Potential directly linked with Contact (not through Account - vtiger_contpotentialrel)
            $directRelCheck = $this->db->pquery('SELECT related_to FROM vtiger_potential WHERE potentialid=? AND contact_id=?', array($id, $return_id));
            if($this->db->num_rows($directRelCheck)) {
                $this->trash($this->module_name, $id);
            }

        } else {
            $sql = 'DELETE FROM vtiger_crmentityrel WHERE (crmid=? AND relmodule=? AND relcrmid=?) OR (relcrmid=? AND module=? AND crmid=?)';
            $params = array($id, $return_module, $return_id, $id, $return_module, $return_id);
            $this->db->pquery($sql, $params);
        }
    }

    function save_related_module($module, $crmid, $with_module, $with_crmids) {
        $adb = PearDatabase::getInstance();

        if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
        foreach($with_crmids as $with_crmid) {
            if($with_module == 'Contacts') { //When we select contact from potential related list
                $sql = "insert into vtiger_contpotentialrel values (?,?)";
                $adb->pquery($sql, array($with_crmid, $crmid));

            } elseif($with_module == 'Products') {//when we select product from potential related list
                $sql = "insert into vtiger_seproductsrel values (?,?,?)";
                $adb->pquery($sql, array($crmid, $with_crmid,'Potentials'));

            } else {
                parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            }
        }
    }

}
?>