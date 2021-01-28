<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses'];
$kdOrg=$_POST['kodeorg'];
$kegiatan=$_POST['kegiatan'];
$tgl1_=$_POST['tgl1'];
$tgl2_=$_POST['tgl2'];
if(($proses=='excel')or($proses=='pdf')){
        $kdOrg=$_GET['kodeorg'];
        $kegiatan=$_GET['kegiatan'];
	$tgl1_=$_GET['tgl1'];
	$tgl2_=$_GET['tgl2'];
}

$optnamakeg=makeOption($dbname,'setup_kegiatan','kodekegiatan,namakegiatan');

$tgl1_=tanggalsystem($tgl1_); $tgl1=substr($tgl1_,0,4).'-'.substr($tgl1_,4,2).'-'.substr($tgl1_,6,2);
$tgl2_=tanggalsystem($tgl2_); $tgl2=substr($tgl2_,0,4).'-'.substr($tgl2_,4,2).'-'.substr($tgl2_,6,2);

if(($proses=='preview')or($proses=='excel')or($proses=='pdf')){
    if(($tgl1_=='')or($tgl2_=='')){
            echo"Error: Tanggal tidak boleh kosong."; exit;
    }

    if($tgl1>$tgl2){
            echo"Error: Tanggal pertama tidak boleh lebih besar dari tanggal kedua."; exit;
    }
	
}
#ambil tahun tanam
$tahuntanam=Array();
$str="select kodeorg,tahuntanam from ".$dbname.".setup_blok where kodeorg like '".$kdOrg."%'";
$res=mysql_query($str);

while($bar=mysql_fetch_object($res))
{
    $tahuntanam[$bar->kodeorg]=$bar->tahuntanam;
}
#ambil namakegiatan
$namakegiatan=Array();
$str="select noakun,namaakun from ".$dbname.".keu_5akun where length(noakun)=7";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $namakegiatan[$bar->noakun]=$bar->namaakun;
}

if($kegiatan=='')
    $addwhere= " and (noakun like '126%' or noakun like '62%')";
else
    $addwhere= " and noakun ='".$kegiatan."'";
#generate SQL
$str="select kodekegiatan,noakun,sum(debet) as biaya,kodeblok from ".$dbname.".keu_jurnaldt_vw 
      where tanggal between '".$tgl1."' and '".$tgl2."' and kodeorg='".$kdOrg."'
      ".$addwhere." group by noakun,kodeblok";
	 // echo $str;
$res=mysql_query($str);
#+++++++++++++++++++++++process data
$stream="UNIT:".$kdOrg."<br>
                RANGE:".tanggalnormal($tgl1)." - ".tanggalnormal($tgl2)."";
                if($proses=='excel')$stream.="<table cellspacing='1' border='1' class='sortable'>";
                else $stream.="<table cellspacing='1' border='0' class='sortable'>";
                $stream.="<thead class=rowheader>
                <tr>
                <td>No</td>
                <td>".$_SESSION['lang']['noakun']."</td>
                <td>".$_SESSION['lang']['kegiatan']."</td>
                <td>".$_SESSION['lang']['blok']."</td>
                <td>".$_SESSION['lang']['tahuntanam']."</td>    
                <td>".$_SESSION['lang']['jumlah']."(Rp.)</td>
                </tr></thead>
                <tbody>";
$no=0;
$ttl=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $stream.="<tr class=rowcontent>
                <td>".$no."</td>
                <td>".$bar->noakun."</td>    
                <td>".$optnamakeg[$bar->kodekegiatan]."</td>
                <td>".$bar->kodeblok."</td>
                <td>".$tahuntanam[$bar->kodeblok]."</td>    
                <td align=right>".number_format($bar->biaya)."</td>
              </tr>";
    $ttl+=$bar->biaya;
}
$stream.="<tr class=rowcontent>
                <td colspan=5>Total</td>
                <td align=right>".number_format($ttl)."</td>
              </tr>";
$stream.="</tbody><tfoot></tfoot></table>";
 #+++++++++++++++++++++++++++++++++++++++++++++++++            
switch($proses)
{
      case 'preview':
                     
		echo $stream;
            break;
       case 'excel':
$qwe=date("YmdHms");
           $nop_="Laporan Biaya Kegiatan Per Blok ".$kdOrg."_".$kegiatan."_".$qwe;
            if(strlen($stream)>0)
            {
                 $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
                 gzwrite($gztralala, $stream);
                 gzclose($gztralala);
                 echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls.gz';
                    </script>";
            } 
	    break;
	case'pdf':
//            echo "Format Belum tersedia";

            class PDF extends FPDF
                    {
                        function Header() {
                            global $conn;
                            global $dbname;
                            global $align;
                            global $length;
                            global $colArr;
                            global $title;
                                            global $kdOrg;
                                            global $kdAfd;
                                            global $tgl1;
                                            global $tgl2;
                                            global $where;
                                            global $nmOrg;
                                            global $lok;

                                            $cols=247.5;
                            $query = selectQuery($dbname,'organisasi','alamat,telepon',
                                "kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'");
                            $orgData = fetchData($query);

                            $width = $this->w - $this->lMargin - $this->rMargin;
                            $height = 20;
                            $path='images/logo.jpg';
							$this->Image($path,35,5,40);
                            //$this->Image($path,$this->lMargin,$this->tMargin,70);	
                            $this->SetFont('Arial','B',9);
                            $this->SetFillColor(255,255,255);	
                            $this->SetX(100);   
                            $this->Cell($width-100,$height,$_SESSION['org']['namaorganisasi'],0,1,'L');	 
                            $this->SetX(100); 		
                            $this->Cell($width-100,$height,$orgData[0]['alamat'],0,1,'L');	
                            $this->SetX(100); 			
                            $this->Cell($width-100,$height,"Tel: ".$orgData[0]['telepon'],0,1,'L');	
                            $this->Line($this->lMargin,$this->tMargin+($height*3),
                            $this->lMargin+$width,$this->tMargin+($height*3));

                            $this->SetFont('Arial','B',10);

                                            $this->Cell($width,$height,"Laporan Biaya Kegiatan Per Blok ".$kdOrg." ".$kegiatan,'',0,'C');
                                            $this->Ln();
                                            $this->Cell($width,$height,strtoupper($_SESSION['lang']['periode'])." :". tanggalnormal($tgl1)." s.d. ". tanggalnormal($tgl2),'',0,'C');
                                            $this->Ln();
                            $this->SetFont('Arial','B',10);
                            $this->SetFillColor(220,220,220);
                                            $this->Cell(8/100*$width,$height,$_SESSION['lang']['nomor'],1,0,'C',1);		
                                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['noakun'],1,0,'C',1);		
                                            $this->Cell(30/100*$width,$height,$_SESSION['lang']['kegiatan'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['blok'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);		
                                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['jumlah'],1,1,'C',1);		
                       }

                        function Footer()
                        {
                            $this->SetY(-15);
                            $this->SetFont('Arial','I',8);
                            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
                        }
                    }
                    $pdf=new PDF('P','pt','Legal');
                    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
                    $height = 13;
                            $pdf->AddPage();
                            $pdf->SetFillColor(255,255,255);
                            $pdf->SetFont('Arial','',10);

$res=mysql_query($str);
$no=0;
$ttl=0;
while($bar=mysql_fetch_object($res))
{
    $no+=1;
    $pdf->Cell(8/100*$width,$height,$no,1,0,'C',1);		
    $pdf->Cell(12/100*$width,$height,$bar->noakun,1,0,'C',1);		
    $pdf->Cell(30/100*$width,$height,$optnamakeg[$bar->kodekegiatan],1,0,'L',1);		
    $pdf->Cell(15/100*$width,$height,$bar->kodeblok,1,0,'L',1);		
    $pdf->Cell(15/100*$width,$height,$tahuntanam[$bar->kodeblok],1,0,'C',1);		
    $pdf->Cell(15/100*$width,$height,number_format($bar->biaya),1,1,'R',1);		
    $ttl+=$bar->biaya;
}
    $pdf->Cell(80/100*$width,$height,'Total',1,0,'C',1);		
    $pdf->Cell(15/100*$width,$height,number_format($ttl),1,1,'R',1);		

                    $pdf->Output();
            
	break;
	default:
	break;
}

?>