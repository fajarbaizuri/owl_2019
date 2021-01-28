<?
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
 
<script language=javascript1.2 src=js/setup_gantiLokasiTugas.js></script>
<?
include('master_mainMenu.php');


//print_r($_SESSION['standard']); 
if ($_SESSION['standard']['username']=='uli.hartati' || $_SESSION['standard']['username']=='nurush' || $_SESSION['standard']['username']=='irza.zahrun.2013' || $_SESSION['standard']['username']=='irza.zahrun' || $_SESSION['standard']['username']=='idris.amin' || $_SESSION['standard']['username']=='afuan.ortega' || $_SESSION['standard']['username']==indrawibi || $_SESSION['standard']['username']==angelwhite || $_SESSION['standard']['username']==arieftjahyadi || $_SESSION['standard']['username']=='didin.wahyudin'|| $_SESSION['standard']['username']=='alfajri'|| $_SESSION['standard']['username']=='mulya.yasin'|| $_SESSION['standard']['username']=='fernanto'|| $_SESSION['standard']['username']=='joeratmoko'|| $_SESSION['standard']['username']=='leo.candra' || $_SESSION['standard']['username']=='tribudi' || $_SESSION['standard']['username']=='ruslin.arifin' || $_SESSION['standard']['username']=='ruslin.arifin.2018' || $_SESSION['standard']['username']=='budi.arsono' || $_SESSION['standard']['username']=='mulya.yasin' || $_SESSION['standard']['username']=='akhmad.adam' || $_SESSION['standard']['username']=='apip.kamil' || $_SESSION['standard']['username']=='rahma.ian'|| $_SESSION['standard']['username']=='dadeng'|| $_SESSION['standard']['username']=='Andriyanto'|| $_SESSION['standard']['username']=='leo.dediyanto'|| $_SESSION['standard']['username']=='yudi.utomo'|| $_SESSION['standard']['username']=='ktu.usj.backup') 
{
	//$_SESSION['standard']['username']==indrawibi || 
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where length(kodeorganisasi)=4 
	  order by namaorganisasi";
}
else if ($_SESSION['standard']['username']=='hevi.wahyuni' || $_SESSION['standard']['username']=='agus.gega' || $_SESSION['standard']['username']=='achmad.rivai')
{
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where kodeorganisasi in('TDAE','TDBE','CBGM','USJE','FBAO','FBHO') 
	  order by namaorganisasi";
}
else if ($_SESSION['standard']['username']=='t.izwar' || $_SESSION['standard']['username']=='ibnu.hajar' || $_SESSION['standard']['username']=='veradillah')
{
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where kodeorganisasi in('TDAE','TDBE','CBGM','TKFB','USJE','FBAO') 
	  order by namaorganisasi";
}
else if ($_SESSION['standard']['username']=='rita.anggraini')
{
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where kodeorganisasi in('TKFB','FBAO') 
	  order by namaorganisasi";
}

else
{
	$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where tipe='HOLDING' and length(kodeorganisasi)=4 
	  order by namaorganisasi";
}
/*$str="select kodeorganisasi,namaorganisasi,alokasi from ".$dbname.".organisasi 
      where tipe='HOLDING' and length(kodeorganisasi)=4 
	  order by namaorganisasi";*/
$res=mysql_query($str);
   $opt="<option value='".$_SESSION['empl']['kodeorganisasi']."'>".$_SESSION['empl']['lokasitugas']."</option>";
   
   
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value='".$bar->alokasi."'>".$bar->kodeorganisasi."</option>";
}
OPEN_BOX('',$_SESSION['lang']['pindahtugas']);
echo "<br><br>You are ON:<b>".$_SESSION['empl']['lokasitugas']."</b><br> ".$_SESSION['lang']['tujuan']."
      <select id=tjbaru>".$opt."</select><br>
	  <button class=mybutton onclick=gantiLokasitugas()>".$_SESSION['lang']['save']."</button>
	  ";
CLOSE_BOX();
echo close_body();
?>
