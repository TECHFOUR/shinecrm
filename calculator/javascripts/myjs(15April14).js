$(function(){
var overlay = $('<div id="overlay"></div>');
$('.close').click(function(){
$('.popup').hide();
overlay.appendTo(document.body).remove();
return false;
});

$('.x').click(function(){
$('.popup').hide();
overlay.appendTo(document.body).remove();
return false;
});

$('.click').click(function(){
overlay.show();
overlay.appendTo(document.body);
$('.popup').show();
return false;
});
});


function active_form(val){
	
	document.getElementById("calculator").reset();
	document.getElementById('product').value = val;
	if(val == 'EVENTS'){
		document.getElementById("event_form").style.display="block";
		}
	else{
		document.getElementById("event_form").style.display="none";
		}
	if(val == 'INVENTORY'){
		document.getElementById("inventory_form").style.display="block";
		}
	else{
		document.getElementById("inventory_form").style.display="none";
		}
	if(val == 'PRINT'){
		document.getElementById("print_form").style.display="block";
		}
	else{
		document.getElementById("print_form").style.display="none";
		}
	if(val == 'DATABASE'){
		document.getElementById("database_form").style.display="block";
		}
	else{
		document.getElementById("database_form").style.display="none";
		}
	if(val == 'EDUCATION'){
		document.getElementById("education_form").style.display="block";
		}
	else{
		document.getElementById("education_form").style.display="none";
		}	
		
	if(val == 'LOGO'){
		document.getElementById("logo_form").style.display="block";
		}
	else{
		document.getElementById("logo_form").style.display="none";
		}	
		
		if(val == 'SMARTMATCH'){
		document.getElementById("smartmatch_form").style.display="block";
		}
	else{
		document.getElementById("smartmatch_form").style.display="none";
		}	
		
		if(val == 'ELITE MIND & SHINE VERIFIED'){
		document.getElementById("elitemind_form").style.display="block";
		}
	else{
		document.getElementById("elitemind_form").style.display="none";
		}	
		
		if(val == 'FLEXI HIRE'){
		document.getElementById("flexi_form").style.display="block";
		}
	else{
		document.getElementById("flexi_form").style.display="none";
		}	
		
		if(val == 'SMART JOBS'){
		document.getElementById("smartjob_form").style.display="block";
		}
	else{
		document.getElementById("smartjob_form").style.display="none";
		}	
	
	}	
	

function showProduct(str)
{
	
	if(str == 'EVENTS')	{
		
		if (str=="")
		  {
		  document.getElementById("event_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("event_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_event_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	if(str == 'INVENTORY')	{
		
		if (str=="")
		  {
		  document.getElementById("inventory_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("inventory_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_inventory_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	if(str == 'PRINT')	{
		
		if (str=="")
		  {
		  document.getElementById("print_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("print_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_print_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	if(str == 'EDUCATION')	{
		if (str=="")
		  {
		  document.getElementById("education_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("education_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_education_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	if(str == 'ELITE MIND & SHINE VERIFIED')	{
		if (str=="")
		  {
		  document.getElementById("elite_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("elite_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_elite_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	if(str == 'SMART JOBS')	{
		if (str=="")
		  {
		  document.getElementById("smartjob_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("smartjob_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_smartjob_product_type.php?product="+str,true);
		xmlhttp.send();
	}
if(str == 'FLEXI HIRE')	{
		if (str=="")
		  {
		  document.getElementById("flexi_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("flexi_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_flexi_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
if(str == 'LOGO')	{
		if (str=="")
		  {
		  document.getElementById("logo_product_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("logo_product_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_logo_product_type.php?product="+str,true);
		xmlhttp.send();
	}
	
	
if(str == 'SMARTMATCH')	{
		if (str=="")
		  {
		  document.getElementById("smartmatch_no_of_jobs_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("smartmatch_no_of_jobs_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_smartmatch_job.php?month="+str,true);
		xmlhttp.send();
	}	

if(str == 'DATABASE')	{
		if (str=="")
		  {
		  document.getElementById("database_geography_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("database_geography_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_database_geography.php?geography="+str,true);
		xmlhttp.send();
	}	
		
	
	
}


function price_calculate(val){
	
	var product = document.getElementById('product').value;
	
	if(product == 'EVENTS'){
		var mrp = document.getElementById('event_mrp').value;
	}
	if(product == 'INVENTORY'){
		var mrp = document.getElementById('inventory_mrp').value;
	}
	if(product == 'PRINT'){
		var mrp = document.getElementById('print_mrp').value;
	}
	if(product == 'EDUCATION'){
		var mrp = document.getElementById('education_mrp').value;
	}
	if(product == 'ELITE MIND & SHINE VERIFIED'){
		var mrp = document.getElementById('elitemind_mrp').value;
	}
	if(product == 'SMART JOBS'){
		var mrp = document.getElementById('smartjob_mrp').value;
	}
	
	if(product == 'FLEXI HIRE'){
		var mrp = document.getElementById('flexi_mrp').value;
	}
	
	if(product == 'LOGO'){
		var mrp = document.getElementById('logo_mrp').value;
	}
	
	if(product == 'SMARTMATCH'){
		var mrp = document.getElementById('smartmatch_mrp').value;
	}
	
	if(product == 'DATABASE'){
		var mrp = document.getElementById('database_mrp').value;
	}
		var discount_amount = mrp-val;
		var discount_per = (discount_amount/mrp)*100;
		var serice_tax = document.getElementById('serice_tax_amount').value;
		var total_amount = val+serice_tax;
		
		document.getElementById('discount').value = discount_per;
		document.getElementById('discount_amount').value = discount_amount;
		document.getElementById('total_amount').value = total_amount;
}
	


/*Start code for Event product by jitendra singh on 11 April 2014********************************************************************************************************/

function showProductType(str)
{

		if (str=="")
		  {
		  document.getElementById("sponser_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		  
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("sponser_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		product = document.getElementById('product').value;
		xmlhttp.open("GET","get_sponser.php?producttype="+str+"&product="+product,true);
		xmlhttp.send();
}

function showEventPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("event_month").value = arr[0];
			document.getElementById("event_bottom_price").value = arr[1];
			document.getElementById("event_mrp").value = arr[2];
			}
		  }
		 product = document.getElementById('product').value;
		 product_type = document.getElementById('prodct_type').value;
		xmlhttp.open("GET","get_event_prices.php?sponser="+str+"&product="+product+"&product_type="+product_type,true);
		xmlhttp.send();
}


/*End code for Event product by jitendra singh on 11 April 2014********************************************************************************************************/
	
/*Start code for Inventory product by jitendra singh on 11 April 2014****************************************************************************************************/	
	
function showInventoryDatabase(str)
{

		if (str=="")
		  {
		  document.getElementById("inventory_database_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		  
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("inventory_database_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		  product = document.getElementById('product').value; 
		xmlhttp.open("GET","get_inventory_database.php?product_type="+str+"&product="+product,true);
		xmlhttp.send();
}


function showInventoryActive(str)
{

		if (str=="")
		  {
		  document.getElementById("inventory_active_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		  
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("inventory_active_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		  
		product = document.getElementById('product').value;
		inventory_product = document.getElementById('inventory_product').value;  
		xmlhttp.open("GET","get_inventory_active.php?tg_database="+str+"&product="+product+"&inventory_product="+inventory_product,true);
		xmlhttp.send();
}

function showInventoryPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("inventory_emailers").value = arr[0];
			document.getElementById("inventory_bottom_price").value = arr[1];
			document.getElementById("inventory_mrp").value = arr[2];
			}
		  }
		 product = document.getElementById('product').value;
		inventory_product = document.getElementById('inventory_product').value; 
		inventory_database = document.getElementById('inventory_database').value;  
		  
		xmlhttp.open("GET","get_inventory_prices.php?active="+str+"&product="+product+"&inventory_product="+inventory_product+"&inventory_database="+inventory_database,true);
		xmlhttp.send();
}


function emailer_cal(){
	
	var active = document.getElementById('inventory_active').value;
	if(active == '6 Months Active'){
		emailer_bottom_price = 28;
		}
	if(active == '6 -12 Months Active'){
		emailer_bottom_price = 22.4;
	}
	mrp = 100;
	
	var no_emailers = document.getElementById('inventory_emailers').value;
	document.getElementById('inventory_bottom_price').value = no_emailers*emailer_bottom_price;
	document.getElementById('inventory_mrp').value = mrp*no_emailers;
	
	}


function inventory_price_calculate(val){
	
	var mrp = document.getElementById('event_mrp').value;
	var discount_amount = mrp-val;
	var discount_per = (discount_amount/mrp)*100;
	var serice_tax = document.getElementById('serice_tax_amount').value;
	var total_amount = val+serice_tax;
	
	document.getElementById('discount').value = discount_per;
	document.getElementById('discount_amount').value = discount_amount;
	document.getElementById('total_amount').value = total_amount;
	}
	
/*End code for Inventory product by jitendra singh on 11 April 2014********************************************************************************************************/


/*Start code for Print product by jitendra singh on 11 April 2014********************************************************************************************************/	

function showPrintPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("print_month").value = arr[0];
			document.getElementById("print_bottom_price").value = arr[1];
			document.getElementById("print_mrp").value = arr[2];
			document.getElementById("print_size").value = arr[3];
			}
		  }
		xmlhttp.open("GET","get_print_prices.php?product_type="+str,true);
		xmlhttp.send();
}

function print_price_cal(){
	
	var product_type = document.getElementById('print_product').value;
	if(product_type == 'Hot Jobs'){
		print_bottom_price = 375;
		print_mrp = 1000;
		}
	if(product_type == 'HT Delhi'){
		print_bottom_price = 855;
		print_mrp = 950;
	}
	if(product_type == 'HT Mumbai'){
		print_bottom_price = 315;
		print_mrp = 350;
	}
	
	var size = document.getElementById('print_size').value;
	document.getElementById('print_bottom_price').value = size*print_bottom_price;
	document.getElementById('print_mrp').value = size*print_mrp;
	
	}

/*End code for Print product by jitendra singh on 11 April 2014********************************************************************************************************/

/*Start code for Education product by jitendra singh on 11 April 2014********************************************************************************************************/

function showEducationPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("no_of_company").value = arr[0];
			document.getElementById("education_bottom_price").value = arr[1];
			document.getElementById("education_mrp").value = arr[2];
			}
		  }
		xmlhttp.open("GET","get_education_prices.php?education_product="+str,true);
		xmlhttp.send();
}	


function education_price_cal(){
	
	var no_of_company = document.getElementById('no_of_company').value;
	bottom_price = 3000;
	mrp = 6000;
	document.getElementById('education_bottom_price').value = no_of_company*bottom_price;
	document.getElementById('education_mrp').value = no_of_company*mrp;
	
	}	
	
/*End code for Education product by jitendra singh on 11 April 2014********************************************************************************************************/



/*Start code for Elite product by jitendra singh on 11 April 2014********************************************************************************************************/

function showElitePrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("elitemind_walkin").value = arr[0];
			document.getElementById("elitemind_requirement").value = arr[1];
			document.getElementById("elitemind_mrp").value = arr[2];
			document.getElementById("elitemind_bottom_price").value = arr[3];
			}
		  }
		xmlhttp.open("GET","get_elite_prices.php?elite_product="+str,true);
		xmlhttp.send();
}	


function elite_price_cal(val){
	
	price = 100000;
	document.getElementById('elitemind_bottom_price').value = val*price;
	document.getElementById('elitemind_mrp').value = val*price;
	
	}	
	
/*End code for Elite product by jitendra singh on 11 April 2014********************************************************************************************************/


/*Start code for Smart Jobs product by jitendra singh on 11 April 2014********************************************************************************************************/

function showSmartjobPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("smartjob_no_of_job").value = arr[0];
			document.getElementById("smartjob_bottom_price").value = arr[1];
			document.getElementById("smartjob_mrp").value = arr[2];
			}
		  }
		xmlhttp.open("GET","get_smartjob_prices.php?smartjob_product="+str,true);
		xmlhttp.send();
}	


function smartjob_price_cal(val){
	price_per_job = 100;
	document.getElementById('smartjob_mrp').value = val*price_per_job;
	
	}	
	
/*End code for Smart Jobs product by jitendra singh on 11 April 2014********************************************************************************************************/


/*Start code for Flexi Hire Jobs product by jitendra singh on 11 April 2014********************************************************************************************************/


function showflexi_geography(str)
{

		if (str=="")
		  {
		  document.getElementById("flexi_geography_type_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		  
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("flexi_geography_type_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_flexi_geography.php?product_type="+str,true);
		xmlhttp.send();
}


function show_flexi_access(str)
{

		if (str=="")
		  {
		  document.getElementById("flexi_access_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("flexi_access_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		  
		  product = document.getElementById('product').value; 
		  smart_flexi_type = document.getElementById('smart_flexi_type').value; 
		xmlhttp.open("GET","get_flexi_access.php?geography="+str+"&product="+product+"&smart_flexi_type="+smart_flexi_type,true);
		xmlhttp.send();
}

function show_flexi_duration(str)
{

		if (str=="")
		  {
		  document.getElementById("flexi_duration_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("flexi_duration_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		  
		 product = document.getElementById('product').value; 
		 smart_flexi_type = document.getElementById('smart_flexi_type').value; 
		 flexi_geography = document.getElementById('flexi_geography').value; 
		xmlhttp.open("GET","get_flexi_duration.php?access="+str+"&product="+product+"&smart_flexi_type="+smart_flexi_type+"&flexi_geography="+flexi_geography,true);
		xmlhttp.send();
}


function show_Flexi_Prices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("flexi_bottom_price").value = arr[0];
			document.getElementById("flexi_mrp").value = arr[1];
			}
		  }
		 product = document.getElementById('product').value; 
		 smart_flexi_type = document.getElementById('smart_flexi_type').value; 
		 flexi_geography = document.getElementById('flexi_geography').value; 
		 flexi_access  = document.getElementById('flexi_access').value; 
		 
		xmlhttp.open("GET","get_flexi_prices.php?flexi_duration="+str+"&product="+product+"&smart_flexi_type="+smart_flexi_type+"&flexi_geography="+flexi_geography+"&flexi_access="+flexi_access,true);
		xmlhttp.send();
}	


function flexi_price_cal(val){
	price_per_job = 100;
	document.getElementById('smartjob_mrp').value = val*price_per_job;
	
	}	
	
/*End code for Flexi Hire product by jitendra singh on 11 April 2014********************************************************************************************************/


/*Start code for Logo product by jitendra singh on 11 April 2014********************************************************************************************************/

function showLogoPrices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("logo_month").value = arr[0];
			document.getElementById("logo_bottom_price").value = arr[1];
			document.getElementById("logo_mrp").value = arr[2];
			}
		  }
		xmlhttp.open("GET","get_logo_prices.php?logo_product="+str,true);
		xmlhttp.send();
}	


function smartjob_price_cal(val){
	price_per_job = 100;
	document.getElementById('smartjob_mrp').value = val*price_per_job;
	
	}	
	
/*End code for Logo product by jitendra singh on 11 April 2014********************************************************************************************************/

/*Start code for Smartmatch product by jitendra singh on 11 April 2014********************************************************************************************************/

function show_Smartmatch_Duraion(str)
{

		if (str=="")
		  {
		  document.getElementById("smartmatch_month_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("smartmatch_month_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		xmlhttp.open("GET","get_smartmatch_duration.php?duration="+str,true);
		xmlhttp.send();
}


	
function show_Smartmatch_Prices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("smartmatch_bottom_price").value = arr[0];
			document.getElementById("smartmatch_mrp").value = arr[1];
			}
		  }
		  product = document.getElementById('product').value; 
		  smartmatch_jobs = document.getElementById('smartmatch_jobs').value; 
		xmlhttp.open("GET","get_smartmatch_prices.php?smartmatch_duration="+str+"&product="+product+"&smartmatch_jobs="+smartmatch_jobs,true);
		xmlhttp.send();
}	
	
/*End code for Smartmatch product by jitendra singh on 11 April 2014********************************************************************************************************/


/*Start code for Database product by jitendra singh on 11 April 2014********************************************************************************************************/

function show_Database_it(str)
{

		if (str=="")
		  {
		  document.getElementById("database_it_non_it_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("database_it_non_it_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		 product = document.getElementById('product').value;
		xmlhttp.open("GET","get_database_it.php?database_geography="+str+"&product="+product,true);
		xmlhttp.send();
}


function show_database_limit(str)
{

		if (str=="")
		  {
		  document.getElementById("database_limit_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("database_limit_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		var product = document.getElementById('product').value;
		var geography = document.getElementById('database_geography').value;
		xmlhttp.open("GET","get_database_limit.php?database_it="+str+"&product="+product+"&geography="+geography,true);
		xmlhttp.send();
}


function show_database_duration(str)
{

		if (str=="")
		  {
		  document.getElementById("database_duration_tab").innerHTML="";
		  return;
		  }
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("database_duration_tab").innerHTML=xmlhttp.responseText;
			}
		  }
		 var product = document.getElementById('product').value;
		 var database_geography = document.getElementById('database_geography').value;
		 var database_it = document.getElementById('database_it').value; 
		  
		xmlhttp.open("GET","get_database_duration.php?database_limit="+str+"&product="+product+"&database_geography="+database_geography+"&database_it="+database_it,true);
		xmlhttp.send();
}
	
function show_Database_Prices(str)
{
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  
		xmlhttp.onreadystatechange=function()
		  {
			  
			  var all_prices = xmlhttp.responseText;
			 arr = all_prices.split("*");
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			document.getElementById("database_bottom_price").value = arr[0];
			document.getElementById("database_mrp").value = arr[1];
			
			document.getElementById("master_bottom_price").value = arr[0];
			document.getElementById("master_mrp").value = arr[1];
			
			document.getElementById("temp_bottom_price").value = arr[0];
			document.getElementById("temp_mrp").value = arr[1];
			
			document.getElementById("master_upsell_word").value = arr[2];
			document.getElementById("master_upsell_excel").value = arr[3];
			document.getElementById("master_upsell_emailer").value = arr[4];
			document.getElementById("master_downsell_word").value = arr[5];
			document.getElementById("master_downsell_excel").value = arr[6];
			document.getElementById("master_downsell_emailer").value = arr[7];
			}
		  }
		 var product = document.getElementById('product').value;
		 var database_geography = document.getElementById('database_geography').value;
		 var database_it = document.getElementById('database_it').value; 
		  var database_limit = document.getElementById('database_limit').value; 
		 
		  
		xmlhttp.open("GET","get_database_prices.php?database_duration="+str+"&product="+product+"&database_geography="+database_geography+"&database_it="+database_it+"&database_limit="+database_limit,true);
		xmlhttp.send();
}	
	
/*End code for Database product by jitendra singh on 11 April 2014********************************************************************************************************/



// For Upsell*******************************

function get_upsell_excel(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		
		document.getElementById('upsell_word').value = "Select an Option";
		document.getElementById('upsell_emailer').value = "Select an Option";
		document.getElementById('downsell_word').value = "Select an Option";
		document.getElementById('downsell_excel').value = "Select an Option";
		document.getElementById('downsell_emailer').value = "Select an Option";
	}
	else{	
		var upsell_excel = document.getElementById("master_upsell_excel").value;
		var upsell_excel = upsell_excel.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		var percentage = val*upsell_excel;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price = parseInt(cal_bottom_price) + parseInt(bottom_price);
		var actual_mrp = parseInt(cal_mrp) + parseInt(mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp; 
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
}

function get_upsell_word(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('upsell_excel').value = "Select an Option";
		document.getElementById('upsell_emailer').value = "Select an Option";
		document.getElementById('downsell_word').value = "Select an Option";
		document.getElementById('downsell_excel').value = "Select an Option";
		document.getElementById('downsell_emailer').value = "Select an Option";
	}
	else{	
		var upsell_word = document.getElementById("master_upsell_word").value;
		var upsell_word = upsell_word.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		var percentage = val*upsell_word;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price = parseInt(cal_bottom_price) + parseInt(bottom_price);
		var actual_mrp = parseInt(cal_mrp) + parseInt(mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp; 
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
}

function get_upsell_emailer(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('upsell_excel').value = "Select an Option";
		document.getElementById('upsell_word').value = "Select an Option";
		document.getElementById('downsell_word').value = "Select an Option";
		document.getElementById('downsell_excel').value = "Select an Option";
		document.getElementById('downsell_emailer').value = "Select an Option";
	}
	else{	
		var upsell_emailer = document.getElementById("master_upsell_emailer").value;
		var upsell_emailer = upsell_emailer.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		var percentage = val*upsell_emailer;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price = parseInt(cal_bottom_price) + parseInt(bottom_price);
		var actual_mrp = parseInt(cal_mrp) + parseInt(mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp; 
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
}



//For Downsell

function get_downsell_excel(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('upsell_excel').value = "Select an Option";
		document.getElementById('upsell_word').value = "Select an Option";
		document.getElementById('upsell_emailer').value = "Select an Option";
		document.getElementById('downsell_word').value = "Select an Option";
		document.getElementById('downsell_emailer').value = "Select an Option";
	}
	else{	
		var downsell_excel = document.getElementById("master_downsell_excel").value;
		var downsell_excel = downsell_excel.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		//alert(bottom_price+''+downsell_excel);
		var percentage = val*downsell_excel;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price =parseInt(bottom_price) - parseInt(cal_bottom_price);
		var actual_mrp = parseInt(mrp) - parseInt(cal_mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp; 
		
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
}

function get_downsell_word(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('upsell_excel').value = "Select an Option";
		document.getElementById('upsell_word').value = "Select an Option";
		document.getElementById('upsell_emailer').value = "Select an Option";
		document.getElementById('downsell_excel').value = "Select an Option";
		document.getElementById('downsell_emailer').value = "Select an Option";
	}
	else{	
		var downsell_word = document.getElementById("master_downsell_word").value;
		var downsell_word = downsell_word.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		var percentage = val*downsell_word;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price = parseInt(bottom_price) - parseInt(cal_bottom_price);
		var actual_mrp = parseInt(mrp) - parseInt(cal_mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp; 
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
}

function get_downsell_emailer(val)
{
	if(val == 'Select an Option'){
		document.getElementById('database_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('database_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('temp_bottom_price').value = document.getElementById('master_bottom_price').value;
		document.getElementById('temp_mrp').value = document.getElementById('master_mrp').value;
		document.getElementById('upsell_excel').value = "Select an Option";
		document.getElementById('upsell_word').value = "Select an Option";
		document.getElementById('upsell_emailer').value = "Select an Option";
		document.getElementById('downsell_word').value = "Select an Option";
		document.getElementById('downsell_excel').value = "Select an Option";
	}
	else{	
		var downsell_emailer = document.getElementById("master_downsell_emailer").value;
		var downsell_emailer = downsell_emailer.replace('%','');

		var bottom_price = document.getElementById('temp_bottom_price').value;
		var mrp = document.getElementById('temp_mrp').value;
		var percentage = val*downsell_emailer;
		var cal_bottom_price = bottom_price*(percentage/100);
		var cal_mrp = mrp*(percentage/100);
		
		var actual_bottom_price = parseInt(bottom_price) - parseInt(cal_bottom_price);
		var actual_mrp = parseInt(mrp) - parseInt(cal_mrp);
		
		document.getElementById('database_bottom_price').value = actual_bottom_price; 
		document.getElementById('database_mrp').value = actual_mrp;
		document.getElementById('temp_bottom_price').value = actual_bottom_price; 
		document.getElementById('temp_mrp').value = actual_mrp; 
	}
} 

