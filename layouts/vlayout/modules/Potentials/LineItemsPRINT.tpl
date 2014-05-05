{literal}
<script>
    var total_active_print_row = 1;
    $(document).ready(function() {
        var idprintone = "{/literal}{$COUNTER_PRINT_ASSIGN}{literal}";
        total_active_print_row++;
        // Add button functionality
        $("table.dynatableprint button.add").click(function() {
            idprintone++;
            var master = $(this).parents("table.dynatableprint");
            // Get a new row based on the prototypeprintone row
            var prot = master.find(".prototypeprintone").clone();
            var css_val = "tr1";
            if(idprintone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idprintone").attr("value", idprintone);// row one
            prot.find(".deleteRow").attr("id", idprintone);// row one
            prot.find(".product_type_print").attr("id", "product_type_print___"+idprintone);// row one
            prot.find(".size_sqcm_print").attr("id", "size_sqcm_print__"+idprintone);// row one
            prot.find(".mrp_print").attr("id", "mrp_print"+idprintone);// row one
            prot.find(".bottom_price_print").attr("id", "bottom_price_print"+idprintone);// row one
            master.find("tbody").append(prot);

            // Get a new row based on the prototypeprintone row
            var prot_two = master.find(".prototypeprinttwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonprinttwo"+idprintone);// row one
            prot_two.find(".idprinttwo").attr("value", "__"+idprintone); // row two
            prot_two.find(".discount_print").attr("id", "discount_print"+idprintone);// row one
            prot_two.find(".discount_amount_print").attr("id", "discount_amount_print"+idprintone);// row one
            prot_two.find(".offered_amount_print").attr("id", "offered_amount_print"+idprintone);// row one
            prot_two.find(".service_tax_amount_print").attr("id", "service_tax_amount_print"+idprintone);// row one
            prot_two.find(".total_amount_print").attr("id", "total_amount_print"+idprintone);// row one
            master.find("tbody").append(prot_two);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonprintgapthree"+idprintone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatableprint .deleteRow").live("click", function() {
            $("#buttonprinttwo"+this.id).click();
            $("#buttonprintgapthree"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_print_row--;
        });
    });

</script>

    <style>
        .dynatableprint {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableprint th,
        .dynatableprint td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableprint .prototypeprintone {
            display:none;
        }

        .dynatableprint .prototypeprinttwo {
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

<table class="dynatableprint" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Print</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$PRINT_LIST}
        <tr {if $counter eq 0}class="prototypeprintone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_print[]" {if $data.product ne ''} value="{$data.printid}" {else} value="0"   {/if} />
                <i {if $data.print_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_print[]" placeholder="Product" title="Product" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_PRINT_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_print" id="product_type_print___{$counter}" name="product_type_print[]" placeholder="Product Type" title="Product Type" onchange="getMRPBottomPrice(this.value, this.id, 'print');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    <p>{$data.edu_product_type}</p>
                    {foreach item=print key=PICKLIST_NAME from=$PRINT_PRODUCTS}
                        {assign var="print_display" value="__"|explode:$print}
                        <option value="{$print}" {if $print_display[0] eq $data.print_product_type} selected="selected" {/if} >{$print_display[0]}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input class="textwidth size_sqcm_print" maxlength="5"  type="text" id="size_sqcm_print__{$counter}" name="size_sqcm_print[]" value="{$data.print_size}" onkeyup="getNewMRPBottomPrice(this.id, this.value, 'print');" placeholder="Size(Sq.Cm)" title="Size(Sq.Cm)" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth bottom_price_print" readonly type="text" id="bottom_price_print{$counter}" name="bottom_price_print[]" value="{$data.bottom_price}" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_print" readonly type="text" id="mrp_print{$counter}" name="mrp_print[]" value="{$data.mrp}" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeprinttwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonprinttwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_print" readonly id="discount_print{$counter}" type="text" name="discount_print[]" value="{$data.ps_discount}"  class="idprinttwo" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_print" readonly id="discount_amount_print{$counter}" type="text" name="discount_amount_print[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_print" maxlength="15"  id="offered_amount_print{$counter}" type="text" name="offered_amount_print[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'print')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_print" readonly id="service_tax_amount_print{$counter}" type="text" name="service_tax_amount_print[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_print" readonly id="total_amount_print{$counter}" type="text" name="total_amount_print[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonprintgapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
