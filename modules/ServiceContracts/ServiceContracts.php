<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
require_once('data/CRMEntity.php');
require_once('data/Tracker.php');
require_once('modules/Emails/mail.php');

class ServiceContracts extends CRMEntity {
    var $db, $log; // Used in class functions of CRMEntity

    var $table_name = 'vtiger_servicecontracts';
    var $table_index= 'servicecontractsid';
    var $column_fields = Array();

    /** Indicator if this is a custom module or standard module */
    var $IsCustomModule = true;

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_servicecontractscf', 'servicecontractsid');

    /**
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_servicecontracts', 'vtiger_servicecontractscf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_servicecontracts' => 'servicecontractsid',
        'vtiger_servicecontractscf'=>'servicecontractsid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Subject' => Array('servicecontracts', 'subject'),
        'Assigned To' => Array('crmentity','smownerid'),
        'Contract No' => Array('servicecontracts','contract_no'),
        'Used Units' => Array('servicecontracts','used_units'),
        'Total Units' => Array('servicecontracts','total_units')
    );
    var $list_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Subject' => 'subject',
        'Assigned To' => 'assigned_user_id',
        'Contract No' =>  'contract_no',
        'Used Units' => 'used_units',
        'Total Units' => 'total_units'
    );

    // Make the field link to detail view
    var $list_link_field = 'subject';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'Subject' => Array('servicecontracts', 'subject'),
        'Contract No' => Array('servicecontracts', 'contract_no'),
        'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        'Used Units' => Array('servicecontracts','used_units'),
        'Total Units' => Array('servicecontracts','total_units')
    );
    var $search_fields_name = Array (
        /* Format: Field Label => fieldname */
        'Subject' => 'subject',
        'Contract No' => 'contract_no',
        'Assigned To' => 'assigned_user_id',
        'Used Units' => 'used_units',
        'Total Units' => 'total_units'
    );

    // For Popup window record selection
    var $popup_fields = Array ('subject');

    // Placeholder for sort fields - All the fields will be initialized for Sorting through initSortFields
    var $sortby_fields = Array();

    // For Alphabetical search
    var $def_basicsearch_col = 'subject';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'subject';

    // Required Information for enabling Import feature
    var $required_fields = Array ('assigned_user_id'=>1);

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('subject','assigned_user_id');

    // Callback function list during Importing
    var $special_functions = Array('set_import_assigned_user');

    var $default_order_by = 'subject';
    var $default_sort_order='ASC';

    function __construct() {
        global $log;
        $this->column_fields = getColumnFields(get_class($this));
        $this->db = new PearDatabase();
        $this->log = $log;
    }

    function save_module($module) {
        global $adb, $current_user;
        $return_action = $_REQUEST['action'];
        $for_module = $_REQUEST['sourceModule'];
        $for_crmid  = $_REQUEST['sourceRecord'];
        $approval_status  = $_REQUEST['approval_status'];
        $currentdatetime = date('Y-m-d H:i:s');
        //echo "<pre>";print_r($_REQUEST);

        if(isset($_REQUEST['currentid']) && $_REQUEST['currentid'] != "" ) {
            $paymentsdata = "";
            $total_payment_amount = "";
            $payment_qry = $adb->query("SELECT leadsource, potentialname, payment_mode, checkdate, vtiger_projectcf.amount as amt FROM vtiger_project
                                        INNER JOIN vtiger_projectcf ON vtiger_projectcf.projectid = vtiger_project.projectid
                                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_project.projectid
                                        INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_project.projectid AND vtiger_crmentityrel.relmodule = 'Project')
                                        INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                        INNER JOIN vtiger_potential ON vtiger_potential.potentialid = vtiger_potentialscf.potentialid
                                        WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $for_crmid ");
            if($adb->num_rows($payment_qry) > 0) {
                while($row = $adb->fetchByAssoc($payment_qry)) {
                    $paymentsdata .= '<tr><td>'.$row["payment_mode"].'</td><td>'.$row["checkdate"].'</td><td>'.$row["amt"].'</td></tr>';
                    $csaf_no = $row["potentialname"];
                    $creditperiod = $row["leadsource"];
                    $total_payment_amount = $total_payment_amount + str_replace(",","",$row['amt']);
                }
            }

            $total_bp_amount = 0.00;
            $total_offered_amount = 0.00;
            $total_sale_amount = 0.00;
            $discount_bp_amount = 0.00;
            $total_package_name = "";
            $packagesold_qry = $adb->query("SELECT campaigntype,ps_discount_amount, ps_total_amount, crmentity_pot.smownerid as ownerid, payment_mode_app, product_bottom_price, ps_offered_amount FROM vtiger_campaign
                                            INNER JOIN vtiger_campaignscf ON vtiger_campaignscf.campaignid = vtiger_campaign.campaignid
                                            INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_campaign.campaignid
                                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_campaign.campaignid AND vtiger_crmentityrel.relmodule = 'Campaigns')
                                            INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                            INNER JOIN vtiger_crmentity as crmentity_pot ON crmentity_pot.crmid = vtiger_potentialscf.potentialid
                                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $for_crmid
                                             AND campaigntype != 'SMART JOBS' ORDER BY vtiger_crmentity.crmid ");
            $temp_coumter = 0;
            if($adb->num_rows($packagesold_qry) > 0) {
                while($row = $adb->fetchByAssoc($packagesold_qry)) {
                    $total_bp_amount = $total_bp_amount + str_replace(",","",$row['product_bottom_price']);
                    $total_offered_amount = $total_offered_amount + str_replace(",","",$row['ps_offered_amount']);
                    $total_sale_amount = $total_sale_amount + str_replace(",","",$row['ps_total_amount']);
                    $discount_bp_amount = $discount_bp_amount + str_replace(",","",$row['ps_discount_amount']);
                    $payment_mode = $row['payment_mode_app'];
                    $assigned_userid = $row['ownerid'];
                    if($temp_coumter == 0)
                        $total_package_name = $row['campaigntype'];
                    else
                        $total_package_name .= ', '.$row['campaigntype'];
                    $temp_coumter++;
                }
            }

            if(floor($total_sale_amount) === floor($total_payment_amount)) {
                $result_assigned = $this->getCurrentUserDetail($assigned_userid);
                $am_name = $result_assigned['fullname'];
                $assigned_email = $result_assigned['emailid'];

                $customer_qry = $adb->query("SELECT
                                   DISTINCT cf_927
                                FROM
                                  vtiger_contactscf
                                  INNER JOIN vtiger_crmentity
                                    ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
                                  INNER JOIN vtiger_crmentityrel
                                    ON (
                                      vtiger_crmentityrel.crmid = vtiger_contactscf.contactid
                                      AND vtiger_crmentityrel.relmodule = 'Potentials'
                                    )
                                  INNER JOIN vtiger_potential
                                    ON vtiger_potential.potentialid = vtiger_crmentityrel.relcrmid
                                WHERE vtiger_crmentity.deleted = 0
                                  AND vtiger_potential.potentialid = $for_crmid  ");
                if($adb->num_rows($customer_qry) > 0) {
                    $row = $adb->fetchByAssoc($customer_qry);
                    $clientname = $row["cf_927"];

                }
                $approval_level = 2; // National Head
                $new_bp_amount = $total_bp_amount - (($total_bp_amount*15)/100);
                if($total_offered_amount >= $total_bp_amount)
                    $approval_level = 5; // Branch Head
                elseif($total_offered_amount < $total_bp_amount && $total_offered_amount > $new_bp_amount)
                    $approval_level = 3; // National Sales Manager
//echo $total_offered_amount.'___'.$total_bp_amount.'___'.$new_bp_amount.'___'.$approval_level;die;
                $approval_history = '';
                $last_action_userid = '';
                $all_userid = array();
                $approval_qry = $adb->query("SELECT vtiger_users.email1 as email, smownerid, approval_status, remark, concat(vtiger_users.first_name,' ',vtiger_users.last_name) as 'fullname' FROM vtiger_servicecontractscf
                                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_servicecontractscf.servicecontractsid
                                                INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_servicecontractscf.servicecontractsid AND vtiger_crmentityrel.relmodule = 'ServiceContracts')
                                                INNER JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_crmentityrel.crmid
                                                INNER JOIN vtiger_users  on vtiger_users.id = vtiger_crmentity.smownerid
                                                WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $for_crmid
                                                ORDER BY vtiger_crmentity.crmid ");
                if($adb->num_rows($approval_qry) > 0) {
                    while($row = $adb->fetchByAssoc($approval_qry)) {
                        $fullname = $row['fullname'];
                        $remark = $row['remark'];
                        $approvestatus = $row['approval_status'];
                        $last_action_userid = $row['smownerid'];
                        $all_userid[$last_action_userid] = $row['email'];
                        $approval_history .= '<tr><td>'.$fullname.'</td><td>'.$approvestatus.'</td><td>'.$remark.'</td></tr>';
                    }
                }

                if($last_action_userid == "" || $approvestatus == 'Rejected'){
                    $current_action_userid = $assigned_userid;
                    $result_reportto = $this->getCurrentUserDetail($current_action_userid);
                    $userid1 = $result_reportto['userid'];
                    $fullname1 = $result_reportto['fullname'];
                    $user_name1 = $result_reportto['username'];
                    $email1 = $result_reportto['emailid'];
                    $depth1 = $result_reportto['depth'];
                    $profileid1 = $result_reportto['profileid'];
                }
                else{
                    $result_reportto = $this->getReportToDetail($last_action_userid,'Active');
                    $userid1 = $result_reportto['userid'];
                    $user_name1 = $result_reportto['username'];
                    $fullname1 = $result_reportto['fullname'];
                    $email1 = $result_reportto['emailid'];
                    $depth1 = $result_reportto['depth'];
                    $profileid1 = $result_reportto['profileid'];
                    $current_action_userid = $userid1;
                }
                $current_userid = $current_action_userid;
                for($i=0; $i<=5; $i++) {
                    if($i > 0){
                        $result_next_reportto = $this->getReportToDetail($current_userid);
                        $current_userid = $result_next_reportto['userid'];
                    }
                    $result_reportto = $this->getReportToDetail($current_userid,'Active');
                    if(count($result_reportto) > 0){
                        $userid2 = $result_reportto['userid'];
                        $user_name2 = $result_reportto['username'];
                        $fullname2 = $result_reportto['fullname'];
                        $email2 = $result_reportto['emailid'];
                        $depth2 = $result_reportto['depth'];
                        $profileid2 = $result_reportto['profileid'];
                        break;
                    }
                }
                //$descriptionsalesapproval
                //$descriptionsalesapproved
                //$descriptionfinancesapproval
                //$descriptionfinanceapproved

                $approval_end_status = 0;
                $finance_approval = 0;
                //echo $approval_level.'___'.$depth1;die;
                if($approval_level == $depth1){
                    $approval_end_status = 1;
                }

                if($approval_end_status == 1 && $payment_mode != "Full Payment"){
                    $finance_approval = 1;
                    if($payment_mode == "PDC"){
                        $result_reportto = $this->getCurrentUserDetail(2, 'Finance');
                        $userid2 = $result_reportto['userid'];
                        $email2 = $result_reportto['emailid'];
                        $fullname3 = $result_reportto['fullname'];
                    }else{
                        $result_reportto = $this->getCurrentUserDetail(3, 'Finance');
                        $userid2 = $result_reportto['userid'];
                        $email2 = $result_reportto['emailid'];
                        $fullname3 = $result_reportto['fullname'];
                    }
                }
                $sales_profileid = array(4, 8, 9, 10);
                $finance_profileid = array(2, 3);
                if($approval_status == "Rejected")
                    $url_user_id = $assigned_userid;
                else
                    $url_user_id = $userid2;
                $approval_history .= '<tr><td>'.$fullname1.'</td><td>'.$_REQUEST['approval_status'].'</td><td>'.$_REQUEST['remark'].'</td></tr>';
                $urldataapprove = 'approvalid__'.$for_crmid.'%*@userid__'.$url_user_id.'%*@approvalstatus__Approved';
                $urldatareject = 'approvalid__'.$for_crmid.'%*@userid__'.$url_user_id.'%*@approvalstatus__Rejected';
                $urldataapprove = base64_encode($urldataapprove);
                $urldatareject = base64_encode($urldatareject);

                $descriptionsalesrejected = '<table width="80%" cellpadding="5" cellspacing="1" border="0" align="left">
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" width="50%">Dear Manager , </td></tr>
                            <tr><td align="left" colspan="2">I Here by request you to verify and approve/Reject the same. </td></tr>
                            <tr><td align="left" colspan="2">The Sale Details are as follows: </td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><th align="left">CSAF number : </th><td>'.$csaf_no.'</td></tr>
                            <tr><th align="left">Client Name : </th><td>'.$clientname.'</td></tr>
                            <tr><th align="left">Am Name : </th><td>'.$am_name.'</td></tr>
                            <tr><th align="left">Package Sold : </th><td>'.$total_package_name.'</td></tr>
                            <tr><th align="left">Total Package Amount : </th><td>'.$total_sale_amount.'</td></tr>
                            <tr><th align="left">Bottom Price : </th><td>'.$total_bp_amount.'</td></tr>
                            <tr><th align="left">Discount Over BP : </th><td>'.$discount_bp_amount.'</td></tr>
                            <tr><th align="left">Approved By</th><th align="left">Approval Status</th>

                            <th width="36%" align="left">Remarks</th></tr>

                            [RemarksData]
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left">Thanks and Regards </td></tr>
                            <tr><td align="left">'.$fullname1.'</td></tr>
                            </table>';

                $descriptionsalesapproval = '<table width="80%" cellpadding="5" cellspacing="1" border="0" align="left">
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" width="50%">Dear Manager , </td></tr>
                            <tr><td align="left" colspan="2">I Here by request you to verify and approve/Reject the same. </td></tr>
                            <tr><td align="left" colspan="2">The Sale Details are as follows: </td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><th align="left">CSAF number : </th><td>'.$csaf_no.'</td></tr>
                            <tr><th align="left">Client Name : </th><td>'.$clientname.'</td></tr>
                            <tr><th align="left">Am Name : </th><td>'.$am_name.'</td></tr>
                            <tr><th align="left">Package Sold : </th><td>'.$total_package_name.'</td></tr>
                            <tr><th align="left">Total Package Amount : </th><td>'.$total_sale_amount.'</td></tr>
                            <tr><th align="left">Bottom Price : </th><td>'.$total_bp_amount.'</td></tr>
                            <tr><th align="left">Discount Over BP : </th><td>'.$discount_bp_amount.'</td></tr>
                            <tr><th align="left">Approved By</th><th align="left">Approval Status</th>

                            <th width="36%" align="left">Remarks</th></tr>

                            [RemarksData]
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" colspan="2">Please approve this package by clicking the below link <br><a href="[SITEURL]index.php?module=SalesOrder&parenttab=Sales&action=DetailView&record='.$for_crmid.'">Click Here To Login</a><br></td></tr>
                            <tr><th align="left">Approval URL : </th><td>Approve this package by clicking the below link <br><a href="[SITEURL]ApprovalPackage.php?urldataapprove='.$urldataapprove.'">Click Here To Approve</a><br></td></tr>
                            <tr><th align="left">Reject URL : </th><td>Reject this package by clicking the below link <br><a href="[SITEURL]ApprovalPackage.php?urldatareject='.$urldatareject.'">Click Here To Reject</a><br></td></tr>
                            <tr><td align="left">Thanks and Regards </td></tr>
                            <tr><td align="left">'.$fullname1.'</td></tr>
                            </table>';

                $descriptionsalesapproved = '<table width="80%" cellpadding="5" cellspacing="1" border="0" align="left">
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" width="50%">Dear Manager ('.$am_name.')</td></tr>
                            <tr><td align="left" colspan="2">Your below Sale is approved. </td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><th align="left">CSAF number : </th><td>'.$csaf_no.'</td></tr>
                            <tr><th align="left">Client Name : </th><td>'.$clientname.'</td></tr>
                            <tr><th align="left">Am Name : </th><td>'.$am_name.'</td></tr>
                            <tr><th align="left">Package Sold : </th><td>'.$total_package_name.'</td></tr>
                            <tr><th align="left">Total Package Amount : </th><td>'.$total_sale_amount.'</td></tr>
                            <tr><th align="left">Bottom Price : </th><td>'.$total_bp_amount.'</td></tr>
                            <tr><th align="left">Discount Over BP : </th><td>'.$discount_bp_amount.'</td></tr>
                            <tr><th align="left">Approved By</th><th align="left">Approval Status</th>

                            <th width="36%" align="left">Remarks</th></tr>

                            [RemarksData]
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" colspan="2">It is sent to Finance for (PDC , Credit ) Approval.</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left">Thanks and Regards </td></tr>
                            <tr><td align="left">'.$fullname1.'</td></tr>
                            </table>';

                $descriptionfinancesapproval = '<table width="80%" cellpadding="5" cellspacing="1" border="0" align="left">
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" width="41%">Dear Finance, </td>
                            </tr>
                            <tr><td align="left" colspan="3">Below Sales is approved , I Here by request you to approve pdccredit </td></tr>
                            <tr><td align="left" colspan="3">reasons</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><th align="left">CSAF number : </th><td width="23%">'.$csaf_no.'</td>
                            </tr>
                            <tr><th align="left">Client Name : </th><td>'.$clientname.'</td></tr>
                            <tr><th align="left">Am Name : </th><td>'.$am_name.'</td></tr>
                            <tr><th align="left">Package Sold : </th><td>'.$total_package_name.'</td></tr>
                            <tr><th align="left">Total Package Amount : </th><td>'.$total_sale_amount.'</td></tr>
                            <tr><th align="left">Bottom Price : </th><td>'.$total_bp_amount.'</td></tr>
                            <tr><th align="left">Discount Over BP : </th><td>'.$discount_bp_amount.'</td></tr>
                            <tr><th align="left">Credit Preiod : </th><td>'.$creditperiod.'</td></tr>
                            <tr><th align="left">Approved By</th><th align="left">Approval Status</th>

                            <th width="36%" align="left">Remarks</th></tr>

                            [RemarksData]
                            <tr><td>&nbsp;</td></tr>
                            <tr><th align="left">Payment Mode</th><th align="left">Cheque Date</th>
                            <th width="36%" align="left">Amount</th></tr>
                            [Paymentdata]
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" colspan="3">Please approve this package by clicking the below link <br><a href="[SITEURL]index.php?module=SalesOrder&parenttab=Sales&action=DetailView&record='.$for_crmid.'">Click Here To Login</a><br></td></tr>
                            <tr><th align="left">Approval URl : </th><td colspan="2">Approve this package by clicking the below link <br><a href="[SITEURL]ApprovalPackage.php?urldataapprove='.$urldataapprove.'">Click Here To Approve</a><br></td></tr>
                            <tr><th align="left">Reject URL : </th><td colspan="2">Reject this package by clicking the below link <br><a href="[SITEURL]ApprovalPackage.php?urldatareject='.$urldatareject.'">Click Here To Reject</a><br></td></tr>
                            <tr><td align="left">Thanks and Regards </td></tr>
                            <tr><td align="left">'.$fullname1.'</td></tr>
                            </table>';

                $descriptionfinanceapproved = '<table width="80%" cellpadding="5" cellspacing="1" border="0" align="left">
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" width="50%">Dear '.$am_name.'</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left" colspan="2">Your deal is approved for (PDC , Credit) </td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr><td align="left">Thanks and Regards </td></tr>
                            <tr><td align="left">'.$fullname1.'</td></tr>
                            </table>';

                $site_URL = "http://122.248.251.133/shinecrm/";
                $descriptionfinancesapproval = str_replace("[Paymentdata]",$paymentsdata,$descriptionfinancesapproval);
                $descriptionfinancesapproval = str_replace("[SITEURL]",$site_URL,$descriptionfinancesapproval);
                $descriptionfinancesapproval = str_replace("[RemarksData]",$approval_history,$descriptionfinancesapproval);
                $descriptionsalesapproval = str_replace("[SITEURL]",$site_URL,$descriptionsalesapproval);
                $descriptionsalesapproval = str_replace("[RemarksData]",$approval_history,$descriptionsalesapproval);
                $descriptionsalesrejected = str_replace("[RemarksData]",$approval_history,$descriptionsalesrejected);

                $all_user_mail_flag = 1;

                if(in_array($profileid1, $sales_profileid)){ // Sales Approval                
                    if($approval_status == "Approved" && $approval_end_status == 0){
                        $all_user_mail_flag = 0;
                        $subject = "Sales Approval Request for $clientname";
                        $description = $descriptionsalesapproval;
                        $adb->query("UPDATE vtiger_potentialscf SET cf_807 = 'Completed', approval_stage = '$fullname2' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }

                    if($approval_status == "Approved" && $approval_end_status == 1){
                        $subject = "Sale Approved Request for $clientname By Sales";
                        $description = $descriptionsalesapproved;
                        if($finance_approval == 1){
                            $subject1 = "Finance Approval Request for $clientname";
                            $description1 = $descriptionfinancesapproval;
                            send_mail('',$email2,$username1,'',$subject1,$description1,$cc);
                            $adb->query("UPDATE vtiger_potentialscf SET approved_date = '$currentdatetime', sales_approval = 'Yes', approval_stage = '$fullname3' where vtiger_potentialscf.potentialid = $for_crmid ");
                        }else{
                            $adb->query("UPDATE vtiger_potentialscf SET approved_date = '$currentdatetime', final_approval = 'Yes', sales_approval = 'Yes',approval_stage = '$am_name' where vtiger_potentialscf.potentialid = $for_crmid ");
                        }
                    }

                    if($approval_status == "Rejected" && $finance_approval == 0){
                        $subject = "Approval package Canceled By ".$fullname1;
                        $description = $descriptionsalesrejected;
                        $adb->query("UPDATE vtiger_potentialscf SET finance_approval = 'No', final_approval = 'No', sales_approval = 'No',approval_stage = '$am_name' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }
                }

                if(in_array($profileid1, $finance_profileid)){ // Finance Approval
                    if($approval_status == "Approved" && $approval_end_status == 1 && $finance_approval == 1){
                        $subject = "Finance Approved Request for $clientname";
                        $description = $descriptionfinanceapproved;
                        $adb->query("UPDATE vtiger_potentialscf SET finance_approval = 'Yes', final_approval = 'Yes', sales_approval = 'Yes',approval_stage = '' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }

                    if($approval_status == "Rejected" && $finance_approval == 1){
                        $subject = "Approval package Canceled By ".$fullname1;
                        $description = $descriptionsalesrejected;
                        $adb->query("UPDATE vtiger_potentialscf SET finance_approval = 'No', final_approval = 'No', sales_approval = 'No',approval_stage = '$am_name' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }
                }

                // echo $approval_end_status;
                // echo "<pre>";print_r($all_userid);die;
                if($all_user_mail_flag == 1){
                    $cc= "";
                    $counter = 0;
                    foreach($all_userid as $key=>$row){
                        //if($row != $email1 && $row != $assigned_email) {
                        if($counter == 0) {
                            $cc = $row;
                            $counter++;
                        }
                        else
                            $cc .= ', '.$row;
                        //}
                    }
                    $email2 = $assigned_email;
                }
                //$reportemail = 'ajayk@techfoursolutions.com';
                //echo $email2.'___'.$user_name1.'___'.$subject.'___'.$cc;	die;

                send_mail('',$email2,$username1,'',$subject,$description,$cc);
            }
            else{
                $adb->query("UPDATE vtiger_potentialscf SET cf_807 = 'Completed' where vtiger_potentialscf.potentialid = $for_crmid ");
            }
        }
        /*if ($return_action && $for_module && $for_crmid) {
            if ($for_module == 'HelpDesk') {
                $on_focus = CRMEntity::getInstance($for_module);
                $on_focus->save_related_module($for_module, $for_crmid, $module, $this->id);
            }
        }*/
    }

    function getReportToDetail($userid, $status = ''){
        global $adb;
        $user_array = array();
        $status_qry = "";
        if($status == 'Active')
            $status_qry = "AND u2.status = 'Active'";
        $user_qry = $adb->query("select profileid, u2.user_name as username, u2.id as userid, concat(u2.first_name,' ',u2.last_name) as fullname,
                                    u2.email1 as emailid, vtiger_role.depth as 'newdepth' from vtiger_users as u1
                                    INNER JOIN vtiger_users as u2 on u2.id = u1.reports_to_id
                                    INNER JOIN vtiger_user2role  on vtiger_user2role.userid = u2.id
                                    INNER JOIN vtiger_role  on vtiger_role.roleid = vtiger_user2role.roleid
                                    INNER JOIN vtiger_role2profile ON vtiger_role2profile.roleid = vtiger_user2role.roleid
                                    where u1.id = $userid $status_qry");
        if($adb->num_rows($user_qry) > 0) {
            $row = $adb->fetchByAssoc($user_qry);
            $user_array['userid'] = $row['userid'];
            $user_array['fullname'] = $row['fullname'];
            $user_array['emailid'] = $row['emailid'];
            $user_array['username'] = $row['username'];
            $user_array['depth'] = $row['newdepth'];
            $user_array['profileid'] = $row['profileid'];
        }
        return $user_array;
    }

    function getCurrentUserDetail($userid, $type = 'sales'){
        global $adb;
        $user_array = array();
        if($type == 'sales')
            $sub_query = " AND u1.id = $userid ";
        else
            $sub_query = " AND profileid = $userid ";

        $user_qry = $adb->query("select profileid, u1.user_name as username, u1.id as userid, concat(u1.first_name,' ',u1.last_name) as fullname,
                                    u1.email1 as emailid, vtiger_role.depth as 'newdepth' from vtiger_users as u1
                                    INNER JOIN vtiger_user2role  on vtiger_user2role.userid = u1.id
                                    INNER JOIN vtiger_role  on vtiger_role.roleid = vtiger_user2role.roleid
                                    INNER JOIN vtiger_role2profile ON vtiger_role2profile.roleid = vtiger_user2role.roleid
                                    where u1.status = 'Active' $sub_query ");
        if($adb->num_rows($user_qry) > 0) {
            $row = $adb->fetchByAssoc($user_qry);
            $user_array['userid'] = $row['userid'];
            $user_array['username'] = $row['username'];
            $user_array['fullname'] = $row['fullname'];
            $user_array['emailid'] = $row['emailid'];
            $user_array['depth'] = $row['newdepth'];
            $user_array['profileid'] = $row['profileid'];
        }
        return $user_array;
    }

    /**
     * Return query to use based on given modulename, fieldname
     * Useful to handle specific case handling for Popup
     */
    function getQueryByModuleField($module, $fieldname, $srcrecord) {
        // $srcrecord could be empty
    }

    /**
     * Get list view query.
     */
    function getListQuery($module, $where='') {
        $query = "SELECT vtiger_crmentity.*, $this->table_name.*";

        // Select Custom Field Table Columns if present
        if(!empty($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

        $query .= " FROM $this->table_name";

        $query .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if(!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                " = $this->table_name.$this->table_index";
        }
        $query .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
            " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
            " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($module));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other = CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index =".
                "$this->table_name.$columnname";
        }

        global $current_user;
        $query .= $this->getNonAdminAccessControlQuery($module,$current_user);
        $query .= "WHERE vtiger_crmentity.deleted = 0 ".$where;
        return $query;
    }

    /**
     * Apply security restriction (sharing privilege) query part for List view.
     */
    function getListViewSecurityParameter($module) {
        global $current_user;
        require('user_privileges/user_privileges_'.$current_user->id.'.php');
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

        $sec_query = '';
        $tabid = getTabid($module);

        if($is_admin==false && $profileGlobalPermission[1] == 1 && $profileGlobalPermission[2] == 1
            && $defaultOrgSharingPermission[$tabid] == 3) {

            $sec_query .= " AND (vtiger_crmentity.smownerid in($current_user->id) OR vtiger_crmentity.smownerid IN
					(
						SELECT vtiger_user2role.userid FROM vtiger_user2role
						INNER JOIN vtiger_users ON vtiger_users.id=vtiger_user2role.userid
						INNER JOIN vtiger_role ON vtiger_role.roleid=vtiger_user2role.roleid
						WHERE vtiger_role.parentrole LIKE '".$current_user_parent_role_seq."::%'
					)
					OR vtiger_crmentity.smownerid IN
					(
						SELECT shareduserid FROM vtiger_tmp_read_user_sharing_per
						WHERE userid=".$current_user->id." AND tabid=".$tabid."
					)
					OR
						(";

            // Build the query based on the group association of current user.
            if(sizeof($current_user_groups) > 0) {
                $sec_query .= " vtiger_groups.groupid IN (". implode(",", $current_user_groups) .") OR ";
            }
            $sec_query .= " vtiger_groups.groupid IN
						(
							SELECT vtiger_tmp_read_group_sharing_per.sharedgroupid
							FROM vtiger_tmp_read_group_sharing_per
							WHERE userid=".$current_user->id." and tabid=".$tabid."
						)";
            $sec_query .= ")
				)";
        }
        return $sec_query;
    }

    /**
     * Create query to export the records.
     */
    function create_export_query($where)
    {
        global $current_user,$currentModule;

        include("include/utils/ExportUtils.php");

        //To get the Permitted fields query and the permitted fields list
        $sql = getPermittedFieldsQuery('ServiceContracts', "detail_view");

        $fields_list = getFieldsListFromQuery($sql);

        $query = "SELECT $fields_list, vtiger_users.user_name AS user_name
					FROM vtiger_crmentity INNER JOIN $this->table_name ON vtiger_crmentity.crmid=$this->table_name.$this->table_index";

        if(!empty($this->customFieldTable)) {
            $query .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                " = $this->table_name.$this->table_index";
        }

        $query .= " LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";
        $query .= " LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id and ".
            "vtiger_users.status='Active'";

        $linkedModulesQuery = $this->db->pquery("SELECT distinct fieldname, columnname, relmodule FROM vtiger_field" .
            " INNER JOIN vtiger_fieldmodulerel ON vtiger_fieldmodulerel.fieldid = vtiger_field.fieldid" .
            " WHERE uitype='10' AND vtiger_fieldmodulerel.module=?", array($thismodule));
        $linkedFieldsCount = $this->db->num_rows($linkedModulesQuery);

        for($i=0; $i<$linkedFieldsCount; $i++) {
            $related_module = $this->db->query_result($linkedModulesQuery, $i, 'relmodule');
            $fieldname = $this->db->query_result($linkedModulesQuery, $i, 'fieldname');
            $columnname = $this->db->query_result($linkedModulesQuery, $i, 'columnname');

            $other = CRMEntity::getInstance($related_module);
            vtlib_setup_modulevars($related_module, $other);

            $query .= " LEFT JOIN $other->table_name ON $other->table_name.$other->table_index = ".
                "$this->table_name.$columnname";
        }

        $query .= $this->getNonAdminAccessControlQuery($thismodule,$current_user);
        $where_auto = " vtiger_crmentity.deleted=0";

        if($where != '') $query .= " WHERE ($where) AND $where_auto";
        else $query .= " WHERE $where_auto";

        return $query;
    }

    /**
     * Function which will give the basic query to find duplicates
     */
    function getDuplicatesQuery($module,$table_cols,$field_values,$ui_type_arr,$select_cols='') {
        $select_clause = "SELECT ". $this->table_name .".".$this->table_index ." AS recordid, vtiger_users_last_import.deleted,".$table_cols;

        // Select Custom Field Table Columns if present
        if(isset($this->customFieldTable)) $query .= ", " . $this->customFieldTable[0] . ".* ";

        $from_clause = " FROM $this->table_name";

        $from_clause .= "	INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = $this->table_name.$this->table_index";

        // Consider custom table join as well.
        if(isset($this->customFieldTable)) {
            $from_clause .= " INNER JOIN ".$this->customFieldTable[0]." ON ".$this->customFieldTable[0].'.'.$this->customFieldTable[1] .
                " = $this->table_name.$this->table_index";
        }
        $from_clause .= " LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $where_clause = "	WHERE vtiger_crmentity.deleted = 0";
        $where_clause .= $this->getListViewSecurityParameter($module);

        if (isset($select_cols) && trim($select_cols) != '') {
            $sub_query = "SELECT $select_cols FROM  $this->table_name AS t " .
                " INNER JOIN vtiger_crmentity AS crm ON crm.crmid = t.".$this->table_index;
            // Consider custom table join as well.
            if(isset($this->customFieldTable)) {
                $sub_query .= " INNER JOIN ".$this->customFieldTable[0]." tcf ON tcf.".$this->customFieldTable[1]." = t.$this->table_index";
            }
            $sub_query .= " WHERE crm.deleted=0 GROUP BY $select_cols HAVING COUNT(*)>1";
        } else {
            $sub_query = "SELECT $table_cols $from_clause $where_clause GROUP BY $table_cols HAVING COUNT(*)>1";
        }

        $query = $select_clause . $from_clause .
            " LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=" . $this->table_name .".".$this->table_index .
            " INNER JOIN (" . $sub_query . ") AS temp ON ".get_on_clause($field_values,$ui_type_arr,$module) .
            $where_clause .
            " ORDER BY $table_cols,". $this->table_name .".".$this->table_index ." ASC";

        return $query;
    }

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {

        require_once('include/utils/utils.php');
        global $adb;

        if($eventType == 'module.postinstall') {
            require_once('vtlib/Vtiger/Module.php');

            $moduleInstance = Vtiger_Module::getInstance($moduleName);

            $accModuleInstance = Vtiger_Module::getInstance('Accounts');
            $accModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

            $conModuleInstance = Vtiger_Module::getInstance('Contacts');
            $conModuleInstance->setRelatedList($moduleInstance,'Service Contracts',array('add'),'get_dependents_list');

            $helpDeskInstance = Vtiger_Module::getInstance("HelpDesk");
            $helpDeskInstance->setRelatedList($moduleInstance,"Service Contracts",Array('ADD','SELECT'));

            // Initialize module sequence for the module
            $adb->pquery("INSERT into vtiger_modentity_num values(?,?,?,?,?,?)",array($adb->getUniqueId("vtiger_modentity_num"),$moduleName,'SERCON',1,1,1));

            // Make the picklist value 'Complete' for status as non-editable
            $adb->query("UPDATE vtiger_contract_status SET presence=0 WHERE contract_status='Complete'");

            // Mark the module as Standard module
            $adb->pquery('UPDATE vtiger_tab SET customized=0 WHERE name=?', array($moduleName));

        } else if($eventType == 'module.disabled') {
            $em = new VTEventsManager($adb);
            $em->setHandlerInActive('ServiceContractsHandler');

        } else if($eventType == 'module.enabled') {
            $em = new VTEventsManager($adb);
            $em->setHandlerActive('ServiceContractsHandler');

        } else if($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }

    /**
     * Handle saving related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    function save_related_module($module, $crmid, $with_module, $with_crmids) {

        if(!is_array($with_crmids)) $with_crmids = Array($with_crmids);
        foreach($with_crmids as $with_crmid) {
            parent::save_related_module($module, $crmid, $with_module, $with_crmid);
            if ($with_module == 'HelpDesk') {
                $this->updateHelpDeskRelatedTo($crmid,$with_crmid);
                $this->updateServiceContractState($crmid);
            }
        }
    }

    // Function to Update the parent_id of HelpDesk with sc_related_to of ServiceContracts if the parent_id is not set.
    function updateHelpDeskRelatedTo($focusId, $entityIds) {

        if(!is_array($entityIds)) $entityIds = array($entityIds);
        $selectTicketsQuery = "SELECT ticketid FROM vtiger_troubletickets
								WHERE (parent_id IS NULL OR parent_id = 0 OR contact_id IS NULL OR contact_id =0)
									AND ticketid IN (" . generateQuestionMarks($entityIds) .")";$selectTicketsResult = $this->db->pquery($selectTicketsQuery, array($entityIds));
        $noOfTickets = $this->db->num_rows($selectTicketsResult);
        for($i=0; $i < $noOfTickets; ++$i) {
            $ticketId = $this->db->query_result($selectTicketsResult,$i,'ticketid');
            $serviceContractsRelateToTypeResult = $this->db->pquery('SELECT setype FROM vtiger_crmentity WHERE crmid =
				(SELECT sc_related_to FROM vtiger_servicecontracts WHERE servicecontractsid = ?)', array($focusId));
            $serviceContractsRelateToType = $this->db->query_result($serviceContractsRelateToTypeResult, 0, 'setype');
            if($serviceContractsRelateToType == 'Accounts') {
                $updateQuery = "UPDATE vtiger_troubletickets, vtiger_servicecontracts SET parent_id=vtiger_servicecontracts.sc_related_to" .
                    " WHERE vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to != 0" .
                    " AND vtiger_servicecontracts.servicecontractsid = ? AND vtiger_troubletickets.ticketid = ?";
                $this->db->pquery($updateQuery, array($focusId, $ticketId));
            } elseif($serviceContractsRelateToType == 'Contacts') {
                $updateQuery = "UPDATE vtiger_troubletickets, vtiger_servicecontracts SET contact_id=vtiger_servicecontracts.sc_related_to" .
                    " WHERE vtiger_servicecontracts.sc_related_to IS NOT NULL AND vtiger_servicecontracts.sc_related_to != 0" .
                    " AND vtiger_servicecontracts.servicecontractsid = ? AND vtiger_troubletickets.ticketid = ?";
                $this->db->pquery($updateQuery, array($focusId, $ticketId));
            }
        }
    }

    // Function to Compute and Update the Used Units and Progress of the Service Contract based on all the related Trouble tickets.
    function updateServiceContractState($focusId) {
        $this->id = $focusId;
        $this->retrieve_entity_info($focusId,'ServiceContracts');

        $contractTicketsResult = $this->db->pquery("SELECT relcrmid FROM vtiger_crmentityrel
														WHERE module = 'ServiceContracts'
														AND relmodule = 'HelpDesk' AND crmid = ?
													UNION
														SELECT crmid FROM vtiger_crmentityrel
														WHERE relmodule = 'ServiceContracts'
														AND module = 'HelpDesk' AND relcrmid = ?",
            array($focusId,$focusId));

        $noOfTickets = $this->db->num_rows($contractTicketsResult);
        $ticketFocus = CRMEntity::getInstance('HelpDesk');
        $totalUsedUnits = 0;
        for($i=0; $i < $noOfTickets; ++$i) {
            $ticketId = $this->db->query_result($contractTicketsResult, $i, 'relcrmid');
            $ticketFocus->id = $ticketId;
            if(isRecordExists($ticketId)) {
                $ticketFocus->retrieve_entity_info($ticketId, 'HelpDesk');
                if (strtolower($ticketFocus->column_fields['ticketstatus']) == 'closed') {
                    $totalUsedUnits += $this->computeUsedUnits($ticketFocus->column_fields);
                }
            }
        }
        $this->updateUsedUnits($totalUsedUnits);

        $this->calculateProgress();
    }

    // Function to Upate the Used Units of the Service Contract based on the given Ticket id.
    function computeUsedUnits($ticketData, $operator='+') {
        $trackingUnit = strtolower($this->column_fields['tracking_unit']);
        $workingHoursPerDay = 24;

        $usedUnits = 0;
        if ($trackingUnit == 'incidents') {
            $usedUnits = 1;
        } elseif ($trackingUnit == 'days') {
            if(!empty($ticketData['days'])) {
                $usedUnits = $ticketData['days'];
            } elseif(!empty($ticketData['hours'])) {
                $usedUnits = $ticketData['hours'] / $workingHoursPerDay;
            }
        } elseif ($trackingUnit == 'hours') {
            if(!empty($ticketData['hours'])) {
                $usedUnits = $ticketData['hours'];
            } elseif(!empty($ticketData['days'])) {
                $usedUnits = $ticketData['days'] * $workingHoursPerDay;
            }
        }
        return $usedUnits;
    }

    // Function to Upate the Used Units of the Service Contract.
    function updateUsedUnits($usedUnits) {
        $this->column_fields['used_units'] = $usedUnits;
        $updateQuery = "UPDATE vtiger_servicecontracts SET used_units = $usedUnits WHERE servicecontractsid = ?";
        $this->db->pquery($updateQuery, array($this->id));
    }

    // Function to Calculate the End Date, Planned Duration, Actual Duration and Progress of a Service Contract
    function calculateProgress() {
        $updateCols = array();
        $updateParams = array();

        $startDate = $this->column_fields['start_date'];
        $dueDate = $this->column_fields['due_date'];
        $endDate = $this->column_fields['end_date'];

        $usedUnits = decimalFormat($this->column_fields['used_units']);
        $totalUnits = decimalFormat($this->column_fields['total_units']);

        $contractStatus = $this->column_fields['contract_status'];

        // Update the End date if the status is Complete or if the Used Units reaches/exceeds Total Units
        // We need to do this first to make sure Actual duration is computed properly
        if($contractStatus == 'Complete' || (!empty($usedUnits) && !empty($totalUnits) && $usedUnits >= $totalUnits)) {
            if(empty($endDate)) {
                $endDate = date('Y-m-d');
                $this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(date('Y-m-d'), $this->id));
            }
        } else {
            $endDate = null;
            $this->db->pquery('UPDATE vtiger_servicecontracts SET end_date=? WHERE servicecontractsid = ?', array(null, $this->id));
        }

        // Calculate the Planned Duration based on Due date and Start date. (in days)
        if(!empty($dueDate) && !empty($startDate)) {
            $plannedDurationUpdate = " planned_duration = (TO_DAYS(due_date)-TO_DAYS(start_date)+1)";
        } else {
            $plannedDurationUpdate = " planned_duration = ''";
        }
        array_push($updateCols, $plannedDurationUpdate);

        // Calculate the Actual Duration based on End date and Start date. (in days)
        if(!empty($endDate) && !empty($startDate)) {
            $actualDurationUpdate = "actual_duration = (TO_DAYS(end_date)-TO_DAYS(start_date)+1)";
        } else {
            $actualDurationUpdate = "actual_duration = ''";
        }
        array_push($updateCols, $actualDurationUpdate);

        // Update the Progress based on Used Units and Total Units (in percentage)
        if(!empty($usedUnits) && !empty($totalUnits)) {
            $progressUpdate = 'progress = ?';
            $progressUpdateParams = floatval(($usedUnits * 100) / $totalUnits);
        } else {
            $progressUpdate = 'progress = ?';
            $progressUpdateParams = null;
        }
        array_push($updateCols, $progressUpdate);
        array_push($updateParams, $progressUpdateParams);

        if(count($updateCols) > 0) {
            $updateQuery = 'UPDATE vtiger_servicecontracts SET '. implode(",", $updateCols) .' WHERE servicecontractsid = ?';
            array_push($updateParams, $this->id);
            $this->db->pquery($updateQuery, $updateParams);
        }
    }

    /**
     * Handle deleting related module information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    function delete_related_module($module, $crmid, $with_module, $with_crmid) {
        parent::delete_related_module($module, $crmid, $with_module, $with_crmid);
        if ($with_module == 'HelpDesk') {
            $this->updateServiceContractState($crmid);
        }
    }

    /**
     * Handle getting related list information.
     * NOTE: This function has been added to CRMEntity (base class).
     * You can override the behavior by re-defining it here.
     */
    //function get_related_list($id, $cur_tab_id, $rel_tab_id, $actions=false) { }

    /** Function to unlink an entity with given Id from another entity */
    function unlinkRelationship($id, $return_module, $return_id) {
        global $log, $currentModule;

        if($return_module == 'Accounts') {
            $focus = new $return_module;
            $entityIds = $focus->getRelatedContactsIds($return_id);
            array_push($entityIds, $return_id);
            $entityIds = implode(',', $entityIds);
            $return_modules = "'Accounts','Contacts'";
        } else {
            $entityIds = $return_id;
            $return_modules = "'".$return_module."'";
        }

        $query = 'DELETE FROM vtiger_crmentityrel WHERE (relcrmid='.$id.' AND module IN ('.$return_modules.') AND crmid IN ('.$entityIds.')) OR (crmid='.$id.' AND relmodule IN ('.$return_modules.') AND relcrmid IN ('.$entityIds.'))';
        $this->db->pquery($query, array());

        $sql = 'SELECT tabid, tablename, columnname FROM vtiger_field WHERE fieldid IN (SELECT fieldid FROM vtiger_fieldmodulerel WHERE module=? AND relmodule IN ('.$return_modules.'))';
        $fieldRes = $this->db->pquery($sql, array($currentModule));
        $numOfFields = $this->db->num_rows($fieldRes);
        for ($i = 0; $i < $numOfFields; $i++) {
            $tabId = $this->db->query_result($fieldRes, $i, 'tabid');
            $tableName = $this->db->query_result($fieldRes, $i, 'tablename');
            $columnName = $this->db->query_result($fieldRes, $i, 'columnname');
            $relatedModule = vtlib_getModuleNameById($tabId);
            $focusObj = CRMEntity::getInstance($relatedModule);

            $updateQuery = "UPDATE $tableName SET $columnName=? WHERE $columnName IN ($entityIds) AND $focusObj->table_index=?";
            $updateParams = array(null, $id);
            $this->db->pquery($updateQuery, $updateParams);
        }
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

        $rel_table_arr = Array("Documents"=>"vtiger_senotesrel","Attachments"=>"vtiger_seattachmentsrel");

        $tbl_field_arr = Array("vtiger_senotesrel"=>"notesid","vtiger_seattachmentsrel"=>"attachmentsid");

        $entity_tbl_field_arr = Array("vtiger_senotesrel"=>"crmid","vtiger_seattachmentsrel"=>"crmid");

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
}
?>