<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/fpdf.php');
include_once('lib/zLib.php');

$arr="##kdOrg##kdAfd##tgl1##tgl2##kdKeg";
$proses=$_GET['proses'];
$lksiTgs=$_SESSION['empl']['lokasitugas'];
$kdOrg=$_POST['kdOrg'];
$kdKeg=$_POST['kdKeg'];
$kdAfd=$_POST['kdAfd'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
	$kdOrg=$_GET['kdOrg'];
	$kdAfd=$_GET['kdAfd'];
	$kdKeg=$_GET['kdKeg'];
	$tgl1_=$_GET['tgl1'];  
	$tgl2_=$_GET['tgl2'];  
}
if($kdAfd=='')
    $kdAfd=$kdOrg;
if($kdKeg=='')
    $kdKeg='%%';
$lha=true; if($tgl2_!='')$lha=false;


 

// luas areal
$luas=0;
          $str="select luasareaproduktif from ".$dbname.".setup_blok 
                where kodeorg like '".$kdAfd."%'";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $luas+=$bar->luasareaproduktif;
          }
		  
		  
//          echo $luas;

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);
$tglqwe1=juliantojd(substr($tgl1_,4,2),substr($tgl1_,6,2),substr($tgl1_,0,4));
$tglqwe2=juliantojd(substr($tgl2_,4,2),substr($tgl2_,6,2),substr($tgl2_,0,4));
$jumlahhari=1+$tglqwe2-$tglqwe1;

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if($kdOrg=='N/A'){
            echo"Error: Kebun tidak boleh kosong."; exit;
    }

    if($tgl1_==''){
            echo"Error: Tanggal tidak boleh kosong."; exit;
    }
	
}
 
#---------------------------------------------------------------
#Ambil Kegiatan Pemeliharaan 
#---------------------------------------------------------------
$strA="SELECT  B.kodekegiatan,left(B.kodeorg,6) as afd FROM ".$dbname.".`kebun_aktifitas` A LEFT JOIN ".$dbname.".`kebun_prestasi` B 
ON A.notransaksi=B.notransaksi where A.kodeorg = '".$kdOrg."' and A.jurnal='1' and A.tanggal >='".$tgl1."' and A.tanggal <='".$tgl2."' and A.tipetransaksi<>'PNN' and B.kodeorg like '%".$kdAfd."%' and B.kodekegiatan like '".$kdKeg."' group by B.kodekegiatan,left(B.kodeorg,6) order by left(B.kodeorg,6),B.kodekegiatan asc;";
$resA= mysql_query($strA);
$START=Array();
$A=1;
while($barA=mysql_fetch_object($resA))
{     
   
      $START[$barA->kodekegiatan][$barA->afd]=$A;
	  $A++;
}
// echo $strA;
#---------------------------------------------------------------
#Ambil Prestasi Kegiatan Pemeliharaan
#---------------------------------------------------------------
$str1="SELECT count(A.notransaksi) as rotasi, B.kodekegiatan,left(B.kodeorg,6) as afd,B.kodeorg,sum(B.hasilkerja) as kerja,sum(B.jumlahhk) as hk,sum(B.jumlahhk * A.proumr)as bumr,sum(B.jumlahhk * A.propre)as bpremi FROM 
".$dbname.".`kebun_kehadiran_proporsi` A LEFT JOIN ".$dbname.".`kebun_prestasi` B  ON A.notransaksi=B.notransaksi 
 where A.kodeorg = '".$kdOrg."'  and A.tanggal >='".$tgl1."' and A.tanggal <='".$tgl2."' and B.kodeorg like '%".$kdAfd."%' and B.kodekegiatan like '".$kdKeg."'
 group by B.kodekegiatan,B.kodeorg
 order by B.kodeorg,B.kodekegiatan asc;";

$res1=  mysql_query($str1);
$TAMPUNG=Array();
while($bar1=mysql_fetch_object($res1))
{     
	$TAMPUNG[$bar1->kodekegiatan][$bar1->afd][$bar1->kodeorg]['rotasi']=$bar1->rotasi;
	$TAMPUNG[$bar1->kodekegiatan][$bar1->afd][$bar1->kodeorg]['hasilkerja']=$bar1->kerja;
	$TAMPUNG[$bar1->kodekegiatan][$bar1->afd][$bar1->kodeorg]['jumlahhk']=$bar1->hk;
	$TAMPUNG[$bar1->kodekegiatan][$bar1->afd][$bar1->kodeorg]['bumr']=$bar1->bumr;
	$TAMPUNG[$bar1->kodekegiatan][$bar1->afd][$bar1->kodeorg]['bpremi']=$bar1->bpremi;
}
//echo $str1;
#---------------------------------------------------------------
#Ambil Penggunaan Bahan di pemel
#---------------------------------------------------------------
$str5="SELECT count(A.notransaksi) as rotasi, B.kodekegiatan,left(B.kodeorg,6) as afd,B.kodeorg,
B.kodebarang,sum(B.kwantitas) as qty,C.namabarang, C.satuan as satuanbrg,sum(B.kwantitas * B.hargasatuan) as saldobrg FROM 
".$dbname.".`kebun_kehadiran_proporsi` A LEFT JOIN ".$dbname.".`kebun_pakaimaterial` B  ON A.notransaksi=B.notransaksi 
LEFT JOIN ".$dbname.".`log_5masterbarang` C  ON B.kodebarang=C.kodebarang 
 where A.kodeorg = '".$kdOrg."'  and A.tanggal >='".$tgl1."' and A.tanggal <='".$tgl2."' and B.kodeorg like '%".$kdAfd."%'  and B.kodekegiatan like '".$kdKeg."' 
 group by B.kodekegiatan,B.kodeorg,B.kodebarang order by B.kodeorg,B.kodekegiatan asc;";

  
$res5= mysql_query($str5);
$BAHANVL=Array();
while($bar5=mysql_fetch_object($res5))
{     
		$BAHANVL[$bar5->kodekegiatan][$bar5->afd][$bar5->kodeorg][$bar5->kodebarang]['barang']=$bar5->kodebarang." - ".$bar5->namabarang;
		$BAHANVL[$bar5->kodekegiatan][$bar5->afd][$bar5->kodeorg][$bar5->kodebarang]['satuanbrg']=$bar5->satuanbrg;
		$BAHANVL[$bar5->kodekegiatan][$bar5->afd][$bar5->kodeorg][$bar5->kodebarang]['qty']=$bar5->qty;
		$BAHANVL[$bar5->kodekegiatan][$bar5->afd][$bar5->kodeorg][$bar5->kodebarang]['biayabrg']=$bar5->saldobrg;
}
		
	//echo $str5;							
#---------------------------------------------------------------
#Ambil Master Kegiatan
#---------------------------------------------------------------	 
$str2="SELECT kodekegiatan,namakegiatan,satuan FROM  ".$dbname.".`setup_kegiatan`";
$res2= mysql_query($str2);
$LookUpKEG=Array();
while($bar2=mysql_fetch_object($res2))
{        
      $LookUpKEG[$bar2->kodekegiatan]['nama']=$bar2->kodekegiatan." - ".$bar2->namakegiatan;
	  $LookUpKEG[$bar2->kodekegiatan]['satuan']=$bar2->satuan;
}

#---------------------------------------------------------------
#Ambil Master BLOK
#---------------------------------------------------------------	 
$str3="SELECT kodeorg,tahuntanam,luasareaproduktif FROM  ".$dbname.".`setup_blok` WHERE kodeorg like '%".$kdAfd."%'";
$res3= mysql_query($str3);
$LookUpBLOK=Array();
while($bar3=mysql_fetch_object($res3))
{        
      $LookUpBLOK[$bar3->kodeorg]['tahuntanam']=$bar3->tahuntanam;
	  $LookUpBLOK[$bar3->kodeorg]['luas']=$bar3->luasareaproduktif;
}
#---------------------------------------------------------------
#Ambil Master BLOK PERAFDELING
#---------------------------------------------------------------	 
$str4="SELECT left(kodeorg,6) as afd,sum(luasareaproduktif) as areal FROM  ".$dbname.".`setup_blok` WHERE kodeorg like '%".$kdAfd."%' group by left(kodeorg,6) ";
$res4= mysql_query($str4);
while($bar4=mysql_fetch_object($res4))
{        
	  $LookUpBLOK[$bar4->kodeorg]['luas']=$bar4->areal;
}
	 	 

    if($proses=='getAfdAll'){
		 if ($kdOrg=="N/A"){
			   echo "<option ></option>";
		 }else{
			$str="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi 
                where kodeorganisasi like '".$kdOrg."%' and tipe='AFDELING' and length(kodeorganisasi)=6 order by namaorganisasi";
			$op="<option value=''>".$_SESSION['lang']['all']."</option>";
			$res=mysql_query($str);
			while($bar=mysql_fetch_object($res))
			{
              $op.="<option value='".$bar->kodeorganisasi."'>".$bar->namaorganisasi."</option>";
			}
			echo $op;
           
		 }
          exit();
    }else if($proses=='getKegAll'){
		   if ($kdAfd=="ALL"){
			   echo "<option ></option>";
			}else{
			 $str="SELECT kodekegiatan,concat(kodekegiatan,':',namakegiatan ) as nama FROM  ".$dbname.".`setup_kegiatan` where kelompok NOT in ('MIL','KNT','PNN')";
          $op="<option value=''>".$_SESSION['lang']['all']."</option>";
          $res=mysql_query($str);
          while($bar=mysql_fetch_object($res))
          {
              $op.="<option value='".$bar->kodekegiatan."'>".$bar->nama."</option>";
          }
          echo $op;
          exit();
           
		 }
		 
    }else{
		#----------------------------------------------------------------------------
		#---------ACTION
		#----------------------------------------------------------------------------
		if(empty($START)){
			echo 'Info: Laporan Harian Kegiatan Asisten Belum tersedia di karenakan belum dientri atau terpostingkan..';
		}else {
			
			if(($proses=='excel'))
			{
				
				$data="
                  <table class=sortable cellspacing=1 border=1  style='border-collapse: separate;vertical-align: baseline;width : 100%;   '>
                  <thead >";
			}
			else
			{
				
				$data="<button onclick=\"zExcel(event,'lha_slave_print_new.php','".$arr."')\" class=\"mybutton\" name=\"excel\" id=\"excel\">Excel</button>
                  <table class=sortable cellspacing=1 border=0  style='border-collapse: separate;vertical-align: baseline;width : 100%;   '>
                  <thead >";
			}
			
				
					$data.="<tr  class=rowheader>
					<td rowspan='2' style='min-width:40px'><span style='max-width:40px;display:block;'>No.</span></td>
					<td rowspan='2' style='min-width:350px'><span style='max-width:350px;display:block;'>Item Kegiatan</span></td>
					<td rowspan='2' style='min-width:50px'><span style='max-width:50px;display:block;'>Satuan</span></td>
					<td rowspan='2' style='min-width:80px'><span style='max-width:80px;display:block;'>Blok</span></td>
					<td rowspan='2' style='min-width:80px'><span style='max-width:80px;display:block;'>Tahun Tanam</span></td>
					<td rowspan='2' style='min-width:80px'><span style='max-width:80px;display:block;'>Luas Blok (Ha)</span></td>
					";
					/*
					<td rowspan='2' style='min-width:50px'><span style='max-width:50px;display:block;'>Rotasi</span></td>
					*/
					$data.="
					<td colspan='4' style='min-width:310px'><span style='max-width:310px;display:block;'>Tenaga Kerja</span></td>
					<td colspan='4' style='min-width:430px'><span style='max-width:430px;display:block;'>Bahan dan Material</span></td>
					<td rowspan='2' style='min-width:100px'><span style='max-width:100px;display:block;'>Total Biaya (Rp)(TK + BM)</span></td>
					
					<td rowspan='2' style='min-width:80px'><span style='max-width:100px;display:block;'>Hasil Kerja</span></td>
					<td rowspan='2' style='min-width:80px'><span style='max-width:100px;display:block;'>Rp/Satuan</span></td>
					<td rowspan='2' style='min-width:80px'><span style='max-width:100px;display:block;'>HK/Satuan</span></td>
                    </tr>
					
					<tr  class=rowheader>
					<td style='min-width:50px'><span style='max-width:50px;display:block;'>Jumlah HK</span></td>
					<td style='min-width:80px'><span style='max-width:80px;display:block;'>Upah(Rp.)</span></td>
					<td style='min-width:80px'><span style='max-width:80px;display:block;'>Premi(Rp.)</span></td>
					<td style='min-width:100px'><span style='max-width:100px;display:block;'>Biaya TK(Rp.)</span></td>
					
					<td style='min-width:200px'><span style='max-width:300px;display:block;'>Nama</span></td>
					<td style='min-width:50px'><span style='max-width:50px;display:block;'>Satuan</span></td>
					<td style='min-width:80px'><span style='max-width:80px;display:block;'>Qty</span></td>
					<td style='min-width:100px'><span style='max-width:100px;display:block;'>Biaya BM(Rp.)</span></td>
                    </tr>
                  </thead>
                  <tbody>";
				  


				  $TAMPIL=ARRAY();
					 asort($START);
				  foreach($START as $key =>$baris)
				  {
					  
					foreach($baris as $key2 =>$baris2)
					{
						

						foreach($TAMPUNG[$key][$key2] as $key3 =>$baris3)
						{
							
							if (count($BAHANVL[$key][$key2][$key3])==0){
								
								//Pekerjaan
								$TAMPIL[$key][$key2][$key3]['00000000']['A']=$LookUpKEG[$key]['nama'];
								//Satuan
								$TAMPIL[$key][$key2][$key3]['00000000']['B']=$LookUpKEG[$key]['satuan'];
								//Kode Blok
								$TAMPIL[$key][$key2][$key3]['00000000']['C']=$key3;
								//Tahun Tanam
								$TAMPIL[$key][$key2][$key3]['00000000']['D']=$LookUpBLOK[$key3]['tahuntanam'];
								//luas (Ha)
								$TAMPIL[$key][$key2][$key3]['00000000']['E']=$LookUpBLOK[$key3]['luas'];
								//TOTAL ROTASI
								/*
								$TAMPIL[$key][$key2][$key3]['00000000']['F']=$baris3['rotasi'];
								*/
								//Jumlah HK
								$TAMPIL[$key][$key2][$key3]['00000000']['G']=$baris3['jumlahhk'];
								//Upah(Rp.)
								$TAMPIL[$key][$key2][$key3]['00000000']['H']=$baris3['bumr'];
								//Premi(Rp.)
								$TAMPIL[$key][$key2][$key3]['00000000']['I']=$baris3['bpremi'];
								//Biaya TK(Rp.)
								$TAMPIL[$key][$key2][$key3]['00000000']['J']=$baris3['bumr']+$baris3['bpremi'];
								//Nama Bahan
								$TAMPIL[$key][$key2][$key3]['00000000']['K']='';
								//Satuan Bahan
								$TAMPIL[$key][$key2][$key3]['00000000']['L']='';
								//Qty
								$TAMPIL[$key][$key2][$key3]['00000000']['M']='';
								//Biaya BM(Rp.)
								$TAMPIL[$key][$key2][$key3]['00000000']['N']='';
								//Total Biaya (Rp)
								$TAMPIL[$key][$key2][$key3]['00000000']['O']=$baris3['bumr']+$baris3['bpremi'];
								
								//Hasil Kerja
								$TAMPIL[$key][$key2][$key3]['00000000']['P']=$baris3['hasilkerja'];
								//Rp/Satuan
								$TAMPIL[$key][$key2][$key3]['00000000']['Q']=($baris3['bumr']+$baris3['bpremi'])/$baris3['hasilkerja'];
								//HK/Satuan
								$TAMPIL[$key][$key2][$key3]['00000000']['R']=$baris3['jumlahhk']/$baris3['hasilkerja'];
								
								
							}else{
								
								foreach($BAHANVL[$key][$key2][$key3] as $key4=>$baris4)
								{
									
									
								//Pekerjaan
								$TAMPIL[$key][$key2][$key3][$key4]['A']=$LookUpKEG[$key]['nama'];
								//Satuan
								$TAMPIL[$key][$key2][$key3][$key4]['B']=$LookUpKEG[$key]['satuan'];
								//Kode Blok
								$TAMPIL[$key][$key2][$key3][$key4]['C']=$key3;
								//Tahun Tanam
								$TAMPIL[$key][$key2][$key3][$key4]['D']=$LookUpBLOK[$key3]['tahuntanam'];
								//luas (Ha)
								$TAMPIL[$key][$key2][$key3][$key4]['E']=$LookUpBLOK[$key3]['luas'];
								//TOTAL ROTASI
								/*
								$TAMPIL[$key][$key2][$key3][$key4]['F']=$baris3['rotasi'];
								*/
								//Jumlah HK
								$TAMPIL[$key][$key2][$key3][$key4]['G']=$baris3['jumlahhk'];
								//Upah(Rp.)
								$TAMPIL[$key][$key2][$key3][$key4]['H']=$baris3['bumr'];
								//Premi(Rp.)
								$TAMPIL[$key][$key2][$key3][$key4]['I']=$baris3['bpremi'];
								//Biaya TK(Rp.)
								$TAMPIL[$key][$key2][$key3][$key4]['J']=$baris3['bumr']+$baris3['bpremi'];
								//Nama Bahan
								$TAMPIL[$key][$key2][$key3][$key4]['K']=$baris4['barang'];
								//Satuan Bahan
								$TAMPIL[$key][$key2][$key3][$key4]['L']=$baris4['satuanbrg'];
								//Qty
								$TAMPIL[$key][$key2][$key3][$key4]['M']=$baris4['qty'];
								//Biaya BM(Rp.)
								$TAMPIL[$key][$key2][$key3][$key4]['N']=$baris4['biayabrg'];
								//Total Biaya (Rp)
								$TAMPIL[$key][$key2][$key3][$key4]['O']=$baris3['bumr']+$baris3['bpremi']+$baris4['biayabrg'];
								
								//Hasil Kerja
								$TAMPIL[$key][$key2][$key3][$key4]['P']=$baris3['hasilkerja'];
								//Rp/Satuan
								$TAMPIL[$key][$key2][$key3][$key4]['Q']=($baris3['bumr']+$baris3['bpremi']+$baris4['biayabrg'])/$baris3['hasilkerja'];
								//HK/Satuan
								$TAMPIL[$key][$key2][$key3][$key4]['R']=$baris3['jumlahhk']/$baris3['hasilkerja'];
								
								}
								
							}
								
							
						}
					}
				}
				
				
				$no1=1;
				$tempNo=0;


				//print_r($TES);
			
				
				
				
			asort($TAMPIL);
				$TB_HK=0;
				$TB_UMR=0;
				$TB_PREMI=0;
				$TB_TK=0;
				$TTB_BH=0;
				$TT_BIAYA=0;
				$TB_HASIL=0;
				$TB_RPS=0;
				$TB_HKS=0;
				foreach($TAMPIL as $key =>$baris)
				{
					
					foreach($baris as $key2 =>$baris2)
					{
						
						foreach($baris2 as $key3 =>$baris3)
						{
							
							$data.="<tr  class=rowcontent>
								<td rowspan='".Count($baris3)."' align='center' width='40px'>".$no1."</td>
								<td rowspan='".Count($baris3)."' align='left' >".$LookUpKEG[$key]['nama']."</td>
								<td rowspan='".Count($baris3)."' align='center'>".$LookUpKEG[$key]['satuan']."</td>
								<td rowspan='".Count($baris3)."' align='center'>".$key3."</td>
								<td rowspan='".Count($baris3)."' align='center'>".$LookUpBLOK[$key3]['tahuntanam']."</td>
								<td rowspan='".Count($baris3)."' align='center'>".$LookUpBLOK[$key3]['luas']."</td>
								";
								/*
								<td rowspan='".Count($baris3)."' align='center'>".$TAMPUNG[$key][$key2][$key3]['rotasi']."</td>
								*/
								
								$B_HK=$TAMPUNG[$key][$key2][$key3]['jumlahhk'];
								$B_UMR=$TAMPUNG[$key][$key2][$key3]['bumr'];
								$B_PREMI=$TAMPUNG[$key][$key2][$key3]['bpremi'];
								$B_TK=$B_UMR+$B_PREMI;
								
								$TB_HK=$TB_HK+$B_HK;
								$TB_UMR=$TB_UMR+$B_UMR;
								$TB_PREMI=$TB_PREMI+$B_PREMI;
								$TB_TK=$TB_TK+$B_TK;
							$data.="	
								<td rowspan='".Count($baris3)."' align='center'>".$B_HK."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_UMR)."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_PREMI)."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_TK)."</td>";
								
							$data2="";
							$TB_BH=0;
							$B_BH=0;
							asort($baris3);
							foreach($baris3 as $key4 =>$baris4)
							{	
								$B_BH=$baris4['N'];
								if ($tempNo<>0){
									$data2.=" <tr  class=rowcontent>";
									$data2.="<td align='left'>".$baris4['K']."</td>
									<td align='center'>".$baris4['L']."</td>
									<td align='center'>".number_format($baris4['M'],2)."</td>
									<td align='right'>".number_format($B_BH)."</td>";
									$data2.=" </tr>";
								}else{
									$data.="<td align='left'>".$baris4['K']."</td>
									<td align='center'>".$baris4['L']."</td>
									<td align='center'>".number_format($baris4['M'],2)."</td>
									<td align='right'>".number_format($B_BH)."</td>";
								}
								$TB_BH=$TB_BH+$B_BH;
								$tempNo++;
							}
							$TTB_BH=$TTB_BH+$TB_BH;
							
							$B_HASIL=$TAMPUNG[$key][$key2][$key3]['hasilkerja'];
							$T_BIAYA=$B_TK+$TB_BH;
							$TT_BIAYA=$TT_BIAYA+$T_BIAYA;
							$TB_HASIL=$TB_HASIL+$B_HASIL;
							$B_RPS=$T_BIAYA/$B_HASIL;
							$B_HKS=$B_HK/$B_HASIL;
							$TB_RPS=$TB_RPS+$B_RPS;
							$TB_HKS=$TB_HKS+$B_HKS;
							$data.="
								<td rowspan='".Count($baris3)."' align='right'>".number_format($T_BIAYA)."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_HASIL,2)."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_RPS)."</td>
								<td rowspan='".Count($baris3)."' align='right'>".number_format($B_HKS,2)."</td>
								</tr>";
							$data.=$data2;	
							$no1+=1;
							$tempNo=0;
						}
					}
				}
				
				$data.="
                  <thead >
					<tr  class=rowheader>
					<td colspan='6' style='min-width:680px'><span style='max-width:680px;display:block;'>Total</span></td>
					<td style='min-width:50px'><span style='text-align:right;max-width:50px;display:block;'>".$TB_HK."</span></td>
					<td align='right' style='min-width:80px'><span style='text-align:right;max-width:80px;display:block;'>".number_format($TB_UMR)."</span></td>
					<td align='right' style='min-width:80px'><span style='text-align:right;max-width:80px;display:block;'>".number_format($TB_PREMI)."</span></td>
					<td align='right' style='min-width:100px'><span style='text-align:right;max-width:100px;display:block;'>".number_format($TB_TK)."</span></td>
					<td style='min-width:200px'><span style='max-width:300px;display:block;'></span></td>
					<td style='min-width:50px'><span style='max-width:50px;display:block;'></span></td>
					<td style='min-width:80px'><span style='max-width:80px;display:block;'></span></td>
					<td align='right' style='min-width:100px'><span style='text-align:right;max-width:100px;display:block;'>".number_format($TTB_BH)."</span></td>
					<td align='right' style='min-width:100px'><span style='text-align:right;max-width:100px;display:block;'>".number_format($TT_BIAYA)."</span></td>
					<td align='right'  style='min-width:80px'><span style='text-align:right;max-width:100px;display:block;'></span></td>
					<td align='right'  style='min-width:80px'><span style='text-align:right;max-width:100px;display:block;'></span></td>
					<td align='right' style='min-width:80px'><span style='text-align:right;max-width:100px;display:block;'></span></td>
                    </tr>
                  </thead>
                  ";
				
			$data.= "</tbody>";
			
		}
		
		
		
		
		#######################################################################
############PANGGGGGGGGGGGGGGGGGGILLLLLLLLLLLLLLLLLLLLLLLLLL###########   
#######################################################################

switch($proses)
{
######HTML
	case 'preview':
		echo $data;
    break;

######EXCEL	
	case 'excel':
		//$stream.="Print Time : ".date('H:i:s, d/m/Y')."<br>By : ".$_SESSION['empl']['name'];	
		$tglSkrg=date("Ymd");
		$nop_="LH_Pemeliharaan_Kebun".$tglSkrg;
		if(strlen($data)>0)
		{
			if ($handle = opendir('tempExcel')) {
				while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					@unlink('tempExcel/'.$file);
				}
				}	
				closedir($handle);
			}
			$handle=fopen("tempExcel/".$nop_.".xls",'w');
			if(!fwrite($handle,$data))
			{
				echo "<script language=javascript1.2>
				parent.window.alert('Can't convert to excel format');
				</script>";
				exit;
			}
			else
			{
				echo "<script language=javascript1.2>
				window.location='tempExcel/".$nop_.".xls';
				</script>";
			}
			closedir($handle);
		}           
		break;
	
	
	default:
	break;
}


    }
	

?>