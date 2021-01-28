<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX();
?>
<?php
$optOrg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN'";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optOrg.="<option value=".$rOrg['kodeorganisasi'].">".$rOrg['namaorganisasi']."</option>";
}

$intex=array('0'=>'External','1'=>'Internal','2'=>'Afiliasi');

//$optTbs="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optTbs="<option value=''>".$_SESSION['lang']['all']."</option>";//indra

foreach($intex as $dt => $rw)
{
	$optTbs.="<option value=".$dt.">".$rw."</option>";
}
$arr="##kdPabrik##tgl_1##tgl_2##tipeIntex##unit";
$arr2="##kdPabrik__2##tgl__2##kdUnit__2##kdAfdeling__2";
$arr3="##kdPabrik__3##kdUnit__3##periode__3";
$arr4="##kdPabrik__4##kdUnit__4##periode__4";

$optPabrik="<option value=''>".$_SESSION['lang']['all']."</option>";
$sOrg2="select namaorganisasi,kodeorganisasi from ".$dbname.".organisasi where tipe='PABRIK'";
$qOrg2=mysql_query($sOrg2) or die(mysql_error($conn));
while($rOrg2=mysql_fetch_assoc($qOrg2))
{
	$optPabrik.="<option value=".$rOrg2['kodeorganisasi'].">".$rOrg2['namaorganisasi']."</option>";
}
$sOrg="select distinct kodeorg from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by kodeorg";


$qOrg=mysql_query($sOrg) or die(mysql_error());
$optUnit="<option value=''>".$_SESSION['lang']['all']."</option>";
$unitintimbangan='(';
while($rData=mysql_fetch_assoc($qOrg))
{
        $optUnit.="<option value=".$rData['kodeorg'].">".$rData['kodeorg']."</option>";
        $unitintimbangan.="'".$rData['kodeorg']."',";
}
$unitintimbangan=substr($unitintimbangan,0,-1);
$unitintimbangan.=')';
if ($unitintimbangan==")" ){
	$sOrg="select kodeorganisasi from ".$dbname.".organisasi where tipe = 'AFDELING'  order by kodeorganisasi";
}else{
	$sOrg="select kodeorganisasi from ".$dbname.".organisasi where tipe = 'AFDELING' and induk in ".$unitintimbangan." order by kodeorganisasi";
}


$qOrg=mysql_query($sOrg) or die(mysql_error());
$optAfdeling2="<option value=''>".$_SESSION['lang']['all']."</option>";
while($rData=mysql_fetch_assoc($qOrg))
{
        $optAfdeling2.="<option value=".$rData['kodeorganisasi'].">".$rData['kodeorganisasi']."</option>";
}
$sOrg="select distinct substr(tanggal,1,7) as periode from ".$dbname.".pabrik_timbangan where kodeorg!='' and millcode like '%%' order by periode desc";
$qOrg=mysql_query($sOrg) or die(mysql_error());
$optPeriode="<option value=''></option>";
while($rData=mysql_fetch_assoc($qOrg))
{
        $optPeriode.="<option value=".$rData['periode'].">".$rData['periode']."</option>";
}

$sPEL="select distinct a.kodecustomer,b.namasupplier from ".$dbname.".pabrik_timbangan a LEFT JOIN ".$dbname.".log_5supplier b ON a.kodecustomer=b.kodetimbangan where a.kodeorg='' and a.millcode like '%%' and a.kodebarang='40000003' order by a.kodecustomer";
$qPEL=mysql_query($sPEL) or die(mysql_error());
$optPEL="<option value=''>".$_SESSION['lang']['all']."</option>";

while($rPel=mysql_fetch_assoc($qPEL))
{
        $optPEL.="<option value=".$rPel['kodecustomer'].">".$rPel['namasupplier']."</option>";
}



?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>
<script>
function getKode()
{
	tipeIntex=document.getElementById('tipeIntex').options[document.getElementById('tipeIntex').selectedIndex].value;
	param='tipeIntex='+tipeIntex+'&proses=getKdorg';
	tujuan="kebun_slave_3laporanProduksi.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
                  	document.getElementById('unit').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);

}
function getAfd(id)
{
	kdOrg=document.getElementById('kdOrg_'+id).getAttribute('value');
	tglAfd=document.getElementById('tanggal_'+id).getAttribute('value');
	param="kodeOrg="+kdOrg+"&proses=getAfdeling"+"&brsKe="+id+"&tglAfd="+tglAfd;
	tujuan="kebun_slave_3laporanProduksi.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('detail_'+id).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function getUnit(n)
{
	kdPabrik=document.getElementById('kdPabrik__'+n).options[document.getElementById('kdPabrik__'+n).selectedIndex].value;
	param="kodePabrik="+kdPabrik+"&proses=getUnit";
	tujuan="kebun_slave_3laporanProduksi.php";
	//alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('kdUnit__'+n).innerHTML=con.responseText;
                        getAfdeling2();
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function getAfdeling2()
{
	kdPabrik=document.getElementById('kdPabrik__2').options[document.getElementById('kdPabrik__2').selectedIndex].value;
	kdUnit=document.getElementById('kdUnit__2').options[document.getElementById('kdUnit__2').selectedIndex].value;
	param="kodePabrik="+kdPabrik+"&kodeUnit="+kdUnit+"&proses=getAfdeling2";
	tujuan="kebun_slave_3laporanProduksi.php";
//	alert(param);	
    
	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('kdAfdeling__2').innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function detailBlok(idAwal,id)
{
	kdBlok=document.getElementById('kdBlok_'+idAwal+'_'+id).innerHTML;
	nospb=document.getElementById('nospb_'+idAwal+'_'+id).innerHTML;
	tgl=document.getElementById('tanggal_'+idAwal).innerHTML;
	
	param='kdBlok='+kdBlok+'&proses=getPrestasi'+'&tgl='+tgl+'&brsKe='+idAwal+'&endKe='+id+'&nospb='+nospb;
	tujuan="kebun_slave_3laporanProduksi.php";

	 function respon() {
        if (con.readyState == 4) {
            if (con.status == 200) {
                busy_off();
                if (!isSaveResponse(con.responseText)) {
                    alert('ERROR TRANSACTION,\n' + con.responseText);
                } else {
                    // Success Response
				//	alert(con.responseText);
                  	document.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML=con.responseText;
                }
            } else {
                busy_off();
                error_catch(con.status);
            }
        }
    }
    //
  //  alert(fileTarget+'.php?proses=preview', param, respon);
  post_response_text(tujuan, param, respon);
}
function closeBlok(idAwal,id)
{
	document.getElementById('detailBlok_'+idAwal+'_'+id).innerHTML='';
}
function closeAfd(id)
{
	document.getElementById('detail_'+id).innerHTML='';
}

</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['rPenerimaanTbs']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pabrik']?></label></td><td><select id="kdPabrik" name="kdPabrik"  style="width:169px"><? echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type="text" class="myinputtext" id="tgl_1" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" /> s.d. <input type="text" class="myinputtext" id="tgl_2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" />
</td></tr>
<tr><td><label>TBS</label></td><td><select id="tipeIntex" name="tipeIntex" onchange="getKode()" style="width:169px"><? echo $optTbs?></select></td></tr>
<tr><td><?php echo $_SESSION['lang']['unit']."/".$_SESSION['lang']['supplier']?></td><td><select id="unit" style="width:169px"><option value=""><? echo $_SESSION['lang']['all'] ?></option></select></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2penerimaantbs','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zExcel(event,'pabrik_slave_2penerimaantbs.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>
      
<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['tanggal']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pabrik']?></label></td><td><select id="kdPabrik__2" name="kdPabrik__2" onchange="getUnit(2)" style="width:169px"><? echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['tanggal']?></label></td><td><input type="text" class="myinputtext" id="tgl__2" onmousemove="setCalendar(this.id)" onkeypress="return false;"  size="10" maxlength="10" /></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit__2" name="kdUnit__2" onchange="getAfdeling2()" style="width:169px"><? echo $optUnit?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['afdeling']?></label></td><td><select id="kdAfdeling__2" name="kdAfdeling__2" style="width:169px"><? echo $optAfdeling2?></select></td></tr>
<tr><td></td><td></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2penerimaantbs2','<?php echo $arr2?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
 <button onclick="zExcel(event,'pabrik_slave_2penerimaantbs2.php','<?php echo $arr2?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>      

<div>
<fieldset style="float: left;">
<legend><b><?php echo $_SESSION['lang']['rPenerimaanTbs']."/".$_SESSION['lang']['afdeling']."/".$_SESSION['lang']['bulan']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pabrik']?></label></td><td><select id="kdPabrik__3" name="kdPabrik__3" onchange="getUnit(3)" style="width:169px"><? echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['unit']?></label></td><td><select id="kdUnit__3" name="kdUnit__3" style="width:169px"><? echo $optUnit?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode__3" name="periode__3" style="width:169px"><? echo $optPeriode?></select></td></tr>
<tr><td></td><td></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('pabrik_slave_2penerimaantbs3','<?php echo $arr3?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <button onclick="zPdf('pabrik_slave_2penerimaantbs3','<?php echo $arr3?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button><button onclick="zExcel(event,'pabrik_slave_2penerimaantbs3.php','<?php echo $arr3?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div>            

<div>
<fieldset >
<legend><b><?php echo "Penerimaan TBS Eksternal Perbulan"?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['pabrik']?></label></td><td><select id="kdPabrik__4" name="kdPabrik__4" onchange="getUnit(3)" style="width:169px"><? echo $optPabrik?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['supplier']?></label></td><td><select id="kdUnit__4" name="kdUnit__4" style="width:169px"><? echo $optPEL?></select></td></tr>
<tr><td><label><?php echo $_SESSION['lang']['periode']?></label></td><td><select id="periode__4" name="periode__4" style="width:169px"><? echo $optPeriode?></select></td></tr>
<tr><td></td><td></td></tr>
<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2">
<button onclick="zPreview('pabrik_slave_2penerimaantbs4','<?php echo $arr4?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
<button onclick="zExcel(event,'pabrik_slave_2penerimaantbs4.php','<?php echo $arr4?>')" class="mybutton" name="preview" id="preview">Excel</button></td></tr>

</table>
</fieldset>
</div> 

<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>

</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>