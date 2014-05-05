{literal}
    <script>
        var total_active_education_row = 1;
        $(document).ready(function() {
            var ideducationone = "{/literal}{$COUNTER_EDUCATION_ASSIGN}{literal}";
            total_active_education_row++;
            // Add button functionality
            $("table.dynatableeducation button.add").click(function() {
                ideducationone++;
                var master = $(this).parents("table.dynatableeducation");
                // Get a new row based on the prototypeeducationone row
                var prot = master.find(".prototypeeducationone").clone();
                var css_val = "tr1";
                if(ideducationone % 2 == 0)
                    css_val = "tr2";
                prot.attr("class", css_val)
                prot.find(".ideducationone").attr("value", ideducationone);// row one
                prot.find(".deleteRow").attr("id", ideducationone);// row one
                prot.find(".product_type_education").attr("id", "product_type_education___"+ideducationone);// row one
                prot.find(".noofcompany_education").attr("id", "noofcompany_education__"+ideducationone);// row one
                prot.find(".mrp_education").attr("id", "mrp_education"+ideducationone);// row one
                prot.find(".bottom_price_education").attr("id", "bottom_price_education"+ideducationone);// row one
                master.find("tbody").append(prot);

                // Get a new row based on the prototypeeducationone row
                var prot_two = master.find(".prototypeeducationtwo").clone();
                prot_two.attr("class", css_val)
                prot_two.find(".deleteRow").attr("id", "buttoneducationtwo"+ideducationone);// row one
                prot_two.find(".ideducationtwo").attr("value", "__"+ideducationone); // row two
                prot_two.find(".discount_education").attr("id", "discount_education"+ideducationone);// row one
                prot_two.find(".discount_amount_education").attr("id", "discount_amount_education"+ideducationone);// row one
                prot_two.find(".offered_amount_education").attr("id", "offered_amount_education"+ideducationone);// row one
                prot_two.find(".service_tax_amount_education").attr("id", "service_tax_amount_education"+ideducationone);// row one
                prot_two.find(".total_amount_education").attr("id", "total_amount_education"+ideducationone);// row one
                master.find("tbody").append(prot_two);

                var prot_gap = master.find(".prototype_gap").clone();
                prot_gap.find(".deleteRow").attr("id", "buttoneducationgapthree"+ideducationone);// row one
                prot_gap.attr("class", "trgap");
                master.find("tbody").append(prot_gap);
            });

            // Remove button functionality
            $("table.dynatableeducation .deleteRow").live("click", function() {
                $("#buttoneducationtwo"+this.id).click();
                $("#buttoneducationgapthree"+this.id).click();
                $(this).parents("tr").remove();
                if($.isNumeric(this.id))
                    total_active_education_row--;
            });
        });

    </script>

    <style>
        .dynatableeducation {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatableeducation th,
        .dynatableeducation td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatableeducation .prototypeeducationone {
            display:none;
        }

        .dynatableeducation .prototypeeducationtwo {
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

<table class="dynatableeducation" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Education</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$EDUCATION_LIST}
        <tr {if $counter eq 0}class="prototypeeducationone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_education[]" {if $data.product ne ''} value="{$data.educationid}" {else} value="0"   {/if} />
                <i {if $data.edu_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_education[]" placeholder="Product" title="Product" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product</option>
                    {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_EDUCATION_PRODUCTS}
                        <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth product_type_education" id="product_type_education___{$counter}" name="product_type_education[]" placeholder="Product Type" title="Product Type" onchange="getMRPBottomPrice(this.value, this.id, 'education');"   data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a Product Type</option>
                    {foreach item=education key=PICKLIST_NAME from=$EDUCATION_PRODUCTS}
                        {assign var="edcutaion_display" value="__"|explode:$education}
                        <option value="{$education}" {if $edcutaion_display[0] eq $data.edu_product_type} selected="selected" {/if} >{$edcutaion_display[0]}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input class="textwidth noofcompany_education" maxlength="5" type="text" id="noofcompany_education__{$counter}" name="noofcompany_education[]" value="{$data.edu_no_cmp}" onkeyup="getNewMRPBottomPrice(this.id, this.value, 'education');" placeholder="No of Companies/Applications" title="No of Companies/Applications" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth bottom_price_education" readonly type="text" id="bottom_price_education{$counter}" name="bottom_price_education[]" value="{$data.bottom_price}" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth mrp_education" readonly type="text" id="mrp_education{$counter}" name="mrp_education[]" value="{$data.mrp}" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypeeducationtwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttoneducationtwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_education" readonly id="discount_education{$counter}" type="text" name="discount_education[]" value="{$data.ps_discount}"  class="ideducationtwo" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_education" readonly id="discount_amount_education{$counter}" type="text" name="discount_amount_education[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_education" maxlength="15"  id="offered_amount_education{$counter}" type="text" name="offered_amount_education[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'education')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_education" readonly id="service_tax_amount_education{$counter}" type="text" name="service_tax_amount_education[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_education" readonly id="total_amount_education{$counter}" type="text" name="total_amount_education[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttoneducationgapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
