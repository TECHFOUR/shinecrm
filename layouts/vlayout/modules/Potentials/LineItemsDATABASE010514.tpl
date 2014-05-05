{literal}
    <script>
        function getUpsellDownsellAmount(entityid, entityvalue, typevalue, product){
            var rowid = entityid.split("___")[1];
            var type_up_down = typevalue.split("_")[0];
            var product_type_database = $("#product_type_database___"+rowid).val();
            var it_nonit_database = $("#it_nonit_database___"+rowid).val().split("__")[0];
            var limits_database = $("#limits_database___"+rowid).val().split("__")[0];
            var duration_val = $("#duration_database___"+rowid).val().split("__")[0];
            var bottom_price_database = $("#bottom_price_database"+rowid).val();
            var mrp_database = $("#mrp_database"+rowid).val();
            // calculate total up down sell amount
            var up_em_amt =  $("#upsell_emailer_amount"+rowid).val().replace(/,/g, '');
            var up_ex_amt =  $("#upsell_excell_amount"+rowid).val().replace(/,/g, '');
            var up_wo_amt =  $("#upsell_word_amount"+rowid).val().replace(/,/g, '');
            var up_lo_amt =  $("#upsell_login_amount"+rowid).val().replace(/,/g, '');
            var dw_em_amt =  $("#downsell_emailer_amount"+rowid).val().replace(/,/g, '');
            var dw_ex_amt =  $("#downsell_excel_amount"+rowid).val().replace(/,/g, '');
            var dw_wo_amt =  $("#downsell_word_amount"+rowid).val().replace(/,/g, '');
            var total_up_sell_amount = parseFloat(up_em_amt) + parseFloat(up_ex_amt) + parseFloat(up_wo_amt) + parseFloat(up_lo_amt);
            var total_down_sell_amount = parseFloat(dw_em_amt) + parseFloat(dw_ex_amt) + parseFloat(dw_wo_amt);
            var type_value_amount = $("#"+typevalue+"_amount"+rowid).val().replace(/,/g, '');

            var up_em_amt_bp =  $("#upsell_emailer_amount_bp"+rowid).val().replace(/,/g, '');
            var up_ex_amt_bp =  $("#upsell_excell_amount_bp"+rowid).val().replace(/,/g, '');
            var up_wo_amt_bp =  $("#upsell_word_amount_bp"+rowid).val().replace(/,/g, '');
            var up_lo_amt_bp =  $("#upsell_login_amount_bp"+rowid).val().replace(/,/g, '');
            var dw_em_amt_bp =  $("#downsell_emailer_amount_bp"+rowid).val().replace(/,/g, '');
            var dw_ex_amt_bp =  $("#downsell_excel_amount_bp"+rowid).val().replace(/,/g, '');
            var dw_wo_amt_bp =  $("#downsell_word_amount_bp"+rowid).val().replace(/,/g, '');
            var total_up_sell_amount_bp = parseFloat(up_em_amt_bp) + parseFloat(up_ex_amt_bp) + parseFloat(up_wo_amt_bp)+ parseFloat(up_lo_amt_bp);
            var total_down_sell_amount_bp = parseFloat(dw_em_amt_bp) + parseFloat(dw_ex_amt_bp) + parseFloat(dw_wo_amt_bp);
            var type_value_amount_bp = $("#"+typevalue+"_amount_bp"+rowid).val().replace(/,/g, '');

            if(type_up_down == 'upsell') {
                total_up_sell_amount = total_up_sell_amount - type_value_amount;
                total_up_sell_amount_bp = total_up_sell_amount_bp - type_value_amount_bp;
            }
            else {
                total_down_sell_amount = total_down_sell_amount - type_value_amount;
                total_down_sell_amount_bp = total_down_sell_amount_bp - type_value_amount_bp;
            }

           //alert(total_up_sell_amount+"__"+total_up_sell_amount_bp+"__"+total_down_sell_amount+"__"+total_down_sell_amount_bp);
            var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&typevalue="+typevalue+"&type=UpsellDownsell";
            QryString += "&product_type_database="+product_type_database+"&it_nonit_database="+it_nonit_database
                    +"&limits_database="+limits_database+"&duration_val="+duration_val+"&bottom_price_database="+bottom_price_database
                    +"&mrp_database="+mrp_database+"&total_up_sell_amount="+total_up_sell_amount+"&total_down_sell_amount="+total_down_sell_amount
                    +"&total_up_sell_amount_bp="+total_up_sell_amount_bp+"&total_down_sell_amount_bp="+total_down_sell_amount_bp;
            $.ajax({
                type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(result_data){
                 // alert(result_data);
                    var response = result_data.split("###");
                    $("#bottom_price_"+product+rowid).val(response[0]);
                    $("#mrp_"+product+rowid).val(response[1]);
                    $("#discount_"+product+rowid).val('0.00');
                    $("#discount_amount_"+product+rowid).val('0.00');
                    $("#offered_amount_"+product+rowid).val(response[4]);
                    $("#service_tax_amount_"+product+rowid).val(response[5]);
                    $("#total_amount_"+product+rowid).val(response[6]);
                    $("#"+typevalue+"_amount"+rowid).val(response[7]);
                    $("#"+typevalue+"_amount_bp"+rowid).val(response[8]);
                }
            });
        }

        function getMRPBottomPriceDatabase(entityvalue, entityid, product) {
            var rowid = entityid.split("___")[1];
            var QryString = "?entityvalue="+entityvalue+"&master_type="+product+"&type=Default";
            $.ajax({
                type:'POST',
                url:"PackageMaster.php"+QryString,
                success:function(result_data){
                    //alert(result_data);
                    var response = result_data.split("###");
                    $("#bottom_price_"+product+rowid).val(response[0]);
                    $("#mrp_"+product+rowid).val(response[1]);
                    $("#discount_"+product+rowid).val('0.00');
                    $("#discount_amount_"+product+rowid).val('0.00');
                    $("#offered_amount_"+product+rowid).val(response[1]);
                    $("#service_tax_amount_"+product+rowid).val(response[2]);
                    $("#total_amount_"+product+rowid).val(response[3]);
                    $("#upsell_excel_database___"+rowid).val('');
                    $("#upsell_emailer_database___"+rowid).val('');
                    $("#upsell_word_database___"+rowid).val('');
                    $("#upsell_login_database___"+rowid).val('');
                    $("#downsell_excel_database___"+rowid).val('');
                    $("#downsell_emailer_database___"+rowid).val('');
                    $("#downsell_word_database___"+rowid).val('');
                    $("#upsell_emailer_amount"+rowid).val('0.0');
                    $("#upsell_excell_amount"+rowid).val('0.0');
                    $("#upsell_word_amount"+rowid).val('0.0');
                    $("#upsell_login_amount"+rowid).val('0.0');
                    $("#downsell_emailer_amount"+rowid).val('0.0');
                    $("#downsell_excel_amount"+rowid).val('0.0');
                    $("#downsell_word_amount"+rowid).val('0.0');
                    $("#upsell_emailer_amount_bp"+rowid).val('0.0');
                    $("#upsell_excell_amount_bp"+rowid).val('0.0');
                    $("#upsell_word_amount_bp"+rowid).val('0.0');
                    $("#upsell_login_amount_bp"+rowid).val('0.0');
                    $("#downsell_emailer_amount_bp"+rowid).val('0.0');
                    $("#downsell_excel_amount_bp"+rowid).val('0.0');
                    $("#downsell_word_amount_bp"+rowid).val('0.0');
                }
            });
        }
        var total_active_database_row = 1;
        $(document).ready(function() {
            var iddatabaseone = "{/literal}{$COUNTER_DATABASE_ASSIGN}{literal}";
            total_active_database_row++;
            // Add button functionality
            $("table.dynatabledatabase button.add").click(function() {
                iddatabaseone++;
                var master = $(this).parents("table.dynatabledatabase");
                // Get a new row based on the prototypedatabaseone row
                var prot = master.find(".prototypedatabaseone").clone();
                var css_val = "tr1";
                if(iddatabaseone % 2 == 0)
                    css_val = "tr2";
                prot.attr("class", css_val)
                prot.find(".iddatabaseone").attr("value", iddatabaseone);// row one
                prot.find(".deleteRow").attr("id", iddatabaseone);// row one

                prot.find(".product_type_database").attr("id", "product_type_database___"+iddatabaseone);// row one
                prot.find(".it_nonit_database").attr("id", "it_nonit_database___"+iddatabaseone);// row one
                prot.find(".limits_database").attr("id", "limits_database___"+iddatabaseone);// row one
                prot.find(".duration_database").attr("id", "duration_database___"+iddatabaseone);// row one


                master.find("tbody").append(prot);

                // Get a new row based on the prototypedatabaseone row
                var prot_two = master.find(".prototypedatabasetwo").clone();
                prot_two.attr("class", css_val)
                prot_two.find(".deleteRow").attr("id", "buttondatabasetwo"+iddatabaseone);// row one
                prot_two.find(".iddatabasetwo").attr("value", "__"+iddatabaseone); // row two
                prot_two.find(".upsell_excel_database").attr("id", "upsell_excel_database___"+iddatabaseone);// row one
                prot_two.find(".upsell_word_database").attr("id", "upsell_word_database___"+iddatabaseone);// row one
                prot_two.find(".upsell_emailer_database").attr("id", "upsell_emailer_database___"+iddatabaseone);// row one
                prot_two.find(".upsell_excell_amount").attr("id", "upsell_excell_amount"+iddatabaseone);
                prot_two.find(".upsell_word_amount").attr("id", "upsell_word_amount"+iddatabaseone);
                prot_two.find(".upsell_emailer_amount").attr("id", "upsell_emailer_amount"+iddatabaseone);
                prot_two.find(".upsell_excell_amount_bp").attr("id", "upsell_excell_amount_bp"+iddatabaseone);
                prot_two.find(".upsell_word_amount_bp").attr("id", "upsell_word_amount_bp"+iddatabaseone);
                prot_two.find(".upsell_emailer_amount_bp").attr("id", "upsell_emailer_amount_bp"+iddatabaseone);
                prot_two.find(".upsell_login_database").attr("id", "upsell_login_database___"+iddatabaseone);
                prot_two.find(".upsell_login_amount").attr("id", "upsell_login_amount"+iddatabaseone);
                prot_two.find(".upsell_login_amount_bp").attr("id", "upsell_login_amount_bp"+iddatabaseone);
                master.find("tbody").append(prot_two);

                // Get a new row based on the prototypedatabaseone row
                var prot_three = master.find(".prototypedatabasethree").clone();
                prot_three.attr("class", css_val)
                prot_three.find(".deleteRow").attr("id", "buttondatabasethree"+iddatabaseone);// row one
                prot_three.find(".iddatabasethree").attr("value", "__"+iddatabaseone); // row two
                prot_three.find(".mrp_database").attr("id", "mrp_database"+iddatabaseone);// row one
                prot_three.find(".bottom_price_database").attr("id", "bottom_price_database"+iddatabaseone);// row one
                prot_three.find(".downsell_excel_database").attr("id", "downsell_excel_database___"+iddatabaseone);// row one
                prot_three.find(".downsell_word_database").attr("id", "downsell_word_database___"+iddatabaseone);// row one
                prot_three.find(".downsell_emailer_database").attr("id", "downsell_emailer_database___"+iddatabaseone);// row one
                prot_three.find(".downsell_excel_amount").attr("id", "downsell_excel_amount"+iddatabaseone);
                prot_three.find(".downsell_word_amount").attr("id", "downsell_word_amount"+iddatabaseone);
                prot_three.find(".downsell_emailer_amount").attr("id", "downsell_emailer_amount"+iddatabaseone);
                prot_three.find(".downsell_excel_amount_bp").attr("id", "downsell_excel_amount_bp"+iddatabaseone);
                prot_three.find(".downsell_word_amount_bp").attr("id", "downsell_word_amount_bp"+iddatabaseone);
                prot_three.find(".downsell_emailer_amount_bp").attr("id", "downsell_emailer_amount_bp"+iddatabaseone);
                master.find("tbody").append(prot_three);

                // Get a new row based on the prototypedatabaseone row
                var prot_four = master.find(".prototypedatabasefour").clone();
                prot_four.attr("class", css_val)
                prot_four.find(".deleteRow").attr("id", "buttondatabasefour"+iddatabaseone);// row one
                prot_four.find(".iddatabasefour").attr("value", "__"+iddatabaseone); // row two
                prot_four.find(".discount_database").attr("id", "discount_database"+iddatabaseone);// row one
                prot_four.find(".discount_amount_database").attr("id", "discount_amount_database"+iddatabaseone);// row one
                prot_four.find(".offered_amount_database").attr("id", "offered_amount_database"+iddatabaseone);// row one
                prot_four.find(".service_tax_amount_database").attr("id", "service_tax_amount_database"+iddatabaseone);// row one
                prot_four.find(".total_amount_database").attr("id", "total_amount_database"+iddatabaseone);// row one
                master.find("tbody").append(prot_four);

                var prot_gap = master.find(".prototype_gap").clone();
                prot_gap.find(".deleteRow").attr("id", "buttondatabasegapfive"+iddatabaseone);// row one
                prot_gap.attr("class", "trgap");
                master.find("tbody").append(prot_gap);
            });

            // Remove button functionality
            $("table.dynatabledatabase .deleteRow").live("click", function() {
                $("#buttondatabasetwo"+this.id).click();
                $("#buttondatabasethree"+this.id).click();
                $("#buttondatabasefour"+this.id).click();
                $("#buttondatabasegapfive"+this.id).click();
                $(this).parents("tr").remove();
                if($.isNumeric(this.id))
                    total_active_database_row--;
            });
        });

    </script>

    <style>
        .dynatabledatabase {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatabledatabase th,
        .dynatabledatabase td {
            border: solid 1px #000;
            padding: 2px 1px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatabledatabase .prototypedatabaseone {
            display:none;
        }

        .dynatabledatabase .prototypedatabasetwo {
            display:none;
        }

        .dynatabledatabase .prototypedatabasethree {
            display:none;
        }

        .dynatabledatabase .prototypedatabasefour {
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

<table class="dynatabledatabase" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Database</button></th>
    </tr>
    </thead>

    <tbody>

    {foreach item=data key=counter from=$DATABASE_LIST}
            <tr {if $counter eq 0}class="prototypedatabaseone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
               <td>
                   <input type="hidden" name="record_mode_database[]" {if $data.product ne ''} value="{$data.databaseid}" {else} value="0"   {/if} />
                   <i {if $data.geography_database eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
               </td>
                <td>
                    <select class="textwidth"  name="product_database[]" placeholder="Product" title="Product"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                        <option value="">Select a Product</option>
                        {foreach item=leaddata key=PICKLIST_NAME from=$RELATED_DATABASE_PRODUCTS}
                            <option value="{$leaddata.leadno}" {if $leaddata.leadno eq $data.product} selected="selected" {/if} >{$leaddata.product}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <select class="textwidth product_type_database" id="product_type_database___{$counter}" name="geography_database[]" placeholder="Geography" title="Geography" onchange="getNextTypeValues(this.id, this.value, 'database', 'database_it', 'it_nonit_', 1);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                        <option value="">Select a Product Type</option>
                        {foreach item=database key=PICKLIST_NAME from=$DATABASE_PRODUCTS}
                            <option value="{$database}" {if $database eq $data.geography_database} selected="selected" {/if} >{$database}</option>
                        {/foreach}
                    </select>
                </td>
                <td>
                    <select class="textwidth it_nonit_database" name="it_nonit_database[]" id="it_nonit_database___{$counter}" placeholder="IT/Non-IT" title="IT/Non-IT" onchange="getNextTypeValues(this.id, this.value, 'database', 'database_limit', 'limits_', 2);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                        <option value="">Select an IT/Non-IT</option>
                        {if $data.database_it ne ''}
                            <option value="{$data.database_it}__{$data.databaseid}" selected="selected" >{$data.database_it}</option>
                        {/if}
                    </select>
                </td>
                <td>
                    <select class="textwidth limits_database" name="limits_database[]" id="limits_database___{$counter}" placeholder="Limits" title="Limits" onchange="getNextTypeValues(this.id, this.value, 'database', 'database_month', 'duration_', 3);"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                        <option value="">Select a Limits</option>
                        {if $data.database_limit ne ''}
                            <option value="{$data.database_limit}__{$data.databaseid}" selected="selected" >{$data.database_limit}</option>
                        {/if}
                    </select>
                </td>
                <td>
                    <select class="textwidth duration_database" name="duration_database[]" id="duration_database___{$counter}" placeholder="Duration" title="Duration" onchange="getMRPBottomPriceDatabase(this.value, this.id, 'database');"  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" >
                        <option value="">Select a Duration</option>
                        {if $data.database_month ne ''}
                            <option value="{$data.database_month}__{$data.databaseid}" selected="selected" >{$data.database_month}</option>
                        {/if}
                    </select>
                </td>
            </tr>

            <tr {if $counter eq 0} class="prototypedatabasetwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
                <td>
                    <i class="deleteRow cursorPointer" id="buttondatabasetwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
                </td>
                <td>
                    <select class="textwidth upsell_excel_database" onchange="getUpsellDownsellAmount(this.id, this.value,'upsell_excell', 'database');" id="upsell_excel_database___{$counter}" name="upsell_excel_database[]" placeholder="Upsell Excel" title="Upsell Excel"  >
                        <option value="">Select an Upsell Excell</option>
                        {foreach item=excelvalue key=PICKLIST_NAME from=$VALUEEXCEL}
                            <option value="{$excelvalue}" {if $excelvalue eq $data.db_upsell_exl} selected="selected" {/if} >{$excelvalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth upsell_excell_amount" type="hidden" id="upsell_excell_amount{$counter}" name="upsell_excell_amount[]" {if $data.up_excel_amount ne '' }value="{$data.up_excel_amount}" {else} value="0.00" {/if}" >
                    <input class="textwidth upsell_excell_amount_bp" type="hidden" id="upsell_excell_amount_bp{$counter}" name="upsell_excell_amount_bp[]" {if $data.up_excel_amount_bp ne '' }value="{$data.up_excel_amount_bp}" {else} value="0.00" {/if}" >
                </td>
                <td>
                    <select class="textwidth upsell_word_database" onchange="getUpsellDownsellAmount(this.id, this.value,'upsell_word', 'database');"  name="upsell_word_database[]" id="upsell_word_database___{$counter}" placeholder="Upsell Word" title="Upsell Word"  >
                        <option value="">Select an Upsell Word</option>
                        {foreach item=wordvalue key=PICKLIST_NAME from=$VALUEWORD}
                            <option value="{$wordvalue}" {if $wordvalue eq $data.db_upsell_word} selected="selected" {/if}>{$wordvalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth upsell_word_amount" type="hidden" id="upsell_word_amount{$counter}" name="upsell_word_amount[]" {if $data.up_word_amount ne '' }value="{$data.up_word_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth upsell_word_amount_bp" type="hidden" id="upsell_word_amount_bp{$counter}" name="upsell_word_amount_bp[]" {if $data.up_word_amount_bp ne '' }value="{$data.up_word_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td>
                    <select class="textwidth upsell_emailer_database" onchange="getUpsellDownsellAmount(this.id, this.value,'upsell_emailer', 'database');"  name="upsell_emailer_database[]" id="upsell_emailer_database___{$counter}" placeholder="Upsell E-Mailers" title="Upsell E-Mailers"  >
                        <option value="">Select an Upsell Emailer</option>
                        {foreach item=emailervalue key=PICKLIST_NAME from=$VALUEEMAILER}
                            <option value="{$emailervalue}" {if $emailervalue eq $data.db_upsell_email} selected="selected" {/if}>{$emailervalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth upsell_emailer_amount" type="hidden" id="upsell_emailer_amount{$counter}" name="upsell_emailer_amount[]" {if $data.up_email_amount ne '' }value="{$data.up_email_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth upsell_emailer_amount_bp" type="hidden" id="upsell_emailer_amount_bp{$counter}" name="upsell_emailer_amount_bp[]" {if $data.up_email_amount_bp ne '' }value="{$data.up_email_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td>
                    <select class="textwidth upsell_login_database" onchange="getUpsellDownsellAmount(this.id, this.value,'upsell_login', 'database');"  name="upsell_login_database[]" id="upsell_login_database___{$counter}" placeholder="Extra Login Id" title="Extra Login Id"  >
                        <option value="">Select an Extra Login Id</option>
                        {for $extraloginval = 1 to 5}
                            <option value="{$extraloginval}k" {if $extraloginval eq $data.db_upsell_login} selected="selected" {/if}>{$extraloginval}k</option>
                        {/for}
                    </select>
                    <input class="textwidth upsell_login_amount" type="hidden" id="upsell_login_amount{$counter}" name="upsell_login_amount[]" {if $data.up_login_amount ne '' }value="{$data.up_login_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth upsell_login_amount_bp" type="hidden" id="upsell_login_amount_bp{$counter}" name="upsell_login_amount_bp[]" {if $data.up_login_amount_bp ne '' }value="{$data.up_login_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td></td>
            </tr>

            <tr {if $counter eq 0} class="prototypedatabasethree" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
                <td>
                    <i class="deleteRow cursorPointer" id="buttondatabasethree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
                </td>
                <td>
                    <select class="textwidth downsell_excel_database" onchange="getUpsellDownsellAmount(this.id, this.value,'downsell_excel', 'database');" id="downsell_excel_database___{$counter}"  name="downsell_excel_database[]" placeholder="Downsell Excel" title="Downsell Excel" >
                        <option value="">Select a Downsell Excell</option>
                        {foreach item=excelvalue key=PICKLIST_NAME from=$VALUEEXCEL}
                            <option value="{$excelvalue}" {if $excelvalue eq $data.db_dwnsell_exl} selected="selected" {/if} >{$excelvalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth downsell_excel_amount" type="hidden" id="downsell_excel_amount{$counter}" name="downsell_excel_amount[]" {if $data.down_excel_amount ne '' }value="{$data.down_excel_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth downsell_excel_amount_bp" type="hidden" id="downsell_excel_amount_bp{$counter}" name="downsell_excel_amount_bp[]" {if $data.down_excel_amount_bp ne '' }value="{$data.down_excel_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td>
                    <select class="textwidth downsell_word_database" onchange="getUpsellDownsellAmount(this.id, this.value,'downsell_word', 'database');" id="downsell_word_database___{$counter}"  name="downsell_word_database[]" placeholder="Downsell Word" title="Downsell Word"  >
                        <option value="">Select a Downsell Word</option>
                        {foreach item=wordvalue key=PICKLIST_NAME from=$VALUEWORD}
                            <option value="{$wordvalue}" {if $wordvalue eq $data.db_dwnsell_word} selected="selected" {/if}>{$wordvalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth downsell_word_amount" type="hidden" id="downsell_word_amount{$counter}" name="downsell_word_amount[]" {if $data.down_word_amount ne '' }value="{$data.down_word_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth downsell_word_amount_bp" type="hidden" id="downsell_word_amount_bp{$counter}" name="downsell_word_amount_bp[]" {if $data.down_word_amount_bp ne '' }value="{$data.down_word_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td>
                    <select class="textwidth downsell_emailer_database" onchange="getUpsellDownsellAmount(this.id, this.value,'downsell_emailer', 'database');" id="downsell_emailer_database___{$counter}"  name="downsell_emailer_database[]" placeholder="Downsell E-Mailers" title="Downsell E-Mailers" >
                        <option value="">Select a Downsell Emailer</option>
                        {foreach item=emailervalue key=PICKLIST_NAME from=$VALUEEMAILER}
                            <option value="{$emailervalue}" {if $emailervalue eq $data.db_dwnsell_email} selected="selected" {/if}>{$emailervalue}</option>
                        {/foreach}
                    </select>
                    <input class="textwidth downsell_emailer_amount" type="hidden" id="downsell_emailer_amount{$counter}" name="downsell_emailer_amount[]" {if $data.down_email_amount ne '' }value="{$data.down_email_amount}" {else} value="0.00" {/if}"  >
                    <input class="textwidth downsell_emailer_amount_bp" type="hidden" id="downsell_emailer_amount_bp{$counter}" name="downsell_emailer_amount_bp[]" {if $data.down_email_amount_bp ne '' }value="{$data.down_email_amount_bp}" {else} value="0.00" {/if}"  >
                </td>
                <td>
                    <input class="textwidth bottom_price_database" readonly type="text" id="bottom_price_database{$counter}" name="bottom_price_database[]" value="{$data.bottom_price}" placeholder="Bottom Price" title="Botttom Price" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
                </td>
                <td>
                    <input class="textwidth mrp_database" readonly type="text" id="mrp_database{$counter}" name="mrp_database[]" value="{$data.mrp}" placeholder="MRP" title="MRP" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
                </td>
            </tr>

        <tr {if $counter eq 0} class="prototypedatabasefour" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttondatabasefour{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <input class="textwidth discount_database" readonly id="discount_database{$counter}" type="text" name="discount_database[]" value="{$data.ps_discount}"  class="iddatabasefour" placeholder="Discount %" title="Discount %" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth discount_amount_database" readonly id="discount_amount_database{$counter}" type="text" name="discount_amount_database[]" value="{$data.ps_discount_amount}"  placeholder="Discount Amount" title="Discount Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth offered_amount_database" maxlength="15" id="offered_amount_database{$counter}" type="text" name="offered_amount_database[]" value="{$data.ps_offered_amount}" onkeyup="getOfferedAmount(this.id, this.value,'database')"  placeholder="Offered Amount" title="Offered Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_database" readonly id="service_tax_amount_database{$counter}" type="text" name="service_tax_amount_database[]"  value="{$data.ps_service_tax_amount}" placeholder="Service Tax Amount" title="Service Tax Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
            <td>
                <input class="textwidth total_amount_database" readonly id="total_amount_database{$counter}" type="text" name="total_amount_database[]"  value="{$data.ps_total_amount}" placeholder="Total Amount" title="Total Amount" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" />
            </td>
        </tr>
            <tr {if $counter eq 0} class="prototype_gap" {/if}>
                <td colspan="6"> &nbsp;
                    <i class="deleteRow cursorPointer" id="buttondatabasegapfive{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
