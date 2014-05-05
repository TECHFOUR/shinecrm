/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_Edit_Js",{
	

	//Event that will triggered when reference field is selected
	referenceSelectionEvent : 'Vtiger.Reference.Selection',

	//Event that will triggered when reference field is selected
	referenceDeSelectionEvent : 'Vtiger.Reference.DeSelection',

	//Event that will triggered before saving the record
	recordPreSave : 'Vtiger.Record.PreSave',

    refrenceMultiSelectionEvent : 'Vtiger.MultiReference.Selection',

    preReferencePopUpOpenEvent : 'Vtiger.Referece.Popup.Pre',

	editInstance : false,

	/**
	 * Function to get Instance by name
	 * @params moduleName:-- Name of the module to create instance
	 */
	getInstanceByModuleName : function(moduleName){
		if(typeof moduleName == "undefined"){
			moduleName = app.getModuleName();
		}
		var parentModule = app.getParentModuleName();
		if(parentModule == 'Settings'){
			var moduleClassName = parentModule+"_"+moduleName+"_Edit_Js";
			if(typeof window[moduleClassName] == 'undefined'){
				moduleClassName = moduleName+"_Edit_Js";
			}
			var fallbackClassName = parentModule+"_Vtiger_Edit_Js";
			if(typeof window[fallbackClassName] == 'undefined') {
				fallbackClassName = "Vtiger_Edit_Js";
			}
		} else {
			moduleClassName = moduleName+"_Edit_Js";
			fallbackClassName = "Vtiger_Edit_Js";
		}
		if(typeof window[moduleClassName] != 'undefined'){
			var instance = new window[moduleClassName]();
		}else{
			var instance = new window[fallbackClassName]();
		}
		return instance;
	},


	getInstance: function(){
		if(Vtiger_Edit_Js.editInstance == false){
			var instance = Vtiger_Edit_Js.getInstanceByModuleName();
			Vtiger_Edit_Js.editInstance = instance;
			return instance;
		}
		return Vtiger_Edit_Js.editInstance;
	}

},{

	formElement : false,

	getForm : function() {
		if(this.formElement == false){
			this.setForm(jQuery('#EditView'));
		}
		return this.formElement;
	},

	setForm : function(element){
		this.formElement = element;
		return this;
	},

    getPopUpParams : function(container) {
        var params = {};
        var sourceModule = app.getModuleName();
		var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();
        var sourceFieldElement = jQuery('input[class="sourceField"]',container);
		var sourceField = sourceFieldElement.attr('name');
		var sourceRecordElement = jQuery('input[name="record"]');
		var sourceRecordId = '';
		if(sourceRecordElement.length > 0) {
            sourceRecordId = sourceRecordElement.val();
        }

        var isMultiple = false;
        if(sourceFieldElement.data('multiple') == true){
            isMultiple = true;
        }
        if(sourceModule == 'Contacts' && popupReferenceModule == null) /* added by ajay*/
            popupReferenceModule = sourceModule;
		var params = {
			'module' : popupReferenceModule,
			'src_module' : sourceModule,
			'src_field' : sourceField,
			'src_record' : sourceRecordId
		}

        if(isMultiple) {
            params.multi_select = true ;
        }
        return params;
    },


	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = jQuery(e.target).closest('td');

        var params = this.getPopUpParams(parentElem);

        var isMultiple = false;
        if(params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if(prePopupOpenEvent.isDefaultPrevented()) {
            return ;
        }

		var popupInstance =Vtiger_Popup_Js.getInstance();
		popupInstance.show(params,function(data){
				var responseData = JSON.parse(data);
                var dataList = new Array();
				for(var id in responseData){
					var data = {
						'name' : responseData[id].name,
						'id' : id
					}
                    dataList.push(data);
                    if(!isMultiple) {
                        thisInstance.setReferenceFieldValue(parentElem, data);
                    }
				}

                if(isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
                }
			});
	},

	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
	},

	proceedRegisterEvents : function(){
		if(jQuery('.recordEditView').length > 0){
			return true;
		}else{
			return false;
		}
	},

	referenceModulePopupRegisterEvent : function(container){
		var thisInstance = this;
		container.find('.relatedPopup').on("click",function(e){
			thisInstance.openPopUp(e);
		});
		container.find('.referenceModulesList').chosen().change(function(e){
			var element = jQuery(e.currentTarget);
			var closestTD = element.closest('td').next();
			var popupReferenceModule = element.val();
			var referenceModuleElement = jQuery('input[name="popupReferenceModule"]', closestTD);
			var prevSelectedReferenceModule = referenceModuleElement.val();
			referenceModuleElement.val(popupReferenceModule);

			//If Reference module is changed then we should clear the previous value
			if(prevSelectedReferenceModule != popupReferenceModule) {
				closestTD.find('.clearReferenceSelection').trigger('click');
			}
		});
	},

	getReferencedModuleName : function(parenElement){
		return jQuery('input[name="popupReferenceModule"]',parenElement).val();
	},

	searchModuleNames : function(params) {
		var aDeferred = jQuery.Deferred();

		if(typeof params.module == 'undefined') {
			params.module = app.getModuleName();
		}

		if(typeof params.action == 'undefined') {
			params.action = 'BasicAjax';
		}
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(error){
				//TODO : Handle error
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},

	/**
	 * Function which will handle the reference auto complete event registrations
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerAutoCompleteFields : function(container) {
		var thisInstance = this;
		container.find('input.autoComplete').autocomplete({
			'minLength' : '3',
			'source' : function(request, response){
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var tdElement = inputElement.closest('td');
				var searchValue = request.term;
				var params = {};
				var searchModule = thisInstance.getReferencedModuleName(tdElement);
				params.search_module = searchModule
				params.search_value = searchValue;
				thisInstance.searchModuleNames(params).then(function(data){
					var reponseDataList = new Array();
					var serverDataFormat = data.result
					if(serverDataFormat.length <= 0) {
						serverDataFormat = new Array({
							'label' : app.vtranslate('JS_NO_RESULTS_FOUND'),
							'type'  : 'no results'
						});
					}
					for(var id in serverDataFormat){
						var responseData = serverDataFormat[id];
						reponseDataList.push(responseData);
					}
					response(reponseDataList);
				});
			},
			'select' : function(event, ui ){
				var selectedItemData = ui.item;
				//To stop selection if no results is selected
				if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
					return false;
				}
				selectedItemData.name = selectedItemData.value;
				var element = jQuery(this);
				var tdElement = element.closest('td');
				thisInstance.setReferenceFieldValue(tdElement, selectedItemData)
			},
			'change' : function(event, ui) {
				var element = jQuery(this);
				//if you dont have readonly attribute means the user didnt select the item
				if(element.attr('readonly')== undefined) {
					element.closest('td').find('.clearReferenceSelection').trigger('click');
				}
			},
			'open' : function(event,ui) {
				//To Make the menu come up in the case of quick create
				jQuery(this).data('autocomplete').menu.element.css('z-index','100001');

			}
		});
	},


	/**
	 * Function which will register reference field clear event
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerClearReferenceSelectionEvent : function(container) {
		container.find('.clearReferenceSelection').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var parentTdElement = element.closest('td');
			var fieldNameElement = parentTdElement.find('.sourceField');
			var fieldName = fieldNameElement.attr('name');
			fieldNameElement.val('');
			parentTdElement.find('#'+fieldName+'_display').removeAttr('readonly').val('');
			element.trigger(Vtiger_Edit_Js.referenceDeSelectionEvent);
			e.preventDefault();
		})
	},

	/**
	 * Function which will register event to prevent form submission on pressing on enter
	 * @params - container <jQuery> - element in which auto complete fields needs to be searched
	 */
	registerPreventingEnterSubmitEvent : function(container) {
		container.on('keypress', function(e){
            //Stop the submit when enter is pressed in the form
            var currentElement = jQuery(e.target);
            if(e.which == 13 && (!currentElement.is('textarea'))) {
                e. preventDefault();
            }
		})
	},

	/**
	 * Function which will give you all details of the selected record
	 * @params - an Array of values like {'record' : recordId, 'source_module' : searchModule, 'selectedName' : selectedRecordName}
	 */
	getRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var url = "index.php?module="+app.getModuleName()+"&action=GetData&record="+params['record']+"&source_module="+params['source_module'];
		AppConnector.request(url).then(
			function(data){
				if(data['success']) {
					aDeferred.resolve(data);
				} else {
					aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
		)
		return aDeferred.promise();
	},


	registerTimeFields : function(container) {
		app.registerEventForTimeFields(container);
	},

    referenceCreateHandler : function(container) {
        var thisInstance = this;
        var postQuickCreateSave  = function(data) {
            var params = {};
            params.name = data.result._recordLabel;
            params.id = data.result._recordId;
            thisInstance.setReferenceFieldValue(container, params);
        }

        var referenceModuleName = this.getReferencedModuleName(container);
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
        if(quickCreateNode.length <= 0) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
        }
        quickCreateNode.trigger('click',{'callbackFunction':postQuickCreateSave});
    },

	/**
	 * Function which will register event for create of reference record
	 * This will allow users to create reference record from edit view of other record
	 */
	registerReferenceCreate : function(container) {
		var thisInstance = this;
		container.find('.createReferenceRecord').on('click', function(e){
			var element = jQuery(e.currentTarget);
			var controlElementTd = element.closest('td');

			thisInstance.referenceCreateHandler(controlElementTd);
		})
	},

	/**
	 * Function to register the event status change event
	 */
	registerEventStatusChangeEvent : function(container){
		var followupContainer = container.find('.followUpContainer');
		container.find('select[name="eventstatus"]').on('change',function(e){
			var selectedOption = jQuery(e.currentTarget).val();
			if(selectedOption == 'Held'){
				followupContainer.show();
			} else{
				followupContainer.hide();
			}
		});
	},

	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
		this.referenceModulePopupRegisterEvent(container);
		this.registerAutoCompleteFields(container);
		this.registerClearReferenceSelectionEvent(container);
		this.registerPreventingEnterSubmitEvent(container);
		this.registerTimeFields(container);
		//Added here instead of register basic event of calendar. because this should be registered all over the places like quick create, edit, list..
		this.registerEventStatusChangeEvent(container);
		this.registerRecordAccessCheckEvent(container);
		this.registerEventForPicklistDependencySetup(container);
        /*Start code by jitendra singh on 26 March 2014*/
        this.registerEventForPicklistEvents(container);
        /*End code by jitendra singh on 26 March 2014*/
	},

	/**
	 * Function to register event for image delete
	 */
	registerEventForImageDelete : function(){
		var formElement = this.getForm();
		var recordId = formElement.find('input[name="record"]').val();
		formElement.find('.imageDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var parentTd = element.closest('td');
			var imageUploadElement = parentTd.find('[name="imagename[]"]');
			var fieldInfo = imageUploadElement.data('fieldinfo');
			var mandatoryStatus = fieldInfo.mandatory;
			var imageData = element.closest('div').find('img').data();
			var params = {
				'module' : app.getModuleName(),
				'action' : 'DeleteImage',
				'imageid' : imageData.imageId,
				'record' : recordId

			}
			AppConnector.request(params).then(
				function(data){
					if(data.success ==  true){
						element.closest('div').remove();
						var exisitingImages = parentTd.find('[name="existingImages"]');
						if(exisitingImages.length < 1 && mandatoryStatus){
							formElement.validationEngine('detach');
							imageUploadElement.attr('data-validation-engine','validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
							formElement.validationEngine('attach');
						}
					}
				},
				function(error){
					//TODO : Handle error
				}
			)
		});
	},

	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('#EditView').find('td');
			elements.addClass(widthType);
		}
	},
    /*Start code for common action events by jitendra singh on 26 March 2014*/
    registerEventForPicklistEvents : function(container) {

        /*To Enable Recruitment Details block when Recruitment is checked by jitendra singh on 26 March 2014*/
        container.find('input[name="cf_763"]').on('change',function(e){
            var Recruitment_block = container.find('.Recruitment_block');
            if($('#Contacts_editView_fieldName_cf_763').attr('checked')) {
                Recruitment_block.show();
            } else {
                Recruitment_block.hide();
            }
        });
        /*To Enable Recruitment Details block when Recruitment is checked by jitendra singh on 26 March 2014*/
        container.find('input[name="cf_765"]').on('change',function(e){
            var Marketing_block = container.find('.Marketing_block');
            if($('#Contacts_editView_fieldName_cf_765').attr('checked')) {
                Marketing_block.show();
            } else {
                Marketing_block.hide();
            }
        });
        /*To Enable Recruitment Details block when Recruitment is checked by jitendra singh on 26 March 2014*/
        container.find('input[name="donotcall"]').on('change',function(e){
            var Education_block = container.find('.Education_block');
            if($('#Contacts_editView_fieldName_donotcall').attr('checked')) {
                Education_block.show();
            } else {
                Education_block.hide();
        }
        });
        /*To Enable Recruitment Details block when Recruitment is checked by jitendra singh on 26 March 2014*/
        container.find('input[name="reference"]').on('change',function(e){
            var Government_block = container.find('.Government_block');
            if($('#Contacts_editView_fieldName_reference').attr('checked')) {
                Government_block.show();
            } else {
                Government_block.hide();
        }
        });

        /*To Enable Recruitment Details block when Recruitment is checked by jitendra singh on 26 March 2014*/
        container.find('input[name="notify_owner"]').on('change',function(e){
            var Social_block = container.find('.Social_block');
            if($('#Contacts_editView_fieldName_notify_owner').attr('checked')) {
                Social_block.show();
            } else {
                Social_block.hide();
        }
        });
		
			/*Add by Raghvender Singh on 29042014*/
		  container.find('select[name="payment_mode_app"]').on('change',function(e){
				var selectedOptiont = jQuery(e.currentTarget).val();// jitendra

                var check_no = container.find('.check_no');

                if(selectedOptiont == 'PDC'){
				    check_no.show();
                }

                else{
                    check_no.hide();
                }

            });
			
			container.find('select[name="payment_mode_app"]').on('change',function(e){
				var selectedOptiont = jQuery(e.currentTarget).val();// jitendra

                var leadsource = container.find('.credit_tab');
				var credit_remark = container.find('.credit_remark_tab');

                if(selectedOptiont == 'Credit'){
				    leadsource.show();
					credit_remark.show();
                }

                else{
                    leadsource.hide();
					credit_remark.hide();
                }

            });
			/*Add by Raghvender Singh on 29042014*/
    },

    /*End code by jitendra singh on 26 March 2014*/

	registerSubmitEvent: function() {
		var editViewForm = this.getForm();
		/*Add code by raghvender Singh singh on 22-04-2014*/
		var pathname = window.location.href;
		myString1 = pathname.substring(0, pathname.lastIndexOf("&"));
		var pathname1 = window.location.href;
		myString2 = pathname1.substring(0, pathname1.lastIndexOf("/"));
		checkcondition = myString2+'/index.php?module=Contacts&view=Edit';
		/*End code by raghvender Singh singh on 22-04-2014*/

		editViewForm.submit(function(e){
			/*Open code by raghvender Singh singh on 22-04-2014*/
		
			if(myString1 == checkcondition)
			{
				$ar1 = $('#Contacts_editView_fieldName_cf_763').attr('checked');
				$ar2 = $('#Contacts_editView_fieldName_cf_765').attr('checked');
				$ar3 = $('#Contacts_editView_fieldName_donotcall').attr('checked');
				$ar4 = $('#Contacts_editView_fieldName_reference').attr('checked');
				$ar5 = $('#Contacts_editView_fieldName_notify_owner').attr('checked');
				
				if($ar1 == 'checked' || $ar2 == 'checked' || $ar3 == 'checked' || $ar4 == 'checked' || $ar5 == 'checked'){				
				return true;
				}
				else{
					alert("Select one or more than one Client Business Need");
				return false;
				}
			}
			
			/*End code by raghvender Singh singh on 22-04-2014*/
			
			//Form should submit only once for multiple clicks also
			if(typeof editViewForm.data('submit') != "undefined") {
				
				return false;
			} else {
				
				var module = jQuery(e.currentTarget).find('[name="module"]').val();
				if(editViewForm.validationEngine('validate')) {
					//Once the form is submiting add data attribute to that form element
					editViewForm.data('submit', 'true');
						//on submit form trigger the recordPreSave event
						var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
						editViewForm.trigger(recordPreSaveEvent, {'value' : 'edit'});
						if(recordPreSaveEvent.isDefaultPrevented()) {
							//If duplicate record validation fails, form should submit again
							editViewForm.removeData('submit');
							e.preventDefault();
						}
				} else {
					//If validation fails, form should submit again
					editViewForm.removeData('submit');
					// to avoid hiding of error message under the fixed nav bar
					app.formAlignmentAfterValidation(editViewForm);
				}
			}
		});
	},

	/*
	 * Function to check the view permission of a record after save
	 */

	registerRecordAccessCheckEvent : function(form) {

		form.on(Vtiger_Edit_Js.recordPreSave, function(e, data) {
			var assignedToSelectElement = jQuery('[name="assigned_user_id"]',form);
			if(assignedToSelectElement.data('recordaccessconfirmation') == true) {
				return;
			}else{
				if(assignedToSelectElement.data('recordaccessconfirmationprogress') != true) {
					var recordAccess = assignedToSelectElement.find('option:selected').data('recordaccess');
					if(recordAccess == false) {
						var message = app.vtranslate('JS_NO_VIEW_PERMISSION_AFTER_SAVE');
						Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
						function(e) {
							assignedToSelectElement.data('recordaccessconfirmation',true);
							assignedToSelectElement.removeData('recordaccessconfirmationprogress');
							form.append('<input type="hidden" name="returnToList" value="true" />');
							form.submit();
						},
						function(error, err){
							assignedToSelectElement.removeData('recordaccessconfirmationprogress');
							e.preventDefault();
						});
						assignedToSelectElement.data('recordaccessconfirmationprogress',true);
					} else {
						return true;
					}
				}
			}
			e.preventDefault();
		});
	},

	/**
	 * Function to register event for setting up picklistdependency
	 * for a module if exist on change of picklist value
	 */
	registerEventForPicklistDependencySetup : function(container){
        var picklistDependcyElemnt = jQuery('[name="picklistDependency"]',container);
        if(picklistDependcyElemnt.length <= 0) {
            return;
        }
		var picklistDependencyMapping = JSON.parse(picklistDependcyElemnt.val());

		var sourcePicklists = Object.keys(picklistDependencyMapping);
		if(sourcePicklists.length <= 0){
			return;
		}

		var sourcePickListNames = "";
		for(var i=0;i<sourcePicklists.length;i++){
			sourcePickListNames += '[name="'+sourcePicklists[i]+'"],';
		}
		var sourcePickListElements = container.find(sourcePickListNames);

		sourcePickListElements.on('change',function(e){
			var currentElement = jQuery(e.currentTarget);
			var sourcePicklistname = currentElement.attr('name');

			var configuredDependencyObject = picklistDependencyMapping[sourcePicklistname];
			var selectedValue = currentElement.val();
			var targetObjectForSelectedSourceValue = configuredDependencyObject[selectedValue];
			var picklistmap = configuredDependencyObject["__DEFAULT__"];

			if(typeof targetObjectForSelectedSourceValue == 'undefined'){
				targetObjectForSelectedSourceValue = picklistmap;
			}
			jQuery.each(picklistmap,function(targetPickListName,targetPickListValues){
				var targetPickListMap = targetObjectForSelectedSourceValue[targetPickListName];
				if(typeof targetPickListMap == "undefined"){
					targetPickListMap = targetPickListValues;
				}
				var targetPickList = jQuery('[name="'+targetPickListName+'"]',container);
				if(targetPickList.length <= 0){
					return;
				}

				var listOfAvailableOptions = targetPickList.data('availableOptions');
				if(typeof listOfAvailableOptions == "undefined"){
					listOfAvailableOptions = jQuery('option',targetPickList);
					targetPickList.data('available-options', listOfAvailableOptions);
				}

				var optionSelector = '';
				optionSelector += '[value=""],';
				for(var i=0; i<targetPickListMap.length; i++){
					optionSelector += '[value="'+targetPickListMap[i]+'"],';
				}
				var targetOptions = listOfAvailableOptions.filter(optionSelector);
				//Before updating the list, selected option should be updated
				var targetPickListSelectedValue = '';
				var targetPickListSelectedValue = targetOptions.filter('[selected]').val();
				targetPickList.html(targetOptions).val(targetPickListSelectedValue).trigger("liszt:updated");
			})
		});

		//To Trigger the change on load
		sourcePickListElements.trigger('change');
	},

	registerEvents: function(){
		var editViewForm = this.getForm();
		var statusToProceed = this.proceedRegisterEvents();
		if(!statusToProceed){
			return;
		}

		this.registerBasicEvents(this.getForm());
		this.registerEventForImageDelete();
		this.registerSubmitEvent();

		app.registerEventForDatePickerFields('#EditView');
		editViewForm.validationEngine(app.validationEngineOptions);

		this.registerReferenceCreate(editViewForm);
		//this.triggerDisplayTypeEvent();
	}

});
/* Start added by Jitendra Singh [TECHFOUR]*/
function ValidateCustomFields() {
	$('#Contacts_editView_fieldName_cf_823').keyup(function() {
			//$('span.error-keyup-1').hide();
			var inputVal = $(this).val();
			var numericReg = /^\d*[0-9](|.\d*[0-9]|,\d*[0-9])?$/;
			if(!numericReg.test(inputVal)) {
				value = inputVal.replace(/[^0-9]+/g, '');
				jQuery(this).val(value);
				//$(this).after('<span class="error error-keyup-1">Numeric characters only.</span>');
			}
		});

		$('#Leads_editView_fieldName_registrationno').keyup(function() {
			//$('span.error-keyup-1').hide();
			var inputVal = $(this).val();
			var numeric_and_stringReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
			//if(!numeric_and_stringReg.test(inputVal)) {
				value = inputVal.replace(/[^a-z^A-Z^0-9]+/g, '');
				jQuery(this).val(value.toUpperCase());
				//$(this).after('<span class="error error-keyup-1">Numeric characters only.</span>');
			//}
		});
}
/* End added by Jitendra Singh [TECHFOUR]*/

/* Start added by jitendra [TECHFOUR]*/
function ValidatePriceField(field_id) {
	var field_id1='#'+field_id;
	$(field_id1).keyup(function() {

			//$('span.error-keyup-1').hide();
			var inputVal = $(this).val();
			//alert(field_id1+'___'+inputVal);

			var numericReg = /^[0-9\._]+$/;
			if(!numericReg.test(inputVal)) {
				value = inputVal.replace(/[^0-9]+/g, '');
				jQuery(this).val(value);
			}
		});
}
/* End added by jitendra [TECHFOUR]*/


function AccessFormatedDate(val,dateFormat){   // Function defined for getting the formated data

		var Datevalues = new Array();
		Datevalues=val.split('-');
		if(dateFormat=='mm-dd-yyyy'){
		var CrmDate = Datevalues[2]+'-'+Datevalues[0]+'-'+Datevalues[1];
		}
		if(dateFormat=='dd-mm-yyyy'){
		var CrmDate = Datevalues[2]+'-'+Datevalues[1]+'-'+Datevalues[0];
		}

		if(dateFormat=='yyyy-mm-dd'){
		var CrmDate = Datevalues[0]+'-'+Datevalues[1]+'-'+Datevalues[2];
		}

		if(dateFormat=='yyyy-dd-mm'){
		var CrmDate = Datevalues[0]+'-'+Datevalues[2]+'-'+Datevalues[1];
		}

		//alert("CrmDate"+CrmDate);
		return CrmDate;

}


/*Add code by jitendra singh on 21 March 2014*/

function Closure_Date_Validate(val,id,dateformat){ // Target Start date

				var current_date = new Date(new Date().getTime() - 24 * 60 * 60 * 1000);
				var Caldate = new Date(AccessFormatedDate(val,dateformat));
				if(Caldate < current_date){
					alert('Expected Closure Date should not be less than Current Date.');
					document.getElementById(id).value='';
				 }
	}

	/* End by jitendra singh on 21 March 2014*/