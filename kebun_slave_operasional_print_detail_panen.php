<?php
include_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/formTable.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
$tipe=$_GET['tipe'];
$param = $_GET;


/** Report Prep **/
$cols = array();

# Prestasi
//$col1 = 'nik,kodekegiatan,kodeorg,hasilkerja,jumlahhk,upahkerja,upahpremi,umr';
$col1 = 'tanggal,nik,a.kodeorg,hasilkerja,hasilkerjakg,(hasilkerjakg/hasilkerja) as bjr,upahkerja,upahpremi,rupiahpenalty, dendabasis, IF(rotasiawal=1, "YA", "")';
$col11 = 'tanggal,nik,a.kodeorg,hasilkerja,hasilkerjakg,bjr,upahkerja,upahpremi,rupiahpenalty, dendabasis as Denda Basis, rotasiawal as Pusingan Awal';
$cols[] = explode(',',$col11);
//$query = selectQuery($dbname,'kebun_prestasi',$col1,
//    "notransaksi='".$param['notransaksi']."'");
$query="select ".$col1." from ".$dbname.".kebun_prestasi a left join ".$dbname.".kebun_aktifitas b on a.notransaksi=b.notransaksi where a.notransaksi='".$param['notransaksi']."'";
//exit("Error".$query);
$data[] = fetchData($query);
$align[] = explode(",","L,L,L,R,R,R,R,R,R,R,C");
$length[] = explode(",","7,15,8,6.2,6.2,6.2,10,10,10.2,10.2,10.2");

$total[0] = array(
	'hasilkerja'=>0,
	'hasilkerjakg'=>0,
	'jumlahhk'=>0,
	'upahkerja'=>0,
	'upahpremi'=>0,
	'rupiahpenalty'=>0,
	'dendabasis'=>0
);
foreach($data[0] as $row) {
	$total[0]['hasilkerja']+=$row['hasilkerja'];
	$total[0]['hasilkerjakg']+=$row['hasilkerjakg'];
	$total[0]['jumlahhk']+=$row['jumlahhk'];
	$total[0]['upahkerja']+=$row['upahkerja'];
	$total[0]['upahpremi']+=$row['upahpremi'];
	$total[0]['rupiahpenalty']+=$row['rupiahpenalty'];
	$total[0]['dendabasis']+=$row['dendabasis'];
}

//getNamakaryawan
$sDtKaryawn="select karyawanid,namakaryawan from ".$dbname.".datakaryawan order by namakaryawan asc";
$rData=fetchData($sDtKaryawn);
foreach($rData as $brKary =>$rNamakaryawan)
{
    $RnamaKary[$rNamakaryawan['karyawanid']]=$rNamakaryawan['namakaryawan'];
}
$sOrg="select kodeorganisasi,namaorganisasi from ".$dbname.".organisasi order by namaorganisasi asc";
//exit("Error".$sOrg);
$rDataOrg=fetchData($sOrg);
foreach($rDataOrg as $brOrg =>$rNamaOrg)
{
    $rNmOrg[$rNamaOrg['kodeorganisasi']]=$rNamaOrg['namaorganisasi'];
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
$HeaderT=array('Tanggal','Karyawan','Lokasi','Hasil (Jjg)','Hasil (Kg)','BJR','Upah (Rp)','Premi (Rp)','Denda Panen (Rp)','Denda Basis (Rp)','Pusingan Awal');
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
                
                $pdf->Cell($length[$c][$i]/100*$width,$height,$HeaderT[$i],1,0,'C',1);
                $i++;
            }
            $pdf->Ln();
            
            # Content
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',9);
            foreach($dataD as $key=>$row) {    
                $i=0;
                foreach($row as $cont) {
                    if(strlen($cont)==10)
                    {
                        if(substr($cont,4,1)=='-')
                        {
                            $cont=tanggalnormal($cont);
                        }
                        else
                        {
                            $cont=$RnamaKary[$cont];
                            if($cont=='')
                            {
                                 $cont=$rNmOrg[$row['kodeorg']];
                                // exit("Error".$cont);
                            }
                             // exit("Error".$cont);
                        }
                       
                    }
						if (($i==3)||($i==4)||($i==6)||($i==7)||($i==8)||($i==9)){
							$pdf->Cell($length[$c][$i]/100*$width,$height,number_format($cont),1,0,$align[$c][$i],1);
						}else if ($i==5){
							$pdf->Cell($length[$c][$i]/100*$width,$height,number_format($cont,2),1,0,$align[$c][$i],1);
						}else {
							$pdf->Cell($length[$c][$i]/100*$width,$height,$cont,1,0,$align[$c][$i],1);
						}
                        
						
                    
                    $i++;
                }
                $pdf->Ln();
            }
            $pdf->Cell(($length[$c][0]+$length[$c][1]+$length[$c][2])/100*$width,$height,'TOTAL',1,0,'C',1);
			$pdf->Cell($length[$c][3]/100*$width,$height,number_format($total[$c]['hasilkerja']),1,0,$align[$c][3],1);
			$pdf->Cell($length[$c][4]/100*$width,$height,number_format($total[$c]['hasilkerjakg']),1,0,$align[$c][4],1);
			$pdf->Cell($length[$c][5]/100*$width,$height,number_format($total[$c]['hasilkerjakg']/$total[$c]['hasilkerja'],2),1,0,$align[$c][5],1);
			$pdf->Cell($length[$c][6]/100*$width,$height,number_format($total[$c]['upahkerja']),1,0,$align[$c][6],1);
			$pdf->Cell($length[$c][7]/100*$width,$height,number_format($total[$c]['upahpremi']),1,0,$align[$c][7],1);
			$pdf->Cell($length[$c][8]/100*$width,$height,number_format($total[$c]['rupiahpenalty']),1,0,$align[$c][8],1);
			$pdf->Cell($length[$c][9]/100*$width,$height,number_format($total[$c]['dendabasis']),1,0,$align[$c][9],1);
			$pdf->Cell($length[$c][9]/100*$width,$height,'',1,0,$align[$c][9],1);
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