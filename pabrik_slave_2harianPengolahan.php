<?php
require_once('master_validation.php');
include_once('lib/nangkoelib.php');
include_once('lib/zLib.php');
include_once('lib/biReport.php');
include_once('lib/zPdfMaster.php');
include_once('lib/terbilang.php');

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

#1. Empty
if($param['tanggal']=='') {
    echo 'Warning : Tanggal tidak boleh kosong';
    exit;
}

## Param
$pabrik = $param['pabrik'];
$tgl = $param['tanggal'];
$tglArr = explode('-',$tgl);

switch($level) {
    case '0':
        ## Data
        ##################################### Pabrik Timbangan u/ Penerimaan TBS
        $q1 = "SELECT a.kodeorg,a.kodecustomer,SUM(a.beratbersih) AS netto,".
			"SUM(a.beratbersih-a.kgpotsortasi) AS normal,b.namaorganisasi,b.induk,c.namasupplier FROM ".$dbname.
			".pabrik_timbangan a LEFT JOIN ".$dbname.".organisasi b ON a.kodeorg=b.kodeorganisasi ".
			"left join ".$dbname.".log_5supplier c on a.kodecustomer=c.supplierid WHERE ".
			"date(a.tanggal)='".tanggalsystem($tgl)."' and a.millcode='".$pabrik.
			"' and kodebarang='40000003' GROUP BY a.kodeorg,a.kodecustomer";
        $r1 = fetchData($q1);
		$timbInt=$timbExt=array();
		$totalPt=array();
		$totalTBS=$totalInt=$totalExt=array(
			'netto'=>0,
			'normal'=>0
		);
		foreach($r1 as $r) {
			$totalTBS['netto']+=$r['netto'];
			$totalTBS['normal']+=$r['normal'];
			if($r['induk']=='') {
				$timbExt[$r['kodecustomer']] = array(
					'name'=>$r['namasupplier'],
					'netto'=>$r['netto'],
					'normal'=>$r['normal']
				);
				
				# Total
				$totalExt['netto']+=$r['netto'];
				$totalExt['normal']+=$r['normal'];
			} else {
				$timbInt[$r['induk']][$r['kodeorg']] = array(
					'name'=>$r['namaorganisasi'],
					'netto'=>$r['netto'],
					'normal'=>$r['normal']
				);
				
				# Total
				$totalInt['netto']+=$r['netto'];
				$totalInt['normal']+=$r['normal'];
				if(isset($totalPt[$r['induk']])) {
					$totalPt[$r['induk']]['netto']+=$r['netto'];
					$totalPt[$r['induk']]['normal']+=$r['normal'];
				} else {
					$totalPt[$r['induk']] = array(
						'netto'=>$r['netto'],
						'normal'=>$r['normal']
					);
				}
			}
		}
		#################################### /Pabrik Timbangan u/ Penerimaan TBS
		
		############################################################# Pengolahan
		## Restan
		//$qRestan = selectQuery($dbname,'kebun_restan_v1','sum(jumlahjjgrestan) as restan,periode',
		//	"periode='".$tgl[2]."-".$tgl[1]."'")." group by periode";
		$qRestan = "SELECT sum(b.hasilkerja) as hasilkerja,b.tanggal,a.kodeorg,a.jumlahjjgrestan,a.catatan,b.notransaksi,a.periode,c.blok,c.jjg	
			FROM ".$dbname.".kebun_restan_v1 a
			JOIN ".$dbname.".kebun_prestasi_vw b
			JOIN ".$dbname.".kebun_spbdt c
			ON a.kodeorg=b.kodeorg and a.periode=substr(b.tanggal,1,7) and a.kodeorg=c.blok
			WHERE  a.kodeorg='".$BlokId."' and a.periode='".$periodeId."' and b.notransaksi like '%PNN%' group by b.tanggal ";	 
		$resRestan = fetchData($qRestan);
		if(empty($resRestan)) {
			$restan = 0;
		} else {
			$restan = $resRestan[0]['restan'];
		}
		echo $restan;
		############################################################ /Pengolahan
		break;
    default:
    break;
}

switch($mode) {
    case 'pdf':
        /** Report Prep **/
		# Options
		$optJab = makeOption($dbname,'sdm_5jabatan','kodejabatan,namajabatan',
			"kodejabatan='".$_SESSION['empl']['kodejabatan']."'");
		
        $colPdf = array('no','tanggal','keterangan','kasmasuk','penerimaan',
            'kaskeluar','pengeluaran');
        $title = $_SESSION['lang']['kasharian'];
        $length = explode(",","5,12,35,10,14,10,14");
        
        $pdf = new zPdfMaster('P','pt','A4');
        $pdf->setAttr1($title,$align,$length,$colPdf);
		$pdf->_noThead=true;
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 12;
		$pdf->AddPage();
        
        $pdf->SetFillColor(255,255,255);
		
		
		
		$pdf->Output();
        break;
    default:
		#################################################### [Penerimaan TBS] ##
		$tab = "<div style='font-weight:bold;text-decoration:underline;margin-bottom:5px'>A. TBS DITERIMA</div>";
		$tab .= "<table class=data border=1 cellpadding=1 cellspacing=0><thead>";
		
		## Header
		$tab .= "<tr class=rowheader><td>".$_SESSION['lang']['nourut']."</td>";
		$tab .= "<td align=center style='width:300px'>Supplier</td>";
		$tab .= "<td align=center style='width:160px'>Netto</td>";
		$tab .= "<td align=center style='width:160px'>Normal</td>";
		$tab .= "</tr></thead><tbody>";
		
		## Kebun Sendiri
		$tab .= "<tr class=rowcontent><td align=center>01</td>";
		$tab .= "<td colspan=3 style='font-weight:bold;text-decoration:underline;font-style:italic'>Kebun Sendiri</td></tr>";
		$i=97;
		foreach($timbInt as $pt=>$det) {
			$tab .= "<tr class=rowcontent><td align=center>".chr($i)."</td><td colspan=3>PT ".$pt."</tr>";
			foreach($det as $cont) {
				$tab .= "<tr class=rowcontent><td></td><td>".$cont['name']."</td><td align=right>".
					number_format($cont['netto'],0)." Kg</td><td align=right>".
					number_format($cont['normal'],0)." Kg</td></tr>";
			}
			$tab .= "<tr class=rowcontent><td></td><td align=center>JUMLAH ".$pt."</td><td align=right>".
				number_format($totalPt[$pt]['netto'],0)." Kg</td><td align=right>".
				number_format($totalPt[$pt]['normal'],0)." Kg</td></tr>";
			$i++;
		}
		$tab .= "<tr class=rowcontent style='font-weight:bold'><td colspan=2 align=center>JUMLAH FBG (KEBUN SENDIRI)</td><td align=right>".
			number_format($totalInt['netto'],0)." Kg</td><td align=right>".
			number_format($totalInt['normal'],0)." Kg</td></tr>";
		
		## Pihak Ketiga
		$tab .= "<tr class=rowcontent><td align=center>02</td>";
		$tab .= "<td colspan=3 style='font-weight:bold;text-decoration:underline;font-style:italic'>Pihak Ketiga</td></tr>";
		$i=1;
		foreach($timbExt as $kodeCust=>$row) {
			$tab .= "<tr class=rowcontent><td align=center>".$i."</td>";
			$tab .= "<td>".$row['name']."</td>";
			$tab .= "<td align=right>".number_format($row['netto'],0)." Kg</td>";
			$tab .= "<td align=right>".number_format($row['normal'],0)." Kg</td>";
			$tab .= "</tr>";
			$i++;
		}
		$tab .= "<tr class=rowcontent style='font-weight:bold'><td colspan=2 align=center>JUMLAH PIHAK KETIGA</td><td align=right>".
			number_format($totalExt['netto'],0)." Kg</td><td align=right>".
			number_format($totalExt['normal'],0)." Kg</td></tr>";
		
		## JUMLAH TOTAL TBS
		$tab .= "<tr class=rowcontent style='font-weight:bold'><td colspan=2 align=center>JUMLAH TOTAL</td><td align=right>".
			number_format($totalTBS['netto'],0)." Kg</td><td align=right>".
			number_format($totalTBS['normal'],0)." Kg</td></tr>";
		$tab .= "</tbody></table>";
		################################################### [/Penerimaan TBS] ##
		
		######################################################## [Pengolahan] ##
		$tab .= "<br><div style='font-weight:bold;text-decoration:underline;margin-bottom:5px'>B. PENGOLAHAN</div>";
		$tab .= "<table class=data border=1 cellpadding=1 cellspacing=0><thead>";
		
		## Header
		$tab .= "<tr class=rowheader><td>".$_SESSION['lang']['nourut']."</td>";
		$tab .= "<td align=center style='width:300px'></td>";
		$tab .= "<td align=center style='width:160px'>hari ini</td>";
		$tab .= "<td align=center style='width:160px'>s/d hari ini</td>";
		$tab .= "</tr></thead><tbody>";
		
		$tab .= "</tbody></table>";
		####################################################### [/Pengolahan] ##
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="PengolahanPabrikHarian_".$kodeorg."_".$tgl;
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