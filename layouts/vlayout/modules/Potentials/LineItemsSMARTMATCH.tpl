{literal}
<script>
    var total_active_smartmatch_row = 1;
    $(document).ready(function() {
        var idsmartmatchone = "{/literal}{$COUNTER_SMARTMATCH_ASSIGN}{literal}";
        total_active_smartmatch_row++;
        // Add button functionality
        $("table.dynatablesmartmatch button.add").click(function() {
            idsmartmatchone++;
            var master = $(this).parents("table.dynatablesmartmatch");
            // Get a new row based on the prototypesmartmatchone row
            var prot = master.find(".prototypesmartmatchone").clone();
            var css_val = "tr1";
            if(idsmartmatchone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idsmartmatchone").attr("value", idsmartmatchone);// row one
            prot.find(".deleteRow").attr("id", idsmartmatchone);// row one
            prot.find(".product_type_smartmatch").attr("id", "product_type_smartmatch___"+idsmartmatchone);// row one
            prot.find(".noofjob_smartmatch").attr("id", "noofjob_smartmatch___"+idsmartmatchone);// row one
            prot.find(".mrp_smartmatch").attr("id", "mrp_smartmatch"+idsmartmatchone);// row one
            prot.find(".bottom_price_smartmatch").attr("id", "bottom_price_smartmatch"+idsmartmatchone);// row one
            master.find("tbody").append(prot);

            // Get a new row based on the prototypesmartmatchone row
            var prot_two = master.find(".prototypesmartmatchtwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonsmartmatchtwo"+idsmartmatchone);// row one
            prot_two.find(".idsmartmatchtwo").attr("value", "__"+idsmartmatchone); // row two
            prot_two.find(".discount_smartmatch").attr("id", "discount_smartmatch"+idsmartmatchone);// row one
            prot_two.find(".discount_amount_smartmatch").attr("id", "discount_amount_smartmatch"+idsmartmatchone);// row one
            prot_two.find(".offered_amount_smartmatch").attr("id", "offered_amount_smartmatch"+idsmartmatchone);// row one
            prot_two.find(".service_tax_amount_smartmatch").attr("id", "service_tax_amount_smartmatch"+idsmartmatchone);// row one
            prot_two.find(".total_amount_smartmatch").attr("id", "total_amount_smartmatch"+idsmartmatchone);// row one
            master.find("tbody").append(prot_two);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonsmartmatchgapthree"+idsmartmatchone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatablesmartmatch .deleteRow").live("click", function() {
            $("#buttonsmartmatchtwo"+this.id).click();
            $("#buttonsmartmatchgapthree"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_smartmatch_row--;
        });
    });

</script>

    <style>
        .dynatablesmartmatch {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatablesmartmatch th,
        .dynatablesmartmatch td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatablesmartmatch .prototypesmartmatchone {
            display:none;
        }

        .dynatablesmartmatch .prototypesmartmatchtwo {
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

<table class="dynatablesmartmatch" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Smart Match</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$SMARTMATCH_LIST}
        <tr {if $counter eq 0}class="prototypesmartmatchone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_smartmatch[]" {if $data.match_dura_month ne ''} value="{$data.smartmatchid}" {else} value="0"   {/if} />
                <i {if $data.match_dura_month eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_smartmatch[]" placeholder="Product" title="Product" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_SMARTMATCH_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_smartmatch" id="product_type_smartmatch___{$counter}" name="product_type_smartmatch[]" placeholder="Duration(Month)" title="Duration(Month)" onchange="getNextTypeValues(this.id, this.value, 'smartmatch', 'no_of_jobs', 'noofjob_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Duration(Month)</option>
                    {foreach item=smartmatch key=PICKLIST_NAME from=$SMARTMATCH_PRODUCTS}
                        <option value="{$smartmatch}" {if $smartmatch eq $data.match_dura_month} selected="selected" {/if} >{$smartmatch}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth noofjob_smartmatch" name="noofjob_smartmatch[]" id="noofjob_smartmatch___{$counter}" placeholder="IT/Non-IT" title="IT/Non-IT" onchange="getMRPBottomPrice(this.value, this.id, 'smartmatch');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a No. of Job</option>
                    {if $data.match_no_jobs ne ''}
                        <option value="{$data.match_no_jobs}__{$data.databaseid}" selected="selected" >{$data.match_no_jobs}</option>
                    {/if}
                </select>
            </td>
            <td>
                <input class="textwidth bottom_price_smartmatch" readonly type="text" id="bottom_price_smartmatch{$counter}" name="bottom_price_smartmatch[]" value="{$data.bottom_price}" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_smartmatch" readonly type="text" id="mrp_smartmatch{$counter}" name="mrp_smartmatch[]" value="{$data.mrp}" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypesmartmatchtwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonsmartmatchtwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_smartmatch" readonly id="discount_smartmatch{$counter}" type="text" name="discount_smartmatch[]" value="{$data.ps_discount}"  class="idsmartmatchtwo" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_smartmatch" readonly id="discount_amount_smartmatch{$counter}" type="text" name="discount_amount_smartmatch[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_smartmatch" maxlength="15" id="offered_amount_smartmatch{$counter}" type="text" name="offered_amount_smartmatch[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'smartmatch')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_smartmatch" readonly id="service_tax_amount_smartmatch{$counter}" type="text" name="service_tax_amount_smartmatch[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_smartmatch" readonly id="total_amount_smartmatch{$counter}" type="text" name="total_amount_smartmatch[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonsmartmatchgapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
