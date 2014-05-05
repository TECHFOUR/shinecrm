<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Calendar_Detail_View extends Vtiger_Detail_View {

	function preProcess(Vtiger_Request $request, $display=true) {
		parent::preProcess($request, false);

		$recordId = $request->get('record');
		$moduleName = $request->getModule();
        if(!empty($recordId)){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $activityType = $recordModel->getType();
            if($activityType == 'Events')
                $moduleName = 'Events';
        }
		$detailViewModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		$recordModel = $detailViewModel->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$summaryInfo = array();
		// Take first block information as summary information
		$stucturedValues = $recordStrucure->getStructure();
		foreach($stucturedValues as $blockLabel=>$fieldList) {
			$summaryInfo[$blockLabel] = $fieldList;
			break;
		}

		$detailViewLinkParams = array('MODULE'=>$moduleName,'RECORD'=>$recordId);
		$detailViewLinks = $detailViewModel->getDetailViewLinks($detailViewLinkParams);
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

		$viewer->assign('MODULE_MODEL', $detailViewModel->getModule());
		$viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);

		$viewer->assign('IS_EDITABLE', $detailViewModel->getRecord()->isEditable($moduleName));
		$viewer->assign('IS_DELETABLE', $detailViewModel->getRecord()->isDeletable($moduleName));

        $linkParams = array('MODULE'=>$moduleName, 'ACTION'=>$request->get('view'));
		$linkModels = $detailViewModel->getSideBarLinks($linkParams);

        $viewer->assign('QUICK_LINKS', $linkModels);
		$viewer->assign('NO_SUMMARY', true);

		if($display) {
			$this->preProcessDisplay($request);
		}
	}

	/**
	 * Function shows the entire detail for the record
	 * @param Vtiger_Request $request
	 * @return <type>
	 */
	function showModuleDetailView(Vtiger_Request $request) {
        global $adb;
		$recordId = $request->get('record');
		$moduleName = $request->getModule();

        if(!empty($recordId)){
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            $activityType = $recordModel->getType();
            if($activityType == 'Events')
                $moduleName = 'Events';
        }

		$detailViewModel = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
		$recordModel = $detailViewModel->getRecord();
		$recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_DETAIL);
		$structuredValues = $recordStrucure->getStructure();
		$moduleModel = $recordModel->getModule();

        if ($moduleName == 'Events'){
            $relatedContacts = $recordModel->getRelatedContactInfo();
            foreach($relatedContacts as $index=>$contactInfo) {
                $contactRecordModel = Vtiger_Record_Model::getCleanInstance('Contacts');
                $contactRecordModel->setId($contactInfo['id']);
                $contactid =  $contactInfo['id'];
                $contactInfo['_model'] = $contactRecordModel;
                $relatedContacts[$index] = $contactInfo;
            }
        }else{
            $relatedContacts = array();
        }

		
		$viewer = $this->getViewer($request);
		$viewer->assign('RECORD', $recordModel);
		$viewer->assign('RECORD_STRUCTURE', $structuredValues);
		$viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
		$viewer->assign('RECORD_STRUCTURE_MODEL', $recordStrucure);
		$viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
		$viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('RELATED_CONTACTS', $relatedContacts);
		$viewer->assign('IS_AJAX_ENABLED', $this->isAjaxEnabled($recordModel));
		$viewer->assign('RECURRING_INFORMATION', $recordModel->getRecurringDetails());

        if($moduleName=='Events') {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $accessibleUsers = $currentUser->getAccessibleUsers();
            $viewer->assign('ENTITYID', $contactid);
            $viewer->assign('ACCESSIBLE_USERS', $accessibleUsers);
            $viewer->assign('INVITIES_SELECTED', $recordModel->getInvities());
        }

        // start added by ajay for dynamic lead form in edit form
        if($moduleName == "Events" && $contactid != "") {
            $RELATED_PRODUCTS = array();
            $lead_qry = $adb->query("SELECT vtiger_leaddetails.leadid as leadid, cf_907, cf_909, cf_911, cf_913, cf_915 FROM vtiger_leaddetails
                            INNER JOIN vtiger_crmentityrel ON (vtiger_crmentityrel.relcrmid = vtiger_leaddetails.leadid AND vtiger_crmentityrel.relmodule = 'Leads' )
                            LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_leaddetails.leadid
                            INNER JOIN vtiger_leadscf ON vtiger_leadscf.leadid = vtiger_leaddetails.leadid
                            WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentityrel.crmid = $contactid AND cf_913 NOT IN('Deal Won – 100%', 'Deal Lost – 0%')");
							
							//echo $adb->num_rows($lead_qry); die;
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
			
			//echo $i; die;
			
            $viewer->assign('RELATED_PRODUCTS', $RELATED_PRODUCTS);
        }
        // end added by ajay for dynamic lead form in edit form

		return $viewer->view('DetailViewFullContents.tpl',$moduleName,true);
	}

	/**
	 * Function shows basic detail for the record
	 * @param <type> $request
	 */
	function showModuleBasicView($request) {
		return $this->showModuleDetailView($request);
	}

	/**
	 * Function to get Ajax is enabled or not
	 * @param Vtiger_Record_Model record model
	 * @return <boolean> true/false
	 */
	function isAjaxEnabled($recordModel) {
		return false;
	}

}
