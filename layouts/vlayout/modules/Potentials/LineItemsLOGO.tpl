{literal}
<script>
    var total_active_logo_row = 1;
    $(document).ready(function() {
        var idlogoone = "{/literal}{$COUNTER_LOGO_ASSIGN}{literal}";
        total_active_logo_row++;
        // Add button functionality
        $("table.dynatablelogo button.add").click(function() {
            idlogoone++;
            var master = $(this).parents("table.dynatablelogo");
            // Get a new row based on the prototypelogoone row
            var prot = master.find(".prototypelogoone").clone();
            var css_val = "tr1";
            if(idlogoone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idlogoone").attr("value", idlogoone);// row one
            prot.find(".deleteRow").attr("id", idlogoone);// row one
            prot.find(".product_type_logo").attr("id", "product_type_logo___"+idlogoone);// row one
            prot.find(".duration_logo").attr("id", "duration_logo___"+idlogoone);// row one
            prot.find(".mrp_logo").attr("id", "mrp_logo"+idlogoone);// row one
            prot.find(".bottom_price_logo").attr("id", "bottom_price_logo"+idlogoone);// row one
            master.find("tbody").append(prot);

            // Get a new row based on the prototypelogoone row
            var prot_two = master.find(".prototypelogotwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonlogotwo"+idlogoone);// row one
            prot_two.find(".idlogotwo").attr("value", "__"+idlogoone); // row two
            prot_two.find(".discount_logo").attr("id", "discount_logo"+idlogoone);// row one
            prot_two.find(".discount_amount_logo").attr("id", "discount_amount_logo"+idlogoone);// row one
            prot_two.find(".offered_amount_logo").attr("id", "offered_amount_logo"+idlogoone);// row one
            prot_two.find(".service_tax_amount_logo").attr("id", "service_tax_amount_logo"+idlogoone);// row one
            prot_two.find(".total_amount_logo").attr("id", "total_amount_logo"+idlogoone);// row one
            master.find("tbody").append(prot_two);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonlogogapthree"+idlogoone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatablelogo .deleteRow").live("click", function() {
            $("#buttonlogotwo"+this.id).click();
            $("#buttonlogogapthree"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_logo_row--;
        });
    });

</script>

    <style>
        .dynatablelogo {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatablelogo th,
        .dynatablelogo td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatablelogo .prototypelogoone {
            display:none;
        }

        .dynatablelogo .prototypelogotwo {
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

<table class="dynatablelogo" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Logo</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$LOGO_LIST}
        <tr {if $counter eq 0}class="prototypelogoone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_logo[]" {if $data.product ne ''} value="{$data.logoid}" {else} value="0"   {/if} />
                <i {if $data.logo_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_logo[]" placeholder="Product" title="Product" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_LOGO_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_logo" id="product_type_logo___{$counter}" name="product_type_logo[]" placeholder="Product Type" title="Product Type" onchange="getNextTypeValues(this.id, this.value, 'logo', 'logo_month', 'duration_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    {foreach item=logo key=PICKLIST_NAME from=$LOGO_PRODUCTS}
                        <option value="{$logo}" {if $logo eq $data.logo_product_type} selected="selected" {/if} >{$logo}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth duration_logo" name="duration_logo[]" id="duration_logo___{$counter}" placeholder="Duration" title="Duration" onchange="getMRPBottomPrice(this.value, this.id, 'logo');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Duration</option>
                    {if $data.month ne ''}
                        <option value="{$data.month}__{$data.logoid}" selected="selected" >{$data.month}</option>
                    {/if}
                </select>
            </td>
            <td>
                <input class="textwidth bottom_price_logo" readonly type="text" id="bottom_price_logo{$counter}" name="bottom_price_logo[]" value="{$data.bottom_price}" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_logo" readonly type="text" id="mrp_logo{$counter}" name="mrp_logo[]" value="{$data.mrp}" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypelogotwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonlogotwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_logo" readonly id="discount_logo{$counter}" type="text" name="discount_logo[]" value="{$data.ps_discount}"  class="idlogotwo" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_logo" readonly id="discount_amount_logo{$counter}" type="text" name="discount_amount_logo[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_logo" maxlength="15"  id="offered_amount_logo{$counter}" type="text" name="offered_amount_logo[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'logo')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_logo" readonly id="service_tax_amount_logo{$counter}" type="text" name="service_tax_amount_logo[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_logo" readonly id="total_amount_logo{$counter}" type="text" name="total_amount_logo[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonlogogapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
