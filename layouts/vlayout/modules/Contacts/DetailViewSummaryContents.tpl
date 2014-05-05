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
    {*<!--Change by Raghvender Singh on 02052014-->*}
   
    {if $post_call_update eq 'no' && $EVENTSTATUS_COUNT > 0}
    {literal}
    <script>
		alert('Please update "Customer Post Call details" For the same click on "Edit" button');
	</script>
    {/literal}
    {/if}
   {*<!--End Change by Raghvender Singh on 02052014-->*}
{/strip}



