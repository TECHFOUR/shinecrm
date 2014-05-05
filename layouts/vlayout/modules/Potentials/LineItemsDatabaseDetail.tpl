
    <thead>
        <tr>
            <th class="blockHeader" colspan="6">
                <img data-id="1" data-mode="show" src="layouts/vlayout/skins/woodspice/images/arrowDown.png"
                     class="cursorPointer alignMiddle blockToggle ">
                &nbsp;&nbsp;Database Detail
            </th>
        </tr>
    </thead>


<tr>
    <th class="fieldLabel medium" style="width: 20%">
        <label class="muted">Product</label>
    </th>

    <th class="fieldLabel medium" style="width: 15%">
        <label class="muted">Geography</label>
    </th>

    <th class="fieldLabel medium" style="width: 15%">
        <label class="muted">IT/Non-IT</label>
    </th>

    <th class="fieldLabel medium" style="width: 25%">
        <label class="muted">Limits</label>
    </th>

    <th class="fieldLabel medium" style="width: 25%">
        <label class="muted">Duration</label>
    </th>

</tr>
<tr class="lineItemRow">
    <th>{$data.product_event}</th>
    <th>{$data.geography_database}</th>
    <th>{$data.database_it}</th>
    <th>{$data.database_limit}</th>
    <th>{$data.database_month}</th>
</tr>

    <tr>
        <th class="fieldLabel medium" style="width: 20%">
            <label class="muted">Upsell Excel</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Upsell Word</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Upsell Emailer</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Upsell Extra Login Id</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
        </th>

    </tr>
    <tr class="lineItemRow">
        <th>{$data.db_upsell_exl}</th>
        <th>{$data.db_upsell_word}</th>
        <th>{$data.db_upsell_email}</th>
        <th>{$data.db_upsell_login}</th>
        <th></th>

    </tr>

    <tr>
        <th class="fieldLabel medium" style="width: 20%">
            <label class="muted">Downsell Excel</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Downsell Word</label>
        </th>

        <th class="fieldLabel medium" style="width: 15%">
            <label class="muted">Downsell Emailer</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">Bottom Price</label>
        </th>

        <th class="fieldLabel medium" style="width: 25%">
            <label class="muted">MRP</label>
        </th>

    </tr>
    <tr class="lineItemRow">
        <th>{$data.db_dwnsell_exl}</th>
        <th>{$data.db_dwnsell_word}</th>
        <th>{$data.db_dwnsell_email}</th>
        <th>{$data.product_bottom_price}</th>
        <th>{$data.product_mrp}</th>
    </tr>
