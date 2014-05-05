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

    {if $MODULE eq 'Contacts' || $MODULE eq 'Events'}
			<link type="text/css" href="popupdragable/jquery_002.css" rel="stylesheet">
				<script type="text/javascript" src="popupdragable/index.js"></script>

		<script type="text/javascript" src="popupdragable/jquery_004.js"></script>

	<div id="example8" class="example_block">

   <div class="demo">
				<div id="window_block8" style="display:none;">
					<div style="padding:10px;">

						<div  style="font-size:12px;">
						<div style="color:#FECC00"><b><u>Account Manager Info</u></b></div>

                        <table>
                            <tr><td style="color:#999999">&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td style="color:#999999;font-size: 12px;">Branch</td><td>{if $Branch eq ''}- -{else}: {$Branch}{/if}</td></tr>

                        <tr><td style="color:#999999">Team</td><td>{if $TEAM eq ''}- -{else}: {$TEAM}{/if}</td></tr>

                        <tr><td style="color:#999999">BBSM</td><td>{if $BSM eq ''}- -{else}: {$BSM}{/if}</td></tr>

                        <tr><td style="color:#999999">KAM</td><td>{if $BTL eq ''}- -{else}: {$BTL}{/if}</td></tr>

                        <tr><td style="color:#999999">AM Mobile</td><td>{if $Account_Manager_Contact eq ''}- -{else}: {$Account_Manager_Contact}{/if}</td></tr>

                        <tr><td style="color:#999999">AM Email</td><td>{if $Account_Manager_Email eq ''}- -{else}: {$Account_Manager_Email}{/if}</td></tr>
                        </table>
                      </div>
					</div>
				</div>
				<!--<input value="Click here to know about MFCS Lead related information" onClick="createCustWindow();" type="button">-->
			</div>
		</div>

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
                                {if $SOURCE_MODULE eq 'Contacts'}
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

                        {else if $FIELD_MODEL->get('label') eq 'Contact Name' && $SOURCE_MODULE eq 'Contacts'}{*<!-- Hide Level from Activity module by jitendra singh on 25 march14 -->*}

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
{/strip}