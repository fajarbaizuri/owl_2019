<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zFunction.php');

//Numrows perpage==20;



	/*$thn1='';
	if($thnmsk!='')
	{
		$thn1="and left(tanggalmasuk,4)='".$thnmsk."'   ";
	}
	
	$bln1='';
	if($blnmsk!='')
	{
		$bln1="and mid(tanggalmasuk,6,2)='".$blnmsk."'  ";
	}
	$thn2='';
	if($thnkel!='')
	{
		$thn2="and left(tanggalkeluar,4)='".$thnkel."'  ";
	}
	
	$bln2='';
	if($blnkel!='')
	{
		$bln2="and mid(tanggalkeluar,6,2)='".$blnkel."' ";
	}*/

$getrows=20;
//default query
if($_POST['page'])
   $page=$_POST['page'];
else
   $page=1; 
  
$maxdisplay=($page*$getrows-20);

if(isset($_POST['txtsearch']))
{
	$txtsearch=$_POST['txtsearch'];
	$orgsearch=$_POST['orgsearch'];	
	$tipesearch=$_POST['tipesearch'];
	$statussearch=$_POST['statussearch'];	
	$thnmsk=$_POST['thnmsk'];
	$blnmsk=$_POST['blnmsk'];
	$thnkel=$_POST['thnkel'];
	$blnkel=$_POST['blnkel'];
	$schjk=$_POST['schjk'];
	

}
else
{
	$txtsearch='';
	$orgsearch='';	
	$tipesearch='';
	$statussearch='';	
	$thnmsk='';
	$blnmsk='';
	$thnkel='';
	$blnkel='';
	$schjk='';
}

//exit("Error:$thnkel");
//echo $schjk;

$where='';
if($txtsearch!='')
   $where= " and a.namakaryawan like '%".$txtsearch."%'";
if($orgsearch!='')
   $where .=" and (a.lokasitugas='".$orgsearch."' or a.subbagian='".$orgsearch."') ";    
if($tipesearch!='')
   $where .=" and a.tipekaryawan='".$tipesearch."'";  

	if($thnmsk!='')
	{
		$where.="and left(a.tanggalmasuk,4)='".$thnmsk."'   ";
	}
	

	if($blnmsk!='')
	{
		$where.="and mid(a.tanggalmasuk,6,2)='".$blnmsk."'  ";
	}

	if($thnkel!='')
	{
		$where.="and left(a.tanggalkeluar,4)='".$thnkel."'  ";
	}
	

	if($blnkel!='')
	{
		$where.="and mid(a.tanggalkeluar,6,2)='".$blnkel."' ";
	}   
   
	if($statussearch=='*')
	   $where .=" and (a.tanggalkeluar!='0000-00-00')";
	else if($statussearch=='0000-00-00')
	   $where .=" and (a.tanggalkeluar='0000-00-00')";
	else
	{} 
	 
	 if($schjk!='')
	 {
		 $where.=" and a.jeniskelamin='".$schjk."'";
	 }
	 


	
   
//make sure user can only access allowed data   
$listOrg=ambilLokasiTugasDanTurunannya('list',$_SESSION['empl']['lokasitugas']);
$list=str_replace("|","','",$listOrg);
$list="'".$list."'";

if(trim($_SESSION['empl']['tipelokasitugas'])=='HOLDING')
{
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datasensus a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
	  a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan 
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;
	  
	  //echo $str;    
 $strx="select count(*) as jlh from ".$dbname.".datasensus a where 1=1 ".$where."  ";  
}
else
{
//a.tipekaryawan!=0 orang yang tidak di pusat tidak dapat melihat data orang permanent
$str="select a.*,b.namajabatan,c.namagolongan,d.tipe from ".$dbname.".datakaryawan a, 
      ".$dbname.".sdm_5jabatan b, ".$dbname.".sdm_5golongan c,  ".$dbname.".sdm_5tipekaryawan d where 
      lokasitugas in(".$list.")
	  and a.kodejabatan=b.kodejabatan and a.kodegolongan=c.kodegolongan
	  and d.id=a.tipekaryawan and a.tipekaryawan!=0
	  ".$where."
	  limit ".$maxdisplay.",".$getrows
	  ;
 $strx="select count(*) as jlh from ".$dbname.".datakaryawan a
        where lokasitugas in(".$list.") ".$where."  "; 	     	
}
//==================jlh karyawan
$jlhkar=0;
$resx=mysql_query($strx);
echo mysql_error($conn);
while($barx=mysql_fetch_object($resx))
{
	$jlhkar=$barx->jlh;
}


//=====================

$res=mysql_query($str);
$numrows=mysql_num_rows($res);
/*if($numrows<1)
{
	echo "<tr><td>NOT FOUND</td></tr>";
}
else
{*/


	

	
	$no=$maxdisplay;
	if($jlhkar==0)
	{
		echo"<tr><td colspan=2>DATA NOT FOUND</td></tr>";	
	}
	if($jlhkar!==0)
	{
		echo"<tr><td colspan=2>Total: ".$jlhkar." Person</td></tr>";	
	}
	while($bar=mysql_fetch_object($res))
	{
		//get pendidikan terakhir
		$str1="select a.kelompok from ".$dbname.".sdm_5pendidikan a
		       where a.levelpendidikan=".$bar->levelpendidikan." "; 
		$res1=mysql_query($str1);	
		$pendidikan="";
		while($barpendidikan=mysql_fetch_object($res1))
		{
			$pendidikan=$barpendidikan->kelompok;
		}
		   
		$no+=1;
		echo "<tr class=rowcontent>
		     <td>".$no."</td>
			 <td width=85>".$bar->nik."</td>
			 <td>".$bar->namakaryawan."</td>
			 <td>".$bar->namajabatan."</td>
			 <td>".$bar->namagolongan."</td>
			 <td>".$bar->lokasitugas."</td>
			 <td>".$bar->kodeorganisasi."</td>
			 <td>".$bar->subbagian."</td>
			 <td>".$pendidikan."</td>
			 <td>".$bar->statuspajak."</td>
			 <td>".$bar->statusperkawinan."</td>
			 <td align=right >".$bar->jumlahanak."</td>
			 <td>".tanggalnormal($bar->tanggalmasuk)."</td>
			 <td>".tanggalnormal($bar->tanggalkeluar)."</td>
			 <td>".$bar->tipe."</td>
			 <td>
				    <img src=images/zoom.png class=resicon  title='".$_SESSION['lang']['view']."' onclick=\"previewKaryawan('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">
					<img src=images/pdf.jpg class=resicon  title='".$_SESSION['lang']['pdf']."' onclick=\"previewKaryawanPDF('".$bar->karyawanid."','".$bar->namakaryawan."',event);\">		 
			 </td>
			  </tr>";			 		  
	}
//}
?>
