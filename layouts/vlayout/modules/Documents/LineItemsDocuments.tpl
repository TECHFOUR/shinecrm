{*Start to add more documents by Ajay date : 24-may-13 ****************** *}
	
			<tr>
	<td colspan="3">
    <button class="add btn btn-success" type="button" onclick="fnAddProductRow('{$MODULE}','layouts/vlayout/skins/images');">Add Document</button>
			
			
	</td>
	 </tr>	
     <table width="100%"  border="0" align="center" cellpadding="5" cellspacing="0" class="crmTable" id="proTab">
	<tr><th>Del</th><th align="left"><font color="red">*</font>Title</th><th align="left"><font color="red">*</font>File Name</th><th>&nbsp;</th><th>Notes</th></tr>    
	</table> 

{literal}
<script type="text/javascript">

document.onload = createthreerow();

/*function createthreerow () {
		fnAddProductRow('{$MODULE}','/layouts/vlayout/skins/images/', 100);
}
*/
function fnAddProductRow(module,image_path,condition){
	//alert(module+"----"+image_path);

	//rowCnt++;
	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;	
	var count = eval(prev)-1;//As the table has two headers, we should reduce the count
	var row = tableName.insertRow(prev);
	row.id = "row"+count;
	row.style.verticalAlign = "top";
	

	var colone = row.insertCell(0);
	var coltwo = row.insertCell(1);
	var colthree = row.insertCell(2);
	var colfour = row.insertCell(3);
	var colfive = row.insertCell(4);
	
		
	/* Product Re-Ordering Feature Code Addition Starts */
	iMax = tableName.rows.length;

	for(iCount=0;iCount<=iMax;iCount++)
	{
		if(document.getElementById("row"+iCount) && document.getElementById("row"+iCount).style.display != 'none')
		{
			iPrevRowIndex = iCount;
		}
	}

	iPrevCount = eval(iPrevRowIndex);
	var oPrevRow = tableName.rows[iPrevRowIndex+1]; 
	var delete_row_count=count;
	/* Product Re-Ordering Feature Code Addition ends */
	
	
	//Delete link
	colone.className = "crmTableRow small";
	colone.id = row.id+"_col1";
	if(condition == 100) {
	colone.innerHTML='<img src="delete.gif" border="0" ><input id="deleted'+count+'" name="deleted'+count+'" type="hidden" value="0" ><br/><br/>&nbsp;';		
	}else {
		colone.innerHTML='<img src="delete.gif" border="0" onclick="deleteCustomRow(\''+module+'\','+count+',\'layouts/vlayout/skins/images/\')"><input id="deleted'+count+'" name="deleted'+count+'" type="hidden" value="0"><br/><br/>&nbsp;';
	}
	var temp='';
	
		coltwo.className = "crmTableRow small"
	temp='<input id="title_'+count+'" name="title_'+count+'"   type="text" class="detailedViewTextBoxPayment" style="width:220px;" />';	
	coltwo.innerHTML=temp;	
	
	
	var temp='';
	colthree.className = "crmTableRow small"
	temp='<input id="file_'+count+'" name="file_'+count+'" type="file" class="small " style="width:220px;" onchange="validateFilename(this)"  onfocus="this.className=\'detailedViewTextBoxOn\'" onblur="this.className=\'detailedViewTextBox\'" value=""  data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>';	
	colthree.innerHTML=temp;
	
	var temp='';
	colfour.className = "crmTableRow small"
	temp='<input id="file_'+count+'_hidden" name="file_'+count+'_hidden" type="hidden" class="small " style="width:220px;"  onfocus="this.className=\'detailedViewTextBoxOn\'" onblur="this.className=\'detailedViewTextBox\'" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/>';	
	colfour.innerHTML=temp;		
	
	var temp='';
	colfive.className = "crmTableRow small"
	temp='<textarea id="note_'+count+'" name="note_'+count+'" onblur="this.className=\'detailedViewTextBox\'"  class=small style="width:96%;height:40px" onfocus="this.className=\'detailedViewTextBoxOn\'" value="" data-validation-engine="validate[required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]"/></textarea>';       
	colfive.innerHTML=temp;		
	
	return count;

}

function deleteCustomRow(module,i,image_path)
{
	var tableName = document.getElementById('proTab');
	var prev = tableName.rows.length;

//	document.getElementById('proTab').deleteCustomRow(i);
	document.getElementById("row"+i).style.display = 'none';

// Added For product Reordering starts
	iMax = tableName.rows.length;
	for(iCount=i;iCount>=1;iCount--)
	{
		if(document.getElementById("row"+iCount) && document.getElementById("row"+iCount).style.display != 'none')
		{
			iPrevRowIndex = iCount;
			break;
		}
	}
	iPrevCount = iPrevRowIndex;
	oCurRow = eval(document.getElementById("row"+i));
	sTemp = oCurRow.cells[0].innerHTML;
	document.getElementById('deleted'+i).value = 1;

}

</script>
{/literal}