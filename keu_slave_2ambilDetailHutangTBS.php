<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');


if(isset($_POST['kodesupplier'])){
  $param=$_POST;
}
else
{
  $param=$_GET;
}

#ambil sawal
$str="select sum(debet-kredit) as sawal from ".$dbname.".keu_jurnaldt_vw a 
      left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid
	  where tanggal <'".$param['tgl1']."' and a.noakun='".$param['akuntbs']."' and 
	  kodesupplier='".$param['kodesupplier']."'"; 
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
   $sawal=$bar->sawal;
} 

$str="select tanggal,nojurnal,noreferensi,kredit as hutang, debet as bayar,kodesupplier,namasupplier from ".$dbname.".keu_jurnaldt_vw a 
      left join ".$dbname.".log_5supplier b on a.kodesupplier=b.supplierid
	  where tanggal between '".$param['tgl1']."' and '".$param['tgl2']."' and a.noakun='".$param['akuntbs']."' and 
	  kodesupplier='".$param['kodesupplier']."' order by tanggal"; 
//=================================================
$stream.="Detail Hutang Supplier TBS periode ".tanggalnormal($param['tgl1'])." s/d ".tanggalnormal($param['tgl2']).":<br>
<table border=1 cellspacing=0>
			<tr class=ruheader>
			  <td bgcolor=#DEDEDE align=center>Tanggal</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nojurnal']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['noreferensi']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['namasupplier']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['nilaihutang']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['dibayar']."</td>
			  <td bgcolor=#DEDEDE align=center>".$_SESSION['lang']['sisa']."</td>
			</tr>";
		$stream.="<tr class=rowcontent>
			  <td colspan=4>Saldo Awal</td>
			  <td align=right>".number_format($sawal)."</td>
			  <td align=right>0</td>	
              <td align=right>".number_format($sawal)."</td>			  
			</tr>";			
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
	{
		$no+=1;
		$sawal+=$bar->hutang-$bar->bayar;
		$stream.="<tr class=rowcontent>
			  <td>".tanggalnormal($bar->tanggal)."</td>
			  <td>".$bar->nojurnal."</td>
			  <td>".$bar->noreferensi."</td>
			  <td>".$bar->namasupplier."</td>
			  <td align=right>".number_format($bar->hutang)."</td>
			  <td align=right>".number_format($bar->bayar)."</td>
              <td align=right>".number_format($sawal)."</td>			  
			</tr>";
	}
  $stream.="</table>";	

$stream.="</table>Print Time:".date('YmdHis')."<br>By:".$_SESSION['empl']['name'];	

echo $stream;
?>