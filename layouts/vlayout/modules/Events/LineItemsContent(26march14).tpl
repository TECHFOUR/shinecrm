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

{assign var="expected_closure_date" value="expected_closure_date"|cat:$row_no}
{assign var="remarks" value="remarks"|cat:$row_no}
{assign var="expected_revenue" value="expected_revenue"|cat:$row_no}
{assign var="product" value="product"|cat:$row_no}
{assign var="product_display" value="product"|cat:$row_no|cat:"_display"}
{assign var="Events_editView_fieldName_product_select" value="Events_editView_fieldName_product"|cat:$row_no|cat:"_select"}
{assign var="deleted" value="deleted"|cat:$row_no}
{assign var="lead_stage" value="lead_stage"|cat:$row_no}




{*<!-- Code modified by jitendra singh [TECHFOUR] -->*}
<td xmlns="http://www.w3.org/1999/html">
    <i class="icon-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
    &nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
    <input type="hidden" name="{$deleted}" id="{$deleted}" class="rowNumber" value="{$row_no}" />
</td>

<td>
    <label class="muted pull-left marginRight5px"> <span class="redColor">*</span></label>
    <input type="hidden" value="Project" name="popupReferenceModule">

    <input type="hidden" data-displayvalue="" class="sourceField" value="" name="{$product}" id="{$product}">

    <!--<span class="add-on clearReferenceSelection cursorPointer"><i title="Clear" class="icon-remove-sign" id="Targets_editView_fieldName_{$product}_clear"></i></span>-->

    <input type="text"  placeholder="Product" title="Product"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" value="" class=" marginLeftZero autoComplete ui-autocomplete-input" name="{$product_display}" id="{$product_display}" autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true">
   <span class="add-on relatedPopup cursorPointer">
   <i title="Select" class="icon-search relatedPopup" id="{$Events_editView_fieldName_product_select}"></i></span>

</td>


<td>
    <label class="muted pull-left marginRight5px"> <span class="redColor">*</span></label>
    <input id="{$expected_revenue}" name="{$expected_revenue}" type="text" placeholder="Expected Revenue" style="width:128px" title="No of ROs" class="qty smallInputBox" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" onkeyup="ValidatePriceField(this.id);" />

</td>

<td>
    <label class="muted pull-left marginRight5px"> <span class="redColor">*</span></label>
    <input class="date field" type="Text" id="{$expected_closure_date}" placeholder="Expected Closure Date" title="Expected Closure Date"  name="{$expected_closure_date}" maxlength="25" style="width:128px" size="25"  onclick="javascript:NewCssCal(this.id)" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>
</td>


<td>
    <label class="muted pull-left marginRight5px"> <span class="redColor">*</span></label>
    <input id="{$lead_stage}" name="{$lead_stage}" type="text" placeholder="Lead Stage" style="width:128px" maxlength="35" title="Lead Stage" class="qty smallInputBox" data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />

</td>

<td>
    <label class="muted pull-left marginRight5px"><span class="redColor">*</span></label>
    <textarea class="span3 " type="Text" id="{$remarks}" placeholder="Remarks" title="Remarks" name="{$remarks}"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
    </textarea>

</td>