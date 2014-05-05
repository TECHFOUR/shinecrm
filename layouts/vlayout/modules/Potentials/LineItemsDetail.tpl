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

{if count($RELATED_PACKAGESOLD) gt 0}
    {include file="LineItemsPackageSold.tpl"|@vtemplate_path:$MODULE_NAME}
{/if}

{if count($RELATED_PAYMENTS) gt 0}
{foreach key=row_no item=data from=$RELATED_PAYMENTS}
    {include file="LineItemsPaymentDetail.tpl"|@vtemplate_path:$MODULE_NAME}
{/foreach}
{/if}



