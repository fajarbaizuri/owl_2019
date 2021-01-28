<?//@Copy nangkoelframework
//ind
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');

?>

<script language=javascript src=js/zTools.js></script>
<script language=javascript src='js/zReport.js'></script>
<link rel=stylesheet type=text/css href=style/zTable.css>

<script language=javascript>
	function batal()
	{
		document.getElementById('kdorg').value='';
		document.getElementById('tgl').value='';	
		document.getElementById('kgt').value='';
		document.getElementById('reportcontainer').innerHTML='';	
	}
</script>

<?
$optorg="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql="SELECT kodeorganisasi,namaorganisasi FROM ".$dbname.".organisasi WHERE tipe='KEBUN' AND RIGHT(kodeorganisasi,1)='E'";
$qry = mysql_query($sql) or die ("SQL ERR : ".mysql_error());

while ($data=mysql_fetch_assoc($qry))
			{
				$optorg.="<option value=".$data['kodeorganisasi'].">".$data['namaorganisasi']."</option>";
			}		
$optkgt="<option value=''>".$_SESSION['lang']['all']."</option>";
$sql1="SELECT kelompok,
CASE 
	 WHEN kelompok = 'TB' THEN 'Tanaman Baru'
    WHEN kelompok = 'TM' THEN 'Tanaman Menghasilkan'
    WHEN kelompok = 'TBM' THEN 'Tanaman Belum Menghasilkan'
    WHEN kelompok = 'BBT' THEN 'Bibitan'
END AS 'nama'
 FROM ".$dbname.".setup_kegiatan WHERE kelompok IN('BBT','TB','TBM','TM') group by nama Order by kelompok desc;";
 
//$sql1="SELECT kodekegiatan,namakegiatan,kelompok FROM ".$dbname.".setup_kegiatan WHERE kelompok IN('BBT','TB','TBM','TM') Order by kelompok desc";
$qry1 = mysql_query($sql1) or die ("SQL ERR : ".mysql_error());
while ($data1=mysql_fetch_assoc($qry1))
			{
				$optkgt.="<option value=".$data1['kelompok'].">".$data1['nama']."</option>";
			}		

			
?>


<?

//$arr="##kdorg##noakun##bk1##bk2";
//$arr="##bk1##bk2##tgl1##tgl2##replan##pmks##sisasaldo##giro##kdorg";
$arr="##kdorg##tgl##kgt";



echo "<fieldset style='float:left;'><legend><b>Harian Kegiatan Kebun</b></legend>
<table>
	<tr>
		<td>Kebun</td>
		<td>:</td>	
		<td><select id=kdorg style='width:155px;'>".$optorg."</select></td>
	</tr>
	<tr>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>:</td>
		<td><input type='text' class='myinputtext' id='tgl' onmousemove='setCalendar(this.id)' onkeypress='return false;'  size='7' maxlength='10' ></td>
	</tr>
	<tr>
		<td>Status</td>
		<td>:</td>	
		<td><select id=kgt style='width:100%;'>".$optkgt."</select></td>
	</tr>
	<tr><td colspan=2></td>
		<td colspan=4>
		<button onclick=zPreview('lbm_slave_hariankebunIAN','".$arr."','reportcontainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['preview']."</button>
		<button onclick=zExcel(event,'lbm_slave_hariankebunIAN.php','".$arr."') class=mybutton name=preview id=preview>".$_SESSION['lang']['excel']."</button>
	
		</td>
	</tr>
</table>
</fieldset>";//	<button onclick=batal() class=mybutton name=btnBatal id=btnBatal>".$_SESSION['lang']['cancel']."</button>//<button onclick=zPdf('keu_slave_2rencanabayar','".$arr."','printContainer') class=mybutton name=preview id=preview>".$_SESSION['lang']['pdf']."</button>


/*echo "
<fieldset style='clear:both'><legend><b>".$_SESSION['lang']['printArea']."</b></legend>
<div id='printContainer' style='overflow:auto;height:350px;max-width:1220px'; >
</div></fieldset>";*/





?>