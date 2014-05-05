    <thead>
    <tr>
        <th class="blockHeader" colspan="6">
            <img data-id="1" data-mode="show" src="layouts/vlayout/skins/woodspice/images/arrowDown.png"
                 class="cursorPointer alignMiddle blockToggle ">
            &nbsp;&nbsp;Inventory Detail
        </th>
    </tr>
    </thead>

<tr>
    <th class="fieldLabel medium" style="width: 20%">
        <label class="muted">Product</label>
    </th>

    <th class="fieldLabel medium" style="width: 15%">
        <label class="muted">Product Type</label>
    </th>

    <th class="fieldLabel medium" style="width: 15%">
        <label class="muted">TG Database</label>
    </th>

    <th class="fieldLabel medium" style="width: 25%">
        <label class="muted">Active</label>
    </th>

    <th class="fieldLabel medium" style="width: 25%">
        <label class="muted">NO. of Emailers</label>
    </th>

</tr>
<tr class="lineItemRow">
    <th>{$data.product_event}</th>
    <th>{$data.exp_product_type}</th>
    <th>{$data.exp_tg_db}</th>
    <th>{$data.exp_active}</th>
    <th>{$data.noofemailer}</th>
</tr>

    <tr>
        <th class="fieldLabel medium" style="width: 20%">
            <label class="muted"></label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted"></label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted"></label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Bottom Price</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">MRP</label>
        </th>

    </tr>
    <tr class="lineItemRow">
        <th></th>
        <th></th>
        <th></th>
        <th>{$data.product_bottom_price}</th>
        <th>{$data.product_mrp}</th>
    </tr>
