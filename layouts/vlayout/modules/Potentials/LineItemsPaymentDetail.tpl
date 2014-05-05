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
    <thead>
    <tr>
        <th class="blockHeader" colspan="6">
            <img data-id="1" data-mode="show" src="layouts/vlayout/skins/woodspice/images/arrowDown.png"
                 class="cursorPointer alignMiddle blockToggle ">
            &nbsp;&nbsp;Payment Detail
        </th>
    </tr>
    </thead>
    <tr>
        <th class="fieldLabel medium" style="width: 20%">
            <label class="muted">Payment Mode</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Cheque Date</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Cheque Number</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">RO Available</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Drawee bank</label>
        </th>

       
    </tr>

   
        <tr class="lineItemRow">
            <th>{$data.payment_mode}</th>
            <th>{$data.checkdate}</th>
            <th>{$data.checkno}</th>
            <th>{$data.ro_available}</th>
            <th>{$data.bank_name}</th>
           
        </tr>
  


<tr>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Online Mode</label>
        </th>
		
		<th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Slip No.</label>
        </th>
		
		<th class="fieldLabel medium" style="width: 25%">
            <label class="muted">TAN No.</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">CTS/Non-CTS</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Amount</label>
        </th>
		
		</tr>
		
		
		
        <tr class="lineItemRow">
            <th>{$data.onlinemode}</th>
            <th>{$data.slip_number}</th>
            <th>{$data.tan_no}</th>
            <th>{$data.cts}</th>
            <th>{$data.amount}</th>
        </tr>
    

</table>