<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_laporan_ijin_keluar_kantor.js'></script>
<script>
    tolak="<? echo $_SESSION['lang']['ditolak'];?>";
    </script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['list']." ".$_SESSION['lang']['izinkntor']).'</b>');

$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
$optJenis=$optKary;
$sKary="select distinct karyawanid,namakaryawan from ".$dbname.".datakaryawan where alokasi=1 order by namakaryawan asc";
$qKary=mysql_query($sKary) or die(mysql_error($sKary));
while($rKary=mysql_fetch_assoc($qKary))
{
    $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."</option>";
}
		$arragama=getEnum($dbname,'sdm_ijin','jenisijin');
		foreach($arragama as $kei=>$fal)
		{
			$optJenis.="<option value='".$kei."'>".$fal."</option>";
		}  
echo"
     <img onclick=detailExcel(event,'sdm_slave_laporan_ijin_meninggalkan_kantor.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
     &nbsp;".$_SESSION['lang']['namakaryawan'].": <select id=karyidCari style=width:150px onchange=getCariDt()>".$optKary."</select>&nbsp;
     ".$_SESSION['lang']['jeniscuti'].": <select id=jnsCuti style=width:150px onchange=getCariDt()>".$optJenis."</select>&nbsp;
         <button class=mybutton onclick=dtReset()>".$_SESSION['lang']['cancel']."</button>
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0>
	     <thead>
		    <tr>
			  <td align=center>No.</td>
			  <td align=center>".$_SESSION['lang']['tanggal']."</td>
			  <td align=center>".$_SESSION['lang']['nama']."</td>
			  <td align=center>".$_SESSION['lang']['keperluan']."</td>
                          <td align=center>".$_SESSION['lang']['jenisijin']."</td>  
                          <td align=center>".$_SESSION['lang']['persetujuan']."</td>    
                          <td align=center>".$_SESSION['lang']['approval_status']."</td>
                          <td align=center>".$_SESSION['lang']['dari']."  ".$_SESSION['lang']['jam']."</td>
                          <td align=center>".$_SESSION['lang']['tglcutisampai']."  ".$_SESSION['lang']['jam']."</td>
                          <td align=center>".$_SESSION['lang']['jumlahhk']."(Diambil)</td>
                          <td align=center>".$_SESSION['lang']['atasan']."</td>
                          <td align=center>".$_SESSION['lang']['hrd']."</td> 
                          <td align=center>".$_SESSION['lang']['print']."</td>    
			</tr>  
		 </thead>
		 <tbody id=container><script>loadData()</script>
		 </tbody>
		 		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>