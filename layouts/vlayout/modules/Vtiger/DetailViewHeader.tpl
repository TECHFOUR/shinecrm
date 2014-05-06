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

{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}

<input id="recordId" type="hidden" value="{$RECORD->getId()}" />

<div class="detailViewContainer">

    <div class="row-fluid detailViewTitle">

        <div class="{if $NO_PAGINATION} span12 {else} span10 {/if}">

            <div class="row-fluid">

                <div class="span5">

                    <div class="row-fluid">

                        {include file="DetailViewHeaderTitle.tpl"|vtemplate_path:$MODULE}

                    </div>

                </div>



                <div class="span7">

                    <div class="pull-right detailViewButtoncontainer">

                        <div class="btn-toolbar">



                            {if $MODULE eq 'Contacts'}

                                <span class="btn-group">

                               </span>

                                <span class="btn-group">

                             <a class="btn" href="index.php?module=Calendar&view=Edit&mode=Events&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true&picklistDependency=[]&                        defaultCallDuration=10&defaultOtherEventDuration=5&activitytype=&subject=&eventstatus=&date_start=&time_start=&due_date=&time_end=&assigned_user_id=&cf_917=&cf_919=&cf_921=&cf_923=&cf_925=&contact_id={$LINK_RECORD}&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true">Add Activity</a>

                                </span>

                                <span class="btn-group">

                                <a class="btn" href="index.php?module=Products&view=Edit&picklistDependency=[]&productname=&mobile=&email=&city=&assigned_user_id=1&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true">Add Contacts</a>

                                </span>

                            {else if $MODULE eq 'Leads'}

                            {/if}



                            {foreach item=DETAIL_VIEW_BASIC_LINK from=$DETAILVIEW_LINKS['DETAILVIEWBASIC']}
                            {*<!--Add by Raghvender Singh on 05052014-->*}
                                {if $MODULE eq 'Leads' }

                                {else if $DETAIL_VIEW_BASIC_LINK->getLabel() neq 'LBL_SEND_EMAIL'}
                                    <span class="btn-group">

								<button class="btn" id="{$MODULE_NAME}_detailView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_BASIC_LINK->getLabel())}"

                                        {if $DETAIL_VIEW_BASIC_LINK->isPageLoadLink()}

                                    onclick="window.location.href='{$DETAIL_VIEW_BASIC_LINK->getUrl()}'"

                                        {else}

                                onclick={$DETAIL_VIEW_BASIC_LINK->getUrl()}

                                        {/if}>

                                    <strong>{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</strong>

                                </button>

							</span>
                                {/if}

                            {/foreach}



                            {if $MODULE eq 'Leads'}

                            {else if $DETAILVIEW_LINKS['DETAILVIEW']|@count gt 0}

                                <span class="btn-group">
    
                                    {if $MODULE neq 'Contacts'}
                                        <button class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">

                                            <strong>{vtranslate('LBL_MORE', $MODULE_NAME)}</strong>&nbsp;&nbsp;<i class="caret"></i>

                                        </button>

                                    {/if}

                                    <ul class="dropdown-menu pull-right">

                                        {foreach item=DETAIL_VIEW_LINK from=$DETAILVIEW_LINKS['DETAILVIEW']}

                                            <li id="{$MODULE_NAME}_detailView_moreAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_LINK->getLabel())}">

                                                <a href={$DETAIL_VIEW_LINK->getUrl()} >{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>

                                            </li>

                                        {/foreach}

                                    </ul>
    
                                </span>


                            {/if}
                            {*<!--End Add by Raghvender Singh on 05052014-->*}
                            {if $DETAILVIEW_LINKS['DETAILVIEWSETTING']|@count gt 0}

                                <span class="btn-group">

									<button class="btn dropdown-toggle" href="#" data-toggle="dropdown"><i class="icon-wrench" alt="{vtranslate('LBL_SETTINGS', $MODULE_NAME)}" title="{vtranslate('LBL_SETTINGS', $MODULE_NAME)}"></i>&nbsp;&nbsp;<i class="caret"></i></button>

									<ul class="listViewSetting dropdown-menu">

                                        {foreach item=DETAILVIEW_SETTING from=$DETAILVIEW_LINKS['DETAILVIEWSETTING']}

                                            <li><a href={$DETAILVIEW_SETTING->getUrl()}>{vtranslate($DETAILVIEW_SETTING->getLabel(), $MODULE_NAME)}</a></li>

                                        {/foreach}

                                    </ul>

								</span>

                            {/if}

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {if !{$NO_PAGINATION}}
        {*<!--Add by Raghvender Singh on 05052014-->*}
            {if $MODULE eq 'Leads'}
            {else}
                <div class="span2 detailViewPagingButton">

					<span class="btn-group pull-right">

						<button class="btn" id="detailViewPreviousRecordButton" {if empty($PREVIOUS_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$PREVIOUS_RECORD_URL}'" {/if}><i class="icon-chevron-left"></i></button>

						<button class="btn" id="detailViewNextRecordButton" {if empty($NEXT_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href='{$NEXT_RECORD_URL}'" {/if}><i class="icon-chevron-right"></i></button>

					</span>

                </div>
            {/if}
        {*<!--End Add by Raghvender Singh on 05052014-->*}
        {/if}

    </div>

    <div class="detailViewInfo row-fluid">

        <div class="{if $NO_PAGINATION} span12 {else} span10 {/if} details">

            <form id="detailView" data-name-fields='{ZEND_JSON::encode($MODULE_MODEL->getNameFields())}'>

                <div class="contents">

                    {/strip}

                    {if $MODULE eq 'Potentials'}

                        <div style="color: red; text-align: center"><tr><th><strong>Total PackageSold Amount :&nbsp;</strong></th><td>{$TOTAL_PACKAGE_SOLD_AMOUNT}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                                <th><strong>Total Payment Amount</strong> :&nbsp;</th><td>{$TOTAL_PAYMENT_AMOUNT}</td>

                            </tr>

                        </div>

                    {/if}

