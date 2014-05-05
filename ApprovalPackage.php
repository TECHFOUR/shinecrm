<?php
require_once('modules/Emails/mail.php');
include('config.php');

if(isset($_REQUEST['urldataapprove']) && $_REQUEST['urldataapprove'] != "")
    $ulrdata = base64_decode($_REQUEST['urldataapprove']);

if(isset($_REQUEST['urldatareject']) && $_REQUEST['urldatareject'] != "")
    $ulrdata = base64_decode($_REQUEST['urldatareject']);

$ulrdata = explode("%*@",$ulrdata);

list($x,$for_crmid) = explode("__",$ulrdata[0]);

list($x,$userid) = explode("__",$ulrdata[1]);

list($x,$approval_status) = explode("__",$ulrdata[2]);

//echo $salesorderid.'____'.$userid.'____'.$approvalstatus ;die;

if($for_crmid != "" && $userid != "" && $approval_status != "") {
    global $adb;

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
        $result_assigned = getCurrentUserDetail($assigned_userid);
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
            $result_reportto = getCurrentUserDetail($current_action_userid);
            $userid1 = $result_reportto['userid'];
            $fullname1 = $result_reportto['fullname'];
            $user_name1 = $result_reportto['username'];
            $email1 = $result_reportto['emailid'];
            $depth1 = $result_reportto['depth'];
            $profileid1 = $result_reportto['profileid'];
        }
        else{
            $result_reportto = getReportToDetail($last_action_userid,'Active');
            $userid1 = $result_reportto['userid'];
            $user_name1 = $result_reportto['username'];
            $fullname1 = $result_reportto['fullname'];
            $email1 = $result_reportto['emailid'];
            $depth1 = $result_reportto['depth'];
            $profileid1 = $result_reportto['profileid'];
            $current_action_userid = $userid1;
        }

        if($userid == $userid1) {
            $currentdatetime = date("Y-m-d H:i:s");
            $crmid = $adb->getUniqueID("vtiger_crmentity");
            $querynum = $adb->query("select prefix, cur_id from vtiger_modentity_num where semodule='ServiceContracts' and active = 1 ");
            $resultnum = $adb->fetch_array($querynum);
            $prefix = $resultnum['prefix'];
            $cur_id = $resultnum['cur_id'];
            $approvalnum = $prefix.$cur_id;
            $next_curr_id = $cur_id + 1;

            $query = "INSERT INTO vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime,label)
                                  VALUES (?,?,?,?,?,?,?)";
            $adb->pquery($query, array($crmid, $userid, $userid, 'ServiceContracts', $currentdatetime, $currentdatetime, $approvalnum));
            $adb->query("update vtiger_modentity_num set cur_id = ".$next_curr_id." where semodule='ServiceContracts' ");
            $adb->query("INSERT into vtiger_servicecontracts (servicecontractsid, contract_no) values(".$crmid.", '".$approvalnum."')");
            $adb->query("INSERT into vtiger_servicecontractscf (servicecontractsid, approval_status, remark) values(".$crmid.", '".$approval_status."', '".$custom_remark."')");
            $sql = "insert into vtiger_crmentityrel values (?,?,?,?)";
            $adb->pquery($sql, array($for_crmid,'Potentials',$crmid,'ServiceContracts'));

            $current_userid = $current_action_userid;
            for($i=0; $i<=5; $i++) {
                if($i > 0){
                    $result_next_reportto = getReportToDetail($current_userid);
                    $current_userid = $result_next_reportto['userid'];
                }
                $result_reportto = getReportToDetail($current_userid,'Active');
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
                    $result_reportto = getCurrentUserDetail(3, 'Finance');
                    $userid2 = $result_reportto['userid'];
                    $email2 = $result_reportto['emailid'];
                    $fullname3 = $result_reportto['fullname'];
                }else{
                    $result_reportto = getCurrentUserDetail(2, 'Finance');
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
            $approval_history .= '<tr><td>'.$fullname1.'</td><td>'.$approval_status.'</td><td>'.$custom_remarks.'</td></tr>';
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
            if(in_array($profileid1, $sales_profileid)){ // Sales Approval // Sales Approval
                if($approval_status == "Approved" && $approval_end_status == 0){
                    $all_user_mail_flag = 0;
                    $subject = "Sales Approval Request for $clientname";
                    $description = $descriptionsalesapproval;
                    $approvalmailalert = "Approval package mail has been sent to this email id  $email2";
                    $adb->query("UPDATE vtiger_potentialscf SET approval_stage = '$fullname2' where vtiger_potentialscf.potentialid = $for_crmid ");
                }

                if($approval_status == "Approved" && $approval_end_status == 1){
                    $subject = "Sale Approved Request for $clientname By Sales";
                    $description = $descriptionsalesapproved;
                    if($finance_approval == 1){
                        $subject1 = "Finance Approval Request for $clientname";
                        $description1 = $descriptionfinancesapproval;
                        send_mail('',$email2,$username1,'',$subject1,$description1,$cc);
                        $adb->query("UPDATE vtiger_potentialscf SET approved_date = '$currentdatetime', sales_approval = 'Yes', approval_stage = '$fullname3' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }
                    else{
                        $adb->query("UPDATE vtiger_potentialscf SET approved_date = '$currentdatetime', final_approval = 'Yes', sales_approval = 'Yes',approval_stage = '$am_name' where vtiger_potentialscf.potentialid = $for_crmid ");
                    }
                }

                if($approval_status == "Rejected" && $finance_approval == 0){
                    $subject = "Approval package Canceled By ".$fullname1;
                    $description = $descriptionsalesrejected;
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
                if($approval_status == "Rejected")
                    $approvalmailalert = "Approval package Canceled mail has been sent to this email id  $cc";
                else
                    $approvalmailalert = "Approved package mail has been sent to this email id  $cc";
            }
            //$reportemail = 'ajayk@techfoursolutions.com';
            //echo $email2.'___'.$user_name1.'___'.$subject.'___'.$cc;	die;

            send_mail('',$email2,$username1,'',$subject,$description,$cc);
        }
        else{
            $approvalmailalert = "You has been sent email already.";
        }
    }
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



?>



<script>

    var approvalalert = "<?php echo $approvalmailalert; ?>";
    alert(approvalalert);
    window.close();

</script>
