<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Products_CheckDuplicate_Action extends Vtiger_Action_Controller {

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $accountName = $request->get('accountname');
        $record = $request->get('record');

        if ($record) {
            $recordModel = Vtiger_Record_Model::getInstanceById($record, $moduleName);
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
        }

        $recordModel->set('accountname', $accountName);

        $response_result = $recordModel->checkDuplicate();

        if ($response_result != "") {
            $result = array('success'=>true, 'message'=>vtranslate('LBL_DUPLICATES_EXIST', $moduleName),'error_message'=>$response_result,'error_status'=>1);
        } else
            $result = array('success'=>false);

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }
}
