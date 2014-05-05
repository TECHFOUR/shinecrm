<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Vtiger_RelatedList_View extends Vtiger_Index_View {
    function process(Vtiger_Request $request) {
        global $adb, $current_user;
        $moduleName = $request->getModule();
        $relatedModuleName = $request->get('relatedModule');
        $parentId = $request->get('record');
        $label = $request->get('tab_label');
        $requestedPage = $request->get('page');
        if(empty($requestedPage)) {
            $requestedPage = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page',$requestedPage);

        $parentRecordModel = Vtiger_Record_Model::getInstanceById($parentId, $moduleName);
        $relationListView = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $relatedModuleName, $label);
        $orderBy = $request->get('orderby');
        $sortOrder = $request->get('sortorder');
        if($sortOrder == 'ASC') {
            $nextSortOrder = 'DESC';
            $sortImage = 'icon-chevron-down';
        } else {
            $nextSortOrder = 'ASC';
            $sortImage = 'icon-chevron-up';
        }
        if(!empty($orderBy)) {
            $relationListView->set('orderby', $orderBy);
            $relationListView->set('sortorder',$sortOrder);
        }
        $models = $relationListView->getEntries($pagingModel);
        $links = $relationListView->getLinks();
        $header = $relationListView->getHeaders();
        $noOfEntries = count($models);

        $relationModel = $relationListView->getRelationModel();
        $relatedModuleModel = $relationModel->getRelationModuleModel();
        $relationField = $relationModel->getRelationField();


        //Add by Raghvender Singh on 24042014
        $tt = "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $url1=explode('&', $tt);
        $url=array_pop($url1);
        $url1=implode('&', $url1);
        $url1=explode('&', $url1);
        $url=array_pop($url1);
        $url1=implode('&', $url1);
        $url1=explode('&', $url1);
        $url=array_pop($url1);
        $url1=implode('&', $url1);

        $url2=explode('/', $tt);
        $url=array_pop($url2);
        $url2=implode('/', $url2);
        $patht = $url2.'/index.php?module=Contacts&relatedModule=Calendar&view=Detail';
        $patht1 = $url2.'/index.php?module=Contacts&relatedModule=Potentials&view=Detail';
        $patht2 = $url2.'/index.php?module=Contacts&relatedModule=Leads&view=Detail';
        $patht3 = $url2.'/index.php?module=Contacts&relatedModule=Products&view=Detail';
		//Add by Raghvender Singh on 02052014
		$patht4 = $url2.'/index.php?module=Potentials&relatedModule=Documents&view=Detail';
		$patht5 = $url2.'/index.php?module=Potentials&relatedModule=Campaigns&view=Detail';
		$patht6 = $url2.'/index.php?module=Potentials&relatedModule=Project&view=Detail';
		//End Add by Raghvender Singh on 02052014
		//Add by Raghvender Singh on 03052014
		$patht7 = $url2.'/index.php?module=Potentials&relatedModule=ServiceContracts&view=Detail';
		//End Add by Raghvender Singh on 03052014
        $record = $_REQUEST['record'];

        $viewer = $this->getViewer($request);
        $viewer->assign('LINK_RECORD' , $record);
        $viewer->assign('OLDPATH' , $url1);
        $viewer->assign('NEWPATH' , $patht);
        $viewer->assign('NEWPATH1' , $patht1);
        $viewer->assign('NEWPATH2' , $patht2);
        $viewer->assign('NEWPATH3' , $patht3);
		//Add by Raghvender Singh on 02052014
		$viewer->assign('NEWPATH4' , $patht4);
		$viewer->assign('NEWPATH5' , $patht5);
		$viewer->assign('NEWPATH6' , $patht6);
		//End Add by Raghvender Singh on 02052014
			//Add by Raghvender Singh on 03052014
		$viewer->assign('NEWPATH7' , $patht7);
		//End Add by Raghvender Singh on 03052014

        $viewer->assign('RELATED_RECORDS' , $models);
        $viewer->assign('PARENT_RECORD', $parentRecordModel);
        $viewer->assign('RELATED_LIST_LINKS', $links);
        $viewer->assign('RELATED_HEADERS', $header);
        $viewer->assign('RELATED_MODULE', $relatedModuleModel);
        $viewer->assign('RELATED_ENTIRES_COUNT', $noOfEntries);
        $viewer->assign('RELATION_FIELD', $relationField);
        $edit_permission = 0;
        if($moduleName == 'Potentials' && $relatedModuleName == 'ServiceContracts'){
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
                $final_approval = $row['final_approval'];
                $approvestatus = $row['approval_status'];
                $ownerid = $row['ownerid'];
                $last_action_userid = $row['lastactionuserid'];
                $reportto_id = $row['reportto_id'];
                if(($current_user->id == $reportto_id) && $approvestatus == 'Approved')
                    $edit_permission = 1;
                if($current_user->id == $ownerid && $approvestatus == 'Rejected')
                    $edit_permission = 1;
            }else{
                $pot_qry = $adb->query("SELECT smownerid FROM vtiger_potentialscf
                                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potentialscf.potentialid
                                                WHERE vtiger_crmentity.deleted = 0 AND vtiger_potentialscf.potentialid = $record ");
                if($adb->num_rows($pot_qry) > 0) {
                    $row = $adb->fetchByAssoc($pot_qry);
                    $smownerid = $row['smownerid'];
                    if($smownerid == $current_user->id)
                        $edit_permission = 1;
                }
            }
            if($final_approval == 'Yes')
                $edit_permission = 0;
// end for sales Approval edit condition ***********************************
        }
        $viewer->assign('APPROVAL_EDIT_PERMISSION',$edit_permission);
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            $totalCount = $relationListView->getRelatedEntriesCount();
            $pageLimit = $pagingModel->getPageLimit();
            $pageCount = ceil((int) $totalCount / (int) $pageLimit);

            if($pageCount == 0){
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('TOTAL_ENTRIES', $totalCount);
            $viewer->assign('PERFORMANCE', true);
        }

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELMODULE', $relatedModuleName);
        $viewer->assign('PAGING', $pagingModel);

        $viewer->assign('ORDER_BY',$orderBy);
        $viewer->assign('SORT_ORDER',$sortOrder);
        $viewer->assign('NEXT_SORT_ORDER',$nextSortOrder);
        $viewer->assign('SORT_IMAGE',$sortImage);
        $viewer->assign('COLUMN_NAME',$orderBy);

        $viewer->assign('IS_EDITABLE', $relationModel->isEditable());
        $viewer->assign('IS_DELETABLE', $relationModel->isDeletable());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('VIEW', $request->get('view'));

        return $viewer->view('RelatedList.tpl', $moduleName, 'true');
    }
}