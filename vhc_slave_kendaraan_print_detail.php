<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$param = $_GET;
$notransaksi = $_GET['notransaksi'];

	
	
	$query="select notransaksi,tanggal,kelompok,nopol,kondisinm,mandor,nmmandor,posting,postingnm,catatan,bbm,stsalat from ".$dbname.".vhc_kendaraan_vw where notransaksi='".$param['notransaksi']."'";
	$Quer = mysql_query($query);
	$HasilQue=mysql_fetch_assoc($Quer);
	
	//kegiatan
	$query1="select kodekegiatan,namakegiatan,noakun,satuan from ".$dbname.".vhc_kegiatan_vw ";
	$Quer1 = mysql_query($query1);
	while($HasilQue1=mysql_fetch_assoc($Quer1)){
			$Keg[$HasilQue1['kodekegiatan']]['nama']=$HasilQue1['namakegiatan'];
			$Keg[$HasilQue1['kodekegiatan']]['satuan']=$HasilQue1['satuan'];
	}
	
	//kendaraan_kegiatan
	$query2="select notransaksi,kodekegiatan,rit,lokasi,volume,satuan,satuk,
	awal,akhir,total,convwaktu from ".$dbname.".vhc_kendaraan_kegiatan where notransaksi='".$param['notransaksi']."'";
	$Quer2 = mysql_query($query2);
	$i=0;
	$j=0;
	$k=0;
	$b="";
	$Has=Array();
	while($HasilQue2=mysql_fetch_assoc($Quer2)){
			$Has[$i]['notransaksi']=$HasilQue2['notransaksi'];
			$Has[$i]['kodekeg']=$HasilQue2['kodekegiatan'];
			$Has[$i]['nmkeg']="[".$HasilQue2['rit']."]".$Keg[$HasilQue2['kodekegiatan']]['nama'];
			$Has[$i]['lokasi']=$HasilQue2['lokasi'];
			$Has[$i]['volume']=$HasilQue2['volume'];
			$Has[$i]['satuan']=$HasilQue2['satuan'];
			$Has[$i]['awal']=$HasilQue2['awal'];
			$Has[$i]['akhir']=$HasilQue2['akhir'];
			$Has[$i]['total']=$HasilQue2['total'];
			$Has[$i]['satuk']=$HasilQue2['satuk'];
			$Has[$i]['convwaktu']=$HasilQue2['convwaktu'];
			$j=$j+$HasilQue2['total'];		
			$k=$k+$HasilQue2['convwaktu'];	
			$b=$HasilQue2['satuk'];
			$i++;
	}
	$HasA['satuan']=$b;
	$HasA['hasil']=$j;
	$HasA['convwaktu']=$k;
	
	//getJabatan
	$sDtKaryawnA="select kodejabatan,namajabatan from ".$dbname.".sdm_5jabatan order by namajabatan asc";
	$rDataA=fetchData($sDtKaryawnA);
	foreach($rDataA as $brKaryA =>$rNamakaryawanA)
	{
		$RJabat[$rNamakaryawanA['kodejabatan']]=$rNamakaryawanA['namajabatan'];
	}
	
	//getNamakaryawan
	$sDtKaryawn="select karyawanid,namakaryawan,kodejabatan,nik from ".$dbname.".datakaryawan order by namakaryawan asc";
	$rData=fetchData($sDtKaryawn);
	foreach($rData as $brKary =>$rNamakaryawan)
	{
		$RnamaKary[$rNamakaryawan['karyawanid']]['nama']=$rNamakaryawan['namakaryawan'];
		$RnamaKary[$rNamakaryawan['karyawanid']]['nik']=$rNamakaryawan['nik'];
		$RnamaKary[$rNamakaryawan['karyawanid']]['jabat']=$RJabat[$rNamakaryawan['kodejabatan']];
	}
	
	//kendaraan_tenaga
	$query3="select notransaksi,idkaryawan,upah,premi,umkn
	 from ".$dbname.".vhc_kendaraan_tenaga where notransaksi='".$param['notransaksi']."' ";
	 //echo $query3;
	$Quer3 = mysql_query($query3);
	$i=0;
	$j=0;
	$b=0;
	$c=0;
	$d=0;
	while($HasilQue3=mysql_fetch_assoc($Quer3)){
			$Has1[$i]['nik']=$RnamaKary[$HasilQue3['idkaryawan']]['nik'];
			$Has1[$i]['nmkar']=$RnamaKary[$HasilQue3['idkaryawan']]['nama'];
			$Has1[$i]['nmjab']=$RnamaKary[$HasilQue3['idkaryawan']]['jabat'];
			$Has1[$i]['upah']=$HasilQue3['upah'];
			$Has1[$i]['premi']=$HasilQue3['premi'];
			$Has1[$i]['umkn']=$HasilQue3['umkn'];
			$Has1[$i]['subtotal']=$HasilQue3['upah']+$HasilQue3['premi']+$HasilQue3['umkn'];
			$c=$c+$HasilQue3['umkn'];
			$j=$j+$HasilQue3['upah'];
			$b=$b+$HasilQue3['premi'];
			$d=$d+$HasilQue3['upah']+$HasilQue3['premi']+$HasilQue3['umkn'];
			
			$i++;
	}
	$HasA1['upah']=$j;
	$HasA1['premi']=$b;
	$HasA1['umkn']=$c;
	$HasA1['subtotal']=$d;
	
	//getNamabarang
	$sDtBarang="select kodebarang,namabarang from ".$dbname.".log_5masterbarang order by namabarang asc";
	$rData=fetchData($sDtBarang);
	foreach($rData as $brBarang =>$rNamabarang)
	{
		$RnamaBarang[$rNamabarang['kodebarang']]=$rNamabarang['namabarang'];
	}
	$align[] = explode(",","C,L,C,R,L,R");
$length[] = explode(",","10,30,20,15,5,20");
	//kendaraan_material
	/*
	$query4="select notransaksi,kodebarang,jumlah,satuan, keterangan 
	 from ".$dbname.".vhc_kendaraan_material where notransaksi='".$param['notransaksi']."' ";
	$Quer4 = mysql_query($query4);
	$i=0;
	while($HasilQue4=mysql_fetch_assoc($Quer4)){
			$Has2[$i]['kode']=$HasilQue4['kodebarang'];
			$Has2[$i]['nmbar']=$RnamaBarang[$HasilQue4['kodebarang']];
			$Has2[$i]['satuan']=$HasilQue4['satuan'];
			$Has2[$i]['volume']=$HasilQue4['jumlah'];
			$Has2[$i]['keterangan']=$HasilQue4['keterangan'];
			$i++;
	}
	*/
		
/*

#################################################################### Prestasi ##
//$col1 = 'nik,kodekegiatan,kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$col1 = 'tanggal,a.kodekegiatan,a.kodeorg,hasilkerja,c.satuan,jumlahhk';
$col1plain = 'tanggal,kodekegiatan,kodeorg,hasilkerja,satuan,jumlahhk';
$cols[] = explode(',',$col1plain);
//$query = selectQuery($dbname,'kebun_prestasi',$col1,
//    "notransaksi='".$param['notransaksi']."'");
$query="select ".$col1." from ".$dbname.".kebun_prestasi a left join ".$dbname.
	".kebun_aktifitas b on a.notransaksi=b.notransaksi left join ".$dbname.
	".setup_kegiatan c on a.kodekegiatan=c.kodekegiatan where a.notransaksi='".$param['notransaksi']."'";
$dataPres = fetchData($query);
$listKeg = '';
foreach($dataPres as $row) {
	if($listKeg!='') {
		$listKeg.=',';
	}
	$listKeg.="'".$row['kodekegiatan']."'";
}
$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan',
	"kodekegiatan in (".$listKeg.")");
foreach($dataPres as $key=>$row) {
	$dataPres[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
}
$data[] = $dataPres;
$align[] = explode(",","C,L,C,R,L,R");
$length[] = explode(",","10,30,20,15,5,20");
################################################################### /Prestasi ##

# Kehadiran
$col2 = 'nik,absensi,jhk,umr,insentif';
$cols[] = explode(',',$col2);
$query = selectQuery($dbname,'kebun_kehadiran',$col2,
    "notransaksi='".$param['notransaksi']."'");
$data[] = fetchData($query);
$align[] = explode(",","L,C,R,R,R");
$length[] = explode(",","20,20,20,20,20");

# Pakai Material
$col3 = 'kodekegiatan,kodeorg,kodebarang,kwantitas,kwantitasha,hargasatuan';
$cols[] = explode(',',$col3);
$query = selectQuery($dbname,'kebun_pakaimaterial',$col3,
    "notransaksi='".$param['notransaksi']."'");
$dataMat = fetchData($query);
foreach($dataMat as $key=>$row) {
	$dataMat[$key]['kodekegiatan'] = $optKeg[$row['kodekegiatan']];
}
$data[] = $dataMat;
$align[] = explode(",","L,C,L,R,R,R");
$length[] = explode(",","20,15,20,15,15,15");

//getNamabarang
$sDtBarang="select kodebarang,namabarang from ".$dbname.".log_5masterbarang order by namabarang asc";
$rData=fetchData($sDtBarang);
foreach($rData as $brBarang =>$rNamabarang)
{
    $RnamaBarang[$rNamabarang['kodebarang']]=$rNamabarang['namabarang'];
}
//getNamakaryawan
$sDtKaryawn="select karyawanid,namakaryawan from ".$dbname.".datakaryawan order by namakaryawan asc";
$rData=fetchData($sDtKaryawn);
foreach($rData as $brKary =>$rNamakaryawan)
{
    $RnamaKary[$rNamakaryawan['karyawanid']]=$rNamakaryawan['namakaryawan'];
}

*/
$titleDetail = array('Prestasi Pekerjaan','Kebutuhan Tenaga','Penggunaan Material');

/** Output Format **/
switch($proses) {
    case 'pdf':
        
        $pdf=new zPdfMaster('L','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1('Buku Kerja Alat Berat/Kendaraan',$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',10);
		$pdf->SetXY(28,130);
		$pdf->Cell($width,$height,"No. Transaksi",0,1,'L',1);
		$pdf->SetXY(108,130);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,130);
        $pdf->Cell($width,$height,$HasilQue['notransaksi'],0,1,'L',1);
		
		$pdf->SetXY(520,130);
		$pdf->Cell($width,$height,"Pemilik",0,1,'L',1);
		$pdf->SetXY(620,130);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,130);
        $pdf->Cell($width,$height,$HasilQue['stsalat'],0,1,'L',1);

		$pdf->SetXY(28,144);
		$pdf->Cell($width,$height,"Status",0,1,'L',1);
		$pdf->SetXY(108,144);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,144);
        $pdf->Cell($width,$height,$HasilQue['postingnm'],0,1,'L',1);
		
		$pdf->SetXY(520,144);
		$pdf->Cell($width,$height,"Kelompok",0,1,'L',1);
		$pdf->SetXY(620,144);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,144);
        $pdf->Cell($width,$height,$HasilQue['kelompok'],0,1,'L',1);
		
		$pdf->SetXY(28,158);
		$pdf->Cell($width,$height,"Tanggal",0,1,'L',1);
		$pdf->SetXY(108,158);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,158);
        $pdf->Cell($width,$height,tanggalnormal($HasilQue['tanggal']),0,1,'L',1);
		

		$pdf->SetXY(520,158);
		$pdf->Cell($width,$height,"Plat No",0,1,'L',1);
		$pdf->SetXY(620,158);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,158);
        $pdf->Cell($width,$height,$HasilQue['nopol'],0,1,'L',1);
			
		
		$pdf->SetXY(28,172);
		$pdf->Cell($width,$height,"Kondisi",0,1,'L',1);
		$pdf->SetXY(108,172);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,172);
        $pdf->Cell($width,$height,$HasilQue['kondisinm'],0,1,'L',1);
		
		
		$pdf->SetXY(520,172);
		$pdf->Cell($width,$height,"Penggunaan BBM",0,1,'L',1);
		$pdf->SetXY(620,172);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,172);
        $pdf->Cell($width,$height,$HasilQue['bbm']." Liter",0,1,'L',1);
		
		
		
		
		if ($HasilQue['kondisinm']!="Beroperasi"){
		$pdf->SetXY(28,186);
		$pdf->Cell($width,$height,"Keterangan",0,1,'L',1);
		$pdf->SetXY(108,186);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(118,186);
        $pdf->Cell($width,$height,$HasilQue['catatan'],0,1,'L',1);
		}
		
		
		$pdf->SetXY(520,186);
		$pdf->Cell($width,$height,"Mandor Transport",0,1,'L',1);
		$pdf->SetXY(620,186);
		$pdf->Cell($width,$height,":",0,1,'L',1);
		$pdf->SetXY(630,186);
        $pdf->Cell($width,$height,$HasilQue['nmmandor'],0,1,'L',1);
		
		
		$pdf->SetXY(28,206);
		$pdf->Ln(); 
		//----------------------------------------------------------
		$pdf->Cell($width,$height,$titleDetail[0],0,1,'L',1);
		$pdf->Cell(330,$height,"Kegiatan",1,1,'C',1);
		$pdf->SetXY(358,230);
		$pdf->Cell(70,24,"Lokasi Kerja",1,1,'C',1);
		$pdf->SetXY(428,230);
		$pdf->Cell(100,12,"Prestasi",1,1,'C',1);
		$pdf->SetXY(528,230);
		$pdf->Cell(285,12,"Waktu Operasi",1,1,'C',1);
		
		$pdf->SetXY(28,242);
		$pdf->Cell(65,$height,"Kode",1,1,'C',1);
		$pdf->SetXY(93,242);
		$pdf->Cell(265,$height,"Nama",1,1,'C',1);
		$pdf->SetXY(428,242);
		$pdf->Cell(55,$height,"Volume",1,1,'C',1);
		$pdf->SetXY(483,242);
		$pdf->Cell(45,$height,"Satuan",1,1,'C',1);
		$pdf->SetXY(528,242);
		$pdf->Cell(60,$height,"Awal",1,1,'C',1);
		$pdf->SetXY(588,242);
		$pdf->Cell(60,$height,"Akhir",1,1,'C',1);
		$pdf->SetXY(648,242);
		$pdf->Cell(60,$height,"Total",1,1,'C',1);
		$pdf->SetXY(708,242);
		$pdf->Cell(60,$height,"Konversi",1,1,'C',1);
		$pdf->SetXY(768,242);
		$pdf->Cell(45,$height,"Satuan",1,1,'C',1);
		$pdf->SetXY(28,254);
		$H=254;
		//---------------isi
		foreach($Has as $key=>$row) {
			$pdf->Cell(65,$height,$row['kodekeg'],1,1,'C',1);
			$pdf->SetXY(93,$H);
			$pdf->CellFitSpace(265,$height,$row['nmkeg'],1,1,'L',1);
			//$pdf->Cell(265,$height,$row['nmkeg'],1,1,'L',1);
			$pdf->SetXY(358,$H);
			$pdf->Cell(70,$height,$row['lokasi'],1,1,'C',1);
			$pdf->SetXY(428,$H);
			$pdf->Cell(55,$height,number_format($row['volume'],2),1,1,'C',1);
			$pdf->SetXY(483,$H);
			$pdf->Cell(45,$height,$row['satuan'],1,1,'C',1);
			$pdf->SetXY(528,$H);
			$pdf->Cell(60,$height,number_format($row['awal'],2),1,1,'C',1);
			$pdf->SetXY(588,$H);
			$pdf->Cell(60,$height,number_format($row['akhir'],2),1,1,'C',1);
			$pdf->SetXY(648,$H);
			$pdf->Cell(60,$height,number_format($row['total'],2),1,1,'C',1);
			$pdf->SetXY(708,$H);
			$pdf->Cell(60,$height,number_format($row['convwaktu'],2),1,1,'C',1);
			$pdf->SetXY(768,$H);
			$pdf->Cell(45,$height,$row['satuk'],1,1,'C',1);
			$H=$H+12;
		}
		
		//---------------total
		
		
		$pdf->SetXY(28,$H);
		$pdf->Cell(620,$height,"Sub Total",1,1,'R',1);
		$pdf->SetXY(648,$H);
		$pdf->Cell(60,$height,number_format($HasA['hasil'],2),1,1,'C',1);
		$pdf->SetXY(708,$H);
		$pdf->Cell(60,$height,number_format($HasA['convwaktu'],2),1,1,'C',1);
		$pdf->SetXY(768,$H);
		$pdf->Cell(45,$height,$HasA['satuan'],1,1,'C',1);
		$H=$H+20;
		$pdf->SetXY(28,$H);
		$pdf->Ln(); 
		//----------------------------------------------------------
		//tenaga
		//----------------------------------------------------------
		$H=$H+24;
		
		$pdf->Cell($width,$height,$titleDetail[1],0,1,'L',1);
		$pdf->Cell(410,$height,"Tenaga",1,1,'C',1);
		$pdf->SetXY(438,$H);
		$pdf->Cell(370,12,"Biaya",1,1,'C',1);
		$H=$H+12;
		$pdf->SetXY(28,$H);
		$pdf->Cell(90,$height,"Nik",1,1,'C',1);
		$pdf->SetXY(118,$H);
		$pdf->Cell(170,$height,"Nama",1,1,'C',1);
		$pdf->SetXY(288,$H);
		$pdf->Cell(150,$height,"Jabatan",1,1,'C',1);
		$pdf->SetXY(438,$H);
		
		$pdf->Cell(92,$height,"Upah (Rp/HK)",1,1,'C',1);
		$pdf->SetXY(530,$H);
		$pdf->Cell(92,$height,"Premi",1,1,'C',1);
		$pdf->SetXY(622,$H);
		$pdf->Cell(92,$height,"U. Makan (Rp/HK)",1,1,'C',1);
		$pdf->SetXY(714,$H);
		$pdf->Cell(94,$height,"Subtotal",1,1,'C',1);
		
		//---------------isi
		foreach($Has1 as $keyA=>$rowA) {
			$H=$H+12;
			$pdf->SetXY(28,$H);
			$pdf->Cell(90,$height,$rowA['nik'],1,1,'C',1);
			$pdf->SetXY(118,$H);
			$pdf->Cell(170,$height,$rowA['nmkar'],1,1,'L',1);
			$pdf->SetXY(288,$H);
			$pdf->Cell(150,$height,$rowA['nmjab'],1,1,'C',1);
			$pdf->SetXY(438,$H);
			$pdf->Cell(92,$height,number_format($rowA['upah'],0),1,1,'R',1);
			$pdf->SetXY(530,$H);
			$pdf->Cell(92,$height,number_format($rowA['premi'],0),1,1,'R',1);
			$pdf->SetXY(622,$H);
			$pdf->Cell(92,$height,number_format($rowA['umkn'],0),1,1,'R',1);
			$pdf->SetXY(714,$H);
			$pdf->Cell(94,$height,number_format($rowA['subtotal'],0),1,1,'R',1);
		
		}
		
		//---------------total
		$H=$H+12;
		$pdf->SetXY(28,$H);
		$pdf->Cell(410,$height,"Total (Rp.)",1,1,'R',1);
		$pdf->SetXY(438,$H);
		$pdf->Cell(92,$height,number_format($HasA1['upah'],0),1,1,'R',1);
		$pdf->SetXY(530,$H);
		$pdf->Cell(92,$height,number_format($HasA1['premi'],0),1,1,'R',1);
		$pdf->SetXY(622,$H);
		$pdf->Cell(92,$height,number_format($HasA1['umkn'],0),1,1,'R',1);
		$pdf->SetXY(714,$H);
		$pdf->Cell(94,$height,number_format($HasA1['subtotal'],0),1,1,'R',1);
		$H=$H+20;
		$pdf->SetXY(28,$H);
		$pdf->Ln(); 
		/*
		//----------------------------------------------------------
		//material
		//----------------------------------------------------------
		$H=$H+24;
		$pdf->Cell($width,$height,$titleDetail[2],0,1,'L',1);
		$pdf->Cell(390,$height,"Barang",1,1,'C',1);
		$pdf->SetXY(418,$H);
		$pdf->Cell(110,24,"Volume",1,1,'C',1);
		$pdf->SetXY(528,$H);
		$pdf->Cell(280,24,"Keterangan",1,1,'C',1);
		$H=$H+12;
		$pdf->SetXY(28,$H);
		$pdf->Cell(80,$height,"Kode",1,1,'C',1);
		$pdf->SetXY(108,$H);
		$pdf->Cell(190,$height,"Nama",1,1,'C',1);
		$pdf->SetXY(298,$H);
		$pdf->Cell(120,$height,"Satuan",1,1,'C',1);
		$pdf->SetXY(418,$H);
		//---------------isi
		foreach($Has2 as $keyB=>$rowB) {
			$H=$H+12;
			$pdf->SetXY(28,$H);
			$pdf->Cell(80,$height,$rowB['kode'],1,1,'C',1);
			$pdf->SetXY(108,$H);
			$pdf->Cell(190,$height,$rowB['nmbar'],1,1,'L',1);
			$pdf->SetXY(298,$H);
			$pdf->Cell(120,$height,$rowB['satuan'],1,1,'C',1);
			$pdf->SetXY(418,$H);
			$pdf->Cell(110,$height,number_format($rowB['volume'],0),1,1,'C',1);
			$pdf->SetXY(528,$H);
			$pdf->Cell(280,$height,$rowB['keterangan'],1,1,'L',1);
		}
		//---------------total
		 */
		
		//----------------------------------------------------------
		
		/*
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell($width,$height,$_SESSION['lang']['notransaksi']." : ".$param['notransaksi'],0,1,'L',1);
        foreach($data as $c=>$dataD) {
            # Header
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell($width,$height,$titleDetail[$c],0,1,'L',1);
            $pdf->SetFillColor(220,220,220);
            $i=0;
            foreach($cols[$c] as $column) {
                if(substr($column,1,1)==".")
                {
                    $column=substr($column,2,7);
                    //exit("error".$column);
                }
                if ($column=='nik'){
                $pdf->Cell($length[$c][$i]/100*$width,$height,$_SESSION['lang']['namakaryawan'],1,0,'C',1);
				}
				else {
				if ($column=='kodebarang'){
                $pdf->Cell($length[$c][$i]/100*$width,$height,$_SESSION['lang']['namabarang'],1,0,'C',1);
				}
				else {
                $pdf->Cell($length[$c][$i]/100*$width,$height,$_SESSION['lang'][$column],1,0,'C',1);
				}
				}
                $i++;
            }
            $pdf->Ln();
			
			# Prepare Total
			if($c==0) {
				$totalHkPres = 0;
			}
			if($c==1) {
				$totalHkPres = $totalHk = $totalUmr = $totalPres = $totalPremi = 0;
			}
            
            # Content
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',9);
            foreach($dataD as $key=>$row) {    
                $i=0;
                foreach($row as $head=>$cont) {
                    if(strlen($cont)==10)
                    {
                        if(substr($cont,4,1)=='-')
                        {
                            $cont=tanggalnormal($cont);
                        } elseif(isset($RnamaKary[$cont])) {
							$cont=$RnamaKary[$cont];
                        }
                    }
					if(strlen($cont)==8)
					{
						if(isset($RnamaBarang[$cont])) {
							$cont=$RnamaBarang[$cont];
						}
					}
					if (($c==0 and ($i==3 or $i==5 or $i==6 or $i==7)) or ($c==1 and ($i==3 or $i==4)) or ($c==2 and $i==4) ) {
                        $pdf->Cell($length[$c][$i]/100*$width,$height,number_format($cont,0),1,0,$align[$c][$i],1);
					}
					else {
                        $pdf->Cell($length[$c][$i]/100*$width,$height,$cont,1,0,$align[$c][$i],1);
                    }
                    $i++;
                }
				$pdf->Ln();
				if($c==0) {
					$totalHkPres += $row['jumlahhk'];
				}
				if($c==1) {
					$totalHk += $row['jhk'];
					$totalUmr += $row['umr'];
					$totalPremi += $row['insentif'];
				}
            }
			$pdf->SetFont('Arial','B',9);
			if($c==0) {
				$pdf->Cell(80/100*$width,$height,'TOTAL',1,0,'C',1);
				$pdf->Cell(20/100*$width,$height,$totalHkPres,1,0,'R',1);
				$pdf->Ln();
			}
			if($c==1) {
				$pdf->Cell(40/100*$width,$height,'TOTAL',1,0,'C',1);
				$pdf->Cell(20/100*$width,$height,$totalHk,1,0,'R',1);
				$pdf->Cell(20/100*$width,$height,number_format($totalUmr),1,0,'R',1);
				$pdf->Cell(20/100*$width,$height,$totalPremi,1,0,'R',1);
				$pdf->Ln();
			}
			$pdf->SetFont('Arial','',9);
           
		    $pdf->Ln();
        
		
		}
		*/
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>