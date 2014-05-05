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

{assign var="FIELD_INFO" value=Zend_Json::encode($FIELD_MODEL->getFieldInfo())}

{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}

{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}



{if $MODULE eq 'Contacts' && $FIELD_MODEL->getFieldName() eq 'account_uses'}



<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH} row-fluid {/if}" disabled="disabled" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>

		{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}

	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}

        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>

    {/foreach}

</select>



{else if $MODULE eq 'Events' && $FIELD_MODEL->getFieldName() eq 'taskpriority'}

    <div class="{if $FIELD_MODEL->get('fieldvalue') eq ''}hide {/if} reason_of_planned_id">

    <select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH}  row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>

        {if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}

        {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}

            <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>

        {/foreach}

    </select>

    </div>

{*<!--Add by Raghvender Singh on 29042014-->*}
{else if $MODULE eq 'Potentials' && $FIELD_MODEL->getFieldName() eq 'cf_809'}
<div class="hide check_no">
<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH}  row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}"   data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>
		{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}
	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
    {/foreach}
</div>
</select>
{*<!--End Add by Raghvender Singh on 29042014-->*}



{else}

<select class="chzn-select {if $OCCUPY_COMPLETE_WIDTH}  row-fluid {/if}" name="{$FIELD_MODEL->getFieldName()}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO|escape}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} data-selected-value='{$FIELD_MODEL->get('fieldvalue')}'>

		{if $FIELD_MODEL->isEmptyPicklistOptionAllowed()}<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>{/if}

	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}

        <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>

    {/foreach}

</select>

{/if}



{/strip}