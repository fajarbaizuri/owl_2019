<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$namaperusahaan=$_POST['namaperusahaan'];
$bidangusaha=$_POST['bidangusaha'];

$blnmasuk=$_POST['blnmasuk'];
$thnmasuk=$_POST['thnmasuk'];
$blnkeluar=$_POST['blnkeluar'];
$thnkeluar=$_POST['thnkeluar'];
//hitung masa kerja
$thn=$thnkeluar-$thnmasuk;
$bln=intval($blnkeluar)-intval($blnmasuk);
$masakerja=(($thn*12)+$bln)/12;

//exit("Error:".$thn.":".$bln.":".$masakerja);
$blnkeluar=$blnkeluar."-".$thnkeluar;
$blnmasuk=$blnmasuk."-".$thnmasuk;
$jabatan=$_POST['jabatan'];
$bagian=$_POST['bagian'];
$alamat=$_POST['alamat'];
$karyawanid=$_POST['karyawanid'];
$nourut=$_POST['nomor'];

if($masakerja>0 or $_POST['del']=='true' or isset($_POST['queryonly']))
{
if($nourut=='')
   $nourut=0;
	if(isset($_POST['del']) and $_POST['del']=='true')
	{
		$str="delete from ".$dbname.".sdm_sensuscv where nomor=".$nourut;
	}
	else if( isset($_POST['queryonly']))
	{
		$str="select 1=1";
	}
	else
	{
		$str="insert into ".$dbname.".sdm_sensuscv
		     (`karyawanid`,
			  `namaperusahaan`,
			  `bidangusaha`,
			  `bulanmasuk`,
			  `bulankeluar`,
			  `jabatan`,
			  `bagian`,
			  `masakerja`,
			  `alamatperusahaan`
			  )
			  values(".
			  $karyawanid.",
			  '".$namaperusahaan."',
			  '".$bidangusaha."',
			  '".$blnmasuk."',
			  '".$blnkeluar."',
			  '".$jabatan."',
			  '".$bagian."',
			  ".$masakerja.",
			  '".$alamat."'
			  )";
	}
if(mysql_query($str))
   {
	 $str="select * from ".$dbname.".sdm_sensuscv
	  where karyawanid=".$karyawanid." order by bulanmasuk desc";
	 $res=mysql_query($str);
	 $no=0;
	 $mskerja=0;
	 while($bar=mysql_fetch_object($res))
	 {
	 $no+=1;
	  //$msk=mktime(0,0,0,substr(str_replace("-","",$bar->bulanmasuk),4,2),1,substr($bar->bulanmasuk,0,4));	
	 // $klr=mktime(0,0,0,substr(str_replace("-","",$bar->bulankeluar),4,2),1,substr($bar->bulankeluar,0,4));	
	//  $dateDiff = $klr - $msk;
      //$mskerja = floor($dateDiff/(60*60*24))/365; 
	  
	 echo"	  <tr class=rowcontent>
			  <td class=firsttd>".$no."</td>
			  <td>".$bar->namaperusahaan."</td>
			  <td>".$bar->bidangusaha."</td>
			  <td>".$bar->bulanmasuk."</td>
			  <td>".$bar->bulankeluar."</td>
			  <td>".$bar->jabatan."</td>
			  <td>".$bar->bagian."</td>
			  <td>".number_format($bar->masakerja,2,',','.')." Th.</td>
			  <td>".$bar->alamatperusahaan."</td>	
			  <td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPengalaman('".$karyawanid."','".$bar->nomor."');\"></td>
			</tr>";	 	
	 }
    }
	else
	{
		echo " Gagal:".addslashes(mysql_error($conn)).$str;
	}
}
else
{
	echo" Error: Incorrect Period";
}
?>
