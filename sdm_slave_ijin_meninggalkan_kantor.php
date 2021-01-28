<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$tglijin=tanggalsystem($_POST['tglijin']);
$jnsIjin=$_POST['jnsIjin'];
$jamDr=$_POST['jamDr'];
$jamSmp=$_POST['jamSmp'];
$keperluan=$_POST['keperluan'];
$ket=$_POST['ket'];
$atasan=$_POST['atasan'];
$tglAwal=explode("-",$_POST['tglAwal']);
$tgl1=$tglAwal[2]."-".$tglAwal[1]."-".$tglAwal[0];
$tglEnd=explode("-",$_POST['tglEnd']);
$tgl2=$tglEnd[2]."-".$tglEnd[1]."-".$tglEnd[0];
$jamDr1=$tgl1." ".$jamDr;
$jamSmp1=$tgl2." ".$jamSmp;
$arrNmkary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');
$arrKeputusan=array("0"=>$_SESSION['lang']['diajukan'],"1"=>$_SESSION['lang']['disetujui'],"2"=>$_SESSION['lang']['ditolak']);
$where=" tanggal='".$tglijin."' and karyawanid='".$_SESSION['standard']['userid']."'";
$atsSblm=$_POST['atsSblm'];
$hk=$_POST['jumlahhk'];
$hrd=$_POST['hrd'];
$periodec=$_POST['periodec'];

	switch($proses)
	{
		
		case'insert':
                //===============ambil sisa cuti
                //ambil cuti ybs
                $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid']." 
                    and periodecuti=".$periodec;
                $res=mysql_query($strf);

                $sisa='';
                while($barf=mysql_fetch_object($res))
                {
                    $sisa=$barf->sisa;
                }
                if($sisa=='')
                    $sisa=0;
                //=============================                    
                  //  exit("Error:masuk");
		if(($tglijin=='')||($jnsIjin=='')||($jamDr1=='')||($jamSmp1=='')||($keperluan=='')||($atasan==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
                $wktu="0000-00-00 00:00:00";
		$sCek="select tanggal from ".$dbname.".sdm_ijin where  ".$where.""; //echo "warning:".$sCek;
		$qCek=mysql_query($sCek) or die(mysql_error());
		$rCek=mysql_fetch_row($qCek);
		if($rCek<1)
		{
                    if($atasan!='')
                    {
                        $wktu=date("Y-m-d H:i:s");
                    }
			//$sIns="insert into ".$dbname.".sdm_ijin (karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, jenisijin,hrd,periodecuti,jumlahhari) 
			//values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr1."','".$jamSmp1."','".$jnsIjin."',".$hrd.",".$periodec.",".$hk.")";
		 
		 $sIns="insert into ".$dbname.".sdm_ijin (karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, jenisijin) 
		 values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr1."','".$jamSmp1."','".$jnsIjin."')";
		 // exit("Error:Data Pada Tanggal ".$sIns." Sudah ada");
			if(mysql_query($sIns))
			{
                            if($atasan!='')
                            {
                                #send an email to incharge person
                                    $to=getUserEmail($atasan);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                            }
                        }
			else
			{
                            echo "DB Error : ".mysql_error($conn);
                        }
		}
                else
                {
                    exit("Error:Data Pada Tanggal ".$_POST['tglijin']." Sudah ada");
                }
		break;
		
		case'loadData':
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'  order by `tanggal` desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		$slvhc="select * from ".$dbname.".sdm_ijin where karyawanid='".$_SESSION['standard']['userid']."'   order by `tanggal` desc limit ".$offset.",".$limit." ";
		$qlvhc=mysql_query($slvhc) or die(mysql_error());
		$user_online=$_SESSION['standard']['userid'];
		while($rlvhc=mysql_fetch_assoc($qlvhc))
		{
		$no+=1;
               
		echo"
		<tr class=rowcontent>
                <td>".$no."</td>
                <td>".tanggalnormal($rlvhc['tanggal'])."</td>
                <td>".$rlvhc['keperluan']."</td>
                <td>".$rlvhc['jenisijin']."</td>
                <td>".$arrNmkary[$rlvhc['persetujuan1']]."</td>
                <td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>
                <td>".tanggalnormald($rlvhc['darijam'])."</td>
                <td>".tanggalnormald($rlvhc['sampaijam'])."</td>";
		if($rlvhc['stpersetujuan1']==0 and $rlvhc['stpersetujuanrd']==0)
                {
                echo"<td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$rlvhc['keperluan']."','".tanggalnormal($rlvhc['tanggal'])."','".$rlvhc['jenisijin']."','".$rlvhc['persetujuan1']."','".$rlvhc['stpersetujuan1']."','".$rlvhc['darijam']."','".$rlvhc['sampaijam']."','".$rlvhc['hrd']."','".$rlvhc['jumlahhari']."','".$rlvhc['periodecuti']."');\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delData('".tanggalnormal($rlvhc['tanggal'])."');\" ></td>";
                    //<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_ijin','".$rlvhc['tanggal'].",".$rlvhc['karyawanid']."','','sdm_slave_ijin_meninggalkan_kantor',event)\"></td>";
                }
                else
                {
                    echo "<td>".$arrKeputusan[$rlvhc['stpersetujuan1']]."</td>";
                   // echo"<td> <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('sdm_ijin','".$rlvhc['tanggal'].",".$rlvhc['karyawanid']."','','sdm_slave_ijin_meninggalkan_kantor',event)\"></td>";
                }//end if updateby
	
	}//end while
		echo"
		</tr><tr class=rowheader><td colspan=9 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
		break;
		case'getKet':
		$sket="select distinct keterangan from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet);
                
                echo $rKet['keterangan'];
		break;
		
		case'deleteData':
                $sket="select distinct stpersetujuan1 from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet); 
                if($rKet['stpersetujuan1']==0)
                {
                        $sDel="delete from ".$dbname.".sdm_ijin where ".$where."";
                        //exit("Error".$sDel);
                        if(mysql_query($sDel))
                        echo"";
                        else
                        echo "DB Error : ".mysql_error($conn);                        
                }
                else
                {
                    exit("Error:Sudah ada keputusan");
                }
		break;
		
		case'update':
                    //===============ambil sisa cuti
                    //ambil cuti ybs
                    $strf="select sisa from ".$dbname.".sdm_cutiht where karyawanid=".$_SESSION['standard']['userid']." 
                        and periodecuti=".$periodec;
                    $res=mysql_query($strf);

                    $sisa='';
                    while($barf=mysql_fetch_object($res))
                    {
                        $sisa=$barf->sisa;
                    }
                    if($sisa=='')
                        $sisa=0;
                    //=============================		
                if(($jnsIjin=='')||($jamDr=='')||($jamSmp=='')||($keperluan=='')||($atasan==''))
		{
			echo"warning:Please Complete The Form";
			exit();
		}
                $sket="select distinct stpersetujuan1 from ".$dbname.".sdm_ijin where ".$where."";
                $qKet=mysql_query($sket) or die(mysql_error($conn));
                $rKet=mysql_fetch_assoc($qKet); 
                if($rKet['stpersetujuan1']==0)
                {
                    //(karyawanid, tanggal, keperluan, keterangan, persetujuan1, waktupengajuan, darijam, sampaijam, jenisijin) 
			//values ('".$_SESSION['standard']['userid']."','".$tglijin."','".$keperluan."','".$ket."','".$atasan."','".$wktu."','".$jamDr."','".$jamSmp."','".$jnsIjin."')
                    $sUp="update  ".$dbname.".sdm_ijin set keperluan='".$keperluan."', keterangan='".$ket."', darijam='".$jamDr1."', 
                          sampaijam='".$jamSmp1."',jenisijin='".$jnsIjin."',
                          hrd=".$hrd.",periodecuti=".$periodec.",jumlahhari=".$hk;
                    if($atsSblm!=$atasan)
                    {
                         $wktu=date("Y-m-d H:i:s");
                         $sUp.=",persetujuan1='".$atasan."',waktupengajuan='".$wktu."'";
                    }
                    $sUp.=" where ".$where."";
                    if(mysql_query($sUp))
                    {
                        if($atsSblm!=$atasan)
                        {
                               #send an email to incharge person
                                    $to=getUserEmail($atasan);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                        }
                    }
                    //mysql_query($sUp) or die(mysql_error());
                }
                 else
                {
                    exit("Error:Sudah ada keputusan");
                }
                if($atsSblm!=$atasan)
                {
                                    $to=getUserEmail($atsSblm);
                                            $namakaryawan=getNamaKaryawan($_SESSION['standard']['userid']);
                                            $subject="[Notifikasi]Pembatalan Persetujuan Ijin Keluar Kantor a/n ".$namakaryawan;
                                            $body="<html>
                                                     <head>
                                                     <body>
                                                       <dd>Dengan Hormat,</dd><br>
                                                       <br>
                                                       Pada hari ini, tanggal ".date('d-m-Y')." karyawan a/n  ".$namakaryawan." mengajukan Ijin/".$jnsIjin." (".$keperluan.")
                                                       kepada bapak/ibu. Untuk menindak-lanjuti, silahkan ikuti link dibawah.
                                                       <br>
                                                       <br>
                                                       Note: Sisa cuti ybs periode ".$periodec.":".$sisa." Hari
                                                       <br>
                                                       <br>
                                                       Regards,<br>
                                                       Owl-Plantation System.
                                                     </body>
                                                     </head>
                                                   </html>
                                                   ";
                                            $kirim=kirimEmail($to,$subject,$body);#this has return but disobeying;
                    
                }
		break;
		default:
		break;
	}


?>