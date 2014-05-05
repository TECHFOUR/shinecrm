{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}


{strip}

<div class='popup'>
<div class='content'>
<img src='calculator/images/event-close.png' alt='quit' class='x' id='x' />

<form action="" id="calculator" name="calculator" method="post">
<table width="100%"   class="table table-bordered listViewEntriesTable">
<tr class="listViewHeaders">
<th>Package Sold Calculator</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
<th>&nbsp;</th>
</tr>
<tr>
<td>Product</td>

<td>
<select name="product" id="product" onchange="active_form(this.value);showProduct(this.value);" >
<option>Select an Option</option>
<option>EVENTS</option>
<option>INVENTORY</option>
<option>PRINT</option>
<option>DATABASE</option>
<option>EDUCATION</option>
<option>LOGO</option>
<option>SMARTMATCH</option>
<option>EMSHINEVERIFIED</option>
<option>FLEXI HIRE</option>
<option>SMART JOBS</option>
</select>
</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>


<!--Start Event form -->

<div id="event_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="event_product_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td>Sponsorship</td>
<td  id="sponser_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>


<tr>
<td >Month</td>
<td><input type="text" name="event_month" id="event_month"/></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="event_bottom_price" id="event_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="event_mrp" id="event_mrp" readonly="readonly"/></td>

</tr>
</table>
</div>
<!--Start Event form -->


<!--Start Inventory form -->

<div id="inventory_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="inventory_product_type_tab">
<select name="inventory_product_type" id="inventory_product_type">
<option>Select an Option</option>
</select>
</td>
<td >TG Database</td>
<td id="inventory_database_tab">
<select name="inventory_database" id="inventory_database">
<option>Select an Option</option>
</select>
</td>
</tr>


<tr>
<td>Active</td>
<td id="inventory_active_tab">
<select name="inventory_active" id="inventory_active">
<option>Select an Option</option>
</select>
</td>
<td>No. of Emailer</td>
<td><input type="text" name="inventory_emailers" id="inventory_emailers" onkeyup="emailer_cal();"/></td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="inventory_bottom_price" id="inventory_bottom_price" readonly="readonly"/></td>
<td>MRP</td>
<td><input type="text" name="inventory_mrp" id="inventory_mrp" readonly="readonly"/></td>
</tr>
</table>
</div>
<!--End Inventory form -->


<!--Start Print form -->

<div id="print_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="print_product_type_tab">
<select name="print_product_type" id="print_product_type">
<option>Select an Option</option>
</select>
</td>
<td>Size</td>
<td><input type="text" name="print_size" id="print_size" onkeyup="print_price_cal();"/></td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="print_bottom_price" id="print_bottom_price" readonly="readonly"/></td>
<td>MRP</td>
<td><input type="text" name="print_mrp" id="print_mrp" readonly="readonly"/></td>
</tr>


</table>

</div>
<!--End Print form -->

<!--Start Database form -->

<div id="database_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Geography</td>
<td id="database_geography_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >IT/ Non - IT</td>
<td id="database_it_non_it_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>

<input type="hidden"  id="master_upsell_word" />
<input type="hidden"  id="master_upsell_excel" />
<input type="hidden"  id="master_upsell_emailer" />
<input type="hidden"  id="master_downsell_word" />
<input type="hidden"  id="master_downsell_excel" />
<input type="hidden"  id="master_downsell_emailer" />
<input type="hidden"  id="master_upsell_login" />

<input type="hidden"  id="master_bottom_price" />
<input type="hidden"  id="master_mrp" />

<input type="hidden"  id="temp_bottom_price" />
<input type="hidden"  id="temp_mrp" />

<tr>
<td>Limits</td>
<td id="database_limit_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td>Duration(Months)</td>
<td id="database_duration_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>

<tr>
<td>Upsell Excel(K)</td>
<td>
<select name="upsell_excel" id="upsell_excel" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">3K</option>
<option value="2">6K</option>
<option value="3">9K</option>
</select>
</td>

<td>Downsell Excel(K)</td>
<td>
<select name="downsell_excel" id="downsell_excel" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">3K</option>
<option value="2">6K</option>
<option value="3">9K</option>
</select>
</td>
</tr>

<tr>
<td>Upsell Word(K)</td>
<td>
<select name="upsell_word" id="upsell_word" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">2K</option>
<option value="2">4K</option>
<option value="3">6K</option>
</select>
</td>

<td>Downsell Word(K)</td>
<td>
<select name="downsell_word" id="downsell_word" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">2K</option>
<option value="2">4K</option>
<option value="3">6K</option>
</select>
</td>

</tr>


<tr>


</tr>

<tr>

<td>Upsell E-Mailers(K)</td>
<td>
<select name="upsell_emailer" id="upsell_emailer" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">5K</option>
<option value="2">10K</option>
<option value="3">15K</option>
</select>
</td>
<td>Downsell E-Mailers(K)</td>
<td>
<select name="downsell_emailer" id="downsell_emailer" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">5K</option>
<option value="2">10K</option>
<option value="3">15K</option>
</select>
</td>
</tr>

<tr>

<td>Upsell Login(K)</td>
<td>
<select name="upsell_login" id="upsell_login" onchange="get_upsell_downsell();">
<option value="0">Select an Option</option>
<option value="1">1K</option>
<option value="2">2K</option>
<option value="3">3K</option>
<option value="4">4K</option>
<option value="5">5K</option>
</select>
</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="database_bottom_price" id="database_bottom_price" readonly="readonly"/></td>
<td>MRP</td>
<td><input type="text" name="database_mrp" id="database_mrp" readonly="readonly"/></td>
</tr>

</table>

</div>
<!--End Database form -->



<!--Start Education form -->

<div id="education_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="education_product_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >No. of Companies</td>
<td><input type="text" name="no_of_company" id="no_of_company" onkeyup="education_price_cal();"/></td>
</tr>


<tr>
<td>Bottom Price</td>
<td><input type="text" name="education_bottom_price" id="education_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="education_mrp" id="education_mrp" readonly="readonly"/></td>
</tr>
</table>
</div>
<!--End Education form -->

<!--Start Logo form -->

<div id="logo_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="logo_product_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >Duration(Months)</td>
<td id="logo_month_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>


<tr>
<td>Bottom Price</td>
<td><input type="text" name="logo_bottom_price" id="logo_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="logo_mrp" id="logo_mrp" readonly="readonly"/></td>
</tr>
</table>
</div>
<!--End Logo form -->


<!--Start Smart match form -->

<div id="smartmatch_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>

<td >No. of Jobs</td>
<td id="smartmatch_no_of_jobs_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >Duration(Months)</td>
<td id="smartmatch_month_tab">
<select>
<option>Select an Option</option>
</select>
</td>

</tr>


<tr>
<td>Bottom Price</td>
<td><input type="text" name="smartmatch_bottom_price" id="smartmatch_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="smartmatch_mrp" id="smartmatch_mrp" readonly="readonly"/></td>
</tr>
</table>
</div>
<!--End Smart match form -->



<!--Start Elite Mind form -->

<div id="elitemind_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="elite_product_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >No of Walk In</td>
<td><input type="text" name="elitemind_walkin" id="elitemind_walkin" onkeyup="elite_price_cal(this.value);"/></td>
</tr>


<tr>
<td>Requirement Details</td>
<td><input type="text" name="elitemind_requirement" id="elitemind_requirement" /></td>

<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="elitemind_bottom_price" id="elitemind_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="elitemind_mrp" id="elitemind_mrp" readonly="readonly"/></td>
</tr>

</table>
</div>
<!--End Elite Mind form -->

<!--Start Flexi Mind form -->

<div id="flexi_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="flexi_product_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >Geography</td>
<td id="flexi_geography_type_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>


<tr>
<td >Access</td>
<td id="flexi_access_tab">
<select>
<option>Select an Option</option>
</select>
</td>
<td >Duration</td>
<td id="flexi_duration_tab">
<select>
<option>Select an Option</option>
</select>
</td>
</tr>

<tr>
<td>Bottom Price</td>
<td><input type="text" name="flexi_bottom_price" id="flexi_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="flexi_mrp" id="flexi_mrp" readonly="readonly"/></td>
</tr>

</table>
</div>
<!--End Flexi Mind form -->


<!--Start Smart Job  form -->

<div id="smartjob_form" action="" style="display:none">
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td >Product Type</td>
<td id="smartjob_product_type_tab">
<select id="smartjob_product_type" name="smartjob_product_type">
<option>Select an Option</option>
</select>
</td>
<td >No of Jobs</td>
<td><input type="text" name="smartjob_no_of_job" id="smartjob_no_of_job" onkeyup="smartjob_price_cal(this.value);"/></td>
</tr>


<tr>
<td>Bottom Price</td>
<td><input type="text" name="smartjob_bottom_price" id="smartjob_bottom_price" readonly="readonly"/></td>

<td>MRP</td>
<td><input type="text" name="smartjob_mrp" id="smartjob_mrp" readonly="readonly"/>



</td>
</tr>
</table>
</div>
<!--End Smart Job form -->




<!-- Start Common Fields -->
<table width="100%" class="table table-bordered listViewEntriesTable">

<tr>
<td>Discount(%)</td>
<td><td><input type="text" name="discount" id="discount" readonly="readonly"/></td></td>
<td>Discount Amount</td>
<td><input type="text" name="discount_amount" id="discount_amount" readonly="readonly"/></td>
</tr>

<tr>
<td>Offered Amount</td>
<td><td><input type="text" name="offered_amount" id="offered_amount" onkeyup="price_calculate(this.value);"/></td></td>
<td>Service Tax Amount</td>
<td><input type="text" name="serice_tax_amount" id="serice_tax_amount" readonly="readonly"/>
	<input type="hidden" name="test_serice_tax_amount" id="test_serice_tax_amount" readonly="readonly"/>
</td>
</tr>

<tr>
<td>Total Amount</td>
<td><td><input type="text" name="total_amount" id="total_amount" readonly="readonly"/></td></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
</table>

<!-- End Common Fields -->



</div>
</div> 
</form>        
<div id='container'>
<a href='' class='click' style="color: #393"><img src='calculator/images/calculator.png' /></a> <br/>
</div>
{/strip}

