<?php


//This is the access privilege file
$is_admin=false;

$current_user_roles='H4';

$current_user_parent_role_seq='H1::H2::H3::H4';

$current_user_profiles=array(2,);

$profileGlobalPermission=array('1'=>1,'2'=>1,);

$profileTabsPermission=array('1'=>0,'2'=>0,'3'=>0,'4'=>0,'6'=>1,'7'=>0,'8'=>0,'9'=>0,'10'=>0,'13'=>1,'14'=>0,'15'=>1,'16'=>0,'18'=>1,'19'=>1,'20'=>1,'21'=>1,'22'=>1,'23'=>1,'24'=>1,'25'=>0,'26'=>0,'27'=>0,'30'=>0,'31'=>1,'32'=>0,'33'=>0,'34'=>0,'35'=>1,'36'=>0,'37'=>1,'38'=>0,'39'=>1,'40'=>1,'41'=>0,'42'=>1,'43'=>1,'44'=>0,'45'=>1,'46'=>1,'47'=>1,'28'=>0,);

$profileActionPermission=array(2=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>1,6=>1,10=>0,),4=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>1,6=>1,8=>0,10=>0,),6=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>1,6=>1,8=>0,10=>0,),7=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>1,6=>1,8=>0,9=>0,10=>0,),8=>array(0=>0,1=>0,2=>1,3=>0,4=>0,6=>1,),9=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>0,6=>0,),13=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>1,6=>1,8=>0,10=>0,),14=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>1,6=>1,10=>0,),15=>array(0=>0,1=>0,2=>0,3=>0,4=>0,),16=>array(0=>0,1=>0,2=>1,3=>0,4=>0,),18=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>1,6=>1,10=>0,),19=>array(0=>0,1=>0,2=>0,3=>0,4=>0,),20=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,),21=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,),22=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,),23=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,),26=>array(0=>1,1=>1,2=>1,3=>0,4=>0,),34=>array(0=>0,1=>0,2=>1,3=>0,4=>0,5=>0,6=>0,10=>0,),35=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,10=>0,),37=>array(0=>0,1=>0,2=>0,3=>0,4=>0,),41=>array(0=>0,1=>0,2=>1,3=>0,4=>0,),42=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,10=>0,),43=>array(0=>1,1=>1,2=>1,3=>0,4=>1,5=>0,6=>0,10=>0,),44=>array(0=>1,1=>1,2=>1,3=>0,4=>0,5=>0,6=>0,10=>0,),46=>array(0=>0,1=>0,2=>0,3=>0,4=>0,),);

$current_user_groups=array();

$subordinate_roles=array('H13','H14','H15','H16','H17','H5','H18','H19','H20','H21','H22','H23','H24','H25','H26','H27','H28','H29','H31','H32','H33','H34','H35','H36','H37','H38','H39','H40','H41','H42','H43','H44','H45','H46','H47','H48','H49','H50','H51','H52','H53','H54','H55','H56','H57','H58','H59','H60','H61','H62','H63','H64','H65','H66','H67','H68','H69','H70','H71','H72','H73','H74','H75','H76','H77','H78','H79','H80','H81','H82','H83','H84','H85','H86','H87','H88','H89','H90','H91','H92','H93','H94','H95','H96','H97','H98','H99','H100',);

$parent_roles=array('H1','H2','H3',);

$subordinate_roles_users=array('H13'=>array(),'H14'=>array(),'H15'=>array(),'H16'=>array(),'H17'=>array(),'H5'=>array(),'H18'=>array(6,),'H19'=>array(8,),'H20'=>array(9,),'H21'=>array(10,),'H22'=>array(),'H23'=>array(),'H24'=>array(),'H25'=>array(),'H26'=>array(),'H27'=>array(),'H28'=>array(),'H29'=>array(),'H31'=>array(),'H32'=>array(),'H33'=>array(),'H34'=>array(),'H35'=>array(),'H36'=>array(),'H37'=>array(),'H38'=>array(),'H39'=>array(),'H40'=>array(),'H41'=>array(),'H42'=>array(),'H43'=>array(),'H44'=>array(),'H45'=>array(),'H46'=>array(),'H47'=>array(),'H48'=>array(),'H49'=>array(),'H50'=>array(),'H51'=>array(),'H52'=>array(),'H53'=>array(),'H54'=>array(),'H55'=>array(),'H56'=>array(),'H57'=>array(),'H58'=>array(),'H59'=>array(),'H60'=>array(),'H61'=>array(),'H62'=>array(),'H63'=>array(),'H64'=>array(),'H65'=>array(),'H66'=>array(),'H67'=>array(),'H68'=>array(),'H69'=>array(),'H70'=>array(),'H71'=>array(),'H72'=>array(),'H73'=>array(),'H74'=>array(),'H75'=>array(),'H76'=>array(),'H77'=>array(),'H78'=>array(),'H79'=>array(),'H80'=>array(),'H81'=>array(),'H82'=>array(),'H83'=>array(),'H84'=>array(),'H85'=>array(),'H86'=>array(),'H87'=>array(),'H88'=>array(),'H89'=>array(),'H90'=>array(),'H91'=>array(),'H92'=>array(),'H93'=>array(),'H94'=>array(),'H95'=>array(),'H96'=>array(),'H97'=>array(),'H98'=>array(),'H99'=>array(),'H100'=>array(),);

$user_info=array('user_name'=>'nsm','is_admin'=>'off','user_password'=>'$1$ns000000$T.3yIKeEasy9kequhLWq51','confirm_password'=>'$1$ns000000$T.3yIKeEasy9kequhLWq51','first_name'=>'Balveer','last_name'=>'Bera','roleid'=>'H4','email1'=>'balveerb@techfoursolutions.com','status'=>'Active','activity_view'=>'Today','lead_view'=>'Today','hour_format'=>'12','end_hour'=>'','start_hour'=>'00:00','title'=>'Mr.','phone_work'=>'','department'=>'Sales','phone_mobile'=>'9799046708','reports_to_id'=>'13','phone_other'=>'','email2'=>'','phone_fax'=>'','secondaryemail'=>'','phone_home'=>'','date_format'=>'mm-dd-yyyy','signature'=>'','description'=>'','address_street'=>'','address_city'=>'','address_state'=>'','address_postalcode'=>'','address_country'=>'','accesskey'=>'PnpN1Gsfc3oLFtTu','time_zone'=>'UTC','currency_id'=>'1','currency_grouping_pattern'=>'123,456,789','currency_decimal_separator'=>'.','currency_grouping_separator'=>',','currency_symbol_placement'=>'$1.0','imagename'=>'','internal_mailer'=>'0','theme'=>'woodspice','language'=>'en_us','reminder_interval'=>'','no_of_currency_decimals'=>'2','truncate_trailing_zeros'=>'0','dayoftheweek'=>'Monday','callduration'=>'5','othereventduration'=>'5','calendarsharedtype'=>'public','default_record_view'=>'Summary','leftpanelhide'=>'0','rowheight'=>'medium','branch'=>'709','client_servicing_name'=>'16','coordinator_name'=>'17','ccurrency_name'=>'','currency_code'=>'INR','currency_symbol'=>'?','conv_rate'=>'1.00000','record_id'=>'','record_module'=>'','currency_name'=>'India, Rupees','id'=>'14');
?>