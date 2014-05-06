<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Potentials_Record_Model extends Vtiger_Record_Model {

	function getCreateInvoiceUrl() {
		$invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');
		return 'index.php?module='.$invoiceModuleModel->getName().'&view='.$invoiceModuleModel->getEditViewName().'&account_id='.$this->get('related_to').'&contact_id='.$this->get('contact_id');
	}

    /**
     * Function to check duplicate exists or not
     * @return <boolean>
     */
    public function checkDuplicate() {
        $db = PearDatabase::getInstance();
        $csaf_no = $_REQUEST['accountname'];
        $error_message = "";
        if(strlen($csaf_no) != 7)
            $error_message = "Length of CSAF No. must be 7 digits only";
        $query = "SELECT 1 FROM vtiger_potential
                    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
                    WHERE setype = ? AND potentialname = ? AND deleted = 0";
        $params = array($this->getModule()->getName(), $csaf_no);

        $record = $this->getId();
        if ($record) {
            $query .= " AND crmid != ?";
            array_push($params, $record);
        }

        $result = $db->pquery($query, $params);
        if ($db->num_rows($result)) {
            $error_message .= " Same CSAF No already exist in the system.";
        }
        return $error_message;
    }

	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
	function getCreateEventUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateEventRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function returns the url for create todo
	 * @return <String>
	 */
	function getCreateTaskUrl() {
		$calendarModuleModel = Vtiger_Module_Model::getInstance('Calendar');
		return $calendarModuleModel->getCreateTaskRecordUrl().'&parent_id='.$this->getId();
	}

	/**
	 * Function to get List of Fields which are related from Contacts to Inventyory Record
	 * @return <array>
	 */
	public function getInventoryMappingFields() {
		return array(
				array('parentField'=>'related_to', 'inventoryField'=>'account_id', 'defaultValue'=>''),
				array('parentField'=>'contact_id', 'inventoryField'=>'contact_id', 'defaultValue'=>''),
		);
	}
}
