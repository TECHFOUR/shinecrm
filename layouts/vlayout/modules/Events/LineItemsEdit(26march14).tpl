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

{*<!-- Code modified by jitendra singh [TECHFOUR] -->*}
    <table class="table table-bordered blockContainer lineItemTable" id="lineItemTab">
        <input type="hidden" name="TARGETLIMITVALU}" id="TARGETLIMITVALUE" class="rowNumber" value="{$TARGETLIMITVALUE}" />



        <tr id="row0" class="hide lineItemCloneCopy">
            {include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=0 data=[]}
        </tr>

        {if count($RELATED_PRODUCTS) eq 0}


            <tr id="row1" class="lineItemRow">
                {include file="LineItemsContent.tpl"|@vtemplate_path:$MODULE row_no=1 data=[]}
            </tr>


        {/if}

    </table>


    <div class="row-fluid verticalBottomSpacing">
        <div>
            <div class="btn-toolbar">
                    <span class="btn-group">
                        <button type="button" class="btn addButton" id="addProduct">
                            <i class="icon-plus icon-white"></i><strong>{vtranslate('Add More',$MODULE)}</strong>
                        </button>
                    </span>

            </div>

        </div>
    </div>
{*<!--
   <input type="hidden" name="totalProductCount" id="totalProductCount" value="{$row_no}" />
   <input type="hidden" name="subtotal" id="subtotal" value="" />
   <input type="hidden" name="total" id="total" value="" />

 -->*}
{/strip}