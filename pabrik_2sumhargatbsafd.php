	<?//@Copy nangkoelframework
//ind
require_once('master_validation.php'); 
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();



?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<script language=javascript>


	function batal()
	{
		document.getElementById('kdsup').value='';
		document.getElementById('tgl2').value='';	
		document.getElementById('tgl1').value='';
		document.getElementById('printContainer').innerHTML='';	
	}
	function getAfd(obj)
{
       unt=obj.options[obj.selectedIndex].value;
       param='unit='+unt;
       //alert(param);
       tujuan='lbm_slave_sampul.php';
        post_response_text(tujuan+'?proses=getAfdl', param, respog);
	function respog()
	{
          if(con.readyState==4)
          {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                        alert('ERROR TRANSACTION,\n' + con.responseText);
                }
                else {
                        document.getElementById('afdId').innerHTML=con.responseText;
                }
            }
            else {
                    busy_off();
                    error_catch(con.status);
            }
          }	
	 }  
}

</script>

<?


$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='KEBUN' and right(kodeorganisasi,1)='E'";

$res=mysql_query($str);
$optsup="<option value='___E'>Seluruh Kebun</option>";
while($bar=mysql_fetch_object($res)){
  $optsup.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}


$optafd="<option value='___E__'>Seluruhnya</option>";




//$sql="SELECT namasupplier,`supplierid` FROM ".$dbname.".log_5supplier WHERE substring(kodekelompok,1,1)='S' order by namasupplier asc";
//echo $sql;
//$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
//while ($data=mysql_fetch_assoc($qry))
//			{
//				$optsup.="<option value=".$data['supplierid'].">".$data['namasupplier']."</option>";
//			}
			
?>


<?
include('master_mainMenu.php');
OPEN_BOX();
$arr="##kdsup##tgl1##tgl2##kdkg##afdId";	

echo "<fieldset style='float:left;'><legend><b>Summary Harga TBS Internal PerAfdeling</b></legend>
<table>
	<tr>
		<td>Suplier</td>
		<td>:</td>
		<td><select id=kdsup style='width:155px;' onchange='getAfd(this)'>".$optsup."</select></td>
	</tr>
	<tr>
		<td>Afdeling</td>
		<td>:</td>
		<td><select id=afdId style='width:155px;'>".$optafd."</select></td>
	</tr>
	<tr>
		<td>Kg</td>
		<td>:</td>
		<td>
		<select id=kdkg style='width:155px;'>
		<option value=\"NO\" >Dalam Normal</option>
		<option value=\"NT\" >Dalam Netto</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl1' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' >
		s/d
		<input type='text' class='myinputtext' id='tgl2' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>	

	<tr>
		<td colspan=100>&nbsp;</td>
	</tr>
	<tr>
		<td colspan=100>
		<button onclick=zPreview('pabrik_slave_2sumhargatbsafd','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'pabrik_slave_2sumhargatbsafd.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
		
		
		<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>
		</td>
	</tr>
</table>
</fieldset>";//<button onclick=zPdf('pabrik_slave_2hargatbs','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>

echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";

CLOSE_BOX();
echo close_body();




?>