<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/zLib.php');

function hitung_jam($time1,$time2) 
{ 
$jam_keluar=$time1.":00";
$jam_masuk=$time2.":00";
list($h1,$m1,$s1) = explode(":",$jam_keluar); 
list($h,$m,$s) = explode(":",$jam_masuk);
$dtAwal = mktime($h1,$m1,$s1,"1","1","1");
$dtAkhir = mktime($h,$m,$s,"1","1","1");
if ($h1 <= 24 && $h1 >=$h && $h1 != $h){
	$jam1="24:00:00";
	$jam2="00:00:00";
	list($h3,$m3,$s3) = explode(":",$jam1);
	list($h4,$m4,$s4) = explode(":",$jam2);
	$dtjam1=mktime($h3,$m3,$s3,"1","1","1");
	$dtjam2=mktime($h4,$m4,$s4,"1","1","1");
	
	$dtSelisih1 = $dtjam1-$dtAwal; 
	$dtSelisih2 = $dtAkhir-$dtjam2; 
	
	$totalmenit1=$dtSelisih1/60; 
	$totalmenit2=$dtSelisih2/60; 
	$totalmenit3=$totalmenit1+$totalmenit2;
	
	$jam =explode(".",$totalmenit3/60); 
	
	//$jam1 =explode(".",$totalmenit1/60); 
	
	$sisamenit=($totalmenit3/60)-$jam[0]; 
	$sisamenit2=$sisamenit*60; 
	$jml_jam=$jam[0]; 
	if (strlen($sisamenit2)==1){
		 $val=$jml_jam.".0".$sisamenit2;
	}else{
		 $val=$jml_jam.".".$sisamenit2;
	}
	return $val;
	
	
}else{
	$dtSelisih = $dtAkhir-$dtAwal; 
	$totalmenit=$dtSelisih/60; 
	$jam =explode(".",$totalmenit/60); 
	$sisamenit=($totalmenit/60)-$jam[0]; 
	$sisamenit2=$sisamenit*60; 
	$jml_jam=$jam[0]; 
	if (strlen($sisamenit2)==1){
		 $val=$jml_jam.".0".$sisamenit2;
	}else{
		 $val=$jml_jam.".".$sisamenit2;
	}
	return $val;
	
}
 




} 



$hitung=hitung_jam($_POST['iskel'],$_POST['ismas']);
$jamis=hitung_jam($_POST['iskel'],$_POST['ismas']);
$jamker=hitung_jam($_POST['jdat'],$_POST['jpul']);
$jamefectif=$jamker-$jamis;
$jamls = (empty($_POST['dhil'])) ? 0 : $hitung;
$kodeorg=$_POST['kodeorg'];
$nama=$_POST['nama'];
$jdat=$_POST['jdat'];
$jpul=$_POST['jpul'];
$iskel=$_POST['iskel'];
$ismas=$_POST['ismas'];
$kode=$_POST['kode'];
$hasil = (empty($_POST['dhil'])) ? 0 : $_POST['dhil'];
$senin = (empty($_POST['senin'])) ? 0 : $_POST['senin'];
$selasa = (empty($_POST['selasa'])) ? 0 : $_POST['selasa'];
$rabu = (empty($_POST['rabu'])) ? 0 : $_POST['rabu'];
$kamis = (empty($_POST['kamis'])) ? 0 : $_POST['kamis'];
$jumat = (empty($_POST['jumat'])) ? 0 : $_POST['jumat'];
$sabtu = (empty($_POST['sabtu'])) ? 0 : $_POST['sabtu'];
$minggu = (empty($_POST['minggu'])) ? 0 : $_POST['minggu'];
$method=$_POST['method'];

//$arrEnum=getEnum($dbname,'bgt_tipe','tipe,nama');
switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_5waktudinas set nama='$nama',jammasuk='$jdat',jampulang='$jpul', istirahatkeluar='$iskel',senin='$senin',selasa='$selasa',rabu='$rabu',kamis='$kamis',jumat='$jumat',sabtu='$sabtu',minggu='$minggu',
	istirahatmasuk='$ismas',ihlembur='$hasil',	totallembur='$jamls',lokasi='$kodeorg',jamdinas='$jamefectif'
	 where idjad='".$_POST['kode']."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
case 'insert':
	$str="insert into ".$dbname.".sdm_5waktudinas set nama='$nama',jammasuk='$jdat',jampulang='$jpul', istirahatkeluar='$iskel',senin='$senin',selasa='$selasa',rabu='$rabu',kamis='$kamis',jumat='$jumat',sabtu='$sabtu',minggu='$minggu',
	istirahatmasuk='$ismas',ihlembur='$hasil',	totallembur='$jamls',lokasi='$kodeorg',jamdinas='$jamefectif'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_5waktudinas where idjad=".$kode."";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));}
	break;
default:
   break;					
}
$str1="select * from ".$dbname.".sdm_5waktudinas  order by idjad desc";
if($res1=mysql_query($str1))
{
$no=1;
while($bar1=mysql_fetch_object($res1))
{
		//echo"<tr class=rowcontent><td align=center>".$bar1->regional."</td><td>".$bar1->nama."</td><td><img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->regional."','".$bar1->nama."');\"></td></tr>";
		 echo "<tr class=rowcontent>
    <td align=center>$no</td>
    <td >$bar1->nama</td>
	<td align=center>$bar1->lokasi</td>
	<td align=center>$bar1->jammasuk</td>
    <td align=center>$bar1->jampulang</td>
	<td align=center>$bar1->istirahatkeluar</td>
	<td align=center>$bar1->istirahatmasuk</td>
	<td align=center>$bar1->jamdinas</td>";
	if ($bar1->ihlembur=='1'){
	echo"<td align=center>YA</td>";
	}else{
	echo"<td align=center>TIDAK</td>";
	}
	
	echo"
	<td align=center>$bar1->totallembur</td>
	
	<td>
	<img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillField('".$bar1->idjad."','".$bar1->lokasi."','".$bar1->nama."','".$bar1->jammasuk."','".$bar1->jampulang."','".$bar1->istirahatkeluar."','".$bar1->istirahatmasuk."','".$bar1->ihlembur."','".$bar1->senin."','".$bar1->selasa."','".$bar1->rabu."','".$bar1->kamis."','".$bar1->jumat."','".$bar1->sabtu."','".$bar1->minggu."');\">
	<img src=images/delete_32.png class=resicon  caption='Hapus' onclick=\"delField('".$bar1->idjad."');\"></td>
  </tr>";
		 $no++;
}	 
}
?>
