{literal}
<script>
    var total_active_payment_row = 1;
    $(document).ready(function() {
        var idpaymentone = "{/literal}{$COUNTER_PAYMENT_ASSIGN}{literal}";
        total_active_payment_row++;
        // Add button functionality
        $("table.dynatablepayment button.add").click(function() {
            idpaymentone++;
            var master = $(this).parents("table.dynatablepayment");
            // Get a new row based on the prototypepaymentone row
            var prot = master.find(".prototypepaymentone").clone();
            var css_val = "tr1";
            if(idpaymentone % 2 == 0)
                css_val = "tr2";
            prot.attr("class", css_val)
            prot.find(".idpaymentone").attr("value", idpaymentone);// row one
            prot.find(".deleteRow").attr("id", idpaymentone);// row one
            prot.find(".payment_mode").attr("id", "payment_mode__"+idpaymentone);// row one
            prot.find(".chequedate").attr("id", "chequedate__"+idpaymentone);// row one
            prot.find(".check_number").attr("id", "check_number__"+idpaymentone);// row one
            prot.find(".payble_at_location").attr("id", "payble_at_location__"+idpaymentone);// row one
            prot.find(".bank_name").attr("id", "bank_name__"+idpaymentone);// row one
            master.find("tbody").append(prot);

            // Get a new row based on the prototypepaymentone row
            var prot_two = master.find(".prototypepaymenttwo").clone();
            prot_two.attr("class", css_val)
            prot_two.find(".deleteRow").attr("id", "buttonpaymenttwo"+idpaymentone);// row one
            prot_two.find(".idpaymenttwo").attr("value", "__"+idpaymentone); // row two
            prot_two.find(".discount_payment").attr("id", "discount_payment__"+idpaymentone);// row one
            prot_two.find(".discount_amount_payment").attr("id", "discount_amount_payment__"+idpaymentone);// row one
            prot_two.find(".offered_amount_payment").attr("id", "offered_amount_payment__"+idpaymentone);// row one
            prot_two.find(".service_tax_amount_payment").attr("id", "service_tax_amount_payment__"+idpaymentone);// row one
            prot_two.find(".total_amount_payment").attr("id", "total_amount_payment"+idpaymentone);// row one
            master.find("tbody").append(prot_two);

            var prot_gap = master.find(".prototype_gap").clone();
            prot_gap.find(".deleteRow").attr("id", "buttonpaymentgapthree"+idpaymentone);// row one
            prot_gap.attr("class", "trgap");
            master.find("tbody").append(prot_gap);
        });

        // Remove button functionality
        $("table.dynatablepayment .deleteRow").live("click", function() {
            $("#buttonpaymenttwo"+this.id).click();
            $("#buttonpaymentgapthree"+this.id).click();
            $(this).parents("tr").remove();
            if($.isNumeric(this.id))
                total_active_payment_row--;
        });
    });

    //Start Code for payment


    function payment_mode(id,val){


        var index1 = id.split('__');
        var index = index1[1];

        document.getElementById('check_number__'+index).readOnly =true;
        document.getElementById('chequedate__'+index).readOnly =true;
        document.getElementById('bank_name__'+index).disabled=true;
        document.getElementById('discount_amount_payment__'+index).disabled=true;
        document.getElementById('payble_at_location__'+index).disabled=true;
        document.getElementById('offered_amount_payment__'+index).readOnly =true;
        document.getElementById('service_tax_amount_payment__'+index).readOnly =true;

        /*document.getElementById('check_number__'+index).value =" ";
         document.getElementById('chequedate__'+index).value =" ";
         document.getElementById('bank_name__'+index).value =" ";
         document.getElementById('discount_amount_payment__'+index).value =" ";

         document.getElementById('payble_at_location__'+index).value =" ";
         document.getElementById('offered_amount_payment__'+index).value =" ";
         document.getElementById('service_tax_amount_payment__'+index).value =" ";*/


        if(val == 'Cheque'){
            document.getElementById('check_number__'+index).readOnly =false;
            document.getElementById('chequedate__'+index).readOnly =false;
            document.getElementById('bank_name__'+index).disabled=false;
            document.getElementById('discount_amount_payment__'+index).disabled=false;
        }

        if(val == 'Credit'){
            document.getElementById('payble_at_location__'+index).disabled=false;
        }

        if(val == 'Online'){
            document.getElementById('discount_payment__'+index).disabled=false;
            document.getElementById('chequedate__'+index).readOnly =false;
        }

        if(val == 'Full Payment - Cheque'){
            document.getElementById('check_number__'+index).readOnly =false;
            document.getElementById('chequedate__'+index).readOnly =false;
            document.getElementById('bank_name__'+index).disabled=false;
        }

        if(val == 'Full Payment - Cash'){
            document.getElementById('chequedate__'+index).readOnly =false;
            document.getElementById('offered_amount_payment__'+index).readOnly =false;
        }

        if(val == 'TDC'){
            document.getElementById('service_tax_amount_payment__'+index).readOnly =false;
        }
    }


</script>

    <style>
        .dynatablepayment {
            border: solid 1px #000;
            border-collapse: collapse;
        }
        .dynatablepayment th,
        .dynatablepayment td {
            border: solid 1px #000;
            padding: 2px 2px;
            width: 170px;
            text-align: center;
        }

        .textwidth {
            width: 200px;;
        }
        .dynatablepayment .prototypepaymentone {
            display:none;
        }

        .dynatablepayment .prototypepaymenttwo {
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

<table class="dynatablepayment" style="width: 100%">
    <thead>
    <tr>
        <th style="width: 4%">Action</th>
        <th style="width: 19%"></th>
        <th style="width: 20%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"></th>
        <th style="width: 19%"><button class="add btn btn-success" type="button" >Add Payment</button></th>
    </tr>
    </thead>
    <tbody>
    {foreach item=data key=counter from=$PAYMENT_LIST}
        <tr {if $counter eq 0}class="prototypepaymentone" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if}{/if} >
            <td>
                <input type="hidden" name="record_mode_payment[]" {if $data.payment_mode ne ''} value="{$data.paymentid}" {else} value="0"   {/if} />
                <i {if $data.payment_mode eq ''}class="icon-trash deleteRow cursorPointer" {/if}  id="{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
            <td>
                <select class="textwidth payment_mode" id="payment_mode__{$counter}" name="payment_mode[]"  title="Payment Mode" onchange="payment_mode(this.id,this.value);" >
                    <option value="">Select Payment Mode</option>
                    {foreach item=paymode key=PICKLIST_NAME from=$PAYMENT_MODE_LIST}
                        <option value="{$paymode}" {if $paymode eq $data.payment_mode} selected="selected" {/if} >{$paymode}</option>
                    {/foreach}
                </select>
            </td>
            <td><input type="text" class=" chequedate" name="check_date[]" id="chequedate__{$counter}" value="{$data.checkdate}" placeholder="Cheque Date" title="Cheque Date" onclick="javascript:NewCssCal(this.id)" readonly="readonly" /></td>

            <td><input type="text" class=" check_number" id="check_number__{$counter}" name="check_number[]" value="{$data.checkno}" placeholder="Cheque Number" title="Cheque Number" readonly="true" /></td>

            <td><select class=" payble_at_location" id="payble_at_location__{$counter}" name="payble_at_location[]" value="{$data.ro_available}" placeholder="RO available" title="RO available" disabled="disabled">
                    <option value="">Select RO Available</option>
                    <option value="Yes" {if $data.ro_available eq 'Yes'} selected="selected" {/if} >Yes</option>
                    <option value="No" {if $data.ro_available eq 'No'} selected="selected" {/if} >No</option>

                </select>
            </td>


            <td><select class=" bank_name" id="bank_name__{$counter}" name="bank_name[]" value="{$data.bank_name}" placeholder="Drawee Bank" title="Drawee Bank" disabled="disabled">
                    <option value="">Select Drawee Bank</option>
                    {foreach item=bank_name key=PICKLIST_NAME from=$DRAWEE_BANK_LIST}
                        <option value="{$bank_name}" {if $bank_name eq $data.bank_name} selected="selected" {/if} >{$bank_name}</option>
                    {/foreach}

                </select>
            </td>



        </tr>

        <tr {if $counter eq 0} class="prototypepaymenttwo" {else} {if $counter%2 eq 0} class="tr2" {else}class="tr1"{/if} {/if} >
            <td>
                <i class="deleteRow cursorPointer" id="buttonpaymenttwo{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>

            <td><select class=" discount_payment" id="discount_payment__{$counter}" name="discount_payment[]" value="{$data.onlinemode}" placeholder="Online Mode" title="Online Mode" disabled="disabled">
                    <option value="">Select Online Mode</option>
                    <option value="Credit Card Link" {if $data.onlinemode eq 'Credit Card Link'} selected="selected" {/if} >Credit Card Link</option>
                    <option value="Citrus" {if $data.onlinemode eq 'Citrus'} selected="selected" {/if} >Citrus</option>

                </select>
            </td>


            <td>
                <input class=" offered_amount_payment"  id="offered_amount_payment__{$counter}" type="text" name="offered_amount_payment[]" value="{$data.slip_number}" onkeyup="getOfferedAmount(this.id, this.value,'payment')"  placeholder="Slip No." title="Slip No." readonly="readonly" />
            </td>
            <td>
                <input class="textwidth service_tax_amount_payment" readonly id="service_tax_amount_payment__{$counter}" type="text" name="service_tax_amount_payment[]"  value="{$data.tan_no}" placeholder="TAN No." title="TAN No."   readonly="readonly"/>
            </td>



            <td><select class=" discount_amount_payment" id="discount_amount_payment__{$counter}" name="discount_amount_payment[]" value="{$data.cts}" placeholder="CTS/Non-CTS" title="CTS/Non-CTS" disabled="disabled">
                    <option value="">Select CTS/Non-CTS</option>
                    <option value="Yes" {if $data.cts eq 'Yes'} selected="selected" {/if} >Yes</option>
                    <option value="No" {if $data.cts eq 'No'} selected="selected" {/if} >No</option>

                </select>
            </td>


            <td>
                <input class="textwidth total_amount_payment" id="total_amount_payment{$counter}" type="text" name="total_amount_payment[]"  value="{$data.amount}" placeholder="Total Amount" title="Total Amount" />
            </td>
        </tr>

        <tr {if $counter eq 0} class="prototype_gap" {/if}>
            <td colspan="6">&nbsp;
                <i class="deleteRow cursorPointer" id="buttonpaymentgapthree{$counter}" title="{vtranslate('LBL_DELETE',$MODULE)}" ></i>
            </td>
        </tr>
    {/foreach}
    </tbody>
</table>
