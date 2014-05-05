{literal}
    <script>
        $(document).ready(function() {
            var idmyparichayone = 1;

            // Add button functionality
            $("table.dynatablemyparichay button.add").click(function() {
                idmyparichayone++;
                var master = $(this).parents("table.dynatablemyparichay");
                // Get a new row based on the prototypemyparichayone row
                var prot = master.find(".prototypemyparichayone").clone();
                var css_val = "tr1";
                if(idmyparichayone % 2 == 0)
                    css_val = "tr2";
                prot.attr("class", css_val)
                prot.find(".idmyparichayone").attr("value", idmyparichayone);// row one
                prot.find(".deleteRow").attr("id", idmyparichayone);// row one

                master.find("tbody").append(prot);

                // Get a new row based on the prototypemyparichayone row
                var prot_two = master.find(".prototypemyparichaytwo").clone();
                prot_two.attr("class", css_val)
                prot_two.find(".deleteRow").attr("id", "buttonmyparichaytwo"+idmyparichayone);// row one
                prot_two.find(".idmyparichaytwo").attr("value", "__"+idmyparichayone); // row two
                master.find("tbody").append(prot_two);

                var prot_gap = master.find(".prototype_gap").clone();
                prot_gap.find(".deleteRow").attr("id", "buttonmyparichaygapthree"+idmyparichayone);// row one
                prot_gap.attr("class", "trgap");
                master.find("tbody").append(prot_gap);
            });

            // Remove button functionality
            $("table.dynatablemyparichay .deleteRow").live("click", function() {
                $("#buttonmyparichaytwo"+this.id).click();
                $("#buttonmyparichaygapthree"+this.id).click();
                $(this).parents("tr").remove();
            });
        });

    </script>

    <style>
        .dynatablemyparichay {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatablemyparichay th,
        .dynatablemyparichay td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatablemyparichay .prototypemyparichayone {
            display:none;
        }

        .dynatablemyparichay .prototypemyparichaytwo {
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

<table class="dynatablemyparichay" style="width: 100%">
    <thead>
        <tr>
            <th style="width: 4%">Action</th>
            <th style="width: 19%"></th>
            <th style="width: 20%"></th>
            <th style="width: 19%"></th>
            <th style="width: 19%"></th>
            <th style="width: 19%"><button class="add btn btn-success" type="button" >Add My Parichay</button></th>
        </tr>
    </thead>
    <tbody>
    {for $counter = 0 to 10 max = 2}

        <tr {if $counter eq 0}class="prototypemyparichayone" {else} class="tr1"{/if} >
            <td>
                <i class="icon-trash deleteRow cursorPointer" id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="region_name" placeholder="Product" title="Product" onchange="getOutletName(this.value);" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                    {foreach item=data key=PICKLIST_NAME from=$RELATED_MYPARICHAY_PRODUCTS}
                        <option value="{$data.leadno}" >{$data.product}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <select class="textwidth" name="region_name" placeholder="Product Type" title="Product Type" onchange="getOutletName(this.value);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                    {foreach item=myparichay key=PICKLIST_NAME from=$MYPARICHAY_PRODUCTS}
                        <option value="{$myparichay}" >{$myparichay}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input class="textwidth" type="text" name="col4one[]" value="" placeholder="Duration" title="Duration" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="col3one[]" value="" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="col3one[]" value="" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototypemyparichaytwo" {else} class="tr1" {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonmyparichaytwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth" type="text" name="id[]" value=""  class="idmyparichaytwo" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="nameone[]" value="" placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="col4one[]" value="" placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="col3one[]" value="" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth" type="text" name="col3one[]" value="" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonmyparichaygapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/for}
    </tbody>
</table>
