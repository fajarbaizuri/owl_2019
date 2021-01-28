<?php
session_start();
require_once('master_validation.php');
require_once('config/connection.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');


$proses=$_POST['proses'];
$afdelingId=$_POST['afdelingId'];

$tgl=tanggalsystem($_POST['tgl']);
$idAsisten=$_POST['idAsisten'];
$idPetugas=$_POST['idPetugas']; 
$thnNo=explode("-",$_POST['tgl']);
$notransaksi=$afdelingId."/".$thnNo[2].$thnNo[1].$thnNo[0]."/KLTBM";
$optNmOrg=makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi');
$optNmKary=makeOption($dbname, 'datakaryawan', 'karyawanid,namakaryawan');

$noTrans=$_POST['noTrans'];
$periode=$_POST['periode'];
$kdBlok=$_POST['kdBlok'];
$piringan=$_POST['piringan'];
$gwngan=$_POST['gwngan'];
$pmupukn=$_POST['pmupukn'];
$hpt=$_POST['hpt'];



	switch($proses)
	{		
			case'deleteTenaga':
                $sDel="delete from ".$dbname.".vhc_kendaraan_tenaga where notransaksi='".$_POST['notransaksi']."' and kodekegiatan='".$_POST['kodekegiatan']."' and rit='".$_POST['rit']."'  and idkaryawan='".$_POST['karyawan']."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
			case'loadDetailTenaga':
					$tab="";
					$queryB = "select a.notransaksi,a.kodekegiatan,a.rit,a.idkaryawan,b.namakaryawan, a.prestasi,a.satuan,a.upah,a.umkn,a.premi,a.totpremi from `".$dbname."`.`vhc_kendaraan_tenaga` a left join `".$dbname."`.`datakaryawan` b on a.idkaryawan=b.karyawanid where notransaksi='".$_POST['notransaksi']."' and kodekegiatan='".$_POST['kegiatan']."' and rit='".$_POST['rit']."'";
					$qTpS=mysql_query($queryB) or die(mysql_error());
					while($res=mysql_fetch_assoc($qTpS))
				{
                    $tab.="<tr id=\"tr_ftTenaga_0\" class=\"rowcontent\">
										<td>
											<img id=\"delFTBtn\" class=\"zImgBtn\" src=\"images/skyblue/delete.png\" onclick=\"deleteTenaga('".$res['notransaksi']."','".$res['kodekegiatan']."','".$res['rit']."','".$res['idkaryawan']."')\">
										</td>
										<td id=\"ftTenaga_karyawan\" align=\"left\" >".$res['namakaryawan']."</td>
										<td id=\"ftTenaga_prestasi\" align=\"center\" >".$res['prestasi']."</td>
										<td id=\"ftTenaga_satuan\" align=\"center\" >".$res['satuan']."</td>
										<td id=\"ftTenaga_upah\" align=\"right\" >".number_format($res['upah'],2)."</td>
										<td id=\"ftTenaga_uangmkn\" align=\"right\" >".number_format($res['umkn'],2)."</td>
										<td id=\"ftTenaga_premi\" align=\"right\" >".number_format($res['premi'],2)."</td>
									</tr>";
				}
				 
            echo $tab;
            break;
			case'TambahTenaga':
				if ($_POST['jenis']=='' || $_POST['tenaga']=='' || $_POST['prestasi']=='' || $_POST['prestasi']=='0'){
					exit("Error:Silahkan isi Field yang masih Kosong");
				}
				$tot=$_POST['uangmkn']+$_POST['premi'];
			 $sInsert="insert into ".$dbname.".vhc_kendaraan_tenaga (notransaksi, kodekegiatan, rit, idkaryawan, prestasi, satuan, upah, umkn, premi, totpremi) values ('".$_POST['notransaksi']."','".$_POST['kegiatan']."','".$_POST['rit']."','".$_POST['tenaga']."','".$_POST['prestasi']."','".$_POST['satuan']."','".$_POST['upah']."','".$_POST['uangmkn']."','".$_POST['premi']."','".$tot."')";
                //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
               

            break;
			case 'getKaryawan':
			$optKdvhc="<option value='' >Pilih Data</option>";
			if ($_POST['pilih']=='1'){
				$queryKaryA = "select karyawanid,nama,vhc from `".$dbname."`.`vhc_5operator` a".
					" where aktif=1 and  vhc='".$_POST['plat']."' ";
				$query=mysql_query($queryKaryA) or die(mysql_error());
				
				while($res=mysql_fetch_assoc($query))
				{
					$optKdvhc.="<option value='".$res['karyawanid']."' >".$res['nama']." [".$res['vhc']."]</option>";
				}
			}else if ($_POST['pilih']=='2'){
					if ($_SESSION['empl']['lokasitugas'] != "TKFB"){
						$whereKary = "and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
					}else{
						$whereKary = "";
					}
		
					$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00')";
					$whereKary .= " and subbagian<>''";
					$sStaff2 = "select karyawanid,namakaryawan,subbagian,lokasitugas from ".$dbname.".datakaryawan  where kodejabatan in ('224','181','72','215','216','217','218','219','221','231')  ".$whereKary;
					$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
					$qStaff2=mysql_query($sStaff2) or die(mysql_error());
					while($rStaff2=mysql_fetch_assoc($qStaff2))
					{
							$optKdvhc.="<option value=".$rStaff2['karyawanid']." >".$rStaff2['namakaryawan']." - ".$rStaff2['subbagian']." (".$rStaff2['lokasitugas'].")</option>";
					}
			}else{
				
			}
				
				echo $optKdvhc;
			break;
			
			case 'showTenaga':
			$queryB = "select  a.kodekegiatan,a.rit,b.namakegiatan,b.satuan from `".$dbname."`.`vhc_kendaraan_kegiatan` a left join `".$dbname."`.`vhc_kegiatan` b ON a.kodekegiatan=b.kodekegiatan where a.notransaksi='".$_POST['notransaksi']."' and a.kodekegiatan='".$_POST['kegiatan']."' and a.rit='".$_POST['rit']."' ";
			$qTpS=mysql_query($queryB) or die(mysql_error());
			$res1=mysql_fetch_assoc($qTpS);
			$optKeg="<option value='".$res1['kodekegiatan']."' >".$res1['namakegiatan']." (".$res1['satuan'].")</option>";
			echo "<div id=\"ftTenaga\" style=\"width:100%; overflow:auto;\">
					<div id=\"form_ftTenaga\" style=\"overflow:auto;\">
						<fieldset>
							<legend id=\"form_ftTenaga_title\">
								<b>Form Tenaga Kerja Kendaraan/Alat Berat : <span id=\"form_ftTenaga_mode\">Mode Tambah</span></b>
							</legend>
							<table>
								<tbody>
									<tr>
										<td>Kegiatan</td>
										<td>:</td>
										<td id=\"ftTenaga_kegiatan\">
											<select id=\"XKegiatan\" name=\"XKegiatan\" style=\"width:350px\" disabled=\"disabled\">".$optKeg."</select>
										</td>
									</tr>
									<tr>
										<td> Rit Ke-</td>
										<td>:</td>
										<td id=\"ftTenaga_rit\">
											<input id=\"XRit\" name=\"XRit\" class=\"myinputtextnumber\"  type=\"text\" value=\"".$res1['rit']."\" style=\"width:45px; text-align:center\" disabled=\"disabled\">&nbsp;
										</td>
									</tr>
									<tr>
										<td>Jenis Tenaga</td>
										<td>:</td>
										<td id=\"ftTenaga_jenis\">
											<select id=\"XJenis\" name=\"XJenis\" style=\"width:150px\" onchange=\"GetKaryawan(this,'".$_POST['plat']."')\">
												<option value=\"\" selected>Pilih Data</option>
												<option value=\"1\">Sopir/Operator</option>
												<option value=\"2\">Kernet Muat/Helper</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Tenaga Kerja</td>
										<td>:</td>
										<td id=\"ftTenaga_tenagakerja\">
											<select id=\"XTenagaKerja\" name=\"XTenagaKerja\" style=\"width:150px\">
											<option value=\"\" selected>Pilih Data</option></select>
										</td>
									</tr>
									<tr>
										<td>Prestasi</td>
										<td>:</td>
										<td id=\"ftTenaga_prestasi\">
											<input id=\"XPrestasi\" name=\"XPrestasi\" class=\"myinputtextnumber\"  type=\"text\" value=\"0\" style=\"width:65px; text-align:right\" disabled=\"disabled\">&nbsp;
											<input id=\"XSatuan\" name=\"XSatuan\" class=\"myinputtextnumber\"  type=\"text\" value=\"".$res1['satuan']."\" style=\"width:45px; text-align:center\" disabled=\"disabled\">
										</td>
									</tr>
									<tr>
										<td>Upah</td>
										<td>:</td>
										<td id=\"ftTenaga_upah\">
											<input id=\"XUpah\" name=\"XUpah\" class=\"myinputtextnumber\"  type=\"text\" value=\"0\" style=\"width:65px; text-align:right\">&nbsp;
										</td>
									</tr>
									<tr>
										<td>Uang Makan</td>
										<td>:</td>
										<td id=\"ftTenaga_uangmakan\">
											<input id=\"XUangmakan\" name=\"XUangmakan\" class=\"myinputtextnumber\"  type=\"text\" value=\"0\" style=\"width:65px; text-align:right\" disabled=\"disabled\">&nbsp;
										</td>
									</tr>
									<tr>
										<td>Premi</td>
										<td>:</td>
										<td id=\"ftTenaga_premi\">
											<input id=\"XPremi\" name=\"XPremi\" class=\"myinputtextnumber\"  type=\"text\" value=\"0\" style=\"width:65px; text-align:right\" disabled=\"disabled\">&nbsp;
										</td>
									</tr>
									<tr>
										<td colspan=\"3\">
											<input id=\"ftMaterial_numRow\" name=\"ftMaterial_numRow\" type=\"hidden\" value=\"0\">
											<button id=\"addFTBtn_ftMaterial\" name=\"addFTBtn_ftMaterial\" class=\"mybutton\" onclick=\"addTenaga('".$_POST['notransaksi']."','".$_POST['kegiatan']."','".$_POST['rit']."');\">Simpan</button>
										</td>
									</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
					<div id=\"table_ftTenaga\" style=\"overflow:auto;\">
						<fieldset>
							<legend id=\"table_ftTenaga_title\"><b>Tabel Pakai Tenaga</b></legend>
							<div style=\"max-height:200px;overflow:auto\">
								<table class=\"sortable\" cellspacing=\"1\" border=\"0\" id=\"TenagaTable\">
									<thead id=\"thead_ftTenaga\">
										<tr class=\"rowheader\">
											<td >Aksi</td>
											<td id=\"head_karyawan\" align=\"center\" style=\"width:200px\">Karyawan</td>
											<td id=\"head_prestasi\" align=\"center\" style=\"width:100px\">Prestasi</td>
											<td id=\"head_satuan\" align=\"center\" style=\"width:100px\">Satuan</td>
											<td id=\"head_upah\" align=\"center\" style=\"width:100px\">Upah</td>
											<td id=\"head_uangmkn\" align=\"center\" style=\"width:100px\">Uang Makan</td>
											<td id=\"head_premi\" align=\"center\" style=\"width:100px\">Premi</td>
										</tr>
									</thead>
								<tbody id=\"tbody_ftTenaga\">
								</tbody>
								<tfoot></tfoot>
								</table>
							</div>
						</fieldset>
					</div>
				</div>";
			/*
		$param = $_POST;
		
		$where = "notransaksi='".$param['notransaksi']."' and kodekegiatan='".
			$param['kodekegiatan']."' and kodeorg='".$param['kodeorg']."'";
		$cols = "kodebarang,kwantitas,kwantitasha";
		$query = selectQuery($dbname,'kebun_pakaimaterial',$cols,$where);
		$data = fetchData($query);
		
		if(!empty($data)) {
			$whereBarang = "";
			$i=0;
			foreach($data as $row) {
			if($i==0) {
				$whereBarang .= "kodebarang='".$row['kodebarang']."'";
			} else {
				$whereBarang .= " or kodebarang='".$row['kodebarang']."'";
			}
			$i++;
			}
			$optBarang = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',$whereBarang);
		} else {
			$optBarang = array();
		}
		
		$dataShow = $data;
		foreach($dataShow as $key=>$row) {
			//$dataShow[$key]['kodeorg'] = $optOrg[$row['kodeorg']];
			$dataShow[$key]['kodebarang'] = $optBarang[$row['kodebarang']];
			$dataShow[$key]['kwantitas'] = number_format($row['kwantitas'],2);
			$dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
		}
		
		# Form
		$theForm3 = new uForm('materialForm','Form Pakai Material',1);
		$theForm3->addEls('kodebarang',$_SESSION['lang']['kodebarang'],'','searchBarang','L',20,null,null,'kwantitas_satuan');
		$theForm3->addEls('kwantitas',$_SESSION['lang']['kwantitas'],'0','textnumwsatuan','R',10);
		$theForm3->addEls('kwantitasha',$_SESSION['lang']['kwantitasha'],$param['hasilkerja'],'textnum','R',10);
		$theForm3->_elements[2]->_attr['disabled'] = 'disabled';
		
		# Table
		$theTable3 = new uTable('materialTable','Tabel Pakai Material',$cols,$data,$dataShow);
		
		# FormTable
		$formTab3 = new uFormTable('ftMaterial',$theForm3,$theTable3,null,
			array('notransaksi','tanggal','ftPrestasi_kodekegiatan_'.$param['numRow'],
				'ftPrestasi_kodeorg_'.$param['numRow']));
		$formTab3->_target = "kebun_slave_operasional_material";
		$formTab3->_noClearField = '##kodebarang##kwantitasha';
		$formTab3->_noEnable = '##kodebarang##kwantitasha';
		$formTab3->_defValue = '##kwantitasha='.$param['hasilkerja'];
		
		$formTab3->render();
		*/
		break;
			case 'getUpdateHead':
				
				$queryKaryA = "select karyawanid,nama,vhc from `".$dbname."`.`vhc_5operator`  where aktif=1 ";
				$query1=mysql_query($queryKaryA) or die(mysql_error());
				while($res=mysql_fetch_assoc($query1))
				{
					$optB[$res['karyawanid']]="<option value='".$res['karyawanid']."' >".$res['nama']." [".$res['vhc']."]</option>";
				}
				
				
				$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` ";
				$query=mysql_query($queryKaryA) or die(mysql_error());
				while($res=mysql_fetch_assoc($query))
				{
					$optKdvhc[$res['kodevhc']]="<option value='".$res['kodevhc']."' >".$res['nopol']." [".$res['kodevhc']."]</option>";
				}
					
					$queryB = "select * from `".$dbname."`.`vhc_kendaraanht` where notransaksi='".$_POST['notransaksi']."'  ";
					$qTpS=mysql_query($queryB) or die(mysql_error());
					$res=mysql_fetch_assoc($qTpS);
					$has=$res['notransaksi']."##";
					$has.=tanggalnormal($res['tanggal'])."##";
					if(substr($res['kodevhc'],0,2)=='KD'){
						$has.="<option value=\"\" >Pilih Data</option><option value=\"AB\" >Alat Berat</option><option value=\"KD\" selected>Kendaraan</option>##";
					}else{
							$has.="<option value=\"\" >Pilih Data</option><option value=\"AB\" selected>Alat Berat</option><option value=\"KD\" >Kendaraan</option>##";
					}			
					$has.=$optKdvhc[$res['kodevhc']]."##";
					if($res['kondisi']=='S'){
						$has.="<option value=\"B\">Beroperasi</option><option value=\"S\" selected>Standby</option>##";
					}else{
						$has.="<option value=\"B\" selected>Beroperasi</option><option value=\"S\" >Standby</option>##";
					}			
					
					if ($_SESSION['empl']['lokasitugas'] != "TKFB"){
						$whereKary = "and lokasitugas='".$_SESSION['empl']['lokasitugas']."'";
					}else{
						$whereKary = "";
					}
		
					$whereKary .= " and (tanggalkeluar>".date('Y-m-d')." or tanggalkeluar='0000-00-00')";
					$whereKary .= " and subbagian<>''";
					$sStaff2 = "select karyawanid,namakaryawan,subbagian,lokasitugas from ".$dbname.".datakaryawan  where kodejabatan in ('157','174')  ".$whereKary;
					$optMandor="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
					$qStaff2=mysql_query($sStaff2) or die(mysql_error());
					while($rStaff2=mysql_fetch_assoc($qStaff2))
					{
						if ($res['mandor']==$rStaff2['karyawanid']){
							$optMandor.="<option value=".$rStaff2['karyawanid']." selected>".$rStaff2['namakaryawan']." - ".$rStaff2['subbagian']." (".$rStaff2['lokasitugas'].")</option>";
						}else{
							$optMandor.="<option value=".$rStaff2['karyawanid'].">".$rStaff2['namakaryawan']." - ".$rStaff2['subbagian']." (".$rStaff2['lokasitugas'].")</option>";
						}
						
					}

			
					$has.=$optMandor."##";
					$has.=$optB[$res['sopir']]."##";
					$has.=$res['bbm']."##";
					$has.=$res['noservice']."##";
					$has.=$res['catatan']."##";
					
					if ($_SESSION['empl']['lokasitugas']=='TKFB'){
						$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') ";
					}else{
						$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
					}
					$optOrg="<option value=''>Pilih Data</option>";
					$query="select kodeorganisasi,namaorganisasi from  ".$dbname.".organisasi where ".$whereOrg.";" ;
					$qTph=mysql_query($query) or die(mysql_error());
					while($res1=mysql_fetch_assoc($qTph))
					{
						$optOrg.="<option value='".$res1['kodeorganisasi']."' >".$res1['namaorganisasi']."</option>";
					}
					
					$optKeg="<option value=''>Pilih Data</option>";
					$queryB = "select kodekegiatan,namakegiatan,satuan from `".$dbname."`.`vhc_kegiatan` where kelompok='".substr($res['kodevhc'],0,2)."' ";
					$qTpS=mysql_query($queryB) or die(mysql_error());
					while($res1=mysql_fetch_assoc($qTpS))
					{
						$optKeg.="<option value='".$res1['kodekegiatan']."' >".$res1['namakegiatan']." (".$res1['satuan'].")</option>";
					}
					$optWaktu="<option value=''>Pilih Data</option>";
					$optWaktu.="<option value='JAM'>Jam</option>";
					if(substr($res['kodevhc'],0,2)=="KD"){
						$optWaktu.="<option value='KM/H'>Waktu Tempuh(KM/H)</option>";
					}else{
						$optWaktu.="<option value='HM'>Jam Operasi Alat(HM)</option>";
					}
					
					$has.=$optWaktu."##";
					$has.=$optOrg."##";
					$has.=$optKeg;
					echo $has;
			break;
			case 'CheckCountRit':
				$queryKaryA = "select notransaksi from `".$dbname."`.`vhc_kendaraan_kegiatan` where notransaksi='".$_POST['b']."' and kodekegiatan='".$_POST['a']."' ";
				$queryA = mysql_query($queryKaryA);
				echo mysql_num_rows($queryA)+1;
			break;
			case 'getPlat':
				if ($_SESSION['empl']['lokasitugas']=='TKFB'){
					$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` a".
				"  where kelompokvhc='".$_POST['alat']."'";
				}else{
					$queryKaryA = "select nopol,kodevhc from `".$dbname."`.`vhc_5master` a".
				"  where kelompokvhc='".$_POST['alat']."' and `kodeorg`='".$_SESSION['empl']['lokasitugas']."' ";
				}
				
				$query=mysql_query($queryKaryA) or die(mysql_error());
				$optKdvhc="<option value=''>Pilih Data</option>";
				while($res=mysql_fetch_assoc($query))
				{
					$optKdvhc.="<option value='".$res['kodevhc']."' >".$res['nopol']." [".$res['kodevhc']."]</option>";
				}
				echo $optKdvhc;
			break;
			case 'getDriv':
					$queryKaryA = "select karyawanid,nama,vhc from `".$dbname."`.`vhc_5operator` a".
					" where aktif=1 and  vhc='".$_POST['plat']."' ";
				$query=mysql_query($queryKaryA) or die(mysql_error());
				$optKdvhc="<option value='' >Pilih Data</option>";
				while($res=mysql_fetch_assoc($query))
				{
					$optKdvhc.="<option value='".$res['karyawanid']."' >".$res['nama']." [".$res['vhc']."]</option>";
				}
				echo $optKdvhc;
			break;
            case'getBlok':
                $optKode="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
               
                $sTph="select distinct kodeorganisasi,namaorganisasi from ".$dbname.".organisasi where induk='".$afdelingId."'";
                //exit("Error:".$sTph);
                $qTph=mysql_query($sTph) or die(mysql_error());
                while($rTph=mysql_fetch_assoc($qTph))
                {
                    if($noTph!='')
                    {
                        $optKode.="<option value='".$rTph['kodeorganisasi']."' ".($rTph['kodeorganisasi']==$kdBlok?"selected":'').">".$rTph['namaorganisasi']."</option>";
                    }
                    else
                    {
                        $optKode.="<option value='".$rTph['kodeorganisasi']."'>".$rTph['namaorganisasi']."</option>";
                    }
                }
               
                echo $optKode;
            
            break;
            case'insert':
					if ($_SESSION['empl']['lokasitugas']=='TKFB'){
						$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') ";
					}else{
						$whereOrg = "tipe in ('BLOK','AFDELING','KEBUN','PABRIK') and kodeorganisasi like '".$_SESSION['empl']['lokasitugas']."%'";
					}
					$optOrg="<option value=''>Pilih Data</option>";
					$query="select kodeorganisasi,namaorganisasi from  ".$dbname.".organisasi where ".$whereOrg.";" ;
					$qTph=mysql_query($query) or die(mysql_error());
					while($res=mysql_fetch_assoc($qTph))
					{
						$optOrg.="<option value='".$res['kodeorganisasi']."' >".$res['namaorganisasi']."</option>";
					}
					
					$optKeg="<option value=''>Pilih Data</option>";
					$queryB = "select kodekegiatan,namakegiatan,satuan from `".$dbname."`.`vhc_kegiatan` where kelompok='".$_POST['kelompok']."' ";
					$qTpS=mysql_query($queryB) or die(mysql_error());
					while($res=mysql_fetch_assoc($qTpS))
					{
						$optKeg.="<option value='".$res['kodekegiatan']."' >".$res['namakegiatan']." (".$res['satuan'].")</option>";
					}
					
			if($_POST['tanggal']==''||$_POST['kelompok']==''||$_POST['plat']==''||$_POST['nikmandor']==''||$_POST['sopir']==''){
				exit("Error:Silahkan isi Field yang masih Kosong");
			}
			$optKO = makeOption($dbname,'vhc_5master','kodevhc,kodeorg');
			$kodeA=$optKO[$_POST['plat']];
			
			$fWhere = "DATE_FORMAT(tanggal,'%Y%m')='".substr($_POST['tanggal'], 0, 6)."' and kodeorg='".$kodeA."' ";
			$sCek="select distinct notransaksi from ".$dbname.".vhc_kendaraanht where ".$fWhere."";
			$qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek==0)
            {
				$tempNo = tanggalsystem($_POST['tanggal'])."/".$optKO[$_POST['plat']]."/RUN/001";
			}else{
				$tempNo = tanggalsystem($_POST['tanggal'])."/".$optKO[$_POST['plat']]."/RUN/".addZero($rcek+1,3);
			}
				
			 $sInsert="insert into ".$dbname.".vhc_kendaraanht (notransaksi, kodeorg, tanggal, kodevhc, kondisi, noservice, catatan, mandor, sopir, posting,bbm) values ('".$tempNo."','".$kodeA."','".tanggalsystem($_POST['tanggal'])."','".$_POST['plat']."','".$_POST['kondisi']."','".$_POST['noservice']."','".$_POST['keterangan']."','".$_POST['nikmandor']."','".$_POST['sopir']."','0','".$_POST['bbm']."')";
                //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
                else
                {
					$optWaktu="<option value=''>Pilih Data</option>";
					$optWaktu.="<option value='JAM'>Jam</option>";
					if($_POST['kelompok']=="KD"){
						$optWaktu.="<option value='KM/H'>Waktu Tempuh(KM/H)</option>";
					}else{
						$optWaktu.="<option value='HM'>Jam Operasi Alat(HM)</option>";
					}
					
					
					
                    echo $tempNo."##".$optWaktu."##".$optOrg."##".$optKeg;
                }

            break;
            case'update':
            if($afdelingId==''||$idPetugas==''||$tgl==''||$idAsisten=='')
            {
                exit("Error:Field Tidak Boleh Kosong");
            }
             $sInsert="update ".$dbname.".kebun_qc_kondisitbmht set asisten='".$idAsisten."', petugas='".$idPetugas."', updateby='".$_SESSION['standard']['userid']."' where notransaksi='".$notransaksi."'";
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
				if ($_SESSION['empl']['lokasitugas']=='TKFB'){
					$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_kendaraan_vw  order by `tanggal` desc";
				}else{
					$ql2="select count(*) as jmlhrow from ".$dbname.".vhc_kendaraan_vw  where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc";
				}
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
				$jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
				if ($_SESSION['empl']['lokasitugas']=='TKFB'){
					$sData="select * from ".$dbname.".vhc_kendaraan_vw  order by `tanggal` desc limit ".$offset.",".$limit."";
				}else{
					$sData="select * from ".$dbname.".vhc_kendaraan_vw  where kodeorg='".$_SESSION['empl']['lokasitugas']."' order by `tanggal` desc limit ".$offset.",".$limit."";
				}	
				$tab="";
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData= mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
					
						$tab.="<td align=center>".$rData['notransaksi']."</td>";
						$tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
						$tab.="<td align=center>".$rData['kelompok']."</td>";
						$tab.="<td align=center>".$rData['nopol']."</td>";
						$tab.="<td align=center>".$rData['kondisi']."</td>";
						$tab.="<td align=center>".$rData['nmmandor']."</td>";
						$tab.="<td align=center>".$rData['nmsopir']."</td>";
						
						
						if ($rData['posting']==0){
								$tab.="<td align=center><img src=images/skyblue/edit.png class=zImgBtn  title='Edit' onclick=\"fillField('".$rData['notransaksi']."');\"></td>";
								$tab.="<td align=center><img src=images/skyblue/delete.png class=zImgBtn  title='Delete' onclick=\"delDataHead('".$rData['notransaksi']."');\" ></td>";
								$tab.="<td align=center><img src=images/skyblue/posting.png class=zImgBtn  title='Posting' onclick=\"PostDataHead('".$rData['notransaksi']."');\" ></td>";
						}else{
								$tab.="<td align=center><img src=images/skyblue/edit.png class=zImgBtn  title='Edit' ></td>";
								$tab.="<td align=center><img src=images/skyblue/delete.png class=zImgBtn  title='Delete' ></td>";
								$tab.="<td align=center><img src=images/skyblue/posted.png class=zImgBtn  title='Sudah Terposting'  ></td>";
						}
						
						$tab.="<td align=center><img src=images/skyblue/pdf.jpg class=zImgBtn  title='Posting' onclick=\"detailPDF('".$rData['notransaksi']."',event)\" ></td>";
					
						
                    $tab.="</tr>";
                }
            }
            else
            {
					$tab.="<tr class=rowcontent ><td colspan=9 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
			
            $tab.="</tr>
			<tr class=rowheader>
		
				<td colspan=9 align=center>".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
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
            if($afdelingId!='')
            {
				if($periode!='')
				{
					$where.="where kodeorg='".$afdelingId."' and substr(tanggal,1,7)='".$periode."'";
				}else{
					$where.="where kodeorg='".$afdelingId."' ";
				}
                
            }else{
				if($periode!='')
				{
					if ($_SESSION['empl']['lokasitugas']=='TKFB'){
						$where.="where substr(tanggal,1,7)='".$periode."'";
						
						
					}else{
						$where.="where kodeorg='".$_SESSION['empl']['lokasitugas']."' and substr(tanggal,1,7)='".$periode."'";
						
					}	
					
				}else{
					$where.="";
				}
			}
			
            
            $ql2="select count(*) as jmlhrow from ".$dbname.".vhc_kendaraan_vw  ".$where." order by `tanggal` desc";// echo $ql2;
            $query2=mysql_query($ql2) or die(mysql_error());
            while($jsl=mysql_fetch_object($query2)){
            $jlhbrs= $jsl->jmlhrow;
            }
            if($jlhbrs!=0)
            {
                $sData="select distinct * from ".$dbname.".vhc_kendaraan_vw
                      ".$where." order by `tanggal` desc limit ".$offset.",".$limit."";
                //echo $sData;
                $qData=mysql_query($sData) or die(mysql_error());
                while($rData=  mysql_fetch_assoc($qData))
                {
                    $no+=1;
                    $tab.="<tr class=rowcontent>";
						$tab.="<td align=center>".$rData['notransaksi']."</td>";
						$tab.="<td align=center>".tanggalnormal($rData['tanggal'])."</td>";
						$tab.="<td align=center>".$rData['kelompok']."</td>";
						$tab.="<td align=center>".$rData['nopol']."</td>";
						$tab.="<td align=center>".$rData['kondisi']."</td>";
						$tab.="<td align=center>".$rData['nmmandor']."</td>";
						$tab.="<td align=center>".$rData['nmsopir']."</td>";
						
						if ($rData['posting']==0){
								$tab.="<td align=center><img src=images/skyblue/edit.png class=zImgBtn  title='Edit' onclick=\"fillField('".$rData['notransaksi']."');\"></td>";
								$tab.="<td align=center><img src=images/skyblue/delete.png class=zImgBtn  title='Delete' onclick=\"delDataHead('".$rData['notransaksi']."');\" ></td>";
								$tab.="<td align=center><img src=images/skyblue/posting.png class=zImgBtn  title='Posting' onclick=\"PostDataHead('".$rData['notransaksi']."');\" ></td>";
						}else{
								$tab.="<td align=center><img src=images/skyblue/edit.png class=zImgBtn  title='Edit' ></td>";
								$tab.="<td align=center><img src=images/skyblue/delete.png class=zImgBtn  title='Delete' ></td>";
								$tab.="<td align=center><img src=images/skyblue/posted.png class=zImgBtn  title='Sudah Terposting'  ></td>";
						}
						$tab.="<td align=center><img src=images/skyblue/pdf.jpg class=zImgBtn  title='Posting' onclick=\"detailPDF('".$rData['notransaksi']."',event)\" ></td>";
                    $tab.="</tr>";
                }
            }
            else
            {
                $tab.="<tr class=rowcontent ><td colspan=9 align=center>".$_SESSION['lang']['dataempty']."</td></tr>";
            }
            $tab.="</tr>
		<tr class=rowheader><td colspan=9 align=center>
		".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
		<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
		<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
		</td>
		</tr>";
            echo $tab;
            break;

            case'insert_detail':
			if( $_POST['satuk']=='' || $_POST['lokasi']==''|| $_POST['kodekegiatan']==''|| $_POST['total']<=0|| $_POST['volume']==0)
			{
				exit("Error:Silahkan isi Field yang masih Kosong");
			}
				$has=0;
             if ($_POST['satuk']=="JAM"){
				$whereOrgB = "notransaksi ='".$_POST['notransaksi']."' ";
				$optVHC = makeOption($dbname,'vhc_kendaraanht','notransaksi,kodevhc',$whereOrgB);	
				
				$has=CheckNilaiCon($optVHC[$_POST['notransaksi']],substr($_POST['kodekegiatan'],0,4),$_POST['kodekegiatan'],$dbname);
				if ($has==NULL){
					echo "Error:Silahkan Masukan Konversi Alat Terlebih Dahulu..";
					exit;
				}
			}

  
            $sCek="select distinct notransaksi,kodekegiatan,rit from ".$dbname.".vhc_kendaraan_kegiatan 
                   where notransaksi='".$_POST['notransaksi']."' and kodekegiatan='".$_POST['kodekegiatan']."' and rit='".$_POST['rit']."'";
            //exit("Error:".$sCek);
            $qCek=mysql_query($sCek) or die(mysql_error());
            $rcek=mysql_num_rows($qCek);
            if($rcek<1)
            {
				if ($_POST['satuk']=="JAM"){
					$konversi = '1';
				}else{
					$konversi = '0';
				}	
				$nilaikon = $has;
				if (substr($_POST['kodekegiatan'],0,2)=="DK"){
					$satkon = 'KM/H';
				}else{
					$satkon = 'HM';
				}
		
                $sInsert="insert into ".$dbname.".vhc_kendaraan_kegiatan (notransaksi, kodekegiatan, rit, lokasi, volume, satuan, konversi, nilaikon,satkon, awal, akhir, total, satuk) 
                          values ('".$_POST['notransaksi']."','".$_POST['kodekegiatan']."','".$_POST['rit']."','".$_POST['lokasi']."','".$_POST['volume']."','".$_POST['satuanvalome']."','".$konversi."','".$nilaikon."','".$satkon."','".$_POST['awal']."','".$_POST['akhir']."','".$_POST['total']."','".$_POST['satuanwaktu']."')";
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
             if($kdBlok==''||$piringan==''||$gwngan==''||$pmupukn==''||$hpt=='')
            {
                exit("Error:Field Tidak Boleh Kosong ");
            }
            $sInsert="update ".$dbname.".kebun_qc_kondisitbmdt set piringan='".$piringan."', gawangan='".$gwngan."', pemupukan='".$pmupukn."', hpt='".$hpt."'
                      where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
            //exit("Error".$sInsert);
                if(!mysql_query($sInsert))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
			case'delDataHead':
                $sDel="delete from ".$dbname.".vhc_kendaraanht where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
			case'PostDataHead':
                $sDel="update ".$dbname.".vhc_kendaraanht set posting=1 where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
			
            case'delDetail':
                $sDel="delete from ".$dbname.".vhc_kendaraan_kegiatan where notransaksi='".$_POST['notransaksi']."' and kodekegiatan='".$_POST['kodekegiatan']."' and rit='".$_POST['rit']."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;
            case'loadDetail':
					$queryB = "select kodekegiatan,namakegiatan,satuan from `".$dbname."`.`vhc_kegiatan`  ";
					$qTpS=mysql_query($queryB) or die(mysql_error());
					while($res=mysql_fetch_assoc($qTpS))
					{
						$optKeg[$res['kodekegiatan']]=$res['namakegiatan']." (".$res['satuan'].")";
					}		
			$optWaktu=array('JAM'=>'Jam','KM/H'=>'Waktu Tempuh(KM/H)','HM'=>'Jam Operasi Alat(HM)');
            $sDetail="select * from ".$dbname.".vhc_kendaraan_kegiatan where notransaksi='".$_POST['notransaksi']."'";
            //    exit("Error".$sDetail);
            $qDetail=mysql_query($sDetail) or die(mysql_error());
            while($rData=mysql_fetch_assoc($qDetail))
            {
                    $tab.="<tr class=rowcontent>";
                    $tab.="<td align=center>".$optWaktu[$rData['satkon']]."</td>";
                    $tab.="<td align=center>".$rData['satkon']."</td>";
                    $tab.="<td align=right>".$rData['awal']."</td>";
                    $tab.="<td align=right>".$rData['akhir']."</td>";
                    $tab.="<td align=right>".$rData['total']."</td>";
					
					$tab.="<td align=center>".$rData['lokasi']."</td>";
					$tab.="<td align=left>".$optKeg[$rData['kodekegiatan']]."</td>";
					$tab.="<td align=center>".$rData['rit']."</td>";
					$tab.="<td align=center>".$rData['volume']."</td>";
					$tab.="<td align=center>".$rData['satuan']."</td>";
                    $tab.="<td align=center>";
                    $tab.="<img src=images/skyblue/tenaga.png class=resicon  title='Kebutuhan Tenaga' 
                        onclick=\"showTenaga('".$rData['notransaksi']."','".$rData['kodekegiatan']."','".$rData['rit']."',event);\">
                    <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delDataDetail('".$rData['notransaksi']."','".$rData['kodekegiatan']."','".$rData['rit']."');\" >";
                    $tab.="</td>";
                    $tab.="</tr>";
            }
            echo $tab;
            break;
            case'getDetail':
            $sData="select distinct * from ".$dbname.".kebun_qc_kondisitbmdt where notransaksi='".$noTrans."' and kodeorg='".$kdBlok."'";
            $qData=mysql_query($sData) or die(mysql_error());
            $rData=mysql_fetch_assoc($qData);
            echo $rData['kodeorg']."###".$rData['piringan']."###".$rData['gawangan']."###".$rData['pemupukan']."###".$rData['hpt'];
            break;
            case'delDetailAll':
             $sDel="delete from ".$dbname.".kebun_qc_kondisitbmht where notransaksi='".$noTrans."'";
                 if(!mysql_query($sDel))
                {
                    echo "DB Error : ".mysql_error($conn);
                }
            break;

		default:
		break;
	}

function CheckNilaiCon($a,$b,$c,$dbname){
	$queryKaryA = "select nilai from `".$dbname."`.`vhc_konversi` where kodevhc='".$a."' and kodeorg='".$b."' and kodekegiatan='".$c."'";
	$query = fetchData($queryKaryA);
	return $query['nilai'];
}
?>