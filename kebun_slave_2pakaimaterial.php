<?php

#buat mas adi, update by ind
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');

$level = $_GET['level'];
if(isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} else {
    $mode = 'preview';
}
if($mode=='pdf') {
    $param = $_GET;
    unset($param['mode']);
    unset($param['level']);
} else {
    $param = $_POST;
}

$namakegiatan=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');

//echo"<pre>";
//print_r($param);        
//echo"</pre>";

# Tanggal Margin
$currTahun = $tahun = $param['periode_tahun'];
$currBulan = $bulan = $param['periode_bulan'];
$bulan++;
if($bulan>12) {
    $bulan = 1;
    $tahun++;
}
if($bulan<10) {
    $bulan = '0'.$bulan;
}
$tanggalM = $tahun."-".$bulan."-01";

# Current Periode
if($currBulan<10) {
    $currBulan = '0'.$currBulan;
}
$currPeriod = $currTahun.$currBulan;

//exit("Error:$currPeriod");

switch($level) {
    case '0':
        # Data
        # Afdeling dan Blok
        $afd = substr($param['kodeorg'],0,6);
        $kodeorg = substr($param['kodeorg'],0,4);
        $optBelow = getOrgBelow($dbname,$kodeorg);
        
        # Mutasi Blok
        /*$cols = "akt.tanggal,mat.notransaksi,mat.kodeorg,mat.kodebarang,mat.kwantitas,mat.kwantitasha,mat.hargasatuan";
        $where = "left(mat.notransaksi,6)='".$currPeriod."' and ".
            "left(mat.kodeorg,4)='".$kodeorg."' and ".
            "akt.jurnal=1";*/
			
			$where = "left(notransaksi,6)='".$currPeriod."' and ".
            "left(kodeorg,4)='".$kodeorg."'";
			
			//exit("Error:$where");
/*        $query = "select ".$cols." from `".$dbname."`.`kebun_pakaimaterial` as mat 
			join `".$dbname."`.`kebun_aktifitas` as akt on akt.notransaksi=mat.notransaksi 
			join `".$dbname."`.`kebun_perawatan_vw` as per on per.notransaksi=mat.notransaksi 
			join `".$dbname."`.`setup_kegiatan` as keg on keg.kodekegiatan=per.kodekegiatan 
			where ".$where;*/
			//echo $query;exit();
		$query="select notransaksi,kodeorg,left(kodeorg,6) as afdeling,kodebarang,kwantitas,kwantitasha,hargasatuan,kodekegiatan,tanggal from ".$dbname.".kebun_pakaimaterial where ".$where;
        $tmpRes = fetchData($query);
        if(empty($tmpRes)) {
            echo 'Warning : Data Kosong';
            exit;
        }
//echo $query; exit;  
        # Options
        $whereBrg = "";
        foreach($tmpRes as $key=>$row) {
            if($key==0) {
                $whereBrg .= "kodebarang='".$row['kodebarang']."'";
            } else {
                $whereBrg .= "or kodebarang='".$row['kodebarang']."'";
            }
        }
        $optBrg = makeOption($dbname,'log_5masterbarang','kodebarang,namabarang',
            $whereBrg);
//        $optHrg = makeOption($dbname,'log_5saldobulanan','kodebarang,periode',
//            $whereBrg2);

	$strJ="select induk from ".$dbname.".organisasi where kodeorganisasi = '".$kodeorg."'";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$induk=$barJ->induk;
	}
	$strJ="select * from ".$dbname.".log_5saldobulanan where (".$whereBrg.") and left(kodegudang,4) = '".$kodeorg."' order by periode desc";
	$resJ=mysql_query($strJ,$conn);
	while($barJ=mysql_fetch_object($resJ))
	{
		$optHrg[$barJ->kodebarang]=$barJ->hargarata;
	}
	
//	echo $strJ;
//echo"<pre>";
//print_r($optBelow);        
//echo"</pre>";
        # Set Data
        $data = $tmpRes;
        $dataShow = $dataExcel = $data;
        foreach($data as $key=>$row) {
			$data[$key]['totalbiaya'] = number_format($optHrg[$row['kodebarang']]*$row['kwantitas'],2);
			
            $dataShow[$key]['kodeorg'] = $optBelow[$row['kodeorg']];
            $dataShow[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $dataShow[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
			 $dataShow[$key]['kodekegiatan'] = $namakegiatan[$row['kodekegiatan']];
           // $dataShow[$key]['namakegiatan'] = $row['namakegiatan'];
            $dataShow[$key]['kwantitas'] = number_format($row['kwantitas'],2);
//            $dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
            $dataShow[$key]['hargasatuan'] = number_format($optHrg[$row['kodebarang']],2);
			
			$dataShow[$key]['totalbiaya'] = number_format($optHrg[$row['kodebarang']]*$row['kwantitas'],2);
			

            /*$dataExcel[$key]['kodeorg'] = $optBelow[$row['kodeorg']];
            $dataExcel[$key]['tanggal'] = $row['tanggal'];
            $dataExcel[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
            $dataExcel[$key]['kwantitas'] = number_format($row['kwantitas'],2);
//            $dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
            $dataExcel[$key]['hargasatuan'] = number_format($optHrg[$row['kodebarang']],2);
			$dataExcel[$key]['totalbiaya'] = number_format($optHrg[$row['kodebarang']]*$row['kwantitas'],2);*/
			
			            $dataExcel[$key]['kodeorg'] = $optBelow[$row['kodeorg']];
            $dataExcel[$key]['tanggal'] = tanggalnormal($row['tanggal']);
            $dataExcel[$key]['kodebarang'] = $optBrg[$row['kodebarang']];
			 $dataExcel[$key]['kodekegiatan'] = $namakegiatan[$row['kodekegiatan']];
           // $dataShow[$key]['namakegiatan'] = $row['namakegiatan'];
            $dataExcel[$key]['kwantitas'] = number_format($row['kwantitas'],2);
//            $dataShow[$key]['kwantitasha'] = number_format($row['kwantitasha'],2);
            $dataExcel[$key]['hargasatuan'] = number_format($optHrg[$row['kodebarang']],2);
			$dataExcel[$key]['totalbiaya'] = number_format($optHrg[$row['kodebarang']]*$row['kwantitas'],2);
			
        }
        
        # Report Gen
        $theCols = array(
            $_SESSION['lang']['notransaksi'],
			'Kode Blok','afdeling',
			$_SESSION['lang']['kodebarang'],
			$_SESSION['lang']['kwantitas'],
			$_SESSION['lang']['kwantitas'].' Ha',
			'Harga Satuan',
            $_SESSION['lang']['kodekegiatan'],
            $_SESSION['lang']['tanggal']
        );
        $align = explode(",","L,L,L,L,L,L,L");
        break;
    default:
    break;
}

switch($mode) {
    case 'pdf':
        /** Report Prep **/
        $colPdf = array('notransaksi','tanggal','kodekegiatan','kodeorg','kodebarang','kwantitas',
            'hargasatuan','Total Biaya');
        $title = $_SESSION['lang']['lapmaterial'];
        $length = explode(",","15,7,20,20,20,10,10");
        
        $pdf = new zPdfMaster('L','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        
        # Content
	$pdf->SetFont('Arial','',9);
        foreach($dataShow as $key=>$row) {
	    $i=0;
            foreach($row as $head=>$cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
	    $pdf->Ln();
        }
        
        $pdf->Output();
        break;
    default:
        # Redefine Align
	$alignPrev = array();
	foreach($align as $key=>$row) {
	    switch($row) {
		case 'L':
		    $alignPrev[$key] = 'left';
		    break;
		case 'R':
		    $alignPrev[$key] = 'right';
		    break;
		case 'C':
		    $alignPrev[$key] = 'center';
		    break;
		default:
	    }
	}
	
	/** Mode Header **/
        if($mode=='excel') {
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='kasharian' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }
        
        /** Generate Table **/
        foreach($theCols as $head) {
            $tab .= "<td>".$head."</td>";
        }
		$tab .= "<td>Total Biaya</td>";
        $tab .= "</tr></thead>";
        $tab .= "<tbody>";
        
        # Content
	foreach($data as $key=>$row) {
            $tab .= "<tr class='rowcontent'>";
	    $i=0;
		
		
		
		
		
	    foreach($row as $head=>$cont) {
		if($mode=='excel') {
		    $tab .= "<td align='".$alignPrev[$i]."'>".$dataExcel[$key][$head]."</td>";
		} else {
		    $tab .= "<td align='".$alignPrev[$i]."'>".$dataShow[$key][$head]."</td>";
		}
		$i++;
	    }
	    $tab .= "</tr>";
        }
        $tab .= "</tbody>";
        $tab .= "</table>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="PakaiMaterial";
            if(strlen($stream)>0) {
                # Delete if exist
                if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != "..") {
                            @unlink('tempExcel/'.$file);
                        }
                    }	
                    closedir($handle);
                }
                
                # Write to File
                $handle=fopen("tempExcel/".$nop_.".xls",'w');
                if(!fwrite($handle,$stream)) {
                    echo "Error : Tidak bisa menulis ke format excel";
                    exit;
                } else {
                    echo $nop_;
                }
                fclose($handle);
            }
        } else {
            echo $tab;
        }
        break;
}
?>