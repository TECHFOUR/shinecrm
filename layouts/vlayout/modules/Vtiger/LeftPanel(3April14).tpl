<link type="text/css" href="popupdragable/jquery_002.css" rel="stylesheet">
				<script type="text/javascript" src="popupdragable/index.js"></script>

		<script type="text/javascript" src="popupdragable/jquery_004.js"></script>

	<div id="example8" class="example_block">

   <div class="demo">
				<div id="window_block8" style="display:none;">
                    {if $MODULE eq 'Events' || $MODULE eq 'Calendar'}
                        <div style="padding:10px;">

                            <div  style="font-size:12px;">
                                <div style="color:#FECC00" title="{$client_id}"><b><u><a style="color:#FECC00" href="index.php?module=Contacts&view=Detail&record={$contact_id}&mode=showDetailViewByMode&requestMode=full" target="_blank">Customer Information</a></u></b></div>

                                <table>

                                    <tr><td style="color:#999999">&nbsp;</td><td>&nbsp;</td></tr>
                                  <tr><td style="color:#999999">Name of Client</td><td>{if $name_of_client eq ''}- -{else}: {$name_of_client}{/if}</td></tr>

                                    <tr><td style="color:#999999">Type of Client</td><td>{if $type_of_client eq ''}- -{else}: {$type_of_client}{/if}</td></tr>

                                    <tr><td style="color:#999999">Contact Person</td><td>{if $contct_person eq ''}- -{else}: {$contct_person}{/if}</td></tr>

                                    <tr><td style="color:#999999">Mobile No.</td><td>{if $mobile_no eq ''}- -{else}: {$mobile_no}{/if}</td></tr>

                                    <tr><td style="color:#999999">City</td><td>{if $city eq ''}- -{else}: {$city}{/if}</td></tr>
                                </table>
                            </div>


                        </div>
                    {/if}

					<div style="padding:10px;">

                            <div  style="font-size:12px;">
                                <div style="color:#FECC00"><b><u>Account Manager Info</u></b></div>

                                <table>
                                    <tr><td style="color:#999999">&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td style="color:#999999;font-size: 12px;">Branch</td><td>{if $Branch eq ''}- -{else}: {$Branch}{/if}</td></tr>

                                    <tr><td style="color:#999999">Team</td><td>{if $TEAM eq ''}- -{else}: {$TEAM}{/if}</td></tr>

                                    <tr><td style="color:#999999">BSM</td><td>{if $BSM eq ''}- -{else}: {$BSM}{/if}</td></tr>

                                    <tr><td style="color:#999999">KAM</td><td>{if $BTL eq ''}- -{else}: {$BTL}{/if}</td></tr>

                                    <tr><td style="color:#999999">AM Mobile</td><td>{if $Account_Manager_Contact eq ''}- -{else}: {$Account_Manager_Contact}{/if}</td></tr>

                                    <tr><td style="color:#999999">AM Email</td><td>{if $Account_Manager_Email eq ''}- -{else}: {$Account_Manager_Email}{/if}</td></tr>
                                </table>
                            </div>


					</div>

				</div>
				<!--<input value="Click here to know about MFCS Lead related information" onClick="createCustWindow();" type="button">-->
			</div>
		</div>