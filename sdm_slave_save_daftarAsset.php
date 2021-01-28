<?
require_once('master_validation.php');
require_once('config/connection.php');

$kodeorg		=$_POST['kodeorg'];
$tipe			=$_POST['tipe'];
$kodeasset		=$_POST['kodeasset'];
$kodebarang		=$_POST['kodebarang'];
$namaaset		=$_POST['namaaset'];
$tahunperolehan	=$_POST['tahunperolehan'];
$nilaiperolehan	=$_POST['nilaiperolehan'];
$jumlahbulan	=$_POST['jumlahbulan'];
$bulanawal		=$_POST['bulanawal'];
$keterangan		=$_POST['keterangan'];
$status			=$_POST['status'];
$method			=$_POST['method'];

if($jumlahbulan!=='' and $jumlahbulan!='')
{
   $bulanan=$nilaiperolehan/$jumlahbulan;
   $tahunan=$bulanan*12;
   }
else
{
  $bulanan=0;  
  $tahunan=0;  
  }
$tex='';

if(isset($_POST['txtcari']))
{
	$tex=" and kodeasset like '%".$_POST['txtcari']."%' or namasset like '%".$_POST['txtcari']."%'";
}
//==================
//limit/page
$limit=20;
$page=0;
  if(isset($_POST['page']))
     {
	 	$page=$_POST['page'];
	    if($page<0)
		  $page=0;
	 }
  $offset=$page*$limit;
//===========================

	$str="select a.*		  
		  from ".$dbname.".sdm_daftarasset a
		  where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
		  ".$tex;
	$res=mysql_query($str);	  
	$jlhbrs=mysql_num_rows($res);
	//===================================================
switch($method)
{
case 'update':	
	$str="update ".$dbname.".sdm_daftarasset set 
	       tipeasset='".$tipe."',
		   kodebarang='".$kodebarang."',
		   namasset='".$namaaset."',
		   tahunperolehan=".$tahunperolehan.",
		   status=".$status.",
		   hargaperolehan=".$nilaiperolehan.",
		   jlhblnpenyusutan=".$jumlahbulan.",
		   awalpenyusutan='".$bulanawal."',
		   keterangan='".$keterangan."',
		   user=".$_SESSION['standard']['userid'].",
                   bulanan=".$bulanan.",    
				   tahunan=".$tahunan."
	       where kodeasset='".$kodeasset."'
		   and kodeorg='".$kodeorg."'";
		   
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
		 exit(0);
	}
	break;
case 'insert':

	$str="insert into ".$dbname.".sdm_daftarasset (
	       tipeasset,kodeorg,kodebarang,
		   namasset,tahunperolehan,status,
		   hargaperolehan,jlhblnpenyusutan,
		   awalpenyusutan,keterangan,kodeasset,user,bulanan,tahunan
		   )
	      values(
		    '".$tipe."','".$kodeorg."','".$kodebarang."','".$namaaset."',
			".$tahunperolehan.",".$status.",".$nilaiperolehan.",".$jumlahbulan.",'".$bulanawal."',
			'".$keterangan."','".$kodeasset."','".$_SESSION['standard']['userid']."','".$bulanan."','".$tahunan."' 
			)";
			
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
		 exit(0);
	}	
	break;
case 'delete':
	$str="delete from ".$dbname.".sdm_daftarasset 
	where kodeasset='".$kodeasset."'";
	if(mysql_query($str))
	{}
	else
	{echo " Gagal,".addslashes(mysql_error($conn));
	 exit(0);
	}
	break;
default:
   break;					
}
	$str="select a.*,b.namatipe, 
	      CASE a.status
		  when 0 then '".$_SESSION['lang']['pensiun']."'
		  when 1 then '".$_SESSION['lang']['aktif']."' 
		  when 2 then '".$_SESSION['lang']['rusak']."' 
		  when 3 then '".$_SESSION['lang']['hilang']."' 
		  else 'Unknown'
          END as stat		  
		  from ".$dbname.".sdm_daftarasset a
	      left join  ".$dbname.".sdm_5tipeasset b
	      on a.tipeasset=.b.kodetipe
		  where kodeorg='".substr($_SESSION['empl']['lokasitugas'],0,4)."' ".$tex." 
		  order by tahunperolehan desc,awalpenyusutan desc,namatipe asc
		   limit ".$offset.",".$limit;
		  
	$res=mysql_query($str);

	$no=$offset;
	while($bar=mysql_fetch_object($res))
	{
	  $no+=1;
	  echo"<tr class=rowcontent>
	          <td>".$no."</td>
		      <td>".$bar->kodeorg."</td>
			  <td>".$bar->namatipe."</td>
			  <td>".$bar->kodeasset."</td>
			  <td>".$bar->namasset."</td>
			  <td align=right>".$bar->tahunperolehan."</td>
			  <td>".$bar->stat."</td>
			  <td align=right>".number_format($bar->hargaperolehan,2,'.',',')."</td>
			  <td align=right>".$bar->jlhblnpenyusutan."</td>
			  <td align=center>".substr($bar->awalpenyusutan,5,2)."-".substr($bar->awalpenyusutan,0,4)."</td>
			  <td>".$bar->keterangan."</td>
			  <td>
			   <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAsset('".$bar->kodeorg."','".$bar->tipeasset."','".$bar->kodeasset."','".$bar->namasset."','".$bar->kodebarang."','".$bar->tahunperolehan."','".$bar->stat."','".$bar->hargaperolehan."','".$bar->jlhblnpenyusutan."','".$bar->awalpenyusutan."');\">
			   
		      &nbsp
			  </td>
		   </tr>
		   </tr>";		
		   /*
		    <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAsset('".$bar->kodeorg."','".$bar->kodeasset."');\">
			*/
	}
  echo"<tr><td colspan=12 align=center>
       ".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
	   <br>
       <button class=mybutton onclick=cariAsset(".($page-1).");>".$_SESSION['lang']['pref']."</button>
	   <button class=mybutton onclick=cariAsset(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
	   </td>
	   </tr>";	
?>
