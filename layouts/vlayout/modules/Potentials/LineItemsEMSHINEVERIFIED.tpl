{literal}
<script>
    var total_active_emshineverified_row = 1;
    $(document).ready(function() {
        var idemshineverifiedone = "{/literal}{$COUNTER_EMSHINEVERIFIED_ASSIGN}{literal}";
        // Add button functionality
        $("table.dynatableemshineverified button.add").click(function() {
            idemshineverifiedone++;
            total_active_emshineverified_row++;
            var master = $(this).parents("table.dynatableemshineverified");
            // Get a new row based on the prototypeemshineverifiedone row
            var prot = master.find(".prototypeemshineverifiedone").clone();
            var css_val = "tr1";
            if(idemshineverifiedone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idemshineverifiedone").attr("value", idemshineverifiedone);// row one
            prot.find(".deleteRow").attr("id", idemshineverifiedone);// row one
            prot.find(".product_type_emshineverified").attr("id", "product_type_emshineverified___"+idemshineverifiedone);// row one
            prot.find(".walkin_emshineverified").attr("id", "walkin_emshineverified__"+idemshineverifiedone);// row one
            prot.find(".rdetail_emshineverified").attr("id", "rdetail_emshineverified___"+idemshineverifiedone);// row one

            master.find("tbody").append(prot);

            // Get a new row based on the prototypeemshineverifiedone row
            var prot_two = master.find(".prototypeemshineverifiedtwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonemshineverifiedtwo"+idemshineverifiedone);// row one
            prot_two.find(".idemshineverifiedtwo").attr("value", "__"+idemshineverifiedone); // row two
            prot_two.find(".bottom_price_emshineverified").attr("id", "bottom_price_emshineverified"+idemshineverifiedone);// row one
            prot_two.find(".mrp_emshineverified").attr("id", "mrp_emshineverified"+idemshineverifiedone);// row one
            master.find("tbody").append(prot_two);

            // Get a new row based on the prototypeemshineverifiedone row
            var prot_three = master.find(".prototypeemshineverifiedthree").clone();
            prot_three.attr("class", css_val)
            prot_three.find(".deleteRow").attr("id", "buttonemshineverifiedthree"+idemshineverifiedone);// row one
            prot_three.find(".idemshineverifiedthree").attr("value", "__"+idemshineverifiedone); // row two
            prot_three.find(".discount_emshineverified").attr("id", "discount_emshineverified"+idemshineverifiedone);// row one
            prot_three.find(".discount_amount_emshineverified").attr("id", "discount_amount_emshineverified"+idemshineverifiedone);// row one
            prot_three.find(".offered_amount_emshineverified").attr("id", "offered_amount_emshineverified"+idemshineverifiedone);// row one
            prot_three.find(".service_tax_amount_emshineverified").attr("id", "service_tax_amount_emshineverified"+idemshineverifiedone);// row one
            prot_three.find(".total_amount_emshineverified").attr("id", "total_amount_emshineverified"+idemshineverifiedone);// row one
            master.find("tbody").append(prot_three);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonemshineverifiedgapfour"+idemshineverifiedone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatableemshineverified .deleteRow").live("click", function() {
            $("#buttonemshineverifiedtwo"+this.id).click();
            $("#buttonemshineverifiedthree"+this.id).click();
            $("#buttonemshineverifiedgapfour"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_emshineverified_row--;
        });
    });

</script>

    <style>
        .dynatableemshineverified {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableemshineverified th,
        .dynatableemshineverified td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableemshineverified .prototypeemshineverifiedone {
            display:none;
        }

        .dynatableemshineverified .prototypeemshineverifiedtwo {
            display:none;
        }

        .dynatableemshineverified .prototypeemshineverifiedthree {
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

<table class="dynatableemshineverified" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add EM & Shine Verified</button></th>
    </tr>
    </thead>
    <tbody>

    {foreach item=data key=counter from=$EMSHINEVERIFIED_LIST}
        <tr {if $counter eq 0}class="prototypeemshineverifiedone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_emshineverified[]" {if $data.elite_mproduct_type ne ''} value="{$data.emshineverifiedid}" {else} value="0"   {/if} />
                <i {if $data.elite_mproduct_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_emshineverified[]" placeholder="Product" title="Product"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_EMSHINEVERIFIED_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_emshineverified" id="product_type_emshineverified___{$counter}" name="product_type_emshineverified[]" placeholder="Product Type" title="Product Type" onchange="getMRPBottomPrice(this.value, this.id, 'emshineverified');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    {foreach item=emshineverified key=PICKLIST_NAME from=$EMSHINEVERIFIED_PRODUCTS}
                        {assign var="emshineverified_display" value="__"|explode:$emshineverified}
                        <option value="{$emshineverified}" {if $emshineverified_display[0] eq $data.elite_mproduct_type} selected="selected" {/if} >{$emshineverified_display[0]}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input class="textwidth walkin_emshineverified" id="walkin_emshineverified__{$counter}" type="text" name="walkin_emshineverified[]" value="{$data.elite_no_walk}" maxlength="5" placeholder="No. of Walkin" title="No. of Walkin" onkeyup="getNewMRPBottomPrice(this.id, this.value, 'emshineverified');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth rdetail_emshineverified" id="rdetail_emshineverified__{$counter}" type="text" name="rdetail_emshineverified[]" value="{$data.elite_re_details}"  placeholder="Requirement Details" title="Requirement Details"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td></td>
        </tr>

        <tr {if $counter eq 0} class="prototypeemshineverifiedtwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonemshineverifiedtwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input class="textwidth bottom_price_emshineverified" id="bottom_price_emshineverified{$counter}" readonly type="text" name="bottom_price_emshineverified[]" value="{$data.bottom_price}"  class="idemshineverifiedtwo" placeholder="Bottom Price" title="Bottom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_emshineverified" id="mrp_emshineverified{$counter}" readonly type="text" name="mrp_emshineverified[]" value="{$data.mrp}"  placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeemshineverifiedthree" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonemshineverifiedthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_emshineverified" readonly id="discount_emshineverified{$counter}" type="text" name="discount_emshineverified[]" value="{$data.ps_discount}"  class="idemshineverifiedthree" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_emshineverified" readonly id="discount_amount_emshineverified{$counter}" type="text" name="discount_amount_emshineverified[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_emshineverified" maxlength="15"  id="offered_amount_emshineverified{$counter}" type="text" name="offered_amount_emshineverified[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'emshineverified')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_emshineverified" readonly id="service_tax_amount_emshineverified{$counter}" type="text" name="service_tax_amount_emshineverified[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_emshineverified" readonly id="total_amount_emshineverified{$counter}" type="text" name="total_amount_emshineverified[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonemshineverifiedgapfour{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
