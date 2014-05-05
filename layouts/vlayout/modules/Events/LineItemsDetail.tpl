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
    <tr>
        <th class="fieldLabel medium" style="width: 20%">
            <label class="muted">Product</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Expected Revenue</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Expected Closure Date</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Lead Stage</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Remarks</label>
        </th>
    </tr>

    {foreach key=row_no item=data from=$RELATED_PRODUCTS}
        <tr class="lineItemRow">
            <th>{$data.product}</th>
            <th>{$data.expected_revenue}</th>
            <th>{$data.expected_closure_date}</th>
            <th>{$data.lead_stage}</th>
            <th>{$data.remarks}</th>
        </tr>
    {/foreach}

</table>