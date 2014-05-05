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
            $query_more_contact = "SELECT 1 FROM vtiger_productcf
                        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_productcf.productid
                        WHERE setype = ? AND mobile = ? OR email = ? AND deleted = 0";
            $params_more_contact = array('Products', $mobile, $email);

            $result_more_contact = $db->pquery($query_more_contact, $params_more_contact);
            if ($db->num_rows($result_more_contact)) {
                $error_message = "Mobile or Email Id already exist in the system";
            }
        }
        $query = "SELECT 1 FROM vtiger_contactscf
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactscf.contactid
                    WHERE setype = ? AND cf_927 = ? AND deleted = 0";
        $params = array($this->getModule()->getName(), $clientname);

        if ($record) {
            $query .= " AND crmid != ?";
            array_push($params, $record);
        }

        $result = $db->pquery($query, $params);
        if ($db->num_rows($result)) {
            if($error_message == "")
                $error_message = " Same ClientName already exist in the system";
            else
                $error_message .= " AND same ClientName already exist in the system";
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
