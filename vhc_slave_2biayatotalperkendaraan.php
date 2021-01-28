<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

//	$pt=$_POST['pt']; source: log_laporanHutangSupplier.php
	$unit=$_POST['unit'];
	$periode=$_POST['periode'];
        $tglAwal=tanggalsystemd($_POST['tglAwal']);
        $tglAkhir=tanggalsystemd($_POST['tglAkhir']);
if($unit=='')
{
    echo"warning:Unit tidak boleh kosong";exit();
}
if($tglAwal==''||$tglAkhir==''){
	echo "Warning: silakan mengisi tanggal"; exit;
}

$str="select induk from ".$dbname.".organisasi
      where kodeorganisasi ='".$unit."'";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
	$induk=$bar->induk;
	$hasil.="<option value='".$bar->periode."'>".$bar->periode."</option>";
}
//$str="select tanggalmulai, tanggalsampai from ".$dbname.".setup_periodeakuntansi
//      where kodeorg ='".$unit."' and periode='".$periode."'";
$str="select distinct tanggalmulai, tanggalsampai,periode from ".$dbname.".setup_periodeakuntansi
      where kodeorg ='".substr($unit,0,4)."' and substring(tanggalmulai,1,7)='".substr($tglAwal,0,7)."'  
      and substring(tanggalsampai,1,7)='".substr($tglAkhir,0,7)."'";
//exit("Error".$str);
$res=mysql_query($str);
$row=mysql_num_rows($res);
if($row!=0)
{
    while($bar=mysql_fetch_object($res))
    {
        if($bar->periode!='')
        {
            $tanggalmulai=$bar->tanggalmulai;
            $tanggalsampai=$bar->tanggalsampai;
            $periode=$bar->periode;
        }

    }
}
else
{
    echo"warning:Tanggal diluar periode akutansi";
    exit();
}

	/*$str="select sum(debet) as jumlah, kodevhc from ".$dbname.".keu_jurnaldt_vw where
		  kodevhc in (select kodevhc from ".$dbname.".vhc_5master where kodeorg like '%".substr($unit,0,4)."%')
		  and tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and nojurnal like '%".substr($unit,0,4)."%'
		  and noreferensi not like '%ALK_KERJA_AB%'
                  group by kodevhc";*/
				  
				  
		$str="select sum(a.debet) as jumlah, a.kodevhc,c.namajenisvhc from ".$dbname.".keu_jurnaldt_vw a
		 join ".$dbname.".vhc_5master b
		 join ".$dbname.".vhc_5jenisvhc c
		 on  a.kodevhc=b.kodevhc and b.jenisvhc=c.jenisvhc
		  where
		  a.kodevhc in (select b.kodevhc from ".$dbname.".vhc_5master where kodeorg like '%".substr($unit,0,4)."%')
		  and tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and nojurnal like '%".substr($unit,0,4)."%'
		  and noreferensi not like '%ALK_KERJA_AB%'
                  group by a.kodevhc";	
				  
//				  echo $str;		  

//=================================================
	 
	$res=mysql_query($str);
	$no=0;
	if(mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=4>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
#ambil jumlah jam per kendaraan
//   $str1="select sum(jumlah) as jumlah,kodevhc from ".$dbname.".vhc_rundt_vw
//      where tanggal like '".$periode."%' and kodevhc in (select kodevhc from ".$dbname.".vhc_5master
//      where kodetraksi like '".$unit."%')
//      group by kodevhc";
            
   $str1="select sum(jumlah) as jumlah,kodevhc from ".$dbname.".vhc_rundt_vw
      where tanggal>='".$tglAwal."' and tanggal<='".$tglAkhir."' and kodevhc in (select kodevhc from ".$dbname.".vhc_5master
      where kodetraksi like '".$unit."%')
      group by kodevhc";

   $res1=mysql_query($str1); 
   $jumlahjam=Array();
   while($bar1=mysql_fetch_object($res1))
   {
       $jumlahjam[$bar1->kodevhc]=$bar1->jumlah;
   }
    #loop per kendaraan        
            while($bar=mysql_fetch_object($res))
		{
			$no+=1; $total=0;
			if($jumlahjam[$bar->kodevhc]>0)
                            $rpunit=$bar->jumlah/$jumlahjam[$bar->kodevhc];
                        else
                            $rpunit=0;
                        
                       if(isset($jumlahjam[$bar->kodevhc])){
                            $color='#dedede';
                            $title='Normal';
                            $tmblDetail="<img onclick=\"detailAlokasi(event,'".$bar->kodevhc."','".$rpunit."');\" title=\"Detail Alokasi\" class=\"resicon\" src=\"images/zoom.png\">";
                       }
                       else{
                            $color='red';
                            $title='Tidak ada pekerjaan';
                            $tmblDetail="";
                       }
                       $ondiKlik=" style='cursor:pointer;' title='Click' onclick=\"viewDetail(event,'".$bar->kodevhc."','".$tglAwal."','".$tglAkhir."','".substr($unit,0,4)."','".$periode."');\"";
                        echo"<tr class=rowcontent  class=rowcontent >
				  <td align=right ".$ondiKlik." >".$no."</td>
				  <td ".$ondiKlik.">".$bar->kodevhc."</td>
				  <td ".$ondiKlik.">".$bar->namajenisvhc."</td>
			
				
				  <td ".$ondiKlik." align=right>".number_format($bar->jumlah)."</td>
                                  <td ".$ondiKlik." align=right bgcolor=".$color." title='".$title."'>".$jumlahjam[$bar->kodevhc]."</td> 
                                  <td ".$ondiKlik." align=right>".number_format($rpunit)."</td> 
                                  <td align=center>".$tmblDetail."</td>
				</tr>";
		}

	}
         // <td ".$ondiKlik." align=right>".$periode."</td>
?>