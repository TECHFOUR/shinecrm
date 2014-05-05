<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Save_Action extends Vtiger_Save_Action {

	public function process(Vtiger_Request $request) {
		$result = Vtiger_Util_Helper::transformUploadedFiles($_FILES, true);
		$_FILES = $result['imagename'];

		/*Start code to check the Client business need is empty or not by jitendra singh on 3 May 2014*/
		
		if($request->get('cf_763') != 'on'){ //Recruitment
			$request->set('not_contactabel_email', '');
			$request->set('portal', '');
			$request->set('cf_769', '');
			$request->set('cf_767', '');
			$request->set('contact_role', '');
			$request->set('cf_757', '');
		}
			
		if($request->get('cf_765') != 'on'){ //Marketing 
			$request->set('cf_791', '');
			$request->set('cf_799', '');
			$request->set('cf_795', ''); 
			}
			
		if($request->get('donotcall') != 'on'){ //Education
			$request->set('birthday', '');
			$request->set('cf_771', '');
			$request->set('btl', '');
			$request->set('cf_775', '');
			$request->set('cf_761', '');
			$request->set('bsm', '');
			$request->set('account_branch', '');
			$request->set('cf_759', '');
			$request->set('cf_793', '');
			$request->set('mailingstate', '');
			}

		if($request->get('reference')!= 'on'){ //Government
			$request->set('cf_987', '');
			$request->set('otherzip', '');
			$request->set('otherpobox', '');
			}
			
		if($request->get('notify_owner')!= 'on'){ //Social
			$request->set('cf_773', '');
			$request->set('cf_781', '');
			$request->set('cf_783', '');
			$request->set('cf_785', '');
			$request->set('cf_787', '');
			}
		
		/*End code to check the Client business need is empty or not by jitendra singh on 3 May 2014*/

		//To stop saveing the value of salutation as '--None--'
		$salutationType = $request->get('salutationtype');
		if ($salutationType === '--None--') {
			$request->set('salutationtype', '');
		}
		parent::process($request);
	}
}
