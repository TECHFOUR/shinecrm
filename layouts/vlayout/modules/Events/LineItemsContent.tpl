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
{assign var="deleted" value="deleted"|cat:$row_no}
{assign var="leadid" value="leadid"|cat:$row_no}
{assign var="product" value="product"|cat:$row_no}
{assign var="product_display" value="product"|cat:$row_no|cat:"_display"}
{assign var="Events_editView_fieldName_product_select" value="Events_editView_fieldName_product"|cat:$row_no|cat:"_select"}
{assign var="expected_revenue" value="expected_revenue"|cat:$row_no}
{assign var="expected_closure_date" value="expected_closure_date"|cat:$row_no}
{assign var="lead_stage" value="lead_stage"|cat:$row_no}
{assign var="remarks" value="remarks"|cat:$row_no}


{*<!-- Code modified by jitendra singh [TECHFOUR] -->*}
<td xmlns="http://www.w3.org/1999/html">

    <i {if $data.leadid eq ''} class="icon-trash deleteRow cursorPointer" {/if} title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
    &nbsp;
    <input type="hidden" name="{$deleted}" id="{$deleted}" class="rowNumber" value="" />
    {if $data.leadid ne ''}
        <input type="hidden" name="{$leadid}" id="{$leadid}" class="rowNumber" value="{$data.leadid}" />
    {/if}
</td>


    <td>

        <select  style="width:190px" name="{$product}" id="{$product}" >
            <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
        {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$LEAD_PRODUCTS}
            <option value="{$PICKLIST_VALUE}" {if $data.product eq $PICKLIST_VALUE} selected {/if} >{$PICKLIST_VALUE}</option>
        {/foreach}
        </select>
    </td>
{*<td>
   
    <input type="hidden" value="Project" name="popupReferenceModule">

    <input type="hidden" data-displayvalue="{$data.product}" class="sourceField" value="{$data.product}" name="{$product}" id="{$product}">

    <input type="text"  placeholder="Product" title="Product"
           data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" value="{$data.product}"
           class=" marginLeftZero autoComplete ui-autocomplete-input" name="{$product_display}" id="{$product_display}" autocomplete="off"
           role="textbox" aria-autocomplete="list" aria-haspopup="true">
   <span class="add-on relatedPopup cursorPointer">
   <i title="Select" class="icon-search relatedPopup" id="{$Events_editView_fieldName_product_select}"></i></span>

</td>*}


<td>
   
    <input id="{$expected_revenue}" name="{$expected_revenue}" value="{$data.expected_revenue}" type="text"
           placeholder="Expected Revenue" style="width:128px" maxlength="15" title="Expected Revenue" class="qty smallInputBox"
           data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" onkeyup="ValidatePriceField(this.id);" />

</td>

<td>
  
  
    <input class="date field" type="Text" id="{$expected_closure_date}" value="{$data.expected_closure_date}"
           placeholder="Expected Closure Date" title="Expected Closure Date"  name="{$expected_closure_date}" maxlength="25" style="width:128px"
           size="25"   onchange="checkDate(this.value,'{$DATEFORM}',this.id)"   onclick="javascript:NewCssCal(this.id)" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
</td>


    <td>
        
        <select id="{$lead_stage}" style="width:190px" name="{$lead_stage}"  data-fieldinfo='{$FIELD_INFO|escape}'
                data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
            <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$LEAD_STAGE}
                <option value="{$PICKLIST_VALUE}" {if $data.lead_stage eq $PICKLIST_VALUE} selected {/if}>{$PICKLIST_VALUE}</option>
            {/foreach}
        </select>
    </td>

<td>
    
    <textarea class="span3" rows="1" cols="2" type="Text" id="{$remarks}" placeholder="Remarks" title="Remarks" name="{$remarks}"
              data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
    {$data.remarks}
    </textarea>

</td>

{/strip}


	
