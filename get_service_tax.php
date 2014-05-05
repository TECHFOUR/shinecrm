<?php
function getTaxValue($master_type){
    global $adb;
    $Qry_ServiceTax = $adb->query("SELECT percentage FROM vtiger_inventorytaxinfo WHERE deleted = 0 AND taxlabel LIKE '%$master_type%'");
    if($adb->num_rows($Qry_ServiceTax) > 0) {
        $row = $adb->fetch_array($Qry_ServiceTax);
        return $row['percentage'];
    }
	
}
?>