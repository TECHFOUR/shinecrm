/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
var error_custom_status = "";
var error_custom_message = 0;
Vtiger_Edit_Js("Products_Edit_Js",{

},{

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
    getMobile : function(container){
        return jQuery('input[name="mobile"]',container).val();
    },

    getEmail : function(container){
        return jQuery('input[name="email"]',container).val();
    },
    /**
     * This function will return the current RecordId
     */
    getRecordId : function(container){
        return jQuery('input[name="record"]',container).val();
    },

    /**
     * This function will return the current RecordId
     */
    /**
     * This function will register before saving any record
     */

    registerRecordPreSaveEvent : function(form) {
        var thisInstance = this;
        if(typeof form == 'undefined') {
            form = this.getForm();
        }
        form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
            var accountName = thisInstance.getMobile(form);
            var email = thisInstance.getEmail(form);
            var recordId = thisInstance.getRecordId(form);
            var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName,
                    'moduleName' : 'Products',
                    'email' : email,
                    'recordId' : recordId
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
                        error_custom_message = data['error_message'];
                        error_custom_status = data['error_status'];
                        var message = app.vtranslate('JS_DUPLICTAE_MOBILE_OR_EMAIL_EXIT');
                        delete thisInstance.duplicateCheckCache[accountName];
                        Vtiger_Helper_Js.showConfirmationBoxContactCustom({'message' : message}).then(
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
                    var message = app.vtranslate('JS_DUPLICTAE_MOBILE_OR_EMAIL_EXIT');
                    delete thisInstance.duplicateCheckCache[accountName];
                    Vtiger_Helper_Js.showConfirmationBoxContactCustom({'message' : message}).then(
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


    /**
     * Function which will register basic events which will be used in quick create as well
     *
     */
    registerBasicEvents : function(container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);

        //container.trigger(Vtiger_Edit_Js.recordPreSave, {'value': 'edit'});
    }
});