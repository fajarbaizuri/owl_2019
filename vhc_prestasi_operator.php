<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
?>
<script language=javascript1.2 src="js/vhc_laporan.js"></script>
<?
include('master_mainMenu.php');
OPEN_BOX('','<b>Monitoring Penghasilan Operator/Sopir</b>');

//get existing period
$str="select distinct substr(tanggal,1,7) as periode from ".$dbname.".vhc_kendaraanht
      order by periode desc";
	  
	  
$res=mysql_query($str);
#$optper="<option value=''>".$_SESSION['lang']['sekarang']."</option>";
$optper="";
while($bar=mysql_fetch_object($res))
{
	$optper.="<option value='".$bar->periode."'>".substr($bar->periode,5,2)."-".substr($bar->periode,0,4)."</option>";
}	
if($_SESSION['empl']['tipelokasitugas']=='HOLDING' or $_SESSION['empl']['tipelokasitugas']=='KANWIL' or $_SESSION['empl']['lokasitugas']=='FBAO')
{   
  
        //=================ambil PT;  
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
              where tipe='PT'
                  order by namaorganisasi";
        $res=mysql_query($str);
        $optpt="";
        while($bar=mysql_fetch_object($res))
        {
                $optpt.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }

        //=================ambil gudang;  
		$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                        where length(kodeorganisasi)=4 and induk = 'FBB'
                        ";
						/*
        $str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi
                        where (tipe='KEBUN' or tipe='PABRIK' or tipe='KANWIL'
                        or tipe='HOLDING' or tipe='TRAKSI')  and induk!=''
                        ";
						*/
        $res=mysql_query($str);
        $optgudang="<option value=''>".$_SESSION['lang']['all']."</option>";
        while($bar=mysql_fetch_object($res))
        {
                $optgudang.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";

        }
}
else
{
        $optpt="";
        $optpt.="<option value='".$_SESSION['empl']['kodeorganisasi']."'>". $_SESSION['empl']['kodeorganisasi']."</option>";
         $optgudang="<option value='".$_SESSION['empl']['lokasitugas']."'>".$_SESSION['empl']['lokasitugas']."</option>";
   
}


echo"<fieldset>
     <legend>Pencarian Berdasarkan</legend>
	 ".$_SESSION['lang']['pt']." : "."<select id=pt style='width:200px;'  onchange=ambilAnak(this.options[this.selectedIndex].value)>".$optpt."</select>
	 ".$_SESSION['lang']['']."<select id=gudang style='width:150px;' onchange=hideById('printPanel') >".$optgudang."</select>
	 
	 ".$_SESSION['lang']['periode']." : "."<select id=periode onchange=hideById('printPanel')>".$optper."</select>
	 <button class=mybutton onclick=getPrestasiOperator()>".$_SESSION['lang']['proses']."</button>
	 </fieldset>";
CLOSE_BOX();
OPEN_BOX('','Result:');
echo"<span id=printPanel style='display:none;'>
     <img onclick=fisikKeExcelSopir(event,'vhc_slave_pressopir.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 </span>    
	 <div style='width:100%;height:359px;overflow:scroll;'>
       <table class=sortable cellspacing=1 border=0 width=100%>
	     <thead>
		    <tr>
			  <td align=center rowspan=2>Unit</td>	
			  <td align=center rowspan=2>Nama Karyawan</td>
			  <td align=center rowspan=2>Jabatan</td>
			  <td align=center rowspan=2>Status</td>
			  <td align=center colspan=5>Penghasilan Karyawan</td>
			   <td align=center rowspan=2 width=\"180px\">Info Laporan</td>
			</tr>  
			 <tr>
			  <td align=center >Upah</td>
			  <td align=center >Premi</td>
			  <td align=center >Uang Makan</td>
			  <td align=center >Insentiv</td>
			  <td align=center >Total</td>
			</tr>  
		 </thead>
		 <tbody id=container>
		 </tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>
     </div>";
CLOSE_BOX();
close_body();
?>