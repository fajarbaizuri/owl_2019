<?//@Copy nangkoelframework 
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/keu_laporan.js"></script>
<?
include('master_mainMenu.php');
OPEN_BOX();

$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
    where tipe in('KEBUN','PABRIK','GUDANG','TRAKSI','KANWIL') or (tipe='HOLDING' and length(kodeorganisasi)=4)
    order by kodeorganisasi";
$res=mysql_query($str);
$optkodeorg="<option value=''>".$_SESSION['lang']['all']."</option>";
while($bar=mysql_fetch_object($res))
{
    $optkodeorg.="<option value='".$bar->kodeorganisasi."'>".$bar->kodeorganisasi." (".$bar->namaorganisasi.")</option>";
}

?>
<fieldset style="float: left;"> 
<legend><b><?php echo $_SESSION['lang']['periode'].' '.$_SESSION['lang']['tutupbuku']?></b></legend>
<table cellspacing="1" border="0" >
<tr><td><label><?php echo $_SESSION['lang']['kodeorganisasi']?></label></td><td><select id=kodeorg style='width:200px;' onchange=document.getElementById('container').innerHTML=''><?php echo $optkodeorg; ?></select></td></tr>
<tr height="20"><td colspan="2"><button class=mybutton onclick=getPeriodeAkuntansi()><?php echo $_SESSION['lang']['preview'] ?></button></td></tr>
</table>
</fieldset>
<?

CLOSE_BOX();
OPEN_BOX('','Result:');
//echo"<span id=printPanel style='display:none;'>
//     <img onclick=periksajurnalKeExcel(event,'keu_slave_2periksaJurnal_Excel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
//	 <img onclick=periksajurnalKePDF(event,'keu_slave_2periksaJurnal_pdf.php') title='PDF' class=resicon src=images/pdf.jpg>
//	 </span>";    
echo"<div style='width:100%;height:359px;overflow:scroll;'>
    <table class=sortable cellspacing=1 border=0 width=100%>
    <thead>
    <tr>
        <td align=center>No.</td>
        <td align=center>".$_SESSION['lang']['kodeorg']."</td>
        <td align=center>".$_SESSION['lang']['periode']."</td>
        <td align=center>".$_SESSION['lang']['tanggalmulai']."</td>
        <td align=center>".$_SESSION['lang']['tanggalsampai']."</td>
        <td align=center>".$_SESSION['lang']['status']."</td>
    </tr>  
    </thead>
    <tbody id=container>
    </tbody>
    <tfoot>
    </tfoot>		 
    </table>
</div>";
CLOSE_BOX();
close_body();

?>
