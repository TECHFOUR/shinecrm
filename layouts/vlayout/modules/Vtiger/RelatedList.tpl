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
    <div class="relatedContainer">
        <input type="hidden" name="currentPageNum" value="{$PAGING->getCurrentPage()}" />
        <input type="hidden" name="relatedModuleName" class="relatedModuleName" value="{$RELATED_MODULE->get('name')}" />
        <input type="hidden" value="{$ORDER_BY}" id="orderBy">
        <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        <input type="hidden" value="{$RELATED_ENTIRES_COUNT}" id="noOfEntries">
        <input type='hidden' value="{$PAGING->getPageLimit()}" id='pageLimit'>
        <input type='hidden' value="{$TOTAL_ENTRIES}" id='totalCount'>
        <div class="relatedHeader ">
            <div class="btn-toolbar row-fluid">
                <div class="span8">
                
              
						{if $OLDPATH == $NEWPATH}
                         {*<!--  <div class="btn-group">
                            <div class="btn-group">                            
                          <a  class="btn addButton" name="addButton" href="index.php?module=Calendar&view=Edit&mode=Events&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true&picklistDependency=[]&defaultCallDuration=10&defaultOtherEventDuration=5&activitytype=&subject=&eventstatus=&date_start=&time_start=&due_date=&time_end=&assigned_user_id=&cf_917=&cf_919=&cf_921=&cf_923=&cf_925=&contact_id={$LINK_RECORD}&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true">{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>Add Activity</strong></a>
              				 </div>
    					</div>-->*}
					{else if $OLDPATH == $NEWPATH1}
                             {*<!--Change by Raghvender Singh on 01052014-->*}
                        {else if $OLDPATH == $NEWPATH2}
                         {*<!--Add by Raghvender Singh on 02052014-->*}
                        {else if $OLDPATH == $NEWPATH4}
                         <div class="btn-group">
                            <div class="btn-group">                            
                          <a  class="btn addButton" name="addButton" href="index.php?module=Documents&view=Edit&picklistDependency=[]&notes_title=&assigned_user_id=1&folderid=1&sourceModule=Potentials&sourceRecord={$LINK_RECORD}&relationOperation=true">{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>Add Document</strong></a>
              				 </div>
    					</div>
                          
                        {*<!--End Add by Raghvender Singh on 02052014-->*}
                        {else if $OLDPATH == $NEWPATH5}
                        {else if $OLDPATH == $NEWPATH6}
                       {*<!--Add by Raghvender Singh on 03052014-->*}
                         {else if $OLDPATH == $NEWPATH7}
                         {foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
                            <div class="btn-group">
                                {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
                                <button type="button" class="btn addButton
                                {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
                            {if $IS_SELECT_BUTTON eq true} data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
                            {if ($RELATED_LINK->isPageLoadLink())}
                            {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
                            data-url="{$RELATED_LINK->getUrl()}"
                        {/if}
                        {*<!--Change by Raghevnder Singh on 03052014-->*}
                            {if $IS_SELECT_BUTTON neq true}name="addButton"{/if}>{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>Initiate Approval</strong></button>{*<!--End Change by Raghevnder Singh on 03052014-->*}
                   		 	</div>
						{/foreach}
                       {*<!--End Add by Raghvender Singh on 03052014-->*}
                        {else if $OLDPATH == $NEWPATH3}
                           {foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
                            <div class="btn-group">
                                {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
                                <a href="index.php?module=Products&view=Edit&picklistDependency=[]&productname=&mobile=&email=&city=&assigned_user_id=1&sourceModule=Contacts&sourceRecord={$LINK_RECORD}&relationOperation=true">
                                <button type="button" class="btn addButton
                                {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
                            {if $IS_SELECT_BUTTON eq true} data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
                            {if ($RELATED_LINK->isPageLoadLink())}
                            {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
                            data-url="{$RELATED_LINK->getUrl()}"
                        {/if}
                            {if $IS_SELECT_BUTTON neq true}{/if}>{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white">			</i>{/if}&nbsp;<strong>{$RELATED_LINK->getLabel()}</strong></button>
                            </a>
                   		 	</div>
						{/foreach}
                        
					{else}
 						{foreach item=RELATED_LINK from=$RELATED_LIST_LINKS['LISTVIEWBASIC']}
                            <div class="btn-group">
                                {assign var=IS_SELECT_BUTTON value={$RELATED_LINK->get('_selectRelation')}}
                                <button type="button" class="btn addButton
                                {if $IS_SELECT_BUTTON eq true} selectRelation {/if} "
                            {if $IS_SELECT_BUTTON eq true} data-moduleName={$RELATED_LINK->get('_module')->get('name')} {/if}
                            {if ($RELATED_LINK->isPageLoadLink())}
                            {if $RELATION_FIELD} data-name="{$RELATION_FIELD->getName()}" {/if}
                            data-url="{$RELATED_LINK->getUrl()}"
                        {/if}
                            {if $IS_SELECT_BUTTON neq true}name="addButton"{/if}>{if $IS_SELECT_BUTTON eq false}<i class="icon-plus icon-white"></i>{/if}&nbsp;<strong>{$RELATED_LINK->getLabel()}</strong></button>
                   		 	</div>
						{/foreach}
				{/if}



&nbsp;
</div>
<div class="span4">
    <span class="row-fluid">
        <span class="span7 pushDown">
            <span class="pull-right pageNumbers alignTop" data-placement="bottom" data-original-title="" style="margin-top: -5px">
            {if !empty($RELATED_RECORDS)} {$PAGING->getRecordStartRange()} {vtranslate('LBL_to', $RELATED_MODULE->get('name'))} {$PAGING->getRecordEndRange()}{/if}
        </span>
    </span>
    <span class="span5 pull-right">
        <span class="btn-group pull-right">
            <button class="btn" id="relatedListPreviousPageButton" {if !$PAGING->isPrevPageExists()} disabled {/if} type="button"><span class="icon-chevron-left"></span></button>
            <button class="btn dropdown-toggle" type="button" id="relatedListPageJump" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
                <i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$moduleName)}"></i>
            </button>
            <ul class="listViewBasicAction dropdown-menu" id="relatedListPageJumpDropDown">
                <li>
                    <span class="row-fluid">
                        <span class="span3"><span class="pull-right">{vtranslate('LBL_PAGE',$moduleName)}</span></span>
                        <span class="span4">
                            <input type="text" id="pageToJump" class="listViewPagingInput" value="{$PAGING->getCurrentPage()}"/>
                        </span>
                        <span class="span2 textAlignCenter">
                            {vtranslate('LBL_OF',$moduleName)}
                        </span>
                        <span class="span2" id="totalPageCount">{$PAGE_COUNT}</span>
                    </span>
                </li>
            </ul>
            <button class="btn" id="relatedListNextPageButton" {if (!$PAGING->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if} type="button"><span class="icon-chevron-right"></span></button>
        </span>
    </span>
</span>
</div>
</div>
</div>
<div class="contents-topscroll">
    <div class="topscroll-div">
        &nbsp;
    </div>
</div>
<div class="relatedContents contents-bottomscroll">
    <div class="bottomscroll-div">
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <table class="table table-bordered listViewEntriesTable">
            <thead>
                <tr class="listViewHeaders">
                    {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
							<th {if $HEADER_FIELD@last} colspan="2" {/if} nowrap class="{$WIDTHTYPE}">
                            {if $HEADER_FIELD->get('column') eq 'access_count' or $HEADER_FIELD->get('column') eq 'idlists' }
                                <a href="javascript:void(0);" class="noSorting">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}</a>
                            {elseif $HEADER_FIELD->get('column') eq 'time_start'}
                            {else}
                                <a href="javascript:void(0);" class="relatedListHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-fieldname="{$HEADER_FIELD->get('column')}">{vtranslate($HEADER_FIELD->get('label'), $RELATED_MODULE->get('name'))}
                                    &nbsp;&nbsp;{if $COLUMN_NAME eq $HEADER_FIELD->get('column')}<img class="{$SORT_IMAGE} icon-white">{/if}
                                </a>
                            {/if}
                        </th>
                    {/foreach}
                </tr>
            </thead>
           {*<!--Change by Raghvender Singh on 03052014-->*}  
		{if $MODULE eq 'Contacts'}
        {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
          
                  <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()|cat:'&mode=showDetailViewByMode&requestMode=full'}'>
                
                  {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$HEADER_FIELD->get('name')}
							<td class="{$WIDTHTYPE}" data-field-type="{$HEADER_FIELD->getFieldDataType()}" nowrap>
                            {if $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                <a href="{$RELATED_RECORD->getDetailViewUrl()|cat:'&mode=showDetailViewByMode&requestMode=full'}">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                            {elseif $RELATED_HEADERNAME eq 'access_count'}
                                {$RELATED_RECORD->getAccessCountValue($PARENT_RECORD->getId())}
                            {elseif $RELATED_HEADERNAME eq 'time_start'}
                            {else}
                                {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                            {/if}
          {*<!--Change by Raghvender Singh on 03052014-->*}
                            {if $HEADER_FIELD@last}
								</td><td nowrap class="{$WIDTHTYPE}">
                                <div class="pull-right actions">
                                    <span class="actionImages">
                                        <a href="{$RELATED_RECORD->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                        {*<!--Change by Raghveder Singh on 03052014
                                        {if $IS_EDITABLE}
                                            <a href='{$RELATED_RECORD->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>
                                        {/if}
                                         End Change by Raghveder Singh on 03052014-->*}
                                        {if $IS_DELETABLE}
                                            <a class="relationDelete"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                        {/if}
                                    </span>
                                </div>
                            </td>
                        {/if}
                        </td>
                    {/foreach}
                </tr>
            {/foreach}
        {else}
            {foreach item=RELATED_RECORD from=$RELATED_RECORDS}
            
                <tr class="listViewEntries" data-id='{$RELATED_RECORD->getId()}' data-recordUrl='{$RELATED_RECORD->getDetailViewUrl()}'>
                
                  {foreach item=HEADER_FIELD from=$RELATED_HEADERS}
                        {assign var=RELATED_HEADERNAME value=$HEADER_FIELD->get('name')}
							<td class="{$WIDTHTYPE}" data-field-type="{$HEADER_FIELD->getFieldDataType()}" nowrap>
                            {if $HEADER_FIELD->isNameField() eq true or $HEADER_FIELD->get('uitype') eq '4'}
                                <a href="{$RELATED_RECORD->getDetailViewUrl()}">{$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}</a>
                            {elseif $RELATED_HEADERNAME eq 'access_count'}
                                {$RELATED_RECORD->getAccessCountValue($PARENT_RECORD->getId())}
                            {elseif $RELATED_HEADERNAME eq 'time_start'}
                            {else}
                                {$RELATED_RECORD->getDisplayValue($RELATED_HEADERNAME)}
                            {/if}
                             {*<!--Change by Raghvender Singh on 03052014-->*}
                            {if $HEADER_FIELD@last}
								</td><td nowrap class="{$WIDTHTYPE}">
                                <div class="pull-right actions">
                                    <span class="actionImages">
                                        <a href="{$RELATED_RECORD->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                        {*<!--Change by Raghveder Singh on 03052014
                                        {if $IS_EDITABLE}
                                            <a href='{$RELATED_RECORD->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="icon-pencil alignMiddle"></i></a>
                                        {/if}
                                         End Change by Raghveder Singh on 03052014-->*}
                                        {if $IS_DELETABLE}
                                            <a class="relationDelete"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                        {/if}
                                    </span>
                                </div>
                            </td>
                        {/if}
                        </td>
                    {/foreach}
                </tr>
            {/foreach}
          {/if}  
          {*<!--Change by Raghvender Singh on 03052014-->*}
        </table>
    </div>
</div>
</div>
{/strip}
