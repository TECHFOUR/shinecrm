/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

var expected_revenue_msg = "";
var total_active_Row = 0;
var total_sequence_Row = 0;

Vtiger_Edit_Js("Potentials_Edit_Js",{

},{

    /*start added by ajay*/
    //Container which stores the line item elements
    lineItemContentsContainer : false,
    //Container which stores line item result details
    lineItemResultContainer : false,

    //a variable which will be used to hold the sequence of the row
    rowSequenceHolder : false,

    //holds the element which has basic hidden row which we can clone to add rows
    basicRow : false,

    //will be having class which is used to identify the rows
    rowClass : 'lineItemRow',

    /*end added by ajay*/

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
    getCSAFNo : function(container){
        return jQuery('input[name="potentialname"]',container).val();
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
            var accountName = thisInstance.getCSAFNo(form);
            var recordId = thisInstance.getRecordId(form);
            var params = {};
            if(!(accountName in thisInstance.duplicateCheckCache)) {
                Vtiger_Helper_Js.checkDuplicateName({
                    'accountName' : accountName,
                    'moduleName' : 'Potentials',
                    'recordId' : recordId
                }).then(
                    function(data){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        form.submit();
                    },
                    function(data, err){
                        thisInstance.duplicateCheckCache[accountName] = data['success'];
                        thisInstance.duplicateCheckCache['message'] = data['message'];
                        var message = app.vtranslate('JS_DUPLICTAE_CSAF_No_EXIT');
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
                    var message = app.vtranslate('JS_DUPLICTAE_CSAF_No_EXIT');
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

    /*start added by ajay*/

    getLineItemContentsContainer : function() {
        if(this.lineItemContentsContainer == false) {
            this.setLineItemContainer(jQuery('#lineItemTab'));
        }
        return this.lineItemContentsContainer;
    },

    /**
     * Function to set line item container
     * @params : element - jQuery object which represents line item container
     * @return : current instance ;
     */
    setLineItemContainer : function(element) {
        this.lineItemContentsContainer = element;
        return this;
    },

    /**
     * Function to set line item result container
     * @param : element - jQuery object which represents line item result container
     * @result : current instance
     */
    setLinteItemResultContainer : function(element) {
        this.lineItemResultContainer = element;
        return this;
    },

    /**
     * Function which will give the closest line item row element
     * @return : jQuery object
     */
    getClosestLineItemRow : function(element){
        return element.closest('tr.'+this.rowClass);
    },

    loadRowSequenceNumber: function() {
        if(this.rowSequenceHolder == false) {
            this.rowSequenceHolder = jQuery('.' + this.rowClass, this.getLineItemContentsContainer()).length;
        }
        return this;
    },

    getNextLineItemRowNumber : function() {
        if(this.rowSequenceHolder == false){
            this.loadRowSequenceNumber();
        }
        return ++this.rowSequenceHolder;
    },

    getBasicRow : function() {
        if(this.basicRow == false){
            var lineItemTable = this.getLineItemContentsContainer();
            this.basicRow = jQuery('.lineItemCloneCopy',lineItemTable)
        }
        var newRow = this.basicRow.clone(true,true);
        return newRow.removeClass('hide lineItemCloneCopy');
    },

    registerAddingNewProductsAndServices: function(){
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        jQuery('#addProduct').on('click',function(){
            total_active_Row = lineItemTable.find('.lineItemRow').length;
            var expected_revenue_limit = $("#TARGETLIMITVALUE").val();
            if(expected_revenue_limit == "")
                expected_revenue_limit = 10;
            if(total_active_Row < expected_revenue_limit - 1) {
                var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass)
                jQuery('.lineItemPopup[data-module-name="Services"]',newRow).remove();
                var sequenceNumber = thisInstance.getNextLineItemRowNumber();
                newRow = newRow.appendTo(lineItemTable);
                thisInstance.checkLineItemRow();
                newRow.find('input.rowNumber').val(sequenceNumber);
                thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
                newRow.find('input.productName').addClass('autoComplete');
                thisInstance.registerLineItemAutoComplete(newRow);
                total_sequence_Row = sequenceNumber;
            }else {
                alert("You are not allowed to create more than "+expected_revenue_limit+ " expected_revenues at the same time.");
            }
            //alert(sequenceNumber+"___"+noRow);
        });
    },

    registerDeleteLineItemEvent : function(){
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('click','.deleteRow',function(e){
            var element = jQuery(e.currentTarget);
            //removing the row
            element.closest('tr.'+ thisInstance.rowClass).remove();
            thisInstance.checkLineItemRow();
            thisInstance.lineItemDeleteActions();



        });
    },

    checkLineItemRow : function(){

        var lineItemTable = this.getLineItemContentsContainer();
        var noRow = lineItemTable.find('.lineItemRow').length;
        if(noRow >0){
            this.showLineItemsDeleteIcon();
        }else{
            this.hideLineItemsDeleteIcon();
        }
    },

    showLineItemsDeleteIcon : function(){
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').show();
    },

    hideLineItemsDeleteIcon : function(){
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').hide();
    },

    lineItemActions: function() {
        var lineItemTable = this.getLineItemContentsContainer();
        this.registerDeleteLineItemEvent();
    },

    updateLineItemsElementWithSequenceNumber : function(lineItemRow,expectedSequenceNumber , currentSequenceNumber){
        if(typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        /* Code modified by jitendra singh[TECHFOUR] */
        var idFields = new Array('expected_revenue','product','expected_closure_date','remarks','product_display','Events_editView_fieldName_product_select','lead_stage','leadid');

        var nameFields = new Array('discount');
        var classFields = new Array('taxPercentage');
        //To handle variable tax ids
        for(var classIndex in classFields) {
            var className = classFields[classIndex];
            jQuery('.'+className,lineItemRow).each(function(index, domElement){
                var idString = domElement.id
                //remove last character which will be the row number
                idFields.push(idString.slice(0,(idString.length-1)));
            });
        }

        var expectedRowId = 'row'+expectedSequenceNumber;
        for(var idIndex in idFields ) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;

            /* Start added by ajay [TECHFOUR]*/
            if(elementId == "product_display") {
                expectedElementId = "product"+expectedSequenceNumber+"_display";
                actualElementId = "product0_display";
            }

            if(elementId == "Events_editView_fieldName_product_select") {
                expectedElementId = "Events_editView_fieldName_product"+expectedSequenceNumber+"_select";
                actualElementId = "Events_editView_fieldName_product0_select";
            }

            /* Start added by ajay [TECHFOUR]*/

            lineItemRow.find('#'+actualElementId).attr('id',expectedElementId)
                .filter('[name="'+actualElementId+'"]').attr('name',expectedElementId);
        }

        for(var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRow.find('[name="'+actualElementName+'"]').attr('name',expectedElementName);
        }


        return lineItemRow.attr('id',expectedRowId);
    },



    /*end added by ajay*/


    /**
     * Function which will register basic events which will be used in quick create as well
     *
     */

    registerEvents : function(){
        this._super();
        this.registerAddingNewProductsAndServices(); /*added by ajay*/
        this.lineItemActions(); /*added by ajay*/
    },

    registerBasicEvents : function(container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);

        //container.trigger(Vtiger_Edit_Js.recordPreSave, {'value': 'edit'});
    }
});
