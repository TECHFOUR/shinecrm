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
    {assign var="announcement" value=$ANNOUNCEMENT->get('announcement')}
    {assign var='count' value=0}
    {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
    <div class="navbar commonActionsContainer noprint">
        <div class="actionsContainer row-fluid">
            <div class="span2">
                <img src="layouts/vlayout/skins/images/htmedia_logo.jpg">&nbsp;</span>
            </div>
            <div class="span10 marginLeftZero">
                <div class="row-fluid">
                    <div class="nav quickActions btn-toolbar span2 pull-right">
                        <div class="pull-right commonActionsButtonContainer">
                            {if !empty($announcement)}
                                <div class="btn-group cursorPointer">
                                    <img class='alignMiddle' src="{vimage_path('btnAnnounceOff.png')}" alt="{vtranslate('LBL_ANNOUNCEMENT',$MODULE)}" title="{vtranslate('LBL_ANNOUNCEMENT',$MODULE)}" id="announcementBtn" />
                                </div>&nbsp;
                            {/if}

                            <div class="btn-group cursorPointer" id="guiderHandler">
                                {if !$MAIN_PRODUCT_WHITELABEL}
                                    <img src="{vimage_path('circle_question_mark.png')}" class="alignMiddle" alt="?" title="{vtranslate('LBL_GUIDER',$MODULE)}" style="display:none;"/>
                                {/if}
                            </div>&nbsp;
                            {*<!--Change by Raghvender Singh on 05052014-->*}
                            {if $USER_MODEL->isAdminUser()}
                                <div class="btn-group cursorPointer">
                                    <img id="menubar_quickCreate" src="{vimage_path('btnAdd.png')}" class="alignMiddle" alt="{vtranslate('LBL_QUICK_CREATE',$MODULE)}" title="{vtranslate('LBL_QUICK_CREATE',$MODULE)}" data-toggle="dropdown" />
                                    <ul class="dropdown-menu dropdownStyles commonActionsButtonDropDown">
                                        <li class="title"><strong>{vtranslate('Quick Create',$MODULE)}</strong></li><hr/>
                                        <li id="quickCreateModules">
                                            <div class="row-fluid">
                                                <div class="span12">
                                                    {foreach key=moduleName item=moduleModel from=$MENUS}
                                                        {if $moduleModel->isPermitted('EditView')}
                                                            {assign var='quickCreateModule' value=$moduleModel->isQuickCreateSupported()}
                                                            {assign var='singularLabel' value=$moduleModel->getSingularLabelKey()}
                                                            {if $singularLabel == 'SINGLE_Calendar'}
                                                                {assign var='singularLabel' value='LBL_EVENT_OR_TASK'}
                                                            {/if}
                                                            {if $quickCreateModule == '1'}
                                                                {if $count % 3 == 0}
                                                                    <div class="row-fluid">
                                                                {/if}
                                                                <div class="span4">
                                                                    <a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
                                                                       data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">{vtranslate($singularLabel,$moduleName)}</a>
                                                                </div>
                                                                {if $count % 3 == 2}
                                                                    </div>
                                                                {/if}
                                                                {assign var='count' value=$count+1}
                                                            {/if}
                                                        {/if}
                                                    {/foreach}
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                &nbsp;

                            {else}
                                &nbsp;
                            {/if}
                            {*<!--End Change by Raghvender Singh on 05052014-->*}



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/strip}
