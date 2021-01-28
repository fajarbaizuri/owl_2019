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
$optThn="<option value='%%'>".$_SESSION['lang']['all']."</option>";
$sOrg="select distinct `tahun` from ".$dbname.".log_5monitoring order by tahun desc";
$qOrg=mysql_query($sOrg) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
	$optThn.="<option value=".$rOrg['tahun'].">".$rOrg['tahun']."</option>";
}


$str="SELECT * FROM  ".$dbname.".`organisasi` WHERE  `tipe` =  'GUDANG' order by namaorganisasi asc";
$res=mysql_query($str);
$optGudang="<option value='%%'>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
	$optGudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
}

$arr="##thn##gudang##status";
$optPeriodePo="<option value='%%'>".$_SESSION['lang']['pilihdata']."</option>";
$sPeriodePo="select distinct substring(tanggal,1,7) as periode from ".$dbname.".log_poht order by tanggal asc";
$qPeriodePo=mysql_query($sPeriodePo) or die(mysql_error());
while($rPeriodePo=mysql_fetch_assoc($qPeriodePo))
{
    if($rPeriodePo['periode']!='0000-00')
    {
        if(substr($rPeriodePo['periode'],5,2)=='12')
        {
            $optPeriodePo.="<option value=".substr($rPeriodePo['periode'],0,4).">".substr($rPeriodePo['periode'],0,4)."</option>";
        }
        else
        {
            $optPeriodePo.="<option value=".$rPeriodePo['periode'].">".substr(tanggalnormal($rPeriodePo['periode']),1,7)."</option>";
        }
    }
    //echo substr($rPeriodePo['periode'],5,5);
}
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript src=js/zReport.js></script>

<script>

semua="<? echo $_SESSION['lang']['all'] ?>";
function batal()
{
    document.getElementById('thn').innerHTML='';
    document.getElementById('thn').innerHTML="<option value='%%'>"+semua+"</option>";
	document.getElementById('gudang').innerHTML='';
    document.getElementById('gudang').innerHTML="<option value=''>"+semua+"</option>";
	document.getElementById('status').innerHTML='';
    document.getElementById('status').innerHTML="<option value=''>"+semua+"</option>";
    document.getElementById('printContainer').innerHTML='';
}

</script>
<link rel=stylesheet type=text/css href=style/zTable.css>
<div>
<fieldset style="float: left;">
<legend><b><?php echo "Laporan Monitoring Persediaan";?></b></legend>
<table cellspacing="1" border="0" >
<tr>
	<td>
		<label><?php echo "Tahun"?></label>
	</td>
<td>
	<select id="thn" name="thn" style="width:150px" >
		<?php echo $optThn?>
	</select>
</td>
</tr>
<tr>
	<td>
		<label><?php echo "Gudang"?></label>
	</td>
<td>
	<select id="gudang" name="gudang" style="width:150px" >
		<?php echo $optGudang?>
	</select>
</td>
</tr>
<tr>
	<td>
		<label><?php echo "Status"?></label>
	</td>
<td>
	<select id="status" name="status" style="width:150px" >
		<?php 
			echo "<option value='%%'>".$_SESSION['lang']['all']."</option>";
			echo "<option value='1'>Fast Moving</option>";
			echo "<option value='2'>Slow Moving</option>";
			echo "<option value='3'>Consumable</option>";
		?>
	</select>
</td>
</tr>

<tr height="20"><td colspan="2">&nbsp;</td></tr>
<tr><td colspan="2"><button onclick="zPreview('log_slave_3monitoring','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">Preview</button>
        <!--<button onclick="zPdf('log_slave_2detail_pembelian_brg','<?php echo $arr?>','printContainer')" class="mybutton" name="preview" id="preview">PDF</button>-->
        <button onclick="zExcel(event,'log_slave_3monitoring.php','<?php echo $arr?>')" class="mybutton" name="preview" id="preview">Excel</button>
        <button onclick="batal()" class="mybutton" name="btl" id="btl"><? echo $_SESSION['lang']['cancel']?></button>
</td></tr>

</table>
</fieldset>
</div>


<fieldset style='clear:both'><legend><b>Print Area</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'>
<?php
//$arrBln=array("1"=>"Jan","2"=>"Feb","3"=>"Mar","4"=>"Apr","5"=>"Mei","6"=>"Jun","7"=>"Jul","8"=>"Aug","9"=>"Sept","10"=>"Okt","11"=>"Nov","12"=>"Des");
//echo"<pre>";
//print_r($arrBln);
//echo"</pre>";
//echo"<table class=sortable border=0 cellspacing=1 cellpadding=1><thead><tr class=rowheader>";
//foreach($arrBln as $brs=>$dtBln)
//{
//echo"<td>".$dtBln."</td>";
//}
//echo"<td>action</td></tr></thead>";
//echo"<tbody><tr class=rowcontent>";
//foreach($arrBln as $brs2 =>$dtBln2)
//{
//echo"<td><input type='text' id=jam_".$brs2." /></td>";
//}
//echo"<td>action</td></tr></tbody></table>";
?>
</div></fieldset>

<?php
CLOSE_BOX();
echo close_body();
?>