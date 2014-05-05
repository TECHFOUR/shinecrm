/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("Contacts_Edit_Js",{},{
	
	//Will have the mapping of address fields based on the modules
	addressFieldsMapping : {'Accounts' :
									{'mailingstreet' : 'bill_street',  
									'otherstreet' : 'ship_street', 
									'mailingpobox' : 'bill_pobox',
									'otherpobox' : 'ship_pobox',
									'mailingcity' : 'bill_city',
									'othercity' : 'ship_city',
									'mailingstate' : 'bill_state',
									'otherstate' : 'ship_state',
									'mailingzip' : 'bill_code',
									'otherzip' : 'ship_code',
									'mailingcountry' : 'bill_country',
									'othercountry' : 'ship_country'
									}
							},
							
	//Address field mapping within module
	addressFieldsMappingInModule : {
										'otherstreet' : 'mailingstreet',
										'otherpobox' : 'mailingpobox',
										'othercity' : 'mailingcity',
										'otherstate' : 'mailingstate',
										'otherzip' : 'mailingzip',
										'othercountry' : 'mailingcountry'
								},



    //Stored history of account name and duplicate check result
    duplicateCheckCache : {},

    //This will store the editview form
    editViewForm : false,
    /**
     * This function will return the current form
     */
    getForm : function(){
        if(this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },

    /**
     * This function will return the account name
     */
    getClientName : function(container){
        return jQuery('input[name="cf_927"]',container).val();
    },

    /**
     * This function will return the current RecordId
     */
    getRecordId : function(container){
        return jQuery('input[name="record"]',container).val();
    },


    /**
	 * Function which will register event for Reference Fields Selection
	 */
	registerReferenceSelectionEvent : function(container) {
		var thisInstance = this;
		
		jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function(e, data){
			thisInstance.referenceSelectionEventHandler(data, container);
		});
	},
	
	/**
	 * Reference Fields Selection Event Handler
	 * On Confirmation It will copy the address details
	 */
	referenceSelectionEventHandler :  function(data, container) {
		var thisInstance = this;
		var message = app.vtranslate('OVERWRITE_EXISTING_MSG1')+app.vtranslate('SINGLE_'+data['source_module'])+' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2');
		Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
			function(e) {
				thisInstance.copyAddressDetails(data, container);
			},
			function(error, err){
			});
	},
	
	/**
	 * Function which will copy the address details - without Confirmation
	 */
	copyAddressDetails : function(data, container) {
		var thisInstance = this;
		var sourceModule = data['source_module'];
		thisInstance.getRecordDetails(data).then(
			function(data){
				var response = data['result'];
				thisInstance.mapAddressDetails(thisInstance.addressFieldsMapping[sourceModule], response['data'], container);
			},
			function(error, err){

			});
	},
	
	/**
	 * Function which will map the address details of the selected record
	 */
	mapAddressDetails : function(addressDetails, result, container) {
		for(var key in addressDetails) {
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
		}
	},
	
	/**
	 * Function to swap array
	 * @param Array that need to be swapped
	 */ 
	swapObject : function(objectToSwap){
		var swappedArray = {};
		var newKey,newValue;
		for(var key in objectToSwap){
			newKey = objectToSwap[key];
			newValue = key;
			swappedArray[newKey] = newValue;
		}
		return swappedArray;
	},
	
	/**
	 * Function to copy address between fields
	 * @param strings which accepts value as either odd or even
	 */
	copyAddress : function(swapMode, container){
		
		var salutationtype = container.find('[name="salutationtype"]');
		var fromElement = container.find('[name="cf_927"]');
		var toElement = container.find('[name="lastname"]');
		//toElement.val(salutationtype.val()+' '+fromElement.val());
		toElement.val(fromElement.val());
	},
	
	
	
	/**
	 * Function to register event for copying address between two fileds
	 */
	registerEventForCopyingAddress : function(container){
		var thisInstance = this;
		var swapMode;
		jQuery('[name="copyAddress"]').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var target = element.data('target');
			if(target == "other"){
				swapMode = "false";
			} else if(target == "mailing"){
				swapMode = "true";
			}
			thisInstance.copyAddress(swapMode, container);
		})
	},

    /**
	 * Function to check for Portal User
	 */
	checkForPortalUser : function(form){
		var element = jQuery('[name="portal"]',form);
		var response = element.is(':checked');
		var primaryEmailField = jQuery('[name="email"]');
		var primaryEmailValue = primaryEmailField.val();
		if(response){
			if(primaryEmailField.length == 0){
				Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_PRIMARY_EMAIL_FIELD_DOES_NOT_EXISTS'));
				return false;
			}
			if(primaryEmailValue == ""){
				Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_PLEASE_ENTER_PRIMARY_EMAIL_VALUE_TO_ENABLE_PORTAL_USER'));
				return false;
			}
		}
		return true;
	},

	/**
	 * Function to register recordpresave event
	 */
	registerRecordPreSaveEvent : function(form){
		var thisInstance = this;
		if(typeof form == 'undefined') {
			form = this.getForm();
		}

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var result = thisInstance.checkForPortalUser(form);
			if(!result){
				e.preventDefault();
			}

            var accountName = thisInstance.getClientName(form);
            var recordId = thisInstance.getRecordId(form);
            var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName,
                    'moduleName' : 'Contacts',
                    'recordId' : recordId
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
                        var message = app.vtranslate('JS_DUPLICTAE_CLIENT_NAME_EXIT');
                        delete thisInstance.duplicateCheckCache[accountName];
                        Vtiger_Helper_Js.showConfirmationBoxCustom({'message' : message}).then(
                            function(e) {
                                thisInstance.duplicateCheckCache[accountName] = false;
                                form.submit();
                            },
                            function(error, err) {

                            }
                        );
                    }
                );
            }

            else {
                if(thisInstance.duplicateCheckCache[accountName] == true){
                    var message = app.vtranslate('JS_DUPLICTAE_CLIENT_NAME_EXIT');
                    delete thisInstance.duplicateCheckCache[accountName];
                    Vtiger_Helper_Js.showConfirmationBoxCustom({'message' : message}).then(
                        function(e) {
                            thisInstance.duplicateCheckCache[accountName] = false;
                            form.submit();
                        },
                        function(error, err) {

                        }
                    );
                } else {
                    delete thisInstance.duplicateCheckCache[accountName];
                    return true;
                }
            }
            e.preventDefault();
		})
	},
	
	registerBasicEvents : function(container){
		this._super(container);
		this.registerReferenceSelectionEvent(container);
		this.registerEventForCopyingAddress(container);
		this.registerRecordPreSaveEvent(container);
	}
})