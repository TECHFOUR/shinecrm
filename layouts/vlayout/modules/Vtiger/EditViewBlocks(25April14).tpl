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
{literal}
    <script type="text/javascript">

        function getNextTypeValues(entityid, entityvalue, product, columname, fieldname, argument){
           // alert(entityid+"__"+entityvalue+"__"+product+"__"+columname+"__"+fieldname)
            var rowid = entityid.split("___")[1];
            var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&selectfieldname="+columname+"&argument="+argument+"&type=NextType";
            if(argument == 2) {
               // alert("product_type_"+product+rowid);
                var p_type = $("#product_type_"+product+"___"+rowid).val();
                QryString += "&previous_value="+p_type;
            }

            if(argument == 3) {
                // alert("product_type_"+product+rowid);
                var p_type = $("#product_type_"+product+"___"+rowid).val();
                var p_type_one = $("#it_nonit_database___"+rowid).val();
                QryString += "&previous_value="+p_type+"&previous_value_one="+p_type_one;
            }

            $.ajax({
                type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(result_data){
                   alert(result_data);
                    $("#"+fieldname+product+"___"+rowid).html('');
                    $("#"+fieldname+product+"___"+rowid).html(result_data);

                }
            });
        }

        function getMRPBottomPrice(entityvalue, entityid, product) {
            var rowid = entityid.split("___")[1];
            var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&type=Default";
            $.ajax({
                type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(result_data){
                    var response = result_data.split("###");
                    $("#bottom_price_"+product+rowid).val(response[0]);
                    $("#mrp_"+product+rowid).val(response[1]);
                    $("#discount_"+product+rowid).val('0.00');
                    $("#discount_amount_"+product+rowid).val('0.00');
                    $("#offered_amount_"+product+rowid).val(response[1]);
                    $("#service_tax_amount_"+product+rowid).val(response[2]);
                    $("#total_amount_"+product+rowid).val(response[3]);
                }
            });
        }

        function getOfferedAmount(entityid, entityvalue, product){
            var rowid = entityid.split("offered_amount_"+product)[1];
            var bottom_price =  $("#bottom_price_"+product+rowid).val().replace(",","");
            var offered_amount = $("#offered_amount_"+product+rowid).val().replace(",","");
            if(offered_amount == null || offered_amount == "")
                offered_amount = 0;
            var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&type=Offered";
            $.ajax({
                type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(service_tax){
                var discount_percentage = 100 - ((offered_amount/bottom_price)*100);
                var discount_amount = bottom_price - offered_amount;
                var new_service_tax_amount = (offered_amount*service_tax)/100;
                var new_total_amount = parseFloat(offered_amount) + parseFloat(new_service_tax_amount);
                $("#discount_"+product+rowid).val(discount_percentage.toFixed(2));
                $("#discount_amount_"+product+rowid).val(discount_amount.toFixed(2));
                $("#service_tax_amount_"+product+rowid).val(new_service_tax_amount.toFixed(2));
                $("#total_amount_"+product+rowid).val(new_total_amount.toFixed(2));
                }
            });
        }

                function getNewMRPBottomPrice(entityid, entityvalue, product){
                    var rowid = entityid.split("__")[1];
                    var previousonevalue;
                    var previousvalue =  $("#product_type_"+product+"___"+rowid).val().replace(",","");
                    if(product == 'events') {
                        previousonevalue =  $("#sponsorship_"+product+"___"+rowid).val().replace(",","");
                        previousonevalue = previousonevalue.split("__")[0];
                    }
                    //alert(previousvalue+"__"+previousonevalue);
                var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&previous_value="+previousvalue
                        +"&type=NEWOffered"+"&previousonevalue="+previousonevalue;
                $.ajax({
                    type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(result_data){
                    alert(result_data);
                    var response = result_data.split("###");
                    $("#bottom_price_"+product+rowid).val(response[0]);
                    $("#mrp_"+product+rowid).val(response[1]);
                    $("#discount_"+product+rowid).val('0.00');
                    $("#discount_amount_"+product+rowid).val('0.00');
                    $("#offered_amount_"+product+rowid).val(response[1]);
                    $("#service_tax_amount_"+product+rowid).val(response[2]);
                    $("#total_amount_"+product+rowid).val(response[3]);
                }
            });
        }
    </script>
{/literal}

{strip}

<div class='container-fluid editViewContainer'>

	<form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">

		{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}

		{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}

			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />

		{/if}

		{assign var=QUALIFIED_MODULE_NAME value={$MODULE}}

		{assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}

		{if $IS_PARENT_EXISTS}

			{assign var=SPLITTED_MODULE value=":"|explode:$MODULE}

			<input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />

			<input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />

		{else}

			<input type="hidden" name="module" value="{$MODULE}" />

		{/if}

		<input type="hidden" name="action" value="Save" />

		<input type="hidden" name="record" value="{$RECORD_ID}" />

		<input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}" />

		<input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}" />

		{if $IS_RELATION_OPERATION }

			<input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}" />

			<input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}" />

			<input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}" />

		{/if}

		<div class="contentHeader row-fluid">

		{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}

		{if $RECORD_ID neq ''}

			<h3 class="span8 textOverflowEllipsis" title="{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)} - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>

		{else}

			<h3 class="span8 textOverflowEllipsis">{vtranslate('LBL_CREATING_NEW', $MODULE)} {vtranslate($SINGLE_MODULE_NAME, $MODULE)}</h3>

		{/if}

			<span class="pull-right">

				<button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>

				<a class="cancelLink" type="reset" onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>

			</span>

		</div>



  <!--Code added for popup for Customer module in edit mode Jitendra Singh 21 March 2014--->



    {if $MODULE eq 'Contacts' || $MODULE eq 'Events' || $MODULE eq 'Potentials'}

    

	{include file="LeftPanel.tpl"|@vtemplate_path:$MODULE}		



   	{/if}

	<!--End Code for popup Jitendra Singh 8 March 2014--->







		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}



           {*<!-- Start Code to hide Post Call Updates Tab by jitendra singh on 25 March 2014 -->*}

           {if $MODULE eq 'Contacts' && $MODE eq '' && ($BLOCK_LABEL eq "Post Call Updates" || $BLOCK_LABEL eq "Recruitment Details" || $BLOCK_LABEL eq "Social Details"

           || $BLOCK_LABEL eq "Marketing Details" || $BLOCK_LABEL eq "Education Details" || $BLOCK_LABEL eq "Government Details" || $BLOCK_LABEL eq "Client Business Need") }









            {else}

           {*<!-- Start Code to hide conveynence Tab when "Meeting" activity type is selected by jitendra singh on 24 March 2014 -->*}

           {if $BLOCK_LABEL eq "Conveyance Tab "}

              <div class="hide convenence_block">

           {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Recruitment Details"}

              <div class="hide Recruitment_block">

           {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Marketing Details"}

           <div class="hide Marketing_block">

           {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Education Details"}

           <div class="hide Education_block">

           {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Government Details"}

           <div class="hide Government_block">

           {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Social Details"}

           <div class="hide Social_block">

           {/if}





           {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}



			<table class="table table-bordered blockContainer showInlineTable">

			<tr>

				<th class="blockHeader" colspan="4">{vtranslate($BLOCK_LABEL, $MODULE)}</th>



			</tr>



                {* start code added by ajay *}

                <tr>

                    <td colspan="4">

                {if $MODULE eq 'Events' && vtranslate(vtranslate($BLOCK_LABEL, $MODULE)) eq 'Related Lead'}

                    {include file="LineItemsEdit.tpl"|@vtemplate_path:$MODULE}

                {/if}

                {* end code added by ajay *}

                    </td>

                </tr>



			<tr>

			{assign var=COUNTER value=0}

			{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}



				{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}

				{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}

					{if $COUNTER eq '1'}

						<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>

						{assign var=COUNTER value=0}

					{/if}

				{/if}

				{if $COUNTER eq 2}

					</tr><tr>

					{assign var=COUNTER value=1}

				{else}

					{assign var=COUNTER value=$COUNTER+1}

				{/if}

				<td class="fieldLabel {$WIDTHTYPE}">

					{if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}

						{if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}

						{if $isReferenceField eq "reference"}

							{assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}

							{assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}

							{if $REFERENCE_LIST_COUNT > 1}

								{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}

								{assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}

								{if !empty($REFERENCED_MODULE_STRUCT)}

									{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}

								{/if}

                                {if ($MODULE eq 'Events' || $MODULE eq 'Calendar') && $contact_id neq ''}

								<span class="hide pull-right"> {*<!-- Hide Dropdown from Activity module by jitendra singh on 25 march14 -->*}

								{else}

                                    <span class="pull-right">

                                {/if}

                                    {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}

									<select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown" class="chzn-select referenceModulesList streched" style="width:140px;">

										<optgroup>

											{foreach key=index item=value from=$REFERENCE_LIST}

												<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>

											{/foreach}

										</optgroup>

									</select>

								</span>

							{else}

								<label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>

							{/if}

						{else if $FIELD_MODEL->get('uitype') eq "83"}

							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}





						{*<!-- Hide Level from Activity module by jitendra singh on 25 march14 -->*}

                    	{else if $FIELD_MODEL->get('label') eq 'Contact Name' && $contact_id neq '' && ($MODULE eq 'Events' || $MODULE eq 'Calendar')}

							

                        {else}

							{vtranslate($FIELD_MODEL->get('label'), $MODULE)}

						{/if}

					{if $isReferenceField neq "reference"}</label>{/if}

				</td>



				{if $FIELD_MODEL->get('uitype') neq "83"}

					<td class="fieldValue {$WIDTHTYPE}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>

						<div class="row-fluid">

							<span class="span10">

								{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}

							</span>

						</div>

					</td>

				{/if}

				{if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}

					<td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>

				{/if}



			{/foreach}

			</tr>

			</table>

			<br>

            

            {/if}

            {if $BLOCK_LABEL eq "Conveyance Tab "}

                </div>

            {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Recruitment Details"}

                </div>

            {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Marketing Details"}

                </div>

            {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Education Details"}

                </div>

            {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Government Details"}

                </div>

            {else if $MODULE eq 'Contacts' && $BLOCK_LABEL eq "Social Details"}

                </div>

            {/if}

            

		{/foreach}
        
        {* start code added by ajay *}
        <tr>
            <td colspan="4">
                {if $MODULE eq 'Potentials'}
                    {foreach item=data key=PICKLIST_NAME from=$UNIQUE_PRODUCTS}
                        {include file="LineItems"|cat:$data|cat:".tpl"|@vtemplate_path:$MODULE}
                        <br>
                    {/foreach}
                    {include file="LineItemsPayment.tpl"|@vtemplate_path:$MODULE}
                    <br>
                {/if}
            </td>
        </tr>
        {* end code added by ajay *}

{/strip}