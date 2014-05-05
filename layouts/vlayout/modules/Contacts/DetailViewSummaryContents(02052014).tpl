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
	{include file='SummaryViewWidgets.tpl'|vtemplate_path:$MODULE_NAME}
    
    {include file="LeftPanel.tpl"|@vtemplate_path:$MODULE_NAME}
    
    {$ACTIVITYSTATUS}
    {if ($ACTIVITYSTATUS == '' && $EVENTSTATUS >= 1)}
    {literal}
    <script language="javascript">
	alert('Please update "Customer Post Call details" For the same click on "Edit" button');
	</script> 
    {/literal}
	{/if}
{/strip}



