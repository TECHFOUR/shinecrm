{literal}
<script>
    var total_active_inventory_row = 1;
    $(document).ready(function() {
        var idinventoryone = "{/literal}{$COUNTER_INVENTORY_ASSIGN}{literal}";
        // Add button functionality
        $("table.dynatableinventory button.add").click(function() {
            idinventoryone++;
            total_active_inventory_row++;
            var master = $(this).parents("table.dynatableinventory");
            // Get a new row based on the prototypeinventoryone row
            var prot = master.find(".prototypeinventoryone").clone();
            var css_val = "tr1";
            if(idinventoryone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idinventoryone").attr("value", idinventoryone);// row one
            prot.find(".deleteRow").attr("id", idinventoryone);// row one
            prot.find(".product_type_inventory").attr("id", "product_type_inventory___"+idinventoryone);// row one
            prot.find(".tgdatabase_inventory").attr("id", "tgdatabase_inventory___"+idinventoryone);// row one
            prot.find(".active_inventory").attr("id", "active_inventory___"+idinventoryone);// row one
            prot.find(".noofemailer_inventory").attr("id", "noofemailer_inventory__"+idinventoryone);// row one


            master.find("tbody").append(prot);

            // Get a new row based on the prototypeinventoryone row
            var prot_two = master.find(".prototypeinventorytwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttoninventorytwo"+idinventoryone);// row one
            prot_two.find(".idinventorytwo").attr("value", "__"+idinventoryone); // row two
            prot_two.find(".bottom_price_inventory").attr("id", "bottom_price_inventory"+idinventoryone);// row one
            prot_two.find(".mrp_inventory").attr("id", "mrp_inventory"+idinventoryone);// row one
            master.find("tbody").append(prot_two);

            // Get a new row based on the prototypeinventoryone row
            var prot_three = master.find(".prototypeinventorythree").clone();
            prot_three.attr("class", css_val)
            prot_three.find(".deleteRow").attr("id", "buttoninventorythree"+idinventoryone);// row one
            prot_three.find(".idinventorythree").attr("value", "__"+idinventoryone); // row two
            prot_three.find(".discount_inventory").attr("id", "discount_inventory"+idinventoryone);// row one
            prot_three.find(".discount_amount_inventory").attr("id", "discount_amount_inventory"+idinventoryone);// row one
            prot_three.find(".offered_amount_inventory").attr("id", "offered_amount_inventory"+idinventoryone);// row one
            prot_three.find(".service_tax_amount_inventory").attr("id", "service_tax_amount_inventory"+idinventoryone);// row one
            prot_three.find(".total_amount_inventory").attr("id", "total_amount_inventory"+idinventoryone);// row one
            master.find("tbody").append(prot_three);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttoninventorygapfour"+idinventoryone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatableinventory .deleteRow").live("click", function() {
            $("#buttoninventorytwo"+this.id).click();
            $("#buttoninventorythree"+this.id).click();
            $("#buttoninventorygapfour"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_inventory_row--;
        });
    });

</script>

    <style>
        .dynatableinventory {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableinventory th,
        .dynatableinventory td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableinventory .prototypeinventoryone {
            display:none;
        }

        .dynatableinventory .prototypeinventorytwo {
            display:none;
        }

        .dynatableinventory .prototypeinventorythree {
            display:none;
        }

        .prototype_gap{
            display:none;
        }

        .tr1{
            background-color:#f5f5f5;
        }

        .tr2{
            background-color:#def;
        }

        .tr1:hover{
            background-color: #feffc5;
        }

        .tr2:hover{
            background-color: #ffc3a3;
        }

        .trgap{
            background-color: rgba(149, 255, 53, 0.00);
        }

    </style>
{/literal}

<table class="dynatableinventory" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add inventory</button></th>
    </tr>
    </thead>
    <tbody>

    {foreach item=data key=counter from=$INVENTORY_LIST}
        <tr {if $counter eq 0}class="prototypeinventoryone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_inventory[]" {if $data.exp_product_type ne ''} value="{$data.inventoryid}" {else} value="0"   {/if} />
                <i {if $data.exp_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_inventory[]" placeholder="Product" title="Product"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_INVENTORY_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_inventory" id="product_type_inventory___{$counter}" name="product_type_inventory[]" placeholder="Product Type" title="Product Type" onchange="getNextTypeValues(this.id, this.value, 'inventory', 'tg_database', 'tgdatabase_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    {foreach item=inventory key=PICKLIST_NAME from=$INVENTORY_PRODUCTS}
                        <option value="{$inventory}" {if $inventory eq $data.exp_product_type} selected="selected" {/if} >{$inventory}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth tgdatabase_inventory" name="tgdatabase_inventory[]" id="tgdatabase_inventory___{$counter}" placeholder="TG Database" title="TG Database" onchange="getNextTypeValues(this.id, this.value, 'inventory', 'inventory_active', 'active_', 2);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a TG Database</option>
                    {if $data.exp_tg_db ne ''}
                        <option value="{$data.exp_tg_db}" selected="selected" >{$data.exp_tg_db}</option>
                    {/if}
                </select>
            </td>
            <td>
                <select class="textwidth active_inventory" name="active_inventory[]" id="active_inventory___{$counter}" placeholder="Active" title="Active" onchange="getMRPBottomPrice(this.value, this.id, 'inventory');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select an Active</option>
                    {if $data.exp_active ne ''}
                        <option value="{$data.exp_active}__{$data.inventoryid}" selected="selected" >{$data.exp_active}</option>
                    {/if}
                </select>
            </td>

            <td>
                <input class="textwidth noofemailer_inventory" maxlength="5" id="noofemailer_inventory__{$counter}" type="text" name="noofemailer_inventory[]" value="{$data.noofemailer}" onkeyup="getNewMRPBottomPrice(this.id, this.value, 'inventory');"  placeholder="No. of Emailers" title="No. of Emailers" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeinventorytwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttoninventorytwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input class="textwidth bottom_price_inventory" id="bottom_price_inventory{$counter}" readonly type="text" name="bottom_price_inventory[]" value="{$data.bottom_price}"  class="idinventorytwo" placeholder="Bottom Price" title="Bottom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_inventory" id="mrp_inventory{$counter}" readonly type="text" name="mrp_inventory[]" value="{$data.mrp}"  placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeinventorythree" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttoninventorythree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_inventory" readonly id="discount_inventory{$counter}" type="text" name="discount_inventory[]" value="{$data.ps_discount}"  class="idinventorythree" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_inventory" readonly id="discount_amount_inventory{$counter}" type="text" name="discount_amount_inventory[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_inventory" maxlength="15"  id="offered_amount_inventory{$counter}" type="text" name="offered_amount_inventory[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'inventory')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_inventory" readonly id="service_tax_amount_inventory{$counter}" type="text" name="service_tax_amount_inventory[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_inventory" readonly id="total_amount_inventory{$counter}" type="text" name="total_amount_inventory[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttoninventorygapfour{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
