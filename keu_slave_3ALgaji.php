<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/zPosting.php');

			function tpbiaya($value,$org){
				if ($org=="CBGM"){
					if($value=="CBGM01" ||
						$value=="CBGM02" ||
						$value=="CBGM03" ||
						$value=="CBGM04" ||
						$value=="CBGM05" ||
						$value=="CBGM06" ||
						$value=="CBGM07" ||
						$value=="CBGM08" ||
						$value=="CBGM09" ||
						$value=="CBGM10" ||
						$value=="CBGM11" ){
						return "B/L";
					}else{
						return "B/TL";
					}
				}else{
					if($value=="PEMANEN" ||
						$value=="OPR/KERNET" ||
						$value=="PEMELIHARAAN" ){
						return "B/L";
					}else{
						return "B/TL";
					}
				}
			 }
			 
			 
			 function tpkar($value){
				 switch($value){
					 //pemanen
					 case 64:
					 return "PEMANEN";
					 break;
					 //pemeliharaan
					 case 250:
					 return "PEMELIHARAAN";
					 break;
					 //keamanan
					 /*
					 82:	Pam Portal
					 129	Danru Satpam
					 43		Satpam
					 227	Pam Portal Afd VII
					 228	Pam Portal Afd XI
					 233	Pam Portal Afd VII
					 232	Centeng
					 */
					 case 82:
					 case 129:
					 case 43:
					 case 227:
					 case 228:
					 case 233:
					 case 232:
					 return "KEAMANAN";
					 break;
					 
					 
					 //OPR/KERNET
					 /*
					 72		Kernet
					 181	Kernet Truk Kayu
					 55		Operator Exc. 019
					 61		Operator Exc. 04
					 91		Pemb. Operator Kernel
					 92		Pemb. Operator Boiler
					 95		Operator Alat Berat
					 175	Operator Greader
					 185	Operator Backho Loader
					 199	Operator Tractor 01
					 204	Operator BD 011
					 205	Operator Exc. 018
					 206	Operator Trado
					 207	Operator Exc. 017
					 208	Operator Tractor 03
					 210	Operator Exc. 020
					 211	Operator Exc. 021
					 212	Operator Exc. 016
					 213	Operator Exc. 015
					 220	Operator Exc. 14
					 254	Pembantu Operator
					 
					 56		Supir Truck BL 8378 VL
					 189	Supir Truck BL 8375 VL
					 190	Supir Truck BL 8377 VL
					 191	Supir Truck BL 8379 VL
					 192	Supir Fuso BL 8381 VL
					 193	Supir Fuso BL 8383 VL
					 194	Supir DT BL 8371 VL
					 195	Supir Truck BL 8376 VL
					 196	Supir DT BL 8370 VL
					 197	Supir DT BL 8372 VL
					 200	Supir DT BL 8512 AB
					 201	Supir DT BL 8726 AV
					 202	Supir Fuso BL 8380 VL
					 209	Supir Fuso BL 8382 VL
					 263	SOPIR DT BL8374VL
					 
					 */
					 case 72:
					 case 181:
					 case 55:
					 case 61:
					 case 91:
					 case 92:
					 case 95:
					 case 175:
					 case 185:
					 case 199:
					 case 204:
					 case 205:
					 case 206:
					 case 207:
					 case 208:
					 case 210:
					 case 211:
					 case 212:
					 case 213: 
					 case 220:
					 case 254:
					 case 56:
					 case 189:
					 case 190:
					 case 191:
					 case 192:
					 case 193:
					 case 194:
					 case 195:
					 case 196:
					 case 197:
					 case 200:
					 case 201:
					 case 202:
					 case 209:
					 case 263:
					  case 231:
					 case 244:
					 return "OPR/KERNET";
					 break;
					 
					 default:
					 return "PERANGKAT";
					 
					 break;
					 
				 }
			 }
			 
$param = $_POST;
$tahunbulan = implode("",explode('-',$param['periode']));
#ambil periode akuntansi
$str2="select tanggalmulai,tanggalsampai from ".$dbname.".sdm_5periodegaji 
    where kodeorg='".$_SESSION['empl']['lokasitugas']."'
    and periode='".$param['periode']."'";
$tgmulai='';
$tgsampai='';
$res2=mysql_query($str2);
while($bar2=mysql_fetch_object($res2))
{
    $tgsampai   = $bar2->tanggalsampai;
    $tgmulai    = $bar2->tanggalmulai;
}
if($tgmulai=='' || $tgsampai=='')
    exit("Error: Periode akuntasi tidak terdaftar");








#---------------------------------------------------------------
#Ambil Komponen Gaji yg dimiliki karyawan
#---------------------------------------------------------------
 $str1="select a.jumlah,a.idkomponen,a.karyawanid,b.subbagian,b.kodejabatan from ".$dbname.".sdm_gajidetail_vw a  left join   ".$dbname.".`datakaryawan` b  ON a.karyawanid=b.karyawanid
       where a.kodeorg like '".$_SESSION['empl']['lokasitugas']."%' 
       and a.periodegaji='".$param['periode']."' order by b.subbagian ,b.kodejabatan asc" ;
 $res1=  mysql_query($str1);
 $gaji=Array();
 while($bar1=mysql_fetch_object($res1))
 {        
        $gaji[$bar1->karyawanid][$bar1->idkomponen]=$bar1->jumlah;
 }
 
 
 #2 Ambil subunit setiap karyawan
 $strC="select a.subbagian,a.karyawanid,a.namakaryawan,b.namaorganisasi,a. kodejabatan,c.namajabatan from ((".$dbname.".datakaryawan a left join ".$dbname.".organisasi b ON a.subbagian=b.kodeorganisasi ) LEFT JOIN ".$dbname.".sdm_5jabatan c ON a.kodejabatan=c.kodejabatan)
       where a.lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
	   //exit('Error: '. $str);
 $resC=mysql_query($strC);
 $subunit=Array();
 $nmkaryawan=Array();
 $karjab=Array();
 $nmunit=Array();
 while($barC=mysql_fetch_object($resC))
 {
     $subunit[$barC->karyawanid]=$barC->subbagian;
     $nmkaryawan[$barC->karyawanid]=$barC->namakaryawan;
	 $karjab[$barC->karyawanid]=$barC->namajabatan;
	 $nmunit[$barC->karyawanid]=$barC->namaorganisasi;
	 $kdjab[$barC->karyawanid]=$barC->kodejabatan;
	 
     
 }
 

  
	
	#==========================================================================================  
  #ambil daftar karyawan yang masuk dalam perawatan dan panen
  /*
  $strB="select karyawanid,(sum((jhk*umr)+insentif)) as upah from ".$dbname.".kebun_kehadiran_new_vw
        where unit='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$tgmulai."' 
        and '".$tgsampai."' and jurnal=1 group by karyawanid";
*/
 $strB="select karyawanid,(sum((umr)+insentif)) as upah from ".$dbname.".kebun_kehadiran_new_vw
        where unit='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$tgmulai."' 
        and '".$tgsampai."' and jurnal=1 group by karyawanid";
  //exit ("tess".$str);
  $resB=mysql_query($strB);
  $gjPerawatan=Array();
  while($barB=mysql_fetch_object($resB))
  {
      $gjPerawatan[$barB->karyawanid]=$barB->upah;
  }

  #===================panen
  $str="select karyawanid,(sum(upahkerja + upahpremi)) as upah from ".$dbname.".kebun_prestasi_vw
        where unit='".$_SESSION['empl']['lokasitugas']."' and tanggal between '".$tgmulai."' 
        and '".$tgsampai."' and jurnal=1 group by karyawanid";
//		exit ("tess".$str); 
  $res=mysql_query($str); 
  $gjPanen=Array();
  while($bar=mysql_fetch_object($res))
  {
      $gjPanen[$bar->karyawanid]=$bar->upah;
  }
  #=================================================================
  //sleep(5);
  #hapus karyawan tidaklangsung
  $masukkotak=Array();
  $no=0;
			 $SORTAFD=ARRAY();
            foreach($gaji as $key =>$baris)
            {
			   $dwf=tpkar($kdjab[$key]);
               $tb1=tpbiaya($dwf,$_SESSION['empl']['lokasitugas']);
			   $tb2=tpbiaya($subunit[$key],$_SESSION['empl']['lokasitugas']);
               $no+=1;
			   if (substr($_SESSION['empl']['lokasitugas'], -1) == "E"){	
				   $TPA=$dwf;
				   $BYA=$tb1;
			   }else{
				   $TPA=$nmunit[$key];
				   $BYA=$tb2;
			   }
			   //$gjTA =$gjPerawatan[$key];
			    $gjTA =($gjPanen[$key]+$gjPerawatan[$key])-$baris[43];
				//$gjTA =$gjPanen[$key];
				if(($gjTA!=0) || ($gjTA==0 && $baris[43]!=0))  
				{
					
					
					$afd=$subunit[$key];
					$totgp=$baris[1];
					$totpre=$baris[32];
					$bigaji=($totgp+$baris[2]+$baris[30]+$baris[31]+$baris[40]+$baris[14])-($baris[37]+$baris[34]+$baris[36]+$baris[43]);
					$bigajibt=($baris[37]+$baris[36]);
					
					
					$blembur=$baris[33];
					$bpremi=$totpre+$baris[16]+$baris[38];
					$bthr=$baris[28];
					$binse=$baris[26];
					$tpinj=$baris[5]+$baris[9]+$baris[18];
					$hjht=$baris[3];
					$hjk=$baris[39];
					$jp=$baris[41];
					$phk=$baris[37];
					$ppsd=$baris[36];
					$tpotA=$tpinj+$baris[3]+$baris[39]+$baris[41]; 
					$tpot=$tpinj;
					$hgaji=($bigaji+$blembur+$bpremi+$bthr+$binse)-$tpotA;
					$dbet=$bigaji+$blembur+$bpremi+$bthr+$binse;
					$dkre=$hgaji+$hjht+$hjk+$jp+$tpinj;
					$dbal=$dbet-$dkre;
					$SORTAFD[$BYA][$afd][$TPA][$key]['A'] =$_POST['periode'];
					$SORTAFD[$BYA][$afd][$TPA][$key]['B'] =$nmkaryawan[$key];
					$SORTAFD[$BYA][$afd][$TPA][$key]['C'] =$key;
					$SORTAFD[$BYA][$afd][$TPA][$key]['D'] =$afd;
					$SORTAFD[$BYA][$afd][$TPA][$key]['E'] =$TPA;
					$SORTAFD[$BYA][$afd][$TPA][$key]['F'] =$BYA;
					
					$SORTAFD[$BYA][$afd][$TPA][$key]['G'] =$gjTA;
					$SORTAFD[$BYA][$afd][$TPA][$key]['H'] =$hgaji;
					$hgajiBT=$gjTA-$hgaji;
					$SORTAFD[$BYA][$afd][$TPA][$key]['I'] =$hgajiBT;
					//if($gjTA > $hgaji){
							$SORTAFD[$BYA][$afd][$TPA][$key]['J'] =$hgajiBT-($hjht+$hjk+$jp+$tpot);
					//}else{
					//		$SORTAFD[$BYA][$afd][$TPA][$key]['J'] =$hgajiBT+($hjht+$hjk+$tpot);
					//}
					
					
					
					
					
					$SORTAFD[$BYA][$afd][$TPA][$key]['K'] =$hjht;
					$SORTAFD[$BYA][$afd][$TPA][$key]['L'] =$hjk;
					$SORTAFD[$BYA][$afd][$TPA][$key]['V'] =$jp;
					$SORTAFD[$BYA][$afd][$TPA][$key]['M'] =$tpot;
					$SORTAFD[$BYA][$afd][$TPA][$key]['N'] =$nmunit[$key];
					$SORTAFD[$BYA][$afd][$TPA][$key]['O'] =$phk;
					$SORTAFD[$BYA][$afd][$TPA][$key]['P'] =$ppsd;
					$SORTAFD[$BYA][$afd][$TPA][$key]['Q'] =$baris[40];
					$SORTAFD[$BYA][$afd][$TPA][$key]['R'] =$baris[32];
					$SORTAFD[$BYA][$afd][$TPA][$key]['S'] =$baris[43];
				}
             }
			 
  //$zzz=$masukkotak;
  #bersihkan memory
  //unset($gaji);
  

 #==========================================================================================  
  
   
 #==========================================================================================
 
   #echo $kekurangan; Buang escape ini untuk mengetahui selisih gaji yang belum teralokasi 
 if(empty($SORTAFD))
     exit('Info: gaji karyawan langsung sudah teralokasi dengan benar');
 else {

     
       
             $data="<button class=mybutton onclick=prosesNewGajiLangsung(1) id=btnproses>Process</button>
                  <table class=sortable cellspacing=1 border=0>
                  <thead>
                    <tr class=rowheader>
                    <td>No</td>
                    <td>Periode</td>
                    <td>Nama.Karyawan</td>
                    <td>Karyawanid</td>
					<td>Unit</td>
					<td>Tipe</td>
					<td>Jenis</td>
					<td>Gaji Teralokasi</td>
					<td>Gaji Net.</td>
					<td>H. Gaji BT.</td>
					<td>Biaya Gaji BT</td>
					<td>Hut. JHT BT</td>
					<td>Hut. JK BT</td>
					<td>Hut. JP BT</td>
					<td>Tot. Pinjaman BT</td>
					<td>Pot. HK</td>
					<td>Pot. Premi SB</td>
					<td>T. Natura</td>
					<td>Premi</td>
					<td>Kontanan</td>
					
                    </tr>
                  </thead>
                  <tbody>";
				

            
			 
			  $no1=1;
			  $no2=1;
			  $TtA1=0;
			  $TtB1=0;
			  $TtC1=0;
			  $TtD1=0;
			  $TtE1=0;
			  $TtF1=0;
			  $TtV1=0;
			  $TtH1=0;
			  $TtI1=0;
			  $TtJ1=0;
			  $TtK1=0;
			  $TtL1=0;
				foreach($SORTAFD as $key =>$baris)
				{
				$tA1=0;
				$tB1=0;
				$tC1=0;
				$tD1=0;
				$tE1=0;
				$tF1=0;
				$tV1=0;
				$tG1=0;
				$tH1=0;
				$tI1=0;
				$tJ1=0;
				$tK1=0;
				$tL1=0;
					foreach($baris as $key2 =>$baris2)
					{
						foreach($baris2 as $key3 =>$baris3)
						{
						$tA=0;
						$tB=0;
						$tC=0;
						$tD=0;
						$tE=0;
						$tF=0;
						$tV=0;
						$tG=0;			
						$tH=0;
						$tI=0;			
						$tJ=0;			
						$tK=0;
						$tL=0;						
							foreach($baris3 as $key4 =>$baris4)
							{	
								$data.="<tr class=rowcontent >";
								$data.="<td>".$no1."</td>";
								$data.="<td >".$baris4['A']."</td>";
								$data.="<td >".$baris4['B']."</td>";
								$data.="<td >".$baris4['C']."</td>";    
								$data.="<td >".$baris4['D']."</td>";
								$data.="<td >".$baris4['E']."</td>"; 
								$data.="<td >".$baris4['F']."</td>";
								$data.="<td align=right >".number_format($baris4['G'])."</td>";					
								$data.="<td align=right >".number_format($baris4['H'])."</td>";
								$data.="<td align=right >".number_format($baris4['I'])."</td>";
								$data.="<td align=right>".number_format($baris4['J'])."</td>";
								$data.="<td align=right >".number_format($baris4['K'])."</td>";					
								$data.="<td align=right >".number_format($baris4['L'])."</td>";
								$data.="<td align=right >".number_format($baris4['V'])."</td>";
								$data.="<td align=right >".number_format($baris4['M'])."</td>";
								$data.="<td align=right >".number_format($baris4['O'])."</td>";
								$data.="<td align=right >".number_format($baris4['P'])."</td>";
								$data.="<td align=right >".number_format($baris4['Q'])."</td>";
								$data.="<td align=right >".number_format($baris4['R'])."</td>";
								$data.="<td align=right >".number_format($baris4['S'])."</td>";
								$data.="</tr>";
								$tA+=$baris4['G'];
								$tB+=$baris4['H'];
								$tC+=$baris4['I'];
								$tD+=$baris4['J'];
								$tE+=$baris4['K'];
								$tF+=$baris4['L'];
								$tV+=$baris4['V'];
								$tG+=$baris4['M'];
								$tH+=$baris4['O'];
								$tI+=$baris4['P'];
								$tJ+=$baris4['Q'];
								$tK+=$baris4['R'];
								$tL+=$baris4['S'];
											
								$no1+=1;
							}
					
						$data.="<tr style=\"background-color:yellow\" id='row".$no2."'>";
						$data.="<td  id='unit".$no2."'>".$baris4['D']."  </td>";
						if ($_SESSION['empl']['lokasitugas'] == "CBGM"){
							$data.="<td colspan=4 id='ketA".$no2."'>".$baris4['A'].":".$baris4['E']."  </td>";
							$data.="<td  id='kat".$no2."'>  </td>";
						}else{
							$data.="<td colspan=4 id='ketA".$no2."'>".$baris4['A'].":".$baris4['E']." ".$baris4['N']." </td>";
							$data.="<td  id='kat".$no2."'>".$baris4['E']."  </td>";
						}
                    
						$data.="<td  id='biaya".$no2."'>".$baris4['F']."  </td>";
						$data.="<td align=right id='balok".$no2."'>".number_format($tA)."</td>
						<td align=right id='bnet".$no2."'>".number_format($tB)."</td>
						<td align=right id='bhut".$no2."'>".number_format($tC)."</td>
						<td align=right id='bbiaya".$no2."'>".number_format($tD)."</td>
						<td align=right id='pjht".$no2."'>".number_format($tE)."</td>
						<td align=right id='pjk".$no2."'>".number_format($tF)."</td>
						<td align=right id='jp".$no2."'>".number_format($tV)."</td>
						<td align=right id='ppinjaman".$no2."'>".number_format($tG)."</td>
						<td align=right id='phk".$no2."'>".number_format($tH)."</td>
						<td align=right id='ppsd".$no2."'>".number_format($tI)."</td>
						<td align=right id='pnat".$no2."'>".number_format($tJ)."</td>
						<td align=right id='pmin".$no2."'>".number_format($tK)."</td>
						<td align=right id='pkon".$no2."'>".number_format($tL)."</td>
						</tr>"; 
						$tA1+=$tA;
						$tB1+=$tB;
						$tC1+=$tC;
						$tD1+=$tD;
						$tE1+=$tE;
						$tF1+=$tF;
						$tV1+=$tV;
						$tG1+=$tG;
						$tH1+=$tH;
						$tI1+=$tI;
						$tJ1+=$tJ;
						$tK1+=$tK;
						$tL1+=$tL;
                     	$no2+=1;
						}
					
					}
					$no1++;
					$data.="<tr style=\"background-color:#e5e5e5\" >
                    <td colspan=7>Total ".$baris4['F']."</td>
                    <td align=right>".number_format($tA1)."</td>
					<td align=right>".number_format($tB1)."</td>
					<td align=right>".number_format($tC1)."</td>
					<td align=right>".number_format($tD1)."</td>
					<td align=right>".number_format($tE1)."</td>
					<td align=right>".number_format($tF1)."</td>
					<td align=right>".number_format($tV1)."</td>
					<td align=right>".number_format($tG1)."</td>
					<td align=right>".number_format($tH1)."</td>
					<td align=right>".number_format($tI1)."</td>
					<td align=right>".number_format($tJ1)."</td>
					<td align=right>".number_format($tK1)."</td>
					<td align=right>".number_format($tL1)."</td>
					
                    </tr>"; 
					
					$TtA1+=$tA1;
					$TtB1+=$tB1;
					$TtC1+=$tC1;
					$TtD1+=$tD1;
					$TtE1+=$tE1;
					$TtF1+=$tF1;
					$TtV1+=$tV1;
					$TtG1+=$tG1;
					$TtH1+=$tH1;
					$TtI1+=$tI1;
					$TtJ1+=$tJ1;
					$TtK1+=$tK1;
					$TtL1+=$tL1;
					
					
					
				}
			 
			 $data.="<tr >
                    <td colspan=7>Total Gaji</td>
                    <td align=right>".number_format($TtA1)."</td>
					<td align=right>".number_format($TtB1)."</td>
					<td align=right>".number_format($TtC1)."</td>
					<td align=right>".number_format($TtD1)."</td>
					<td align=right>".number_format($TtE1)."</td>
					<td align=right>".number_format($TtF1)."</td>
					<td align=right>".number_format($TtV1)."</td>
					<td align=right>".number_format($TtG1)."</td> 
					<td align=right>".number_format($TtH1)."</td> 
					<td align=right>".number_format($TtI1)."</td> 
					<td align=right>".number_format($TtJ1)."</td> 
					<td align=right>".number_format($TtK1)."</td> 
					<td align=right>".number_format($TtL1)."</td> 
					
					
                    </tr>"; 
					
			 
             $data.="</tbody><tfoot></tfoot></table>";
			 $data.= "<input type=hidden id=dari value='".$tgmulai."'>";
             $data.= "<input type=hidden id=sampai value='".$tgsampai."'>";
			 echo $data;

			 
}


#----------------------------------------------------------------
?>
