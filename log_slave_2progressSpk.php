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

# Validasi Periode
$periode1 = $param['periode_from'];
$periode2 = $param['periode_until'];
#1. Empty
if($periode1=='' or $periode2=='') {
    echo 'Warning : Periode tidak boleh kosong';
    exit;
}
#2. Range Terbalik
if(tanggalsystem($periode1)>tanggalsystem($periode2)) {
    $tmp = $periode1;
    $periode1 = $periode2;
    $periode2 = $tmp;
}

$kodeorg = $_SESSION['empl']['lokasitugas'];

# Options
$optBlok = makeOption($dbname,'organisasi','kodeorganisasi,namaorganisasi',
	"tipe in ('BLOK','AFDELING') and left(kodeorganisasi,4)='".$kodeorg."'");
$optKeg = makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');

switch($level) {
    case '0':
        # Data
        ## SPK
        $whereFilter = "";
		if($param['kodeblok']!='') {
			$whereFilter .= " and b.kodeblok='".$param['kodeblok']."'";
		}
		if($param['kegiatan']!='') {
			$whereFilter .= " and b.kodekegiatan='".$param['kegiatan']."'";
		}
		
        $q1 = "select a.*,b.* from ".$dbname.".log_spkht a left join ".$dbname.
			".log_spkdt b on a.notransaksi=b.notransaksi where a.tanggal between '".
			tanggalsystem($periode1)."' and '".tanggalsystem($periode2).
			"' and a.posting=1".$whereFilter;
		$res1 = fetchData($q1);
		$listSpk = '';$i=0;
		foreach($res1 as $row) {
			if($i>0) {
				$listSpk .= ',';
			}
			$listSpk .= "'".$row['notransaksi']."'";
			$i++;
		}
		
		## BAPP
		if(!empty($listSpk)) {
			$q2 = selectQuery($dbname,'log_baspk','*',"notransaksi in (".$listSpk.")");
			$res2 = fetchData($q2);
		}
		
		## Rearrange Data
		$data = array();$tglSpk=array();
		$totalSpk=0;$totalReal=0;
		foreach($res1 as $row) {
			$data[$row['notransaksi']][$row['kodekegiatan']][$row['kodeblok']]['spk'] = array(
				'tanggal'=>$row['tanggal'],
				'hasilkerja'=>$row['hasilkerjajumlah'],
				'hk'=>$row['hk'],
				'satuan'=>$row['satuan'],
				'nilai'=>$row['jumlahrp']
			);
			$tglSpk[$row['notransaksi']]=$row['tanggal'];
			$totalSpk+=$row['jumlahrp'];
		}
		foreach($res2 as $row) {
			if(isset($data[$row['notransaksi']][$row['kodekegiatan']][$row['kodeblok']])) {
				$data[$row['notransaksi']][$row['kodekegiatan']][$row['kodeblok']]['realisasi'][] = array(
					'tanggal'=>$row['tanggal'],
					'hk'=>$row['hkrealisasi'],
					'hasilkerja'=>$row['hasilkerjarealisasi'],
					'nilai'=>$row['jumlahrealisasi']
				);
			}
			$totalReal+=$row['jumlahrealisasi'];
		}
		
		$dataShow = $data;
		$dataExcel = $data;
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
		
		$colPdf = $align = array();
		$title = 'Progress SPK';
		$length = array();
		
		class progressPdf extends zPdfMaster {
			function Header() {
				$width = $this->w - $this->lMargin - $this->rMargin;
				$height = 12;
				
				parent::Header();
				$this->SetFont('Arial','B',8);
				$this->SetFillColor(220,220,220);
				$this->Cell(7/100*$width,$height*2,'No. SPK',1,0,'C',1);
				$this->Cell(8/100*$width,$height*2,'Tanggal SPK',1,0,'C',1);
				$this->Cell(15/100*$width,$height*2,'Kegiatan',1,0,'C',1);
				$this->Cell(13/100*$width,$height*2,'Unit',1,0,'C',1);
				$this->Cell(25/100*$width,$height,'SPK',1,0,'C',1);
				$this->Cell(32/100*$width,$height,'Realisasi',1,0,'C',1);
				$this->Ln($height);
				$this->setX($this->lMargin+(43/100*$width));
				$this->Cell(5/100*$width,$height,'HK',1,0,'C',1);
				$this->Cell(10/100*$width,$height,'Hasil Kerja',1,0,'C',1);
				$this->Cell(10/100*$width,$height,'Nilai (Rp.)',1,0,'C',1);
				$this->Cell(7/100*$width,$height,'Tanggal',1,0,'C',1);
				$this->Cell(5/100*$width,$height,'HK',1,0,'C',1);
				$this->Cell(10/100*$width,$height,'Hasil Kerja',1,0,'C',1);
				$this->Cell(10/100*$width,$height,'Nilai (Rp.)',1,0,'C',1);
				$this->Ln($height);
			}
		}
		
		$pdf = new progressPdf('L','pt','A4');
		$pdf->setAttr1($title,$align,$length,$colPdf);
		$pdf->_noThead=true;
		$pdf->_subTitle = 'Periode : '.$periode1.' - '.$periode2;
		$width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
		$height = 12;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',8);
		foreach($data as $spk=>$row1) {
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(7/100*$width,$height,$spk,1,0,'L',1);
			$pdf->Cell(93/100*$width,$height,$tglSpk[$spk],1,1,'L',1);
			foreach($row1 as $keg=>$row2) {
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(15/100*$width,$height,'',1,0,'L',1);
				$pdf->Cell(85/100*$width,$height,$optKeg[$keg],1,1,'L',1);
				foreach($row2 as $blok=>$row3) {
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(30/100*$width,$height,'',1,0,'L',1);
					$pdf->Cell(13/100*$width,$height,$optBlok[$blok],1,0,'L',1);
					$pdf->Cell(5/100*$width,$height,$row3['spk']['hk'],1,0,'R',1);
					$pdf->Cell(10/100*$width,$height,$row3['spk']['hasilkerja']." ".
						$row3['spk']['satuan'],1,0,'R',1);
					$pdf->Cell(10/100*$width,$height,number_format($row3['spk']['nilai'],0),1,0,'R',1);
					if(empty($row3['realisasi'])) {
						$pdf->Cell(32/100*$width,$height,'',1,0,'L',1);
						$pdf->Ln();
					} else {
						$i=0;$totalReal=0;
						foreach($row3['realisasi'] as $row4) {
							if($i>0) {
								$pdf->Cell(68/100*$width,$height,'',1,0,'L',1);
							}
							$pdf->Cell(7/100*$width,$height,tanggalnormal($row4['tanggal']),1,0,'L',1);
							$pdf->Cell(5/100*$width,$height,$row4['hk'],1,0,'R',1);
							$pdf->Cell(10/100*$width,$height,$row4['hasilkerja'],1,0,'R',1);
							$pdf->Cell(10/100*$width,$height,number_format($row4['nilai']),1,0,'R',1);
							$pdf->Ln();
							$totalReal+=$row4['nilai'];
							$i++;
						}
						$pdf->SetFont('Arial','B',8);
						$pdf->Cell(68/100*$width,$height,'',1,0,'L',1);
						$pdf->Cell(22/100*$width,$height,'Total Realisasi',1,0,'L',1);
						$pdf->Cell(10/100*$width,$height,number_format($totalReal),1,0,'R',1);
						$pdf->Ln();
					}
				}
			}
		}
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(43/100*$width,$height,'',1,0,'L',1);
		$pdf->Cell(15/100*$width,$height,'Total SPK',1,0,'C',1);
		$pdf->Cell(10/100*$width,$height,number_format($totalSpk),1,0,'R',1);
		$pdf->Cell(22/100*$width,$height,'Total Realisasi',1,0,'C',1);
		$pdf->Cell(10/100*$width,$height,number_format($totalReal),1,0,'R',1);
		$pdf->Output();
        break;
    default:
		/** Mode Header **/
        if($mode=='excel') {
            $tab = "<table border='1'>";
            $tab .= "<thead style=\"background-color:#222222\"><tr class='rowheader'>";
        } else {
            $tab = "<table id='progressSpk' class='sortable'>";
            $tab .= "<thead><tr class='rowheader'>";
        }
		
		/** Generate Table **/
        $tab .= "<td rowspan=2>No. SPK</td>";
		$tab .= "<td rowspan=2>Tanggal SPK</td>";
		$tab .= "<td rowspan=2>Kegiatan</td>";
		$tab .= "<td rowspan=2>Blok</td>";
		$tab .= "<td colspan=3>Rencana</td>";
		$tab .= "<td colspan=4>Realisasi</td>";
        $tab .= "</tr><tr class='rowheader'>";
        $tab .= "<td>HK</td><td>Hasil Kerja</td><td>Nilai (Rp.)</td>";
		$tab .= "<td>Tanggal</td><td>HK</td><td>Hasil Kerja</td><td>Nilai (Rp.)</td>";
		$tab .= "</tr></thead>";
        $tab .= "<tbody>";
		foreach($data as $spk=>$row1) {
			$tab .= "<tr class=rowcontent><td>".$spk."</td><td colspan=10>".$tglSpk[$spk]."</td></tr>";
			foreach($row1 as $keg=>$row2) {
				$tab .= "<tr class=rowcontent><td colspan=2></td><td colspan=9>".
					$optKeg[$keg]."</td></tr>";
				foreach($row2 as $blok=>$row3) {
					$tab .= "<tr class=rowcontent><td colspan=3></td>";
					$tab .= "<td>".$optBlok[$blok]."</td>";
					$tab .= "<td align=right>".$row3['spk']['hk']."</td>";
					$tab .= "<td align=right>".$row3['spk']['hasilkerja']." ".
						$row3['spk']['satuan']."</td>";
					if($mode=='excel') {
						$tab .= "<td align=right>".$row3['spk']['nilai']."</td>";
					} else {
						$tab .= "<td align=right>".number_format($row3['spk']['nilai'],0)."</td>";
					}
					if(empty($row3['realisasi'])) {
						$tab .= "<td colspan=4></td></tr>";
					} else {
						$i=0;
						$totalReal=0;
						foreach($row3['realisasi'] as $row4) {
							if($i==0) {
								$tab .= "<td align=left>".tanggalnormal($row4['tanggal'])."</td>";
								$tab .= "<td align=right>".$row4['hk']."</td>";
								$tab .= "<td align=right>".$row4['hasilkerja']." ".
									$row3['spk']['satuan']."</td>";
								if($mode=='excel') {
									$tab .= "<td align=right>".$row4['nilai']."</td></tr>";
								} else {
									$tab .= "<td align=right>".number_format($row4['nilai'])."</td></tr>";
								}
							} else {
								$tab .= "<tr class='rowcontent'><td colspan=7></td>";
								$tab .= "<td align=left>".tanggalnormal($row4['tanggal'])."</td>";
								$tab .= "<td align=right>".$row4['hk']."</td>";
								$tab .= "<td align=right>".$row4['hasilkerja']." ".
									$row3['spk']['satuan']."</td>";
								if($mode=='excel') {
									$tab .= "<td align=right>".$row4['nilai']."</td></tr>";
								} else {
									$tab .= "<td align=right>".number_format($row4['nilai'])."</td></tr>";
								}
							}
							$totalReal+=$row4['nilai'];
							$i++;
						}
						$tab .= "<tr class='rowcontent'><td colspan=7></td>";
						$tab .= "<td align=center colspan=3>Total Realisasi</td>";
						if($mode=='excel') {
							$tab .= "<td align=right>".$totalReal."</td></tr>";
						} else {
							$tab .= "<td align=right>".number_format($totalReal)."</td></tr>";
						}
					}
				}
			}
		}
		$tab .= "<tr class=rowcontent><td colspan=4></td>";
		$tab .= "<td colspan=2>Total SPK</td>";
		$tab .= "<td align=right>".number_format($totalSpk)."</td>";
		$tab .= "<td colspan=3>Total Realisasi</td>";
		$tab .= "<td align=right>".number_format($totalReal)."</td>";
		$tab .= "</tr></tbody>";
        
        /** Output Type **/
        if($mode=='excel') {
            $stream = $tab;
            $nop_="ProgressSPK_".$kodeorg;
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