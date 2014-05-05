{literal}
    <script>
        var total_active_event_row = 1;
        $(document).ready(function() {
            var ideventsone = "{/literal}{$COUNTER_EVENT_ASSIGN}{literal}";
            // Add button functionality
            $("table.dynatableevents button.add").click(function() {
                ideventsone++;
                total_active_event_row++;
                var master = $(this).parents("table.dynatableevents");
                // Get a new row based on the prototypeeventsone row
                var prot = master.find(".prototypeeventsone").clone();
                var css_val = "tr1";
                if(ideventsone % 2 == 0)
                    css_val = "tr2";
                prot.attr("class", css_val)
                prot.find(".ideventsone").attr("value", ideventsone);// row one
                prot.find(".deleteRow").attr("id", ideventsone);// row one
                prot.find(".product_type_events").attr("id", "product_type_events___"+ideventsone);// row one
                prot.find(".month_events").attr("id", "month_events__"+ideventsone);// row one
                prot.find(".sponsorship_events").attr("id", "sponsorship_events___"+ideventsone);// row one

                master.find("tbody").append(prot);

                // Get a new row based on the prototypeeventsone row
                var prot_two = master.find(".prototypeeventstwo").clone();
                prot_two.attr("class", css_val)
                prot_two.find(".deleteRow").attr("id", "buttoneventstwo"+ideventsone);// row one
                prot_two.find(".ideventstwo").attr("value", "__"+ideventsone); // row two
                prot_two.find(".bottom_price_events").attr("id", "bottom_price_events"+ideventsone);// row one
                prot_two.find(".mrp_events").attr("id", "mrp_events"+ideventsone);// row one
                master.find("tbody").append(prot_two);

                // Get a new row based on the prototypeeventsone row
                var prot_three = master.find(".prototypeeventsthree").clone();
                prot_three.attr("class", css_val)
                prot_three.find(".deleteRow").attr("id", "buttoneventsthree"+ideventsone);// row one
                prot_three.find(".ideventsthree").attr("value", "__"+ideventsone); // row two
                prot_three.find(".discount_events").attr("id", "discount_events"+ideventsone);// row one
                prot_three.find(".discount_amount_events").attr("id", "discount_amount_events"+ideventsone);// row one
                prot_three.find(".offered_amount_events").attr("id", "offered_amount_events"+ideventsone);// row one
                prot_three.find(".service_tax_amount_events").attr("id", "service_tax_amount_events"+ideventsone);// row one
                prot_three.find(".total_amount_events").attr("id", "total_amount_events"+ideventsone);// row one
                master.find("tbody").append(prot_three);

                var prot_gap = master.find(".prototype_gap").clone();
                prot_gap.find(".deleteRow").attr("id", "buttoneventsgapfour"+ideventsone);// row one
                prot_gap.attr("class", "trgap");
                master.find("tbody").append(prot_gap);
            });

            // Remove button functionality
            $("table.dynatableevents .deleteRow").live("click", function() {
                $("#buttoneventstwo"+this.id).click();
                $("#buttoneventsthree"+this.id).click();
                $("#buttoneventsgapfour"+this.id).click();
                $(this).parents("tr").remove();
                if($.isNumeric(this.id))
                    total_active_event_row--;
            });
        });

    </script>

    <style>
        .dynatableevents {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableevents th,
        .dynatableevents td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableevents .prototypeeventsone {
            display:none;
        }

        .dynatableevents .prototypeeventstwo {
             display:none;
         }

        .dynatableevents .prototypeeventsthree {
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

<table class="dynatableevents" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Events</button></th>
    </tr>
    </thead>
    <tbody>

    {foreach item=data key=counter from=$EVENT_LIST}
        <tr {if $counter eq 0}class="prototypeeventsone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_event[]" {if $data.comment_product_type ne ''} value="{$data.eventid}" {else} value="0"   {/if} />
                <i {if $data.comment_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_events[]" placeholder="Product" title="Product"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_EVENTS_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_events" id="product_type_events___{$counter}" name="product_type_events[]" placeholder="Product Type" title="Product Type" onchange="getNextTypeValues(this.id, this.value, 'events', 'event_sponsorship', 'sponsorship_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    {foreach item=events key=PICKLIST_NAME from=$EVENTS_PRODUCTS}
                        <option value="{$events}" {if $events eq $data.comment_product_type} selected="selected" {/if} >{$events}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth sponsorship_events" name="sponsorship_events[]" id="sponsorship_events___{$counter}" placeholder="Sponsorship" title="Sponsorship" onchange="getMRPBottomPrice(this.value, this.id, 'events');" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Sponsorship</option>
                    {if $data.campaignstatus ne ''}
                        <option value="{$data.campaignstatus}__{$data.eventid}" selected="selected" >{$data.campaignstatus}</option>
                    {/if}
                </select>
            </td>
            <td>
                <input class="textwidth month_events" id="month_events__{$counter}" type="text" name="month_events[]" value="{$data.month}"  placeholder="Month" title="Month"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td></td>
        </tr>

        <tr {if $counter eq 0} class="prototypeeventstwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttoneventstwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                <input class="textwidth bottom_price_events" id="bottom_price_events{$counter}" readonly type="text" name="bottom_price_events[]" value="{$data.bottom_price}"  class="ideventstwo" placeholder="Bottom Price" title="Bottom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_events" id="mrp_events{$counter}" readonly type="text" name="mrp_events[]" value="{$data.mrp}"  placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeeventsthree" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttoneventsthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_events" readonly id="discount_events{$counter}" type="text" name="discount_events[]" value="{$data.ps_discount}"  class="ideventsthree" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_events" readonly id="discount_amount_events{$counter}" type="text" name="discount_amount_events[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_events" maxlength="15"  id="offered_amount_events{$counter}" type="text" name="offered_amount_events[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'events')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_events" readonly id="service_tax_amount_events{$counter}" type="text" name="service_tax_amount_events[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_events" readonly id="total_amount_events{$counter}" type="text" name="total_amount_events[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttoneventsgapfour{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
