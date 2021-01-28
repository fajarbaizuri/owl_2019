<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$kdOrg=$_POST['kdOrg'];
$noTph=$_POST['noTph'];
$tgl=tanggalsystem($_POST['tgl']);
$noTph=$_POST['noTph'];
$idAsisten=$_POST['idAsisten'];
$idMandor=$_POST['idMandor'];
$idMandor_1=$_POST['idMandor_1'];
$idMandor_2=$_POST['idMandor_2'];
$idMandor_3=$_POST['idMandor_3'];
$brsTph=$_POST['brsTph'];
$buahTph=$_POST['buahTph'];
$brndlanTph=$_POST['brndlanTph'];
$brndlTinggal=$_POST['brndlTinggal'];
$thnNo=explode("-",$_POST['tgl']);
$notransaksi=$kdOrg."/".$noTph."/".$thnNo[2]."/".$thnNo[1]."/".$thnNo[0]."/AP";
$bhTinggal=$_POST['bhTinggal'];
$ptgSensus=$_POST['ptgSensus'];
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$urutPkk=$_POST['urutPkk'];
$psrPikul=$_POST['psrPikul'];
$noTrans=$_POST['noTrans'];
$kiriBrndlanPir=$_POST['dtKiri'][1];//brdpirki
$kiriBrndlanLuarPir=$_POST['dtKiri'][2];//brdlpki
$kiriBuahPkk=$_POST['dtKiri'][3];//buahpkkki
$kiriGaw=$_POST['dtKiri'][4];//bhgwki
$kiriTunas=$_POST['dtKiri'][5];//tunasl

$kananBrndlanPir=$_POST['dtKanan'][1];//brdpirka
$kananBrndlanLuarPir=$_POST['dtKanan'][2];//brdlpka
$kananBuahPkk=$_POST['dtKanan'][3];//buahpkkka
$kananGaw=$_POST['dtKanan'][4];//bhgwka
$kananTunas=$_POST['dtKanan'][5];//brdlama
$periode=$_POST['periode'];

	switch($proses)
	{
            case'getNtph':
                $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
                $sTph="select distinct kode,keterangan from ".$dbname.".kebun_5tph where kodeorg='".$kdOrg."'";
                $qTph=mysql_query($sTph) or die(mysql_error());
                while($rTph=mysql_fetch_assoc($qTph))
                {
                    if($noTph!='')
                    {
                        $optKode.="<option value='".$rTph['kode']."' ".($rTph['kode']==$noTph?"selected":'').">".$rTph['keterangan']."</option>";
                    }
                    else
                    {
                        $optKode.="<option value='".$rTph['kode']."'>".$rTph['keterangan']."</option>";
                    }
                }
                echo $optKode;
            
            break;
            case'insert':
            if($kdOrg==''||$noTph==''||$tgl==''||$brsTph=='0')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
            
            $sCek="select distinct notransaksi from ".$dbname.".kebun_qc_ancakht where notransaksi='".$notransaksi."'";
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rCek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_ancakht (notransaksi, kodeorg, tanggal, baris, notph, jjgtinggal, bdrtinggal, petugas, 
                          updateby, mandor1, madorpanen1, mandorpanen2, mandorpanen3, totjjgtph, totbrdtph,asisten) 
                          values ('".$notransaksi."','".$kdOrg."','".$tgl."','".$brsTph."','".$noTph."','".$bhTinggal."',
                          '".$brndlTinggal."','".$ptgSensus."','".$_SESSION['standard']['userid']."','".$idMandor."','".$idMandor_1."','".$idMandor_2."','".$idMandor_3."'
                          ,'".$buahTph."','".$brndlanTph."','".$idAsisten."')";
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
                else
                {
                    echo $notransaksi;
                }
            }
            else
            {
                exit("Error:Data Sudah Ada");
            }
            break;
            case'update':
            if($tgl==''||$brsTph=='0')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
             $sInsert="update ".$dbname.".kebun_qc_ancakht set baris='".$brsTph."', jjgtinggal='".$bhTinggal."', bdrtinggal='".$brndlTinggal."', petugas='".$ptgSensus."', 
                       updateby='".$_SESSION['standard']['userid']."', mandor1='".$idMandor."', madorpanen1='".$idMandor_1."', mandorpanen2='".$idMandor_2."', mandorpanen3='".$idMandor_3."', 
                       totjjgtph='".$buahTph."', totbrdtph='".$brndlanTph."',asisten='".$idAsisten."'  where notransaksi='".$notransaksi."'";
            if(!mysql_query($sInsert))
            {
                echo "DB Error : ".mysql_error($conn);
            }
            
            break;
            
            case'loadNewData':
            $limit=20;
            $page=0;
            if(isset($_POST['page']))
            {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
            }
            $offset=$page*$limit;

            //$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_ancakht a left join ".$dbname.".kebun_qc_ancakdt b on a.notransaksi=b.notransaksi
                where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_ancakht a left join ".$dbname.".kebun_qc_ancakdt b on a.notransaksi=b.notransaksi
                    where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.",".$limit."";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$rData['notph']."</td>";
                    $tab.="<td align=left>".$rData['urutpkk']."</td>";
                    $tab.="<td align=right>".$rData['brdpikul']."</td>";
                    $tab.="<td align=right>".$rData['brdpirki']."</td>";
                    $tab.="<td align=right>".$rData['brdpirka']."</td>";
                    $tab.="<td align=right>".$rData['brdlpki']."</td>";
                    $tab.="<td align=right>".$rData['brdlpka']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkki']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkka']."</td>";
                    $tab.="<td align=right>".$rData['bhgwki']."</td>";
                    $tab.="<td align=right>".$rData['bhgwka']."</td>";
                    $tab.="<td align=right>".$rData['brdlama']."</td>";
                    $tab.="<td align=right>".$rData['tunasl']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['baris']."','".$rData['notph']."','".$rData['jjgtinggal']."','".$rData['bdrtinggal']."','".$rData['petugas']."','".$rData['mandor1']."','".$rData['madorpanen1']."'
                                ,'".$rData['mandorpanen2']."','".$rData['mandorpanen3']."','".$rData['totjjgtph']."','".$rData['totbrdtph']."','".$rData['asisten']."','".$rData['urutpkk']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."','".$rData['urutpkk']."');\" >
                            <img src=images/delete1.png class=resicon  title='Delete Semua Data di kodeorganisasi ".$rData['kodeorg'].",tanggal ".tanggalnormal($rData['tanggal'])." ' onclick=\"delDataAll('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=16 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=16 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;
            case'cariData':
            $limit=20;
            $page=0;
            if(isset($_POST['page']))
            {
            $page=$_POST['page'];
            if($page<0)
            $page=0;
            }
            $offset=$page*$limit;
            if($kdOrg!='')
            {
                $where.="and kodeorg='".$kdOrg."'";
            }
            if($periode!='')
            {
                $where.="and substr(tanggal,1,7)='".$periode."'";
            }
            //$ql2="select count(*) as jmlhrow from ".$dbname.".kebun_spbht where `kodeorg`='".$lokasi."' order by `nospb` desc";// echo $ql2;
            $ql2="select count(*) as jmlhrow from ".$dbname.".kebun_qc_ancakht a left join ".$dbname.".kebun_qc_ancakdt b on a.notransaksi=b.notransaksi
                where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."'  ".$where." order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct b.*,a.* from ".$dbname.".kebun_qc_ancakht a left join ".$dbname.".kebun_qc_ancakdt b on a.notransaksi=b.notransaksi
                    where substr(`kodeorg`,1,4)='".$_SESSION['empl']['lokasitugas']."'  ".$where." order by `tanggal` desc limit ".$offset.",".$limit."";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td>".$optNmOrg[$rData['kodeorg']]."</td>";
                    $tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
                    $tab.="<td align=left>".$rData['notph']."</td>";
                    $tab.="<td align=left>".$rData['urutpkk']."</td>";
                    $tab.="<td align=right>".$rData['brdpikul']."</td>";
                    $tab.="<td align=right>".$rData['brdpirki']."</td>";
                    $tab.="<td align=right>".$rData['brdpirka']."</td>";
                    $tab.="<td align=right>".$rData['brdlpki']."</td>";
                    $tab.="<td align=right>".$rData['brdlpka']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkki']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkka']."</td>";
                    $tab.="<td align=right>".$rData['bhgwki']."</td>";
                    $tab.="<td align=right>".$rData['bhgwka']."</td>";
                    $tab.="<td align=right>".$rData['brdlama']."</td>";
                    $tab.="<td align=right>".$rData['tunasl']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rData['notransaksi']."','".$rData['kodeorg']."','".tanggalnormal($rData['tanggal'])."',
                            '".$rData['baris']."','".$rData['notph']."','".$rData['jjgtinggal']."','".$rData['bdrtinggal']."','".$rData['petugas']."','".$rData['mandor1']."','".$rData['madorpanen1']."'
                                ,'".$rData['mandorpanen2']."','".$rData['mandorpanen3']."','".$rData['totjjgtph']."','".$rData['totbrdtph']."','".$rData['asisten']."','".$rData['urutpkk']."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetailHead('".$rData['notransaksi']."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=16 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=16 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;
            case'insert_detail':
            if($urutPkk==''||$psrPikul=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
  
            $sCek="select distinct * from ".$dbname.".kebun_qc_ancakdt where notransaksi='".$notransaksi."' and urutpkk='".$urutPkk."'";
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
                $sInsert="insert into ".$dbname.".kebun_qc_ancakdt (notransaksi, urutpkk, brdpikul, brdpirki, brdpirka, brdlpki, brdlpka, brdlama, buahpkkki, buahpkkka, bhgwki, bhgwka, tunasl) 
                          values ('".$noTrans."','".$urutPkk."','".$psrPikul."','".$kiriBrndlanPir."','".$kananBrndlanPir."','".$kiriBrndlanLuarPir."',
                          '".$kananBrndlanLuarPir."','".$kananTunas."','".$kiriBuahPkk."','".$kananBuahPkk."','".$kiriGaw."','".$kananGaw."','".$kiriTunas."')";
               // exit("Error:".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            }
            else
            {
                exit("Error:Data Sudah Ada");
            }
            break;
            case'update_detail':
            if($urutPkk==''||$psrPikul=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
            $sInsert="update ".$dbname.".kebun_qc_ancakdt set brdpikul='".$psrPikul."', brdpirki='".$kiriBrndlanPir."', brdpirka='".$kananBrndlanPir."', brdlpki='".$kiriBrndlanLuarPir."', brdlpka='".$kananBrndlanLuarPir."', 
                      brdlama='".$kananTunas."', buahpkkki='".$kiriBuahPkk."', buahpkkka='".$kananBuahPkk."', bhgwki='".$kiriGaw."', bhgwka='".$kananGaw."', tunasl='".$kiriTunas."' 
                      where notransaksi='".$noTrans."' and urutpkk='".$urutPkk."'";
            //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'delDetail':
                $sDel="delete from ".$dbname.".kebun_qc_ancakdt where notransaksi='".$noTrans."' and urutpkk='".$urutPkk."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'loadDetail':
            $sDetail="select distinct * from ".$dbname.".kebun_qc_ancakdt where notransaksi='".$noTrans."'";
            //    exit("Error".$sDetail);
            $qDetail=mysql_query($sDetail) or die(mysql_error());
            while($rData=mysql_fetch_assoc($qDetail))
            {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=left>".$rData['urutpkk']."</td>";
                    $tab.="<td align=right>".$rData['brdpikul']."</td>";
                    $tab.="<td align=right>".$rData['brdpirki']."</td>";
                    $tab.="<td align=right>".$rData['brdpirka']."</td>";
                    $tab.="<td align=right>".$rData['brdlpki']."</td>";
                    $tab.="<td align=right>".$rData['brdlpka']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkki']."</td>";
                    $tab.="<td align=right>".$rData['buahpkkka']."</td>";
                    $tab.="<td align=right>".$rData['bhgwki']."</td>";
                    $tab.="<td align=right>".$rData['bhgwka']."</td>";
                    $tab.="<td align=right>".$rData['tunasl']."</td>";
                    $tab.="<td align=right>".$rData['brdlama']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/application/application_edit.png class=resicon  title='Edit' 
                        onclick=\"getData('".$rData['notransaksi']."','".$rData['urutpkk']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['notransaksi']."','".$rData['urutpkk']."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
            }
            echo $tab;
            break;
            case'getDetail':
            $sData="select distinct * from ".$dbname.".kebun_qc_ancakdt where notransaksi='".$noTrans."' and urutpkk='".$urutPkk."'";
            $qData=mysql_query($sData) or die(mysql_error());
            $rData=mysql_fetch_assoc($qData);
            echo $rData['urutpkk']."###".$rData['brdpikul']."###".$rData['brdpirki']."###".$rData['brdlpki']."###".$rData['brdlpka']."###".$rData['buahpkkki']."###".$rData['buahpkkka']."###".$rData['bhgwki']."###".$rData['bhgwka']."###".$rData['tunasl']."###".$rData['brdlama'];
            break;
            case'delDetailAll':
             $sDel="delete from ".$dbname.".kebun_qc_ancakht where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;

		default:
		break;
	}


?>