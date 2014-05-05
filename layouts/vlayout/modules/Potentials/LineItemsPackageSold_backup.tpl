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
<table class="table table-bordered blockContainer lineItemTable" id="lineItemTab" >

    {foreach key=row_no item=data from=$RELATED_PACKAGESOLD}

        {if $data.package_master eq 'EVENTS'}
            {include file="LineItemsEventsDetail.tpl"|@vtemplate_path:$MODULE_NAME}
        {/if}

        {if $data.package_master eq 'EDUCATION'}
            {include file="LineItemsEducationDetail.tpl"|@vtemplate_path:$MODULE_NAME}
        {/if}

        <tr>
            <th class="fieldLabel medium" style="width: 20%">
                <label class="muted">Discount %</label>
            </th>

            <th class="fieldLabel medium" style="width: 15%">
                <label class="muted">Discount Amount</label>
            </th>

            <th class="fieldLabel medium" style="width: 15%">
                <label class="muted">Offered Amount</label>
            </th>

            <th class="fieldLabel medium" style="width: 25%">
                <label class="muted">Service Tax Amount</label>
            </th>

            <th class="fieldLabel medium" style="width: 25%">
                <label class="muted">Total Amount</label>
            </th>

            <th class="fieldLabel medium" style="width: 25%">
                <label class="muted"></label>
            </th>
        </tr>

        <tr class="lineItemRow">
            <th>{$data.ps_discount}</th>
            <th>{$data.ps_discount_amount}</th>
            <th>{$data.ps_offered_amount}</th>
            <th>{$data.ps_service_tax_amount}</th>
            <th>{$data.ps_total_amount}</th>
            <th></th>
        </tr>
    {/foreach}

</table>