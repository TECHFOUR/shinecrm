{literal}
<script>
    var total_active_event_row = 1;
    $(document).ready(function() {
        var idflexihireone = "{/literal}{$COUNTER_FLEXIHIRE_ASSIGN}{literal}";
        // Add button functionality
        $("table.dynatableflexihire button.add").click(function() {
            idflexihireone++;
            total_active_event_row++;
            var master = $(this).parents("table.dynatableflexihire");
            // Get a new row based on the prototypeflexihireone row
            var prot = master.find(".prototypeflexihireone").clone();
            var css_val = "tr1";
            if(idflexihireone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idflexihireone").attr("value", idflexihireone);// row one
            prot.find(".deleteRow").attr("id", idflexihireone);// row one
            prot.find(".product_type_flexihire").attr("id", "product_type_flexihire___"+idflexihireone);// row one
            prot.find(".duration_flexihire").attr("id", "duration_flexihire___"+idflexihireone);// row one
            prot.find(".access_flexihire").attr("id", "access_flexihire___"+idflexihireone);// row one

            master.find("tbody").append(prot);

            // Get a new row based on the prototypeflexihireone row
            var prot_two = master.find(".prototypeflexihiretwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonflexihiretwo"+idflexihireone);// row one
            prot_two.find(".idflexihiretwo").attr("value", "__"+idflexihireone); // row two
            prot_two.find(".bottom_price_flexihire").attr("id", "bottom_price_flexihire"+idflexihireone);// row one
            prot_two.find(".mrp_flexihire").attr("id", "mrp_flexihire"+idflexihireone);// row one
            master.find("tbody").append(prot_two);

            // Get a new row based on the prototypeflexihireone row
            var prot_three = master.find(".prototypeflexihirethree").clone();
            prot_three.attr("class", css_val)
            prot_three.find(".deleteRow").attr("id", "buttonflexihirethree"+idflexihireone);// row one
            prot_three.find(".idflexihirethree").attr("value", "__"+idflexihireone); // row two
            prot_three.find(".discount_flexihire").attr("id", "discount_flexihire"+idflexihireone);// row one
            prot_three.find(".discount_amount_flexihire").attr("id", "discount_amount_flexihire"+idflexihireone);// row one
            prot_three.find(".offered_amount_flexihire").attr("id", "offered_amount_flexihire"+idflexihireone);// row one
            prot_three.find(".service_tax_amount_flexihire").attr("id", "service_tax_amount_flexihire"+idflexihireone);// row one
            prot_three.find(".total_amount_flexihire").attr("id", "total_amount_flexihire"+idflexihireone);// row one
            master.find("tbody").append(prot_three);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonflexihiregapfour"+idflexihireone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatableflexihire .deleteRow").live("click", function() {
            $("#buttonflexihiretwo"+this.id).click();
            $("#buttonflexihirethree"+this.id).click();
            $("#buttonflexihiregapfour"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_event_row--;
        });
    });

</script>

    <style>
        .dynatableflexihire {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableflexihire th,
        .dynatableflexihire td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableflexihire .prototypeflexihireone {
            display:none;
        }

        .dynatableflexihire .prototypeflexihiretwo {
            display:none;
        }

        .dynatableflexihire .prototypeflexihirethree {
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

<table class="dynatableflexihire" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Flexi Hire</button></th>
    </tr>
    </thead>
    <tbody>

    {foreach item=data key=counter from=$FLEXIHIRE_LIST}
        <tr {if $counter eq 0}class="prototypeflexihireone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_flexihire[]" {if $data.elite_mgeography ne ''} value="{$data.flexihireid}" {else} value="0"   {/if} />
                <i {if $data.elite_mgeography eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_flexihire[]" placeholder="Product" title="Product"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_FLEXIHIRE_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_flexihire" id="product_type_flexihire___{$counter}" name="product_type_flexihire[]" placeholder="Product Type" title="Product Type" onchange="getNextTypeValues(this.id, this.value, 'flexihire', 'flexi_access', 'access_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Geography</option>
                    {foreach item=flexihire key=PICKLIST_NAME from=$FLEXIHIRE_PRODUCTS}
                        <option value="{$flexihire}" {if $flexihire eq $data.elite_mgeography} selected="selected" {/if} >{$flexihire}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth access_flexihire" name="access_flexihire[]" id="access_flexihire___{$counter}" placeholder="Access" title="Access" onchange="getNextTypeValues(this.id, this.value, 'flexihire', 'flexi_duration', 'duration_', 2);"   data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select an Access</option>
                    {if $data.elite_maccess ne ''}
                        <option value="{$data.elite_maccess}" selected="selected" >{$data.elite_maccess}</option>
                    {/if}
                </select>
            </td>
            <td>
                <select class="textwidth duration_flexihire" name="duration_flexihire[]" id="duration_flexihire___{$counter}" placeholder="Duration" title="Duration" onchange="getMRPBottomPrice(this.value, this.id, 'flexihire');"   data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Duration</option>
                    {if $data.elite_mduration ne ''}
                        <option value="{$data.elite_mduration}__{$data.flexihireid}" selected="selected" >{$data.elite_mduration}</option>
                    {/if}
                </select>
            </td>
            <td></td>
        </tr>

        <tr {if $counter eq 0} class="prototypeflexihiretwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonflexihiretwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input class="textwidth bottom_price_flexihire" id="bottom_price_flexihire{$counter}" readonly type="text" name="bottom_price_flexihire[]" value="{$data.bottom_price}"  class="idflexihiretwo" placeholder="Bottom Price" title="Bottom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_flexihire" id="mrp_flexihire{$counter}" readonly type="text" name="mrp_flexihire[]" value="{$data.mrp}"  placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeflexihirethree" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonflexihirethree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_flexihire" readonly id="discount_flexihire{$counter}" type="text" name="discount_flexihire[]" value="{$data.ps_discount}"  class="idflexihirethree" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_flexihire" readonly id="discount_amount_flexihire{$counter}" type="text" name="discount_amount_flexihire[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_flexihire" maxlength="15"  id="offered_amount_flexihire{$counter}" type="text" name="offered_amount_flexihire[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'flexihire')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_flexihire" readonly id="service_tax_amount_flexihire{$counter}" type="text" name="service_tax_amount_flexihire[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_flexihire" readonly id="total_amount_flexihire{$counter}" type="text" name="total_amount_flexihire[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonflexihiregapfour{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
