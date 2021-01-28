<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src='js/sdm_payrollHO.js'></script>
<link rel=stylesheet type=text/css href=style/payroll.css>
<?
include('master_mainMenu.php');
//+++++++++++++++++++++++++++++++++++++++++++++
$str="select * from ".$dbname.".sdm_ho_component
      where name like '%Angs%'";
$res=mysql_query($str,$conn);
$arr=Array();
$opt='';
while($bar=mysql_fetch_object($res))
{
	$opt.="<option value=".$bar->id.">".$bar->name."</option>";
	$arr[$bar->id]=$bar->name;
}
$opt3='';
for($z=-12;$z<=64;$z++)
{
	$da=mktime(0,0,0,date('m')-$z,date('d'),date('Y'));
	$opt3.="<option value='".date('Y-m',$da)."'>".date('m-Y',$da)."</option>";
}
	OPEN_BOX('','<b>Angsuran</b>');
		echo"<div id=EList>";
		echo OPEN_THEME('Angsuran Karyawan:');
        echo"<br>(Tampilkan Angsuran Bulan:<select id=bln onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>".$opt3."</select>)
		     || (Tampilkan Angsuran Yang<select id=lunas  onchange=showAngsuran(this.options[this.selectedIndex].value)><option value=''></option>
			 <option value=lunas>Sudah Lunas</option>
			 <option value=blmlunas>Belum Lunas</option>
			 <option value=active>Active</option>
			 <option value=notactive>Not Active</option></select>)	 
			 ";
			if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
			{			    
				$str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
				      where a.karyawanid=u.karyawanid
					  (a.tipekaryawan=0 or a.lokasitugas='".$_SESSION['empl']['lokasitugas']."')
				      order by namakaryawan";
			}
			else
			{
				$str="select a.*,u.namakaryawan from ".$dbname.".sdm_angsuran a, ".$dbname.".datakaryawan u
				      where a.karyawanid=u.karyawanid
					  and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
				      order by namakaryawan";		
			}
		echo"<hr><br>Berikut Angsuran Karyawan :<b><span id=caption>Belum Lunas</span></b>
		     <image src=images/pdf.jpg class=resicon title='PDF' onclick=angsuranPDF(event)>
			 <input type=hidden id=val value=''>
			 "; 	  		     
		echo"<table class=sortable width=900px border=0 cellspacing=1>
		      <thead>
			  <tr class=rowheader>
			    <td align=center>No.</td>
				<td align=center>No.Karyawan</td>
			    <td align=center>Nama.Karyawan</td>
				<td align=center>Jenis.Angsuran</td>
				<td align=center>Total.Hutang<br>(Rp.)</td>
				<td align=center>Periode.Awal</td>
				<td align=center>Periode.Akhir</td>
				<td align=center>Lama<br>(Bulan)</td>
				<td align=center>Angs./Bln.<br>(Rp.)</td>				
				<td align=center>Active</td>
			  </tr> 
			  </thead>
			  <tbody id=tbody>";
		$res=mysql_query($str,$conn);
		$no=0;
		while($bar=mysql_fetch_object($res))
		{			  
		   $no+=1;
		   echo"<tr class=rowcontent>
			    <td class=firsttd>".$no."</td>
			    <td>".$bar->karyawanid."</td>
				<td>".$bar->namakaryawan."</td>
				<td>".$arr[$bar->jenis]."</td>
				<td align=right>".number_format($bar->total,2,'.',',')."</td>
				<td align=center>".$bar->start."</td>
				<td align=center>".$bar->end."</td>
				<td align=right>".$bar->jlhbln."</td>
				<td align=right>".number_format($bar->bulanan,2,'.',',')."</td>				
				<td align=center>".($bar->active==1?"Aktif":"Tdk.Aktif")."</td>
			  </tr>"; 
		  $ttl+=$bar->bulanan;	  			
		}
		echo"</tbody>
			  <tfoot></tfoot>
			  </table>";  	  			 
		echo"</div>";
		echo CLOSE_THEME();		
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>