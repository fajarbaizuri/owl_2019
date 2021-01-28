<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include('lib/zMysql.php');
include('lib/zFunction.php');
echo open_body();
?>

<script language=javascript1.2 src=js/vhc.js></script>
<?
include('master_mainMenu.php');
OPEN_BOX('',"<b>".$_SESSION['lang']['datamesinkendaraan']."</b>");

//get enum untuk kelompok vhc;
	$optklvhc="<option value=''></option>";
	$arrklvhc=getEnum($dbname,'vhc_5master','kelompokvhc');
	foreach($arrklvhc as $kei=>$fal)
	{
		switch($kei)
		{
			case 'AB':
			$fal='Alat Berat';
			break;
			case 'KD':
			$fal='Kendaraan';
			break;
			case 'MS':
			$fal='Mesin';
			break;			
		}
		$optklvhc.="<option value='".$kei."'>".$fal."</option>";
	} 
//ambil jenis mesin/kendaraan
  $str="select * from ".$dbname.".vhc_5jenisvhc order  by namajenisvhc";
  $res=mysql_query($str);
  $optjnsvhc="<option value=''></option>";;
  while($bar=mysql_fetch_object($res))
  {
  	$optjnsvhc.="<option value='".$bar->jenisvhc."'>".$bar->namajenisvhc."</option>";
  }	 
//=================ambil master barang untuk aset kendaraan (905)

  $str="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='900' or kelompokbarang='800' order by namabarang";
  $res=mysql_query($str);
  $optbarang='';
  while($bar=mysql_fetch_object($res))
  {
    $optbarang.="<option value='".$bar->kodebarang."'>".$bar->namabarang."</option>";	
  }
#ambil traksi
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe='TRAKSI' order by namaorganisasi";
  $res=mysql_query($str);
  $opttraksi='';
  while($bar=mysql_fetch_object($res))
  {
    $opttraksi.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";	
  }  
  
//ambil kode organisasi selain blok dan afdeling
  $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where tipe  in('KANWIL','HOLDING','KEBUN','PABRIK','TRAKSI') 
        and length(kodeorganisasi)=4 order  by namaorganisasi";
  $res=mysql_query($str);
  $optorg="<option value=''></option>";
  while($bar=mysql_fetch_object($res))
  {
  	$optorg.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
  }	 
    
$optkepemilikan=" <option value=1>".$_SESSION['lang']['miliksendiri']."</option>
                  <option value=0>".$_SESSION['lang']['sewa']."</option>";

echo"<fieldset><table>
     <tr><td>".$_SESSION['lang']['kodekelompok']."</td><td><select id=kelompokvhc onchange=loadJenis(this.options[this.selectedIndex].value),getnopol()>".$optklvhc."</select></td>
	 	 <td>".$_SESSION['lang']['jenkendabmes']."</td><td><select id=jenisvhc onchange=getList()>".$optjnsvhc."</select></td>
		 <td>No. Pol</td><td><input type=text id=nopol size=12 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=20></td></tr>
		 
	 <tr><td>".$_SESSION['lang']['kodeorganisasi']."(Owner)</td><td><select id=kodeorg onchange=getList()>".$optorg."</select></td>
	 	 <td>Kode Kendaraan</td><td><input type=text id=kodevhc size=12 onkeypress=\"return tanpa_kutip(event);\" class=myinputtext maxlength=20></td>
		 <td>Masa Akhir Berlaku KIR</td><td><input type=text class=myinputtext id=tglkir onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10></td>
		 </tr>
		 
	 <tr><td>".$_SESSION['lang']['namabarang']."</td><td><select id=kodebarang onchange style='width:200px'>".$optbarang."</select></td>
	 	 <td>".$_SESSION['lang']['tahunperolehan']."</td><td><input type=text id=tahunperolehan size=4 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=4></td>
		 <td>Masa Akhir Berlaku STNK</td><td><input type=text class=myinputtext id=tglstnk onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10></td></tr>
		 
	 <tr><td>".$_SESSION['lang']['noakun']."</td><td><input type=text id=noakun size=8 onkeypress=\"return angka_doang(event);\" class=myinputtext maxlength=16></td>
	 	 <td>".$_SESSION['lang']['beratkosong']."</td><td><input type=text id=beratkosong size=5 onkeypress=\"return angka_doang(event);\" class=myinputtextnumber maxlength=5>Kg.</td>
		 <td>Masa Akhir Berlaku Pajak</td><td><input type=text class=myinputtext id=tglpajak onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10></td></tr>
		 
	 <tr><td>".$_SESSION['lang']['nomorrangka']."</td><td><input type=text id=nomorrangka size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>
	 	 <td>".$_SESSION['lang']['nomormesin']."</td><td><input type=text id=nomormesin size=30 onkeypress=\"return tanpa_kutip(event);\" class=myinputtextnumber maxlength=45></td>
		 <td>Masa Akhir Berlaku Asuransi</td><td><input type=text class=myinputtext id=tglasuransi onkeypress=\"return false;\" onmouseover=setCalendar(this) size=10></td></tr>
		 
	 <tr><td rowspan=2>".$_SESSION['lang']['tmbhDetail']."</td><td rowspan=2><textarea id=detailvhc cols=25 rows=2 onkeypress=\"return tanpa_kutip(event);\" maxlength=255></textarea></td>
	     <td valign=top>".$_SESSION['lang']['kepemilikan']."</td><td valign=top><select id=kepemilikan>".$optkepemilikan."</select></td>
	 </tr>
         <tr>
            <td>".$_SESSION['lang']['kodetraksi']."</td><td><select id=kodetraksi>".$opttraksi."</select></td>
         </tr>
	 
     </table>
	 <input type=hidden id=method value='insert'>
	 <button class=mybutton onclick=simpanMasterVhc()>".$_SESSION['lang']['save']."</button>
	 <button class=mybutton onclick=cancelMasterVhc()>".$_SESSION['lang']['cancel']."</button>
	 </fieldset>";
echo "<fieldset><legend>".$_SESSION['lang']['list']."</legend>
    <div style='width:95%;height:300px;overflow:scroll;'>";
	$str1="select * from ".$dbname.".vhc_5master where kodetraksi like '".$_SESSON['lokasitugas']."%' order by kodeorg,kodevhc";
	$res1=mysql_query($str1);
	echo"<table class=sortable cellspacing=1 border=0>
	     <thead>
		 <tr class=rowheader>
		  <td>No</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodeorganisasi'])."</td>		 
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodekelompok'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['jenkendabmes'])."</td>
		   <td align=center>".str_replace(" ","<br>",'Kode Alat')."</td>		
                   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['namabarang'])."</td>		
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['tahunperolehan'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['noakun'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['beratkosong'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomorrangka'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['nomormesin'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['detail'])."</td>	   
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kepemilikan'])."</td>
		   <td align=center>".str_replace(" ","<br>",$_SESSION['lang']['kodetraksi'])."</td>
		   <td align=center>".str_replace(" ","<br>",'No. Polisi')."</td>
		   <td align=center>".str_replace(" ","<br>",'Masa Akhir Berlaku KIR')."</td>
		   <td align=center>".str_replace(" ","<br>",'Masa Akhir Berlaku STNK')."</td>
		   <td align=center>".str_replace(" ","<br>",'Masa Akhir Berlaku Pajak')."</td>
		   <td align=center>".str_replace(" ","<br>",'Masa Akhir Asuransi')."</td>
                  <td>".$_SESSION['lang']['action']."</td></tr>
		 </thead>
		 <tbody id=container>";
	$no=0;	 
	while($bar1=mysql_fetch_object($res1))
	{
		$no+=1;
		$str="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$bar1->kodebarang."'";
		$res=mysql_query($str);
		$namabarang='';
		while($bar=mysql_fetch_object($res))
		{
			$namabarang=$bar->namabarang;
		}
		if($bar1->kepemilikan==1)
		{
	      $dptk=$_SESSION['lang']['miliksendiri'];	
		}
		else
		{
			$dptk=$_SESSION['lang']['sewa'];
		}		
		echo"<tr class=rowcontent>
		     <td>".$no."</td>
		     <td>".$bar1->kodeorg."</td>
			 <td>".$bar1->kelompokvhc."</td>				 
			 <td>".$bar1->jenisvhc."</td>			 		
			 <td>".$bar1->kodevhc."</td>
			 <td>".$namabarang."</td>
			 <td>".$bar1->tahunperolehan."</td>
			 <td>".$bar1->noakun."</td>
			 <td>".$bar1->beratkosong."</td>		
			 <td>".$bar1->nomorrangka."</td>	
			 <td>".$bar1->nomormesin."</td> 
			 <td>".$bar1->detailvhc."</td> 	
			 <td>".$dptk."</td>
                         <td>".$bar1->kodetraksi."</td>   
			 <td>".$bar1->nopol."</td>";
			 // kir
			 if($bar1->masaberlakukir==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo "<td>".tanggalnormal($bar1->masaberlakukir)."</td>  ";
			 }
			 
			 //stnk
			 if($bar1->masaberlakustnk==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo "<td>".tanggalnormal($bar1->masaberlakustnk)."</td>  ";
			 }
			//pajak
			 if($bar1->masaberlakupajak==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo " <td>".tanggalnormal($bar1->masaberlakupajak)."</td>  ";
			 }
			 
			 //asuransi
			 if($bar1->masaberlakuasuransi==0000-00-00)
			 {  
			 	echo "<td></td>  ";
			 }
			 else
			 {
				  echo " <td>".tanggalnormal($bar1->masaberlakuasuransi)."</td>  ";
			 }
			 			 
			 echo"
			   
			 <td>
			     <img src=images/application/application_edit.png class=resicon  caption='Edit' onclick=\"fillMasterField('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."','".$bar1->noakun."','".$bar1->beratkosong."','".$bar1->nomorrangka."','".$bar1->nomormesin."','".$bar1->tahunperolehan."','".$bar1->kodebarang."','".$bar1->kepemilikan."','".$bar->kodetraksi."','".$bar1->nopol."','".tanggalnormal($bar1->masaberlakukir)."','".tanggalnormal($bar1->masaberlakustnk)."','".tanggalnormal($bar1->masaberlakupajak)."','".tanggalnormal($bar1->masaberlakuasuransi)."');\">
			     <img src=images/application/application_delete.png class=resicon  caption='Delete' onclick=\"deleteMasterVhc('".$bar1->kodeorg."','".$bar1->kelompokvhc."','".$bar1->jenisvhc."','".$bar1->kodevhc."');\">
			</td></tr>";
	}	
	
	
	
	
	echo"	 
		 </tbody>
		 <tfoot>
		 </tfoot>
		 </table>";
echo "</div></fieldset>";

CLOSE_BOX();
echo close_body();
?>