<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script language=javascript src=js/sdm_pengobatan.js></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
OPEN_BOX('',$_SESSION['lang']['adm_peng']);
$optthn="<option value=''></option>";
for($x=-1;$x<10;$x++)
{
	$mk=mktime(0,0,0,date('m')-$x,15,date('Y'));
	$optthn.="<option value='".(date('Y-m',$mk))."'>".(date('m-Y',$mk))."</option>";
}


//ambil daftar pengobatan dengan tahun sekarang
$str="select a.*, b.*,c.namakaryawan,d.diagnosa as ketdiag from ".$dbname.".sdm_pengobatanht a left join
      ".$dbname.".sdm_5rs b on a.rs=b.id 
	  left join ".$dbname.".datakaryawan c
	  on a.karyawanid=c.karyawanid
	  left join ".$dbname.".sdm_5diagnosa d
	  on a.diagnosa=d.id
	  where a.periode='".date('Y-m')."'
	  and a.kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by a.updatetime desc, a.tanggal desc";
$res=mysql_query($str);

echo"<fieldset>
	  <legend>".$_SESSION['lang']['list']."</legend>
	  ".$_SESSION['lang']['thnplafon'].":
	  <select id=optplafon onchange=loadPengobatanPrint(this.options[this.selectedIndex].value)>".$optthn."</select>
	  <img src=images/excel.jpg onclick=printKlaim('excel') class=resicon>
	  <iframe id=frmku frameborder=0 style='width:0px;height:0px;'></iframe>
	  <table class=sortable cellspacing=1 border=0>
	  <thead>
	    <tr class=rowheader>
		<td width=50></td>
		  <td>No</td>
		  <td width=100>".$_SESSION['lang']['notransaksi']."</td>
		  <td width=50>".$_SESSION['lang']['periode']."</td>
		  <td width=30>".$_SESSION['lang']['tanggal']."</td>
		  <td width=200>".$_SESSION['lang']['namakaryawan']."</td>
		  <td>".$_SESSION['lang']['pasien']."</td>
		  <td width=150>".$_SESSION['lang']['rumahsakit']."</td>
		  <td width=50>".$_SESSION['lang']['jenisbiayapengobatan']."</td>
		  <td width=90>".$_SESSION['lang']['nilaiklaim']."</td>
		  <td>".$_SESSION['lang']['dibayar']."</td>
		  <td>".$_SESSION['lang']['diagnosa']."</td>
		  <td>".$_SESSION['lang']['keterangan']."</td>
		</tr>
	  </thead>
	  <tbody id='container'>";
	  $no=0;
	  while($bar=mysql_fetch_object($res))
	  {
	   $no+=1;
	   
	   $pasien='';
	   //get hubungan keluarga
	   $stru="select hubungankeluarga from ".$dbname.".sdm_karyawankeluarga 
	          where nomor=".$bar->ygsakit;
		$resu=mysql_query($stru);
		while($baru=mysql_fetch_object($resu))
		{
			$pasien=$baru->hubungankeluarga;
		}
	if($pasien=='')
	   $pasien='AsIs';	
			  
	   echo"<tr class=rowcontent>
	      <td>&nbsp <img src=images/zoom.png  title='view' class=resicon onclick=previewPengobatan('".$bar->notransaksi."',event)>
	       </td><td>".$no."</td>
		  <td>".$bar->notransaksi."</td>
		  <td>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</td>
		  <td>".tanggalnormal($bar->tanggal)."</td>
		  <td>".$bar->namakaryawan."</td>
		  <td>".$pasien."</td>
		  <td>".$bar->namars."[".$bar->kota."]"."</td>
		  <td>".$bar->kodebiaya."</td>
		  <td align=right>".number_format($bar->totalklaim,2,'.',',')."</td>
		  <td align=right>".number_format($bar->jlhbayar,2,'.',',')."</td>
		  <td>".$bar->ketdiag."</td>
		  <td>".$bar->keterangan."</td>
		</tr>";	  	
	  }
echo"</tbody>
	 <tfoot>
	 </tfoot>
	 </table>
	 </fieldset> 	 
	 ";	 
CLOSE_BOX();
echo close_body();
?>