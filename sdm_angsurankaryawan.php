<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src=js/sdm_payrollHO.js></script>
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

if($_SESSION['empl']['tipelokasitugas']=='HOLDING')
{
  $str1="select * from ".$dbname.".datakaryawan
      where ((tanggalkeluar='0000-00-00') or tanggalkeluar >'".date('Y-m-d')."')
	  and alokasi=1
	  order by namakaryawan";	  
}
else
{
   $str1="select * from ".$dbname.".datakaryawan
      where ((tanggalkeluar='0000-00-00') or tanggalkeluar >'".date('Y-m-d')."')
	  and tipekaryawan!=0 and LEFT(lokasitugas,4)='".substr($_SESSION['empl']['lokasitugas'],0,4)."'
	  order by namakaryawan";	
}
	  
$res1=mysql_query($str1,$conn);
$opt1='';
while($bar1=mysql_fetch_object($res1))
{
	$opt1.="<option value=".$bar1->karyawanid.">".$bar1->namakaryawan."</option>";
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
		echo"<table><tr><td>";
         echo"<table class=data>
		      <thead>
			  <tr>
			    <td align=center><b>Nama.Karyawan</b></td>
				<td align=center><b>Jenis.Angsuran</b></td>
				<td align=center><b>Total.Hutang<br>(Rp.)</b></td>
				<td align=center>Periode.Awal<br>Pemotongan</td>
				<td align=center>Lama<br>(Bulan)</td>
				<td align=center>Active</td>
			  </tr> 
			  </thead>
			  <tbody>
			  <tr class=rowcontent>
			  <td><select id=userid>".$opt1."</select></td>
			  <td><select id=idx>".$opt."</select></td>
			  <td><input type=text id=total class=myinputtextnumber size=13 maxlength=14 onkeypress=\"return angka_doang(event);\" onblur=change_number(this)></td>
			  <td><select id=start>".$opt3."</select></td>
			  <td><input type=text id=lama class=myinputtextnumber size=4 maxlength=3 onkeypress=\"return angka_doang(event);\" value=0></td>
			  <td><select id=active><option value=1>Active</option>
			  <option value=0>Not Active</option></select>
			  <input type=hidden value='insert' id=method>
			  </td>
			  </tr>
			  </body>
			  <tfoot></tfoot>
		      </table>
			  <center>
			    <button class=mybutton onclick=saveAngsuran()>".$_SESSION['lang']['save']."</button>
			    <button class=mybutton onclick=cancelAngsuran()>".$_SESSION['lang']['cancel']."</button>
			  </center>
			  ";
		echo"</td><td>
			     <fieldset style='text-align:left;width:300px;'>
				   <legend><b><img src=images/info.png align=left height=25px valign=asmiddle>[Info]</b></legend>
				   <p>Satu karyawan hanya dapat memiliki satu setiap jenis angsuran.
				      Jika angsuran sudah ada dan diinput dengan tipe yang  sama maka angsuran lama akan ditimpah. Untuk menambah komponen angsuran
					  gunakan menu <b>Payroll Component</b> dengan syarat, awal nama komponen harus '<b>Angsuran</b>'. 
				   </p>
				   </fieldset>		      
		      </td></tr>
			  </table>";				  
		echo CLOSE_THEME();
		echo"<hr><div id=laporan style='width:100%; height:340px;overflow:scroll;'>
		     List Angsuran:";
         echo"<table class=sortable width=100% border=0 cellspacing=1>
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
				<td align=center></td>
			  </tr> 
			  </thead>
			  <tbody id=tbody>";
	if($_SESSION['org']['tipeorganisasi']=='HOLDING')
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
					<td>
		             <img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"editAngsuran('".$bar->karyawanid."','".$bar->jenis."','".$bar->total."','".$bar->start."','".$bar->jlhbln."','".$bar->active."');\">
		             &nbsp <img src=images/application/application_delete.png class=resicon  title='delete' onclick=\"delAngsuran('".$bar->karyawanid."','".$bar->jenis."');\">		
					</td>				
			  </tr>"; 			
		}	  	  
		echo"</body>
			  <tfoot></tfoot>
		      </table>";  	  			 
		echo"</div>";
		echo"</div>";
	CLOSE_BOX();	
//+++++++++++++++++++++++++++++++++++++++++++
echo close_body();
?>