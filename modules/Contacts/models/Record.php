<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Contacts_Record_Model extends Vtiger_Record_Model {

    /**
     * Function returns the url for create event
     * @return <String>
     */
    function getCreateEventUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateEventRecordUrl().'&contact_id='.$this->getId();
    }

    /**
     * Function returns the url for create todo
     * @return <String>
     */
    function getCreateTaskUrl() {
        $calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
        return $calendarModuleModel->getCreateTaskRecordUrl().'&contact_id='.$this->getId();
    }

    /**
     * Function to check duplicate exists or not
     * @return <boolean>
     */
    public function checkDuplicate() {
        $db = PearDatabase::getInstance();
        $clientname = $_REQUEST['accountname'];
        $mobile = $_REQUEST['mobile'];
        $email = $_REQUEST['email'];
        $error_message = "";
        $record = $this->getId();
        if($record == "") {
            $query_more_contact = "SELECT cf_927, branch_city FROM vtiger_productcf
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_productcf.productid
                        INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_users.branch
                        INNER JOIN vtiger_crmentity AS crmp ON (crmp.crmid = vtiger_purchaseordercf.purchaseorderid AND crmp.deleted = 0)
                        INNER JOIN vtiger_seproductsrel as rel ON (rel.productid = vtiger_productcf.productid AND rel.setype= 'Contacts' )
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = rel.crmid
                        WHERE vtiger_crmentity.setype = ? AND mobile = ? AND vtiger_crmentity.deleted = 0";
            $params_more_contact = array('Products', $mobile);
            $result_more_contact = $db->pquery($query_more_contact, $params_more_contact);
            if ($db->num_rows($result_more_contact) > 0) {
                $res = $db->fetch_array($result_more_contact);
                $client_name = $res['cf_927'];
                $branch_name = $res['branch_city'];
                $error_message = "Mobile No. already exists in the system at $branch_name branch for Client $client_name";
            }

            $query_more_contact = "SELECT cf_927, branch_city FROM vtiger_productcf
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_productcf.productid
                        INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_users.branch
                        INNER JOIN vtiger_crmentity AS crmp ON (crmp.crmid = vtiger_purchaseordercf.purchaseorderid AND crmp.deleted = 0)
                        INNER JOIN vtiger_seproductsrel as rel ON (rel.productid = vtiger_productcf.productid AND rel.setype= 'Contacts' )
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = rel.crmid
                        WHERE vtiger_crmentity.setype = ? AND email = ? AND vtiger_crmentity.deleted = 0";
            $params_more_contact = array('Products', $email);
            $result_more_contact = $db->pquery($query_more_contact, $params_more_contact);
            if ($db->num_rows($result_more_contact) > 0) {
                $res = $db->fetch_array($result_more_contact);
                $client_name = $res['cf_927'];
                $branch_name = $res['branch_city'];
                $error_message = "Email Address already exists in the system at $branch_name branch for Client $client_name";
            }

            $query_more_contact = "SELECT cf_927, branch_city FROM vtiger_productcf
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_productcf.productid
                        INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                        LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_users.branch
                        INNER JOIN vtiger_crmentity AS crmp ON (crmp.crmid = vtiger_purchaseordercf.purchaseorderid AND crmp.deleted = 0)
                        INNER JOIN vtiger_seproductsrel as rel ON (rel.productid = vtiger_productcf.productid AND rel.setype= 'Contacts' )
                        INNER JOIN vtiger_contactscf ON vtiger_contactscf.contactid = rel.crmid
                        WHERE vtiger_crmentity.setype = ? AND mobile = ? AND email = ? AND vtiger_crmentity.deleted = 0";
            $params_more_contact = array('Products', $mobile, $email);
            $result_more_contact = $db->pquery($query_more_contact, $params_more_contact);
            if ($db->num_rows($result_more_contact) > 0) {
                $res = $db->fetch_array($result_more_contact);
                $client_name = $res['cf_927'];
                $branch_name = $res['branch_city'];
                $error_message = "Mobile No. and Email Address already exists in the system at $branch_name branch for Client $client_name";
            }
        }

        $query = "SELECT vtiger_crmentity.crmid, branch_city FROM vtiger_contactscf
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
                    INNER JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
                    LEFT JOIN vtiger_purchaseordercf ON vtiger_purchaseordercf.purchaseorderid = vtiger_users.branch
                    INNER JOIN vtiger_crmentity AS crmp ON (crmp.crmid = vtiger_purchaseordercf.purchaseorderid AND crmp.deleted = 0)
                    WHERE vtiger_crmentity.setype = ? AND cf_927 = ? AND vtiger_crmentity.deleted = 0";
        $params = array($this->getModule()->getName(), $clientname);

        if ($record) {
            $query .= " AND vtiger_crmentity.crmid != ?";
            array_push($params, $record);
        }

        $result = $db->pquery($query, $params);
        if ($db->num_rows($result) > 0 ) {
            $res = $db->fetch_array($result);
            $branch_name = $res['branch_city'];
            if($error_message == "")
                $error_message = " Client Name already exists in the system at $branch_name branch";
            else
                $error_message .= " AND same Client Name already exists in the system at $branch_name branch";
        }

        return $error_message;
    }

    /**
     * Function to get List of Fields which are related from Contacts to Inventory Record
     * @return <array>
     */
    public function getInventoryMappingFields() {
        return array(
            array('parentField'=>'account_id', 'inventoryField'=>'account_id', 'defaultValue'=>''),

            //Billing Address Fields
            array('parentField'=>'mailingcity', 'inventoryField'=>'bill_city', 'defaultValue'=>''),
            array('parentField'=>'mailingstreet', 'inventoryField'=>'bill_street', 'defaultValue'=>''),
            array('parentField'=>'mailingstate', 'inventoryField'=>'bill_state', 'defaultValue'=>''),
            array('parentField'=>'mailingzip', 'inventoryField'=>'bill_code', 'defaultValue'=>''),
            array('parentField'=>'mailingcountry', 'inventoryField'=>'bill_country', 'defaultValue'=>''),
            array('parentField'=>'mailingpobox', 'inventoryField'=>'bill_pobox', 'defaultValue'=>''),

            //Shipping Address Fields
            array('parentField'=>'otherstreet', 'inventoryField'=>'ship_street', 'defaultValue'=>''),
            array('parentField'=>'othercity', 'inventoryField'=>'ship_city', 'defaultValue'=>''),
            array('parentField'=>'otherstate', 'inventoryField'=>'ship_state', 'defaultValue'=>''),
            array('parentField'=>'otherzip', 'inventoryField'=>'ship_code', 'defaultValue'=>''),
            array('parentField'=>'othercountry', 'inventoryField'=>'ship_country', 'defaultValue'=>''),
            array('parentField'=>'otherpobox', 'inventoryField'=>'ship_pobox', 'defaultValue'=>'')
        );
    }

    /**
     * Function to get Image Details
     * @return <array> Image Details List
     */
    public function getImageDetails() {
        $db = PearDatabase::getInstance();
        $imageDetails = array();
        $recordId = $this->getId();

        if ($recordId) {
            $sql = "SELECT vtiger_attachments.*, vtiger_crmentity.setype FROM vtiger_attachments
						INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
						WHERE vtiger_crmentity.setype = 'Contacts Image' and vtiger_seattachmentsrel.crmid = ?";

            $result = $db->pquery($sql, array($recordId));

            $imageId = $db->query_result($result, 0, 'attachmentsid');
            $imagePath = $db->query_result($result, 0, 'path');
            $imageName = $db->query_result($result, 0, 'name');

            //decode_html - added to handle UTF-8 characters in file names
            $imageOriginalName = decode_html($imageName);

            //urlencode - added to handle special characters like #, %, etc.,
            $imageName = urlencode($imageName);

            $imageDetails[] = array(
                'id' => $imageId,
                'orgname' => $imageOriginalName,
                'path' => $imagePath.$imageId,
                'name' => $imageName
            );
        }
        return $imageDetails;
    }
}
