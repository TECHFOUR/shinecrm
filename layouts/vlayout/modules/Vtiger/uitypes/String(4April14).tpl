{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}


{if $MODULE eq 'Contacts'}

<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" 
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" 
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" 
	   name="{$FIELD_MODEL->getFieldName()}" 
       
       {if $MODULE eq 'Contacts' && $FIELD_MODEL->getFieldName() eq 'client_status'}
       value="Active" 
       readonly = "readonly"
       {else}
       value="{$FIELD_MODEL->get('fieldvalue')}"
       {/if} 
       
       {if $MODULE eq 'Contacts' && $FIELD_MODEL->getFieldName() eq 'lastname'}
       placeholder="Name of Client if Different"
       {/if}
       
       {if $MODULE eq 'Contacts' && $FIELD_MODEL->getFieldName() eq 'cf_823'}
       onkeyup="ValidateCustomFields();" 
       maxlength="10"
       {/if}
       
       
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3' 
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} 
				readonly 
		{/if} 
data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />


{else if $MODULE eq 'Leads' &&  $FIELD_MODEL->getFieldName() eq 'cf_903'}

<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" 
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" 
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" 
	   name="{$FIELD_MODEL->getFieldName()}" 
        value="Incomplete"
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3' 
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} 
				readonly 
		{/if} 
data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />

{else if $MODULE eq 'Leads' &&  $FIELD_MODEL->getFieldName() eq 'cf_909'}

<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" 
		onkeyup="ValidatePriceField(this.id);"
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" 
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" 
	   name="{$FIELD_MODEL->getFieldName()}" 
        value="{$FIELD_MODEL->get('fieldvalue')}"
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3' 
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} 
				readonly 
		{/if} 
data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />


{else}
<input id="{$MODULE}_editView_fieldName_{$FIELD_NAME}" type="text" 
	   class="input-large {if $FIELD_MODEL->isNameField()}nameField{/if}" 
	   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true}required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" 
	   name="{$FIELD_MODEL->getFieldName()}" 
        value="{$FIELD_MODEL->get('fieldvalue')}"
		{if ($FIELD_MODEL->get('uitype') eq '106' && $MODE neq '') || $FIELD_MODEL->get('uitype') eq '3' 
				|| $FIELD_MODEL->get('uitype') eq '4'|| $FIELD_MODEL->isReadOnly()} 
				readonly 
		{/if} 
data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} />
{/if}


{* TODO - Handler Ticker Symbol field  ($FIELD_MODEL->get('uitype') eq '106' && $MODE eq 'edit') ||*}
{/strip}