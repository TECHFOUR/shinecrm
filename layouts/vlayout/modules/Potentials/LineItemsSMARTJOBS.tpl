{literal}
<script>
    function getFreejobPrice(entityvalue, entityid, product){
        var rowid = entityid.split("___")[1];
        var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&type=FREEJOB";
        $.ajax({
            type:'POST',
            url:"PackageMaster.php"+QryString,
            success:function(result_data){
                $("#price_per_job__"+rowid).val(result_data);
            }
        });
    }
    var total_active_smartjobs_row = 1;
    $(document).ready(function() {
        var idsmartjobsone = "{/literal}{$COUNTER_SMARTJOBS_ASSIGN}{literal}";
        total_active_smartjobs_row++;
        // Add button functionality
        $("table.dynatablesmartjobs button.add").click(function() {
            idsmartjobsone++;
            var master = $(this).parents("table.dynatablesmartjobs");
            // Get a new row based on the prototypesmartjobsone row
            var prot = master.find(".prototypesmartjobsone").clone();
            var css_val = "tr1";
            if(idsmartjobsone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idsmartjobsone").attr("value", idsmartjobsone);// row one
            prot.find(".deleteRow").attr("id", idsmartjobsone);// row one
            prot.find(".product_type_smartjobs").attr("id", "product_type_smartjobs___"+idsmartjobsone);// row one
            prot.find(".price_per_job").attr("id", "price_per_job__"+idsmartjobsone);// row one
            prot.find(".mrp_smartjobs").attr("id", "mrp_smartjobs"+idsmartjobsone);// row one
            prot.find(".bottom_price_smartjobs").attr("id", "bottom_price_smartjobs"+idsmartjobsone);// row one
            master.find("tbody").append(prot);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonsmartjobsgapthree"+idsmartjobsone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatablesmartjobs .deleteRow").live("click", function() {
            $("#buttonsmartjobstwo"+this.id).click();
            $("#buttonsmartjobsgapthree"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_smartjobs_row--;
        });
    });

</script>

    <style>
        .dynatablesmartjobs {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatablesmartjobs th,
        .dynatablesmartjobs td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatablesmartjobs .prototypesmartjobsone {
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

<table class="dynatablesmartjobs" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Smart Jobs</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$SMARTJOBS_LIST}
        <tr {if $counter eq 0}class="prototypesmartjobsone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_smartjobs[]" {if $data.sjobs_product_type ne ''} value="{$data.smartjobsid}" {else} value="0"   {/if} />
                <i {if $data.sjobs_product_type eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth" name="product_smartjobs[]" placeholder="Product" title="Product" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="Smart Jobs">Smart Jobs</option>
                </select>
            </td>
            <td>
                <select class="textwidth product_type_smartjobs" id="product_type_smartjobs___{$counter}" name="product_type_smartjobs[]" placeholder="Product Type" title="Product Type" onchange="getFreejobPrice(this.value, this.id, 'smartjobs');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                    <option value="">Select a No. of Jobs</option>
                    {foreach item=smartjobs key=PICKLIST_NAME from=$SMARTJOBS_PRODUCTS}
                        <option value="{$smartjobs}" {if $smartjobs eq $data.sjobs_product_type} selected="selected" {/if} >{$smartjobs}</option>
                    {/foreach}
                </select>
            </td>
            <td>
                <input class="textwidth price_per_job" type="text" readonly id="price_per_job__{$counter}" name="price_per_job[]" value="{$data.mrp}" placeholder="Price per Job" title="Price per Job"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth bottom_price_smartjobs" readonly type="text" id="bottom_price_smartjobs{$counter}" name="bottom_price_smartjobs[]" value="0" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonsmartjobsgapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
