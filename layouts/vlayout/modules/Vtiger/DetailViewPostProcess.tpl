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
	{assign var="MODULE_NAME" value=$MODULE_MODEL->get('name')}

					</div>
				</form>
			</div>
			<div class="related span2 marginLeftZero">
				<div class="">
					<ul class="nav nav-stacked nav-pills">
						{foreach item=RELATED_LINK from=$DETAILVIEW_LINKS['DETAILVIEWTAB']}
                        
                        {*To hide *}
                        {if $RELATED_LINK->getLabel() eq 'ModComments' || $RELATED_LINK->getLabel() eq 'LBL_UPDATES'}
                        {else}
						<li class="{if $RELATED_LINK->getLabel()==$SELECTED_TAB_LABEL}active{/if}" data-url="{$RELATED_LINK->getUrl()}&tab_label={$RELATED_LINK->getLabel()}" data-label-key="{$RELATED_LINK->getLabel()}" data-link-key="{$RELATED_LINK->get('linkKey')}" >
							<a href="javascript:void(0);" class="textOverflowEllipsis" style="width:auto" title="{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}"><strong>{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}</strong></a>
						</li>
                        {/if}
						{/foreach}
                         
                            
                         
                        {foreach item=RELATED_LINK from=$DETAILVIEW_LINKS['DETAILVIEWRELATED']}
                 {*<!--Add by Raghvender Singh 01052014-->*}
                 	       
                        
                     
                            
                        {*<!-- Code Starts for Sales Approval by Raghvender Singh 30042014 -->*}                        
                     	{if $RELATED_LINK->getLabel() eq 'Sales Approval'}
                        	
                        	<li class="{if $RELATED_LINK->getLabel()==$SELECTED_TAB_LABEL}active{/if}" data-url="{$RELATED_LINK->getUrl()}&tab_label={$RELATED_LINK->getLabel()}" data-label-key="{$RELATED_LINK->getLabel()}" >
							{* Assuming most of the related link label would be module name - we perform dual translation *}
                            
							{assign var="DETAILVIEWRELATEDLINKLBL" value= vtranslate($RELATED_LINK->getLabel(), $RELATED_LINK->getLabel())}				
                            <a href="javascript:void(0);" class="textOverflowEllipsis" style="width:auto" title="{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}"><strong>{$DETAILVIEWRELATEDLINKLBL}</strong></a>
                           </li>
                           {*<!--End Code Starts for Sales Approval by Raghvender Singh 01052014-->*}  
                           {*<!--Code Starts for Approval Information by Raghvender Singh 01052014-->*}            
                        {else if $RELATED_LINK->getLabel() eq 'Approval Information'}
                        	
                            {if $DIFFERENCE_AMOUNT == 'yes' && $PAYMENT_MODE_APPROVAL == 'yes' && $TDS_AMOUNT_MATCH == 'yes'}
                            
                            	<li class="{if $RELATED_LINK->getLabel()==$SELECTED_TAB_LABEL}active{/if}" data-url="{$RELATED_LINK->getUrl()}&tab_label={$RELATED_LINK->getLabel()}" data-label-key="{$RELATED_LINK->getLabel()}" >
							{* Assuming most of the related link label would be module name - we perform dual translation *}
                            
							{assign var="DETAILVIEWRELATEDLINKLBL" value= vtranslate($RELATED_LINK->getLabel(), $RELATED_LINK->getLabel())}				
                            <a href="javascript:void(0);" class="textOverflowEllipsis" style="width:auto" title="{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}"><strong>{$DETAILVIEWRELATEDLINKLBL}</strong></a>
                            </li>
                            {else}    
                              
                                <div class="dv"><a class="textOverflowEllipsis" onclick="return showApprovalinformationDatabase({$APP_RECORD_ID})" style="width:auto;"><strong>Approval Information</strong></a></div>                 
                            {/if}
                           {*<!--End Code Starts for Approval Information by Raghvender Singh 01052014-->*} 
                           {*<!--Code Starts for Others by Raghvender Singh 01052014-->*}   
                     	 {else}
                     
                <li class="{if $RELATED_LINK->getLabel()==$SELECTED_TAB_LABEL}active{/if}" data-url="{$RELATED_LINK->getUrl()}&tab_label={$RELATED_LINK->getLabel()}" data-label-key="{$RELATED_LINK->getLabel()}" >
							{* Assuming most of the related link label would be module name - we perform dual translation *}
                            
							{assign var="DETAILVIEWRELATEDLINKLBL" value= vtranslate($RELATED_LINK->getLabel(), $RELATED_LINK->getLabel())}				
                            <a href="javascript:void(0);" class="textOverflowEllipsis" style="width:auto" title="{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}"><strong>{$DETAILVIEWRELATEDLINKLBL}</strong></a>
                           
						</li>
                        
                       {/if} 
                       {*<!--End Code for Others by Raghvender Singh 01052014-->*} 
                      {*<!--End Add by Raghvender Singh 01052014-->*}	  
						{/foreach}
                        {if $MODULE_NAME eq Contacts}
                         {if ($date_validation == 'yes' && $lead_won == 'yes' && $post_call_update == 'yes')}
                           
                           <div class="dv" data-url="{$RELATED_LINK->getUrl()}&tab_label={$RELATED_LINK->getLabel()}" data-label-key="{$RELATED_LINK->getLabel()}" data-link-key="{$RELATED_LINK->get('linkKey')}">	
                            
                            {assign var="DETAILVIEWRELATEDLINKLBL" value= vtranslate($RELATED_LINK->getLabel(), $RELATED_LINK->getLabel())}				
                            
							<a href="index.php?module=Potentials&view=Edit&picklistDependency=[]&potentialname=&opportunity_type=Single+Payment&assigned_user_id=1&sourceModule=Contacts&sourceRecord={$RECORDID}&relationOperation=true" class="textOverflowEllipsis" style="width:auto" title="{vtranslate($RELATED_LINK->getLabel(),{$MODULE_NAME})}"><strong>Add Sales Approval</strong></a>
                        </div>
                           	{else}
                             {*<!--Change by Raghvender Singh on 02052014-->*}
                            <div class="dv"><a class="textOverflowEllipsis" onclick="return showSalesapprovalDatabase({$RECORDID})" style="width:auto; " ><strong>Initiate Sales Approval</strong></a></div>	{*<!--End Change by Raghvender Singh on 02052014-->*}
                           {/if}
                         {/if}
					</ul>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>
</div>

{/strip}

 							