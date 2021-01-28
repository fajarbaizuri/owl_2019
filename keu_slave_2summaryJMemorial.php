<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
include_once('lib/zPdfMaster.php');

$proses = $_GET['proses'];
if(empty($_POST)) {
    $param = $_GET;
    unset($param['proses']);
} else {
    $param = $_POST;
}
$tmpPeriod = explode('-',$param['periode']);
$bulan = $tmpPeriod[0];
$tahun = $tmpPeriod[1];

$unit = $param['unit'];
if($unit=='') {
    $unit = makeOption($dbname,'organisasi','kodeorganisasi,kodeorganisasi',"induk='".$param['pt']."'");
}

#=== Get Data ===
# Options
$optOrg = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
    "kodeorganisasi='".$param['pt']."' or induk='".$param['pt']."'");

# Query
$where = "left(h.tanggal,7)='".$tahun."-".$bulan."' and h.kodejurnal='M'";
if(is_array($unit)) {
    $i=0;
    $where .= " and (";
    foreach($unit as $row) {
        if($i==0) {
            $where .= "substr(h.nojurnal,10,4)='".$row."'";
        } else {
            $where .= " or substr(h.nojurnal,10,4)='".$row."'";
        }
        $i++;
    }
    $where .= ")";
} else {
    $where .= " and substr(h.nojurnal,10,4)='".$unit."'";
}
$query = "select h.nojurnal,d.noakun,d.keterangan,d.jumlah from `".$dbname."`.`keu_jurnalht` as h".
    " inner join `".$dbname."`.`keu_jurnaldt` as d on h.nojurnal=d.nojurnal where ".$where;
$tmpRes = fetchData($query);

#=== Rearrange Data ===
# Grouping
$data = array();
$i=1;
$totalDebet = 0;
$totalKredit = 0;
foreach($tmpRes as $key=>$row) {
    $data[$i-1] = array(
        'no'=>$i,
        'nojurnal'=>$row['nojurnal'],
        'pt'=>$optOrg[$param['pt']],
        'unit'=>$optOrg[substr($row['nojurnal'],9,4)],
        'akun'=>$row['noakun'],
        'ket'=>$row['keterangan'],
        'debet'=>0,
        'kredit'=>0
    );
    if($row['jumlah']<0) {
        $data[$i-1]['kredit']=$row['jumlah']*(-1);
        $totalKredit += $row['jumlah']*(-1);
    } else {
        $data[$i-1]['debet']=$row['jumlah'];
        $totalDebet += $row['jumlah'];
    }
    $data[$i-1]['kredit']=number_format($data[$i-1]['kredit'],0);
    $data[$i-1]['debet']=number_format($data[$i-1]['debet'],0);
    $i++;
}
$cols = 'no,nojurnal,pt,unit,noakun,keterangan,debet,kredit';
$colArr = explode(',',$cols);
$align = explode(',','L,L,L,L,L,L,R,R');
$length = explode(",","4,15,20,15,10,16,10,10");
$title = 'Summary Jurnal Memorial';

#=== Formatting ===
switch($proses) {
    case 'preview':
        $table = "<table class='sortable'>";
        $table .= "<thead><tr class='rowheader'>";
        foreach($colArr as $head) {
            $table .= "<td>".$_SESSION['lang'][$head]."</td>";
        }
        $table .= "</tr></thead>";
        $table .= "<tbody>";
        foreach($data as $key=>$row) {
            $table .= "<tr class='rowcontent'>";
            $i=0;
            foreach($row as $cont) {
                switch($align[$i]){
                    case 'L':
                        $tmpAlign='left';
                    case 'R':
                        $tmpAlign='right';
                    case 'C':
                        $tmpAlign='center';
                    default:
                }
                $table .= "<td align='".$tmpAlign."'>".$cont."</td>";
                $i++;
            }
            $table .= "</tr>";
        }
        $table .= "<tr class='rowcontent'><td colspan='6' align='center'>TOTAL</td>";
        $table .= "<td align='right'>".number_format($totalDebet,0)."</td>";
        $table .= "<td align='right'>".number_format($totalKredit,0)."</td>";
        $table .= "</tr>";
        $table .= "</tbody>";
        $table .= "</table>";
        echo $table;
        break;
    case 'pdf':
        $pdf=new zPdfMaster('L','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colArr);
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
	$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','',9);
        foreach($data as $key=>$row) {    
            $i=0;
            foreach($row as $cont) {
                $pdf->Cell($length[$i]/100*$width,$height,$cont,1,0,$align[$i],1);
                $i++;
            }
            $pdf->Ln();
        }
        $pdf->Cell(80/100*$width,$height,'TOTAL',1,0,'C',1);
        $pdf->Cell(10/100*$width,$height,number_format($totalDebet,0),1,0,'R',1);
        $pdf->Cell(10/100*$width,$height,number_format($totalKredit,0),1,0,'R',1);
	
        $pdf->Output();
        break;
    default:
        break;
}
?>