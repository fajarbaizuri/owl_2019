<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_GET;
$notransaksi = $_GET['notransaksi'];

//echo $notransaksi;

/** Report Prep **/
$cols = array();

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


switch($tipe) {
    case "LC":
        $title = "Pembukaan Lahan";
        break;
    case "BBT":
	$title = "Pembibitan";
	break;
    case "TBM":
	$title = "Tanaman Belum Menghasilkan";
	break;
    case "TM":
	$title = "Tanaman Menghasilkan";
	break;
	case "PNN":
	$title = "Panen Dan Pengangkutan Buah";
	break;
    default:
	echo "Error : Atribut Status tidak terdefinisi";
	exit;
	break;
}
$titleDetail = array('Prestasi','Kehadiran','Pemakaian Material');

/** Output Format **/
switch($proses) {
    case 'pdf':
        
        $pdf=new zPdfMaster('L','pt','A4');
        $pdf->_noThead=true;
        $pdf->setAttr1($title,$align,$length,array());
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
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
        $pdf->Output();
        break;
    case 'excel':
        break;
    default:
    break;
}
?>