<?php

require_once('modules/Emails/mail.php');
include('config.php');

global $adb,$log, $current_user;
//echo $_SERVER['HTTP_REFERER'];
$type = $_REQUEST['type'];

switch($type){
    case 'UpsellDownsell':
        getUpsellDownsell();
        break;

    case 'Default':
        getDefault();
        break;

    case 'Offered':
        getOffered();
        break;

    case 'NEWOffered':
        getNEWOffered();
        break;

    case 'NextType':
        getNextType();
        break;
    case 'FREEJOB':
        getFreeJob();
        break;

}

function getFreeJob(){
    $entityvalue = $_REQUEST['entityvalue'];
    echo $entityvalue*100;
}

function getUpsellDownsell() {
    global $adb;
    $typevalue = $_REQUEST['typevalue'];
    $entityvalue = $_REQUEST['entityvalue'];
    $master_type = $_REQUEST['master_type'];
    $product_type_database = $_REQUEST['product_type_database'];
    $it_nonit_database = $_REQUEST['it_nonit_database'];
    $limits_database = $_REQUEST['limits_database'];
    $duration_val = $_REQUEST['duration_val'];
    $bottom_price_database = $_REQUEST['bottom_price_database'];
    $mrp_database = $_REQUEST['mrp_database'];
    $total_up_sell_amount = $_REQUEST['total_up_sell_amount'];
    $total_down_sell_amount = $_REQUEST['total_down_sell_amount'];
    $total_up_sell_amount_bp = $_REQUEST['total_up_sell_amount_bp'];
    $total_down_sell_amount_bp = $_REQUEST['total_down_sell_amount_bp'];

    $Qry = $adb->query("SELECT $typevalue, product_bottom_price, product_mrp FROM vtiger_vendor
                                INNER JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
                                WHERE vtiger_crmentity.deleted = 0 AND package_master = '$master_type' AND geography = '$product_type_database'
                                AND database_it = '$it_nonit_database' AND database_limit = '$limits_database' AND database_month = '$duration_val'");
    if($adb->num_rows($Qry) > 0) {
        $row = $adb->fetch_array($Qry);
        list($typevalue_percentage, $y) = explode("%", $row[$typevalue]);
        $database_bottom_price = str_replace(",","",$row['product_bottom_price']);
        $mrp_price = str_replace(",","",$row['product_mrp']);
    }

    // , upsell_word, upsell_excell, upsell_emailer, downsell_word, downsell_excel, downsell_emailer
    $percentage_value = 0;
    $credit_debit_flag = 0;
    switch($typevalue){
        case 'upsell_excell':
            $credit_debit_flag = 1;
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*3).'k')
                    break;
            }
            break;
        case 'upsell_word':
            $credit_debit_flag = 1;
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*2).'k')
                    break;
            }
            break;

        case 'upsell_emailer':
            $credit_debit_flag = 1;
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*5).'k')
                    break;
            }
            break;

        case 'upsell_login':
            $credit_debit_flag = 1;
            for($i=1; $i<= 5; $i++) {
                if($entityvalue === $i.'k')
                    break;
            }
            break;

        case 'downsell_excel':
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*3).'k')
                    break;
            }
            break;

        case 'downsell_word':
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*2).'k')
                    break;
            }
            break;

        case 'downsell_emailer':
            for($i=1; $i<= 3; $i++) {
                if($entityvalue === ($i*5).'k')
                    break;
            }
            break;

        default :
            $i = 0;
    }
    if($entityvalue == '')
        $i = 0;
    $percentage_value = $typevalue_percentage * $i;
    $service_tax = getTaxValue($master_type);
    // echo "$entityvalue -------------$percentage_value-> $service_tax  $typevalue_percentage ---- $i ";die;
    if($percentage_value != 0) {
        $percentage_value_bp = ($percentage_value*$database_bottom_price)/100;
        $percentage_value_mrp = ($percentage_value*$mrp_price)/100;
    }
    else {
        $percentage_value_bp = 0.0;
        $percentage_value_mrp = 0.0;
    }

    $final_up_down_sell_amount = $total_up_sell_amount - $total_down_sell_amount;
    $final_up_down_sell_amount_bp = $total_up_sell_amount_bp - $total_down_sell_amount_bp;
    $up_down_amount = $percentage_value_mrp;
    $up_down_amount_bp = $percentage_value_bp;

    if($credit_debit_flag == 1) {
        $total_percentage_value_mrp =  $final_up_down_sell_amount + $percentage_value_mrp;
        $total_percentage_value_bp =  $final_up_down_sell_amount_bp + $percentage_value_bp;
        $total_bp = $database_bottom_price + $total_percentage_value_bp;
        $total_mrp = $mrp_price + $total_percentage_value_mrp;
    }
    else {
        $total_percentage_value_mrp =  $final_up_down_sell_amount - $percentage_value_mrp;
        $total_percentage_value_bp =  $final_up_down_sell_amount_bp - $percentage_value_bp;
        $total_bp = $database_bottom_price + $total_percentage_value_bp;
        $total_mrp = $mrp_price + $total_percentage_value_mrp;
    }

//echo "$mrp_price-> $total_mrp  $bottom_price_database ---- $total_bp ";die;
    $service_tax_amount = ($total_mrp * $service_tax)/100;
    $total_amount = $total_mrp + $service_tax_amount;

    echo number_format($total_bp, 2).'###'.number_format($total_mrp, 2).'###'.number_format($discount_percentage, 2).'###'.number_format($discount_amount, 2)
        .'###'.number_format($total_mrp, 2).'###'.number_format($service_tax_amount, 2).'###'.number_format($total_amount, 2)
        .'###'.number_format($up_down_amount, 2).'###'.number_format($up_down_amount_bp, 2);
    die;
}

function getDefault() {
    $entityvalue = $_REQUEST['entityvalue'];
    $master_type = $_REQUEST['master_type'];
    list($noofcompany, $vendorid) = explode("__", $entityvalue);

    $bottom_price =  "product_bottom_price";
    $mrp =  "product_mrp";

    $sub_qry = "AND vtiger_vendorcf.vendorid = $vendorid";
    $result = getBottomMRPPrice($bottom_price, $mrp, $sub_qry);
    $education_bottom_price = $result['bottom_price'];
    $education_mrp = $result['mrp'];
    $percentage = getTaxValue($master_type);
    $service_tax_amount = ($education_mrp * $percentage)/100;
    $total_amount = $education_mrp + $service_tax_amount;

    echo number_format($education_bottom_price, 2).'###'.number_format($education_mrp, 2).'###'.number_format($service_tax_amount, 2).'###'.number_format($total_amount, 2);
    die;
}

function getOffered() {
    echo $percentage = getTaxValue($master_type);
    die;
}

function getNEWOffered() {
    $entityvalue = $_REQUEST['entityvalue'];
    $master_type = $_REQUEST['master_type'];
    $previous_value = $_REQUEST['previous_value'];
    $bottom_price =  "product_bottom_price";
    $mrp =  "product_mrp";
    $previousonevalue = $_REQUEST['previousonevalue'];
    $sub_qry = "";
    if($master_type == 'events') {
        $sub_qry = "AND event_product = '$previous_value' AND event_sponsorship = '$previousonevalue'";
    }
    elseif($master_type == 'inventory'){
        list($x, $vendorid) = explode("__", $previousonevalue);
        $sub_qry = "AND vtiger_vendorcf.vendorid = $vendorid";
    }

    elseif($master_type == 'emshineverified'){
        list($x, $vendorid) = explode("__", $previous_value);
        $sub_qry = "AND vtiger_vendorcf.vendorid = $vendorid";
    }

    elseif($master_type == 'smartjobs'){
        list($x, $vendorid) = explode("__", $previous_value);
        $sub_qry = "AND vtiger_vendorcf.vendorid = $vendorid";
    }
    else {
        list($x, $vendorid) = explode("__", $previous_value);
        $sub_qry = "AND vtiger_vendorcf.vendorid = $vendorid";
    }

    $service_tax = getTaxValue($master_type);
    $result = getBottomMRPPrice($bottom_price, $mrp, $sub_qry);
    $bottom_price = $result['bottom_price'];
    $mrp = $result['mrp'];
    if($entityvalue > 0) {
        $bottom_price = $bottom_price*$entityvalue;
        $mrp = $mrp*$entityvalue;
    }
    $new_service_tax_amount = ($mrp*$service_tax)/100;
    $total_amount = $mrp + $new_service_tax_amount;
    echo number_format($bottom_price, 2).'###'.number_format($mrp, 2).'###'.number_format($new_service_tax_amount, 2).'###'.number_format($total_amount, 2);
    die;
}

function getNextType() {
    global $adb;
    $entityvalue = $_REQUEST['entityvalue'];
    $master_type = $_REQUEST['master_type'];
    $selectfieldname = $_REQUEST['selectfieldname'];
    $argument = $_REQUEST['argument'];
    $previous_value = $_REQUEST['previous_value'];
    $previous_value_one = $_REQUEST['previous_value_one'];
    $sub_query = '';
    if($master_type === 'inventory') {
        $product_type = $master_type."_product";
        if($argument == 1)
            $sub_query = " $product_type = '$entityvalue' group by tg_database ";

        elseif($argument == 2) {
            $sub_query = " $product_type = '$previous_value' AND tg_database = '$entityvalue' ";
        }
    }

    elseif($master_type === 'events') {
        $product_type = "event_product";
        if($argument == 1)
            $sub_query = " $product_type = '$entityvalue'";

        elseif($argument == 2) {
            list($value, $id) = explode("__", $entityvalue);
            $sub_query = " $product_type = '$previous_value' AND vendorname = $value ";
        }
    }

    elseif($master_type === 'smartmatch') {
        $product_type = "smartmatch_month";
        if($argument == 1)
            $sub_query = " $product_type = '$entityvalue'";
    }

    elseif($master_type === 'flexihire') {
        $product_type = "flexi_geography";
        if($argument == 1)
            $sub_query = " $product_type = '$entityvalue'  group by $selectfieldname ";

        elseif($argument == 2) {
            $sub_query = " $product_type = '$previous_value' AND flexi_access = '$entityvalue' ";
        }
    }

    else if($master_type === 'database') {
        $product_type = $master_type."_product";
        if($argument == 1)
            $sub_query = " geography = '$entityvalue' group by $selectfieldname ";

        elseif($argument == 2) {
            $sub_query = " geography = '$previous_value' AND database_it = '$entityvalue' group by $selectfieldname ";
        }

        elseif($argument == 3) {
            list($value, $id) = explode("__", $entityvalue);
            $sub_query = " geography = '$previous_value' AND database_it = '$previous_value_one' AND database_limit = '$value'";
        }
    }
    else  {
        $product_type = $master_type."_product";
        if($entityvalue == 'Top Company  Logo on Jobs  Logo on JD Page')
            $entityvalue = 'Top Company+ Logo on Jobs+ Logo on JD Page';
        $sub_query = " $product_type = '$entityvalue'";
    }

    $option = "<option>"."Select an Option"."</option>";
    $q = "SELECT $selectfieldname, vtiger_vendor.vendorid as venid FROM vtiger_vendor
                                INNER JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
                                WHERE vtiger_crmentity.deleted = 0 AND package_master = '$master_type' AND $sub_query ";
    $Qry = $adb->query($q);
    if($adb->num_rows($Qry) > 0) {
        while($row = $adb->fetch_array($Qry)) {
            $entityvalue = $row[$selectfieldname];
            $vendorid = $row['venid'];
            $value = $entityvalue."__".$vendorid;
            if(($master_type === 'flexihire' || $master_type === 'inventory' || $master_type === 'database') && $argument == 1)
                $option .= "<option value='$entityvalue'>".$entityvalue."</option>";
            elseif(($master_type === 'database') && $argument == 2)
                $option .= "<option value='$entityvalue'>".$entityvalue."</option>";
            else
                $option .= "<option value='$value'>".$entityvalue."</option>";
        }
    }
    echo $option;die();
}

function getTaxValue($master_type){
    global $adb;
    $Qry_ServiceTax = $adb->query("SELECT percentage FROM vtiger_inventorytaxinfo WHERE deleted = 0 AND taxlabel LIKE '%$master_type%'");
    if($adb->num_rows($Qry_ServiceTax) > 0) {
        $row = $adb->fetch_array($Qry_ServiceTax);
        return $row['percentage'];
    }
}

function getBottomMRPPrice($bottom_price, $mrp, $sub_qry){
    global $adb;
    $price_array = array();

    $Qry = $adb->query("SELECT $bottom_price, $mrp  FROM vtiger_vendor
                                INNER JOIN vtiger_vendorcf ON vtiger_vendorcf.vendorid = vtiger_vendor.vendorid
                                INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_vendor.vendorid
                                WHERE vtiger_crmentity.deleted = 0 $sub_qry ");
    if($adb->num_rows($Qry) > 0) {
        $row = $adb->fetch_array($Qry);
        $price_array['bottom_price'] = $row[$bottom_price];
        $price_array['mrp']= $row[$mrp];
    }
    return $price_array;
}