<?php 
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');

$kode_jns=isset($_POST['jns_id'])?$_POST['jns_id']:'';
$kodetraksi=isset($_POST['traksi_id'])?$_POST['traksi_id']:'';
$lokasi=$_SESSION['empl']['lokasitugas'];
$user_entry=$_SESSION['standard']['userid'];
$kode_vhc=isset($_POST['kode_vhc'])?$_POST['kode_vhc']:'';
$tgl_kerja=isset($_POST['tglKerja'])?tanggalsystem($_POST['tglKerja']):'';
$kmhmAwal=isset($_POST['kmhmAwal'])?$_POST['kmhmAwal']:'';
$kmhmAkhir=isset($_POST['kmhmAkhir'])?$_POST['kmhmAkhir']:'';
$satuan=isset($_POST['satuan'])?$_POST['satuan']:'';
$jnsBbm=isset($_POST['jnsBbm'])?$_POST['jnsBbm']:'';
$jumlahBbm=isset($_POST['jumlah'])?$_POST['jumlah']:'';
$notransaksi_head=isset($_POST['no_trans'])?$_POST['no_trans']:'';
$kdVhc=isset($_POST['kdVhc'])?$_POST['kdVhc']:'';
$premiStat=isset($_POST['premiStat'])?$_POST['premiStat']:'';
$proses=isset($_POST['proses'])?$_POST['proses']:'';
$noKntrk=isset($_POST['noKntrk'])?$_POST['noKntrk']:'';
$kdOrg=isset($_POST['kdOrg'])?$_POST['kdOrg']:'';
$kodeOrg=isset($_POST['kodeOrg'])?$_POST['kodeOrg']:'';
$txtTgl=isset($_POST['txtTgl'])?$_POST['txtTgl']:'';
$txtCari=isset($_POST['txtCari'])?$_POST['txtCari']:'';
switch($proses)
{
	case'getKodeVhc':
		$optKdvhc="";
		if($notransaksi_head=='')
		{
			$sql="select nopol,kodevhc,kodetraksi from ".$dbname.".vhc_5master where jenisvhc='".$kode_jns."' and kodetraksi like '%".$kodetraksi."%'"; //echo "warning:".$sql;
		}
		elseif($notransaksi_head!='')
		{
		/*	echo"warning:masuk";
			print"<pre>";
			print_r($_POST);
			print"</pre>";*/
			$sVhc="select jenisvhc,kodevhc from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
			$qVhc=mysql_query($sVhc) or die(mysql_error());
			$rVhc=mysql_fetch_assoc($qVhc);
			$kdVhc=$rVhc['kodevhc'];
			$sql="select nopol,kodevhc,kodetraksi from ".$dbname.".vhc_5master where jenisvhc='".$rVhc['jenisvhc']."' ";  //echo "warning:".$sql;
		}
			
		//$sql="select kodevhc from ".$dbname.".vhc_5master where jenisvhc='".$kode_jns."' "; echo "warning:".$sql;
		$query=mysql_query($sql) or die(mysql_error());
		while($res=mysql_fetch_assoc($query))
		{
			$optKdvhc.="<option value='".$res['kodevhc']."' ".($res['kodevhc']==$kdVhc?'selected=selected':'').">[".$res['nopol']."] [".$res['kodevhc']."] [".$res['kodetraksi']."]</option>";
		}
		echo $optKdvhc;
		break;
	case'get_no_transaksi':
		$tgl=  date('Ymd');
		$bln = substr($tgl,4,2);
		$thn = substr($tgl,0,4);
		
		$notransaksi=$kdOrg."/RUN/".date('Y')."/".date('m')."/";
        $ql="select `notransaksi` from ".$dbname.".`vhc_runht` where notransaksi like '%".$notransaksi."%' order by `notransaksi` desc limit 0,1";
        $qr=mysql_query($ql) or die(mysql_error());
        $rp=mysql_fetch_object($qr);
		if(!isset($rp->notransaksi)){$rp->notransaksi='';}
        $awal=substr($rp->notransaksi,-4,4);
		//echo "warning:".$awal;exit();
        $awal=intval($awal);
        $cekbln=substr($rp->notransaksi,-7,2);
        $cekthn=substr($rp->notransaksi,-12,4);
		//echo "warning:".$awal;exit();
      //  if(($bln!=$cekbln)&&($thn!=$cekthn))
	    if($thn!=$cekthn)
        {
        //echo $awal; exit();
                $awal=1;
        }
        else
        {
              
			    $awal++;
				// echo"warning:masuk".$awal;exit();
        }
        $counter=addZero($awal,4);
		$notransaksi=$kdOrg."/RUN/".$thn."/".$bln."/".$counter;
		
		$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$skary="select a.karyawanid,a.nama from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.aktif='1' and b.lokasitugas='".$kdOrg."' ";//echo $skary;
		$qkary=mysql_query($skary) or die(mysql_error());
		while($rkary=mysql_fetch_assoc($qkary))
		{
			$optKary.="<option value=".$rkary['karyawanid'].">".$rkary['nama']."&nbsp;[".$rkary['karyawanid']."]</option>";
		}
                if($kdOrg=='')$notransaksi = '';
		
        echo $notransaksi."####".$optKary;
		break;
	case'insert_header':
        $thn=substr($tgl_kerja,0,4);
        $bln=substr($tgl_kerja,4,2);
        $periode=$thn."-".$bln;
		if(($tgl_kerja=='')||($jumlahBbm==''))
		{
			echo"warning:Please Complete The Form";exit();
		}
		//elseif($kmhmAwal>=$kmhmAkhir)
		//{
		//	echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";
		//	exit();
		//}
        $str="select * from ".$dbname.".setup_periodeakuntansi where periode='".$periode."' and
        kodeorg='".$kodeOrg."' and tutupbuku=1";
        //exit("Error".$str) ;
        $res=mysql_query($str);
        if(mysql_num_rows($res)>0)
        $aktif=true;
        else
        $aktif=false;
        if($aktif==true)
        {
        exit("Error:Periode sudah tutup buku");
        }
		$jumlah=$kmhmAkhir-$kmhmAwal;
		$sqlCek="select notransaksi from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'"; 
		$queryCek=mysql_query($sqlCek) or die(mysql_error());
		$rowCek=mysql_fetch_row($queryCek);
		if($rowCek<1)
		{
			$sql="insert into ".$dbname.".vhc_runht 
			(`notransaksi`,`kodeorg`,`jenisvhc`,`kodevhc`,`tanggal`,`jenisbbm`,`jlhbbm`,`updateby`,`premi`,`noKntrak`) 
			values ('".$notransaksi_head."','".$kodeOrg."','".$kode_jns."','".$kode_vhc."','".$tgl_kerja."','".$jnsBbm."','".$jumlahBbm."','".$user_entry."','".$premiStat."','".$noKntrk."')";
			if(mysql_query($sql))
			echo"";
			else
			echo "DB Error : ".mysql_error($conn);
		}
		else
		{
			echo"warning:Already Input";
			exit();
		}
		break;
	case 'update_head':
		if(($tgl_kerja=='')||($kmhmAwal=='')||($kmhmAkhir=='')||($jumlahBbm==''))
		{
			echo"warning:Please Complete The Form";exit();
		}
		elseif($kmhmAwal>=$kmhmAkhir)
		{
			echo"warning:".$_SESSION['lang']['vhc_kmhm_awal']." must lower then ".$_SESSION['lang']['vhc_kmhm_akhir']."";exit();
		}
		$jumlah=$kmhmAkhir-$kmhmAwal;
		$sql="update ".$dbname.".vhc_runht set jenisvhc='".$kode_jns."',kodevhc='".$kode_vhc."',tanggal='".$tgl_kerja."',kmhmawal='".$kmhmAwal."',
		kmhmakhir='".$kmhmAkhir."',jumlah='".$jumlah."',satuan='".$satuan."',jenisbbm='".$jnsBbm."',jlhbbm='".$jumlahBbm."',premi='".$premiStat."'  	    ,noKntrak='".$noKntrk."' where notransaksi='".$notransaksi_head."'";
		//echo "warning:".$sql;
		if(mysql_query($sql))
		echo"";
		else
		echo "DB Error : ".mysql_error($conn);
		break;
	case'load_data_header':
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where updateby='".$_SESSION['standard']['userid']."' order by notransaksi desc";// echo $ql2;
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		/*<img src=images/application/application_edit.png class=resicon  title='Edit' 
					onclick=\"fillField('". $res['notransaksi']."','". $res['jenisvhc']."','". $res['kodevhc']."','".tanggalnormal($res['tanggal'])."','". $res['kmhmawal']."','". $res['kmhmakhir']."','". $res['satuan']."','". $res['jenisbbm']."','". $res['jlhbbm']."','". $res['noKntrak']."','".$thn."');\">*/
		
		$sql="select * from ".$dbname.".vhc_runht where updateby='".$_SESSION['standard']['userid']."' order by notransaksi desc limit ".$offset.",".$limit."";
		$query=mysql_query($sql) or die(mysql_error());
		$no=0;
		while($res=mysql_fetch_assoc($query))
		{
			$sSpk="select tanggal from ".$dbname.".log_spkht where notransaksi='".$res['noKntrak']."'";
			//echo "warning:".$sSpk;
			$qSpk=mysql_query($sSpk) or die(mysql_error());
			$rSpk=mysql_fetch_assoc($qSpk);
			$thn=substr($rSpk['tanggal'],0,4);
			
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);
			$rbrg['namabarang'];
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td align=center>".$res['notransaksi']."</td>
			<td align=center>".$res['jenisvhc']."</td>
			<td align=center>".$res['kodevhc']."</td>
			<td align=center>".tanggalnormal($res['tanggal'])."</td>
			<td align=center>".$rbrg['namabarang']."</td>
			<td align=center>".$res['jlhbbm']."</td>
			";
			if($res['updateby']==$_SESSION['standard']['userid'])
			{
				if($res['posting']==1)
				{
					echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">&nbsp;".$_SESSION['lang']['posting']."</td>";
				}
				else
				{
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
					onclick=\"fillField('". $res['notransaksi']."','".$thn."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delHead('". $res['notransaksi']."');\" >	
					<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">
					</td>";
				}
			}
			else
			{	if($res['posting']==1)
				{
					echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">&nbsp;".$_SESSION['lang']['posting']."</td>";
				}
				else
				{
				echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"></td>"; }
			}
		
		}
		echo" </tr><tr class=rowheader><td colspan=11 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
					<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
					</td>
					</tr>";
		break;
	case'cariTransaksi':
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		if($_POST['txtTgl']!='')
		{
			$txtTgl=tanggalsystem($_POST['txtTgl']);
			$txt_tgl_a=substr($txtTgl,0,4);
			$txt_tgl_b=substr($txtTgl,4,2);
			$txt_tgl_c=substr($txtTgl,6,2);
			$txtTgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
			$where.=" and tanggal='".$txtTgl."'";
		}
		if($txtCari!='')
		{
			$where.=" and notransaksi like '%".$txtCari."%'";
		}
		$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_runht where updateby='".$_SESSION['standard']['userid']."' ".$where." order by notransaksi desc";
		
		$sql="select * from ".$dbname.".vhc_runht where updateby='".$_SESSION['standard']['userid']."' ".$where." order by notransaksi desc limit ".$offset.",".$limit."";
		//exit("Error".$sql);
		$query=mysql_query($sql) or die(mysql_error());
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
	
		while($res=mysql_fetch_assoc($query))
		{
			$sSpk="select tanggal from ".$dbname.".log_spkht where notransaksi='".$res['noKntrak']."'";
			//echo "warning:".$sSpk;
			$qSpk=mysql_query($sSpk) or die(mysql_error());
			$rSpk=mysql_fetch_assoc($qSpk);
			$thn=substr($rSpk['tanggal'],0,4);
			
			$sbrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$res['jenisbbm']."'";
			$qbrg=mysql_query($sbrg) or die(mysql_error());
			$rbrg=mysql_fetch_assoc($qbrg);
			$rbrg['namabarang'];
			$no+=1;
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
			<td align=center>".$res['notransaksi']."</td>
			<td align=center>".$res['jenisvhc']."</td>
			<td align=center>".$res['kodevhc']."</td>
			<td align=center>".tanggalnormal($res['tanggal'])."</td>
			<td align=center>".$res['kmhmawal']."</td>
			<td align=center>".$res['kmhmakhir']."</td>
			<td align=center>".$res['satuan']."</td>
			<td align=center>".$rbrg['namabarang']."</td>
			<td align=center>".$res['jlhbbm']."</td>
			";
			if($res['updateby']==$_SESSION['standard']['userid'])
			{
				if($res['posting']==1)
				{
					echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">&nbsp;".$_SESSION['lang']['posting']."</td>";
				}
				else
				{
					echo"
					<td><img src=images/application/application_edit.png class=resicon  title='Edit' 
					onclick=\"fillField('". $res['notransaksi']."','".$thn."');\">
					<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delHead('". $res['notransaksi']."');\" >	
					<img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">
					</td>";
				}
			}
			else
			{	if($res['posting']==1)
				{
					echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\">&nbsp;".$_SESSION['lang']['posting']."</td>";
				}
				else
				{
				echo"<td><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('vhc_runht','".$res['notransaksi'].",". $res['kodevhc']."','','vhc_slave_pekerjaanPrint',event);\"></td>"; }
			}
		
		}
		echo" </tr><tr class=rowheader><td colspan=11 align=center>
			".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."
			<br />
			<button class=mybutton onclick=cariData(".($page-1).");>".$_SESSION['lang']['pref']."</button>
			<button class=mybutton onclick=cariData(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
			</td>
			</tr>";
			break;
	case'cari_lokasi':
		$txtcari=$_POST['txtinputan'];
		$str="select a.kodeorganisasi,a.namaorganisasi from ".$dbname.".organisasi a where  a.tipe in ('KEBUN','AFDELING','BLOK') and a.namaorganisasi like '%".$txtcari."%'";
		// echo $str;
		$res=mysql_query($str);	
		if(mysql_num_rows($res)<1)
		{
			echo"Error: ".$_SESSION['lang']['tidakditemukan'];			
		}
		else
		{
			echo"
			<fieldset>
			<legend>".$_SESSION['lang']['result']."</legend>
			<div style=\"width:450px; height:300px; overflow:auto;\">
			<table class=sortable cellspacing=1 border=0>
			<thead>
			<tr class=rowheader>
			<td>No</td>
			<td>".$_SESSION['lang']['kodeorganisasi']."</td>
			<td>".$_SESSION['lang']['namaorganisasi']."</td>
			</tr>
			</thead>
			<tbody>";
			$no=0;	 
			while($bar=mysql_fetch_object($res))
			{
			$no+=1;
			echo"<tr class=rowcontent style='cursor:pointer;' title='Click' onclick=\"throwThisRow('".$bar->kodeorganisasi."','".$bar->namaorganisasi."');\">
			<td>".$no."</td>
			<td>".$bar->kodeorganisasi."</td>
			<td>".$bar->namaorganisasi."</td>
			</tr>";			   	
			}
			echo    "
			</tbody>
			<tfoot></tfoot>
			</table></div></fieldset>";
		}
		break;
	case 'deleteHead':
		$sdel="delete from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'"; //echo "warning:".$sdel;
		if(mysql_query($sdel))
		{
			$sdel2="delete from ".$dbname.".vhc_rundt where notransaksi='".$notransaksi_head."'";
			mysql_query($sdel2) or die(mysql_error());
			
			$sdel3="delete from ".$dbname.".vhc_runhk where notransaksi='".$notransaksi_head."'";
			mysql_query($sdel3) or die(mysql_error());
		}
		else
		{echo "DB Error : ".mysql_error($conn);}
		break;
	case'getData':
		$sql="select * from ".$dbname.".vhc_runht where notransaksi='".$notransaksi_head."'";
		$query=mysql_query($sql) or die(mysql_error());
		$res=mysql_fetch_assoc($query);
		$sSpk="select tanggal from ".$dbname.".log_spkht where notransaksi='".$res['noKntrak']."'";
		//echo "warning:".$sSpk;
		//noTrans,jnsVhc,KdVhc,tglKrja,kmhmA,kmhmR,sat,jnsBbm,jmlhBbm,nkntrk,Thn
		$qSpk=mysql_query($sSpk) or die(mysql_error());
		$resPk=mysql_fetch_assoc($qSpk);
		$thn=substr($resPk['tanggal'],0,4);
		$optKntrk="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$sSpk2="select notransaksi from ".$dbname.".log_spkht where kodeorg='".$lokasi."' and posting<>'0' and tanggal like '%".$thn."%'";
		//echo "warning:".$sSpk;noTrans,jnsVhc,KdVhc,tglKrja,kmhmA,kmhmR,sat,jnsBbm,jmlhBbm,nkntrk,Thn
		$qSpk2=mysql_query($sSpk2) or die(mysql_error());
		while($rSpk=mysql_fetch_assoc($qSpk2))
		{
			$optKntrk.="<option value=".$rSpk['notransaksi']." ".($rSpk['notransaksi']==$noKntrak?'selected':'').">".$rSpk['notransaksi']."</option>";
		}
		
		$optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
		$skary="select a.karyawanid,a.nama from ".$dbname.".vhc_5operator a left join ".$dbname.".datakaryawan b on a.karyawanid=b.karyawanid where a.aktif='1' and b.lokasitugas='".$res['kodeorg']."' ";//echo $skary;
		$qkary=mysql_query($skary) or die(mysql_error());
		while($rkary=mysql_fetch_assoc($qkary))
		{
			$optKary.="<option value=".$rkary['karyawanid'].">".$rkary['nama']."&nbsp;[".$rkary['karyawanid']."]</option>";
		}
		echo $res['notransaksi']."####".$res['jenisvhc']."####".tanggalnormal($res['tanggal'])."################".$res['jenisbbm']."####".$res['jlhbbm']."####".$res['kodeorg']."####".$thn."####".$optKntrk."####".$optKary;	
		break;
	default:
		break;
}
?>