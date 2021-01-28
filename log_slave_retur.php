<?
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
require_once('master_validation.php');
require_once('config/connection.php');

$method=$_POST['method'];
$scnotran=isset($_POST['scnotran'])?$_POST['scnotran']:'';
$tglcari=isset($_POST['tglcari'])?tanggalsystem($_POST['tglcari']):'';
$notranlama=isset($_POST['notranlama'])?$_POST['notranlama']:'';
$notrandet=isset($_POST['notrandet'])?$_POST['notrandet']:'';
$notran=isset($_POST['notran'])?$_POST['notran']:'';
$tgl=isset($_POST['tgl'])?tanggalsystem($_POST['tgl']):'';
$pta=isset($_POST['pta'])?$_POST['pta']:'';
$kdgudang=isset($_POST['kdgudang'])?$_POST['kdgudang']:'';
$nopo=isset($_POST['nopo'])?$_POST['nopo']:'';
$barang=isset($_POST['barang'])?$_POST['barang']:'';
$jumlah=isset($_POST['jumlah'])?$_POST['jumlah']:'';
$satuan=isset($_POST['satuan'])?$_POST['satuan']:'';
$hargasat=isset($_POST['hargasat'])?$_POST['hargasat']:'';
$supp=isset($_POST['supp'])?$_POST['supp']:'';
//$=$_POST[''];

$optnamabarang=makeOption($dbname,'log_5masterbarang','kodebarang,namabarang');
$optsup=makeOption($dbname,'log_5supplier','supplierid,namasupplier');
$optnmorg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
switch($method)
{
	
	case 'getnotran':
		if(isset($_POST['notranlama'])){
		$notranlama=trim($_POST['notranlama']);
		if($_POST['notranlama']=='')
		{
			echo "warning:Tidak ada nomor transaksi";
			exit();
		}
		else
		{
			$tgl=substr($notranlama,0,6);
			$gudang=substr($notranlama,15,6);
			//exit("Error:$tgl");
			//exit("Error:$gudang");
			//20120600001-GR-TDBE60
			$notran=$tgl."-GI-".$gudang;
			//exit("Error:$notran");
				
				$ql="select `notransaksi` from ".$dbname.".`log_transaksiht` where left(notransaksi,6)='".$tgl
					."' and right(notransaksi,".(3+strlen($gudang)).")='GI-".$gudang."' order by `notransaksi` desc limit 0,1";//201206
				$qr=mysql_query($ql) or die(mysql_error());
				$rp=mysql_fetch_object($qr);
				if(isset($rp->notransaksi)) {
					$awal=substr($rp->notransaksi,6,5);
				} else {
					$awal=0;
				}
				$awal=intval($awal);
				//$cekbln=substr($rp->notransaksi,6,2);
				//$cekthn=substr($rp->notransaksi,9,4);
				
				//if(($bln!=$cekbln)&&($thn!=$cekthn))
				$counter=addZero($awal+1,5);
				$notran=$tgl.$counter."-GI-".$gudang;
				//exit("Error:$notran");
				echo $notran;
				
			}
		}
	break;
	
	
	case 'getdatalama':
		$sql="select kodept,nopo,kodegudang,idsupplier from ".$dbname.".log_transaksiht WHERE notransaksi='".$notranlama."'";	
		$qry=mysql_query($sql) or die(mysql_error());
		$data=mysql_fetch_assoc($qry);
		echo $data['kodept']."###".$data['nopo']."###".$data['kodegudang']."###".$data['idsupplier']."###".$optsup[$data['idsupplier']]."###".$optnmorg[$data['kodegudang']];
	break;
	
	case 'detailbarang':
		$sql="select satuan,hargasatuan from ".$dbname.".log_transaksidt WHERE notransaksi='".$notranlama."' and kodebarang='".$barang."' ";	
		$qry=mysql_query($sql) or die(mysql_error());
		$data=mysql_fetch_assoc($qry);
		echo $data['satuan']."###".$data['hargasatuan'];
	break;
	

	case'getbarang':
	//exit("Error:MASUK");
		$sOpt="select kodebarang from ".$dbname.".log_transaksidt WHERE notransaksi='".$notranlama."' ";
		//exit("Error:$sOpt");
			$qOpt=mysql_query($sOpt) or die(mysql_error());
			$optbarang="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
			while($rOpt=mysql_fetch_assoc($qOpt))
			{
						$optbarang.="<option value=".$rOpt['kodebarang'].">".$optnamabarang[$rOpt['kodebarang']]."</option>";
			}
		echo $optbarang;
	break;
	
			
	case'cekstokbarang':		
		$iCek="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$barang."' and kodegudang='".$kdgudang."'";
		//$ada=true;
		$nCek=mysql_query($iCek)or die(mysql_error());
		while($dCek=mysql_fetch_assoc($nCek))
		$stok=$dCek['saldoqty'];
		//exit("Error:$stok.$jumlah");
		{
			if ($jumlah>$stok)
			{
				echo "warning : Jumlah barang '".$barang."' melebihi stok yang tersedia di gudang, Stok saat ini : ".$stok;
				exit();	
			}
			else
			{
			}	
		}		
	break;
	
	##insert header		
	case 'simpanheader':
		$qTrans = selectQuery($dbname,'log_transaksiht','tanggal',
			"notransaksi='".$notranlama."'");
		$resTrans = fetchData($qTrans);
		if($tgl<str_replace('-','',$resTrans[0]['tanggal'])) {
			exit("Warning: Tanggal harus sebelum penerimaan barang.\n".
				 "Tanggal penerimaan barang: ".tanggalnormal($resTrans[0]['tanggal']));
		}
		
		$start=$_SESSION['org']['period']['start'];
		$end=$_SESSION['org']['period']['end'];
		if($tgl<$start or $tgl>$end) {
			exit("Warning: Tanggal diluar periode ".$_SESSION['org']['period']['tahun'].
				 "-".$_SESSION['org']['period']['bulan']);
		}
		
		$indra="insert into ".$dbname.".log_transaksiht (`notransaksireferensi`,`notransaksi`,`tanggal`,`kodept`,`nopo`,`kodegudang`,`idsupplier`,`tipetransaksi`,`user`)
		values ('".$notranlama."','".$notran."','".$tgl."','".$pta."','".$nopo."','".$kdgudang."','".$supp."','6',".$_SESSION['standard']['userid'].")";
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	#### case insert detail
	case 'simpandetail':
		//exit("Error:masuk");
		$iCek="select saldoqty from ".$dbname.".log_5masterbarangdt where kodebarang='".$barang."' and kodegudang='".$kdgudang."'";
		//$ada=true;
		$nCek=mysql_query($iCek)or die(mysql_error());
		while($dCek=mysql_fetch_assoc($nCek)) {
			$stok=$dCek['saldoqty'];
			if ($jumlah>$stok)
			{
				echo "warning : Jumlah barang '".$barang."' melebihi stok yang tersedia di gudang, Stok saat ini : ".$stok;
				exit();	
			}
		}		
		
		$indra="insert into ".$dbname.".log_transaksidt (`notransaksi`,`kodebarang`,`satuan`,`jumlah`,`hargasatuan`,`updateby`)
		values ('".$notran."','".$barang."','".$satuan."','".$jumlah."','".$hargasat."','".$_SESSION['standard']['userid']."')";
		echo $indra;
		if(mysql_query($indra))
		echo"";
		else
		echo " Gagal,".addslashes(mysql_error($conn));
	break;
	
	
	#####LOAD DETAIL DATA	
	case 'loadDetail';	
		//exit("Error:MASUK OI");
		$no=0;
		$str="select * from ".$dbname.".log_transaksidt where notransaksi='".$notran."' ";
		//echo $str;
		$str2=mysql_query($str) or die(mysql_error());
		while($bar1=mysql_fetch_assoc($str2))
		{
			$no+=1;
			$tab="<tr class=rowcontent>";
			$tab.="<td align=center>".$no."</td>";
			$tab.="<td align=left>".$bar1['kodebarang']."</td>";
			$tab.="<td align=left>".$bar1['satuan']."</td>";
			$tab.="<td align=left>".$bar1['jumlah']."</td>";
			$tab.="<td align=left>".$bar1['hargasatuan']."</td>";
			$tab.="<td><img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"DelDetail('".$bar1['notransaksi']."','".$bar1['kodebarang']."');\" ></td>";
		echo $tab;
		}
	break;
	
	
	##########case delete detail
	case 'deletedetail':
		//exit("Error:Masuk");
		$tab="delete from ".$dbname.".log_transaksidt where notransaksi='".$notran."' and kodebarang='".$barang."' and kodeblok='' ";
		//echo $tab;
		//exit("Error:$tab");
		if(mysql_query($tab))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;	
	
	
	##########case delete
	case 'delete':
		//exit("Error:Masuk Oi");
		$indra="delete from ".$dbname.".log_transaksiht where notransaksi='".$notran."' ";
		//echo $indra;
		//exit($indra);
		if(mysql_query($indra))
		{
		}
		else
		{
			echo " Gagal,".addslashes(mysql_error($conn));
		}			
	break;	
	
	case'loadNewData':
	
		echo"
		<table cellspacing=1 border=0 class=sortable>
		<thead>
		<tr class=rowheader>
		<td>No.</td>
		<td>No. Transaksi</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>PT</td>
		<td>kodegudang</td>
		<td>No. PO</td>
		<td>Action</td>
		</tr>
		</thead>
		<tbody>
		";
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$tmbh='';
                if($scnotran!='')
                {
                    $tmbh=" and notransaksi like '%".$scnotran."%' ";
					//echo $tmbh;
                }
		
		$tmbh2='';
                if($tglcari!='')
                {
                    $tmbh2=" and tanggal='".$tglcari."' ";
					//echo $tmbh2;
                }
		
		$ql2="select count(*) as jmlhrow from ".$dbname.".log_transaksiht where tipetransaksi='6' ".$tmbh." ".$tmbh2." order by `notransaksi` desc";// echo $ql2;notran
		$query2=mysql_query($ql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
		
		$dr="select * from ".$dbname.".log_transaksiht where tipetransaksi='6' ".$tmbh." ".$tmbh2." order by `notransaksi` desc limit ".$offset.",".$limit."";
		$ra=mysql_query($dr) or die(mysql_error());
		//$user_online=$_SESSION['standard']['userid'];
		while($wib=mysql_fetch_assoc($ra))
		{
		$no+=1;
		echo"
		<tr class=rowcontent>
			<td>".$no."</td>
			<td>".$wib['notransaksi']."</td>
			<td>".$wib['tanggal']."</td>
			<td>".$wib['kodept']."</td>
			<td>".$wib['kodegudang']."</td>
			<td>".$wib['nopo']."</td>		
			<td align=center><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$wib['notransaksireferensi']."','".$wib['notransaksi']."','".tanggalnormal($wib['tanggal'])."','".$wib['kodept']."','".$wib['nopo']."','".$wib['kodegudang']."','".$wib['idsupplier']."');\">
		
		<img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"Del('".$wib['notransaksi']."');\" >
		
		</td></tr>";
		}
		
		echo"
			<tr class=rowheader>
				<td colspan=12 align=center>
					".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
					<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
					<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
			</tr>";
		echo"</tbody></table>";
		break;
	
	
	
	
	
	
	
	
	
	
	
	
	
	

default;
}




?>