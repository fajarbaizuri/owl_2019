<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language="javascript" src="js/zTools.js"></script>
<script language="javascript" src="js/zReport.js"></script>
<script language="javascript">
function loadjamDetail(kodevhc,tanggal,ev)
{
	param='kodevhc='+kodevhc+'&tanggal='+tanggal;
	tujuan='vhc_slave_getDetailJam.php'+"?"+param;  
	width='700';
	height='400';
   
	content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
	showDialog1('Detail Pekerjaan Kendaraan',content,width,height,ev); 

}
function qwe()
{
    showById('printPanel');
    zPreview('vhc_slave_getLaporanAlokasiJam','##periode##kdUnit##afdId','printContainer');
}
function qweKeExcel(ev,tujuan)
{
	periode1=document.getElementById('periode');
	periode =periode1.options[periode1.selectedIndex].value;
	kdUnit1=document.getElementById('kdUnit');
	kdUnit =kdUnit1.options[kdUnit1.selectedIndex].value;
	afdId1=document.getElementById('afdId');
	afdId =afdId1.options[afdId1.selectedIndex].value;

	param='apa=excel'+'&periode='+periode+'&kdUnit='+kdUnit+'&afdId='+afdId;
//alert(param);                
                
	judul='Report Ms.Excel';	
	printFile(param,tujuan,judul,ev)	
}
function qweKePDF(ev,tujuan)
{
	periode1=document.getElementById('periode');
	periode =periode1.options[periode1.selectedIndex].value;
	kdUnit1=document.getElementById('kdUnit');
	kdUnit =kdUnit1.options[kdUnit1.selectedIndex].value;
	afdId1=document.getElementById('afdId');
	afdId =afdId1.options[afdId1.selectedIndex].value;

	param='apa=pdf'+'&periode='+periode+'&kdUnit='+kdUnit+'&afdId='+afdId;
//alert(param);                
                
	judul='Report PDF';	
	printFile(param,tujuan,judul,ev)	
}
function printFile(param,tujuan,title,ev)
{
   tujuan=tujuan+"?"+param;  
   width='900';
   height='400';
   content="<iframe frameborder=0 width=100% height=100% src='"+tujuan+"'></iframe>"
   showDialog1(title,content,width,height,ev); 	
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
                        document.getElementById			('afdId').innerHTML=con.responseText;
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

OPEN_BOX(''); 

$sql = "SELECT distinct periode FROM ".$dbname.".setup_periodeakuntansi where  kodeorg	='TKFB' ORDER BY periode desc limit 6";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());
while ($data=mysql_fetch_assoc($qry))
{
	$optPrd.="<option value=".$data['periode'].">".$data['periode']."</option>";
}	

$sUnit="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where CHAR_LENGTH(kodeorganisasi)='4' and tipe in ('KEBUN','PABRIK') order by namaorganisasi asc";
$qUnit=mysql_query($sUnit) or die(mysql_error());
while($rUnit=mysql_fetch_assoc($qUnit))
{
   $optUnit.="<option value='".$rUnit['kodeorganisasi']."'>".$rUnit['namaorganisasi']."</option>";
}
$optAfd="<option value=''>".$_SESSION['lang']['all']."</option>";
  
?>


<fieldset>
<legend><b>Alokasi Jam Kerja/Kendaraan[HM/KM]</b></legend>
<table cellspacing="1" border="0" >

<tr><td><label>Periode</label></td><td><select id=periode style='width:200px;' ><?php echo $optPrd; ?></select></td></tr>
<tr><td><label>Unit</label></td><td><select id=kdUnit style='width:200px;' onchange="getAfd(this)"><?php echo $optUnit; ?></select></td></tr>
<tr><td><label>Afdeling</label></td><td><select id=afdId style='width:200px;' ><?php echo $optAfd; ?></select></td></tr>

<!--<tr height="20"><td colspan="2">&nbsp;</td></tr>-->
<tr height="20"><td colspan="2"><?php echo "<button class=mybutton onclick=qwe()>".$_SESSION['lang']['proses']."</button>"; ?></td></tr>
</table>
</fieldset>
<?
//echo "<fieldset><legend><b>".$_SESSION['lang']['jmljamkerja']."</b></legend><table>
//       <tr><td>".$_SESSION['lang']['periode']."</td>
//           <td><select id=periode>".$optper."</select></td></tr>
//       <tr><td>".$_SESSION['lang']['kodetraksi']."</td>
//           <td><select id=kodetraksi>".$opttrx."</select></td></tr>
//       </table>
//       <button class=mybutton onclick=zPreview('vhc_slave_getLaporanJamKerja','##periode##kodetraksi','printContainer')>".$_SESSION['lang']['proses']."</button>
//       </fieldset>";

echo"<fieldset style=\"clear: both;\"><legend><b>Print Area</b></legend>
<span id=printPanel style='display:none;'>
     <img onclick=qweKeExcel(event,'vhc_slave_getLaporanAlokasiJam.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	
	 </span><div id='printContainer' style=\"overflow: auto; height: 350px; max-width: 1220px;\">

</div></fieldset>";
CLOSE_BOX();
?>
<?php
echo close_body();
?>