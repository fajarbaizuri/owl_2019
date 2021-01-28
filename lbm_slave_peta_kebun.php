<?
require_once('master_validation.php');
//require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

//$proses=$_GET['proses'];
$_POST['proses']==''?$proses=$_GET['proses']:$proses=$_POST['proses'];
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
//$_POST['periode']==''?$periode=$_GET['periode']:$periode=$_POST['periode'];
$periode='2011-01';
$qwe=explode('-',$periode);
$tahun=$qwe[0];
$bulan=$qwe[1];

$optBulan['01']=$_SESSION['lang']['jan'];
$optBulan['02']=$_SESSION['lang']['peb'];
$optBulan['03']=$_SESSION['lang']['mar'];
$optBulan['04']=$_SESSION['lang']['apr'];
$optBulan['05']=$_SESSION['lang']['mei'];
$optBulan['06']=$_SESSION['lang']['jun'];
$optBulan['07']=$_SESSION['lang']['jul'];
$optBulan['08']=$_SESSION['lang']['agt'];
$optBulan['09']=$_SESSION['lang']['sep'];
$optBulan['10']=$_SESSION['lang']['okt'];
$optBulan['11']=$_SESSION['lang']['nov'];
$optBulan['12']=$_SESSION['lang']['dec'];

$optNm=makeOption($dbname, 'organisasi', 'kodeorganisasi,namaorganisasi');
$optPt=makeOption($dbname, 'organisasi', 'kodeorganisasi,induk');

?>
    <div id="progress" style="border: 1px solid orange; width: 150px; position: fixed; right: 20px; top: 65px; color: rgb(255, 0, 0); font-family: Tahoma; font-size: 13px; font-weight: bolder; text-align: center; background-color: rgb(255, 255, 255); z-index: 10000; display: none;">
    Please wait.....!
    <br>
    <img src="images/progress.gif">
    </div>
    <script ype="text/javascript" src="js/generic.js"></script>
    <script type="text/javascript" src="js/lbm_peta_kebun.js"></script>
    <link rel=stylesheet type='text/css' href='style/generic.css'>
<?

switch($proses)
{
    case'legend':
    $viewbox=''; 
    $kegiatan="SELECT * FROM ".$dbname.".setup_blok WHERE kodeorg = '".$unit."'";
    $query=mysql_query($kegiatan) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $viewbox.='<table class=sortable border=0 cellspacing=1>';
        $viewbox.='<tr class=rowcontent><td>kodeorg</td><td>'.$res['kodeorg'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>tahuntanam</td><td>'.$res['tahuntanam'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>luas</td><td>'.number_format($res['luasareaproduktif'],2).'+'.number_format($res['luasareanonproduktif'],2).' ha</td></tr>';
        $viewbox.='<tr class=rowcontent><td>pokok</td><td>'.$res['jumlahpokok'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>status</td><td>'.$res['statusblok'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>tahuntanam</td><td>'.$res['tahuntanam'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>mulaipanen</td><td>'.$res['tahunmulaipanen'].'-'.$res['bulanmulaipanen'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>tanah</td><td>'.$res['kodetanah'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>klasifikasi tanah</td><td>'.$res['klasifikasitanah'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>topografi</td><td>'.$res['topografi'].'</td></tr>';
        $viewbox.='<tr class=rowcontent><td>bibit</td><td>'.$res['jenisbibit'].'</td></tr>';
    }
    echo $viewbox; 
    break;

    case'pdf':
    if($unit==''||$periode=='')
    {
        exit("Error:Field Tidak Boleh Kosong.".$unit."_".$periode);
    }

    $kegiatan="SELECT * FROM ".$dbname.".kebun_peta WHERE kodeorg = '".$unit."'";
    $query=mysql_query($kegiatan) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $viewbox=$res['viewbox'];
    }    
    $asd=explode(' ',$viewbox);
    $viewbox=$asd[0].' '.$asd[1].' '.$asd[2].' '.$asd[2]; // make sure that the viewbox is square, abandon $asd[3]
//    $modifx=$asd[3]/10; buat tulisan
//    $modify=$asd[3]/30;
//    $vbx=$asd[0]-$modifx;
//    $vby=$asd[1]+$modify; 

    // legend
    echo'<div class=rowcontent id=legend style="position: absolute; top: 10px; right: 10px; width: "100%"; background-color: azure;">
    </div>';
    
    echo'<svg version="1.1" baseProfile="full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
        xml:space="preserve" preserveAspectRatio="xMinYMin meet"  width="100%" height="100%" viewBox="'.$viewbox.'">';
    $id=0;
    // display all div
    $kegiatan="SELECT * FROM ".$dbname.".kebun_peta WHERE kodeorg like '".$unit."%'";
    //echo $kegiatan;
    $query=mysql_query($kegiatan) or die(mysql_error($conn));
    while($res=mysql_fetch_assoc($query))
    {
        $id+=1;
        $div=substr($res['kodeorg'],4,2);
        $warna='white';
        if($div=='01')$warna='#C69633';
        if($div=='02')$warna='#963939';
        if($div=='03')$warna='#C3690F';
        if($div=='04')$warna='#6C3306';
        if($div=='05')$warna='#6C3336';
        if($div=='06')$warna='#C96333';
        if($div=='07')$warna='#FF6F00';
        if($div=='08')$warna='#9F3333';
        if($div=='09')$warna='#C9960C';
        if($div=='10')$warna='#96693F';
        echo'<g id="'.$res['kodeorg'].'" style="display:inline;fill-rule:evenodd">';
        echo'<desc>Layer '.$res['kodeorg'].'</desc>';
        echo'<path id="'.$id.'" d="'.$res['path'].'" title=\''.$res['kodeorg'].'\'
            onmouseover="evt.target.setAttribute(\'opacity\', \'0.5\'); gantul(\''.$res['kodeorg'].'\')"
            onmouseout="evt.target.setAttribute(\'opacity\', \'1\'); gantul(\'\')"
            onclick="tampilpeta(\''.$res['kodeorg'].'\')"
            style="fill:'.$warna.';stroke-linejoin:round;stroke:black;stroke-width:10;cursor:pointer;"/>';
        echo'</a>';
        echo'</g>';
//    echo'<text x="'.$res['textx'].'" y="'.$res['texty'].'" style="font-family:Arial;fill:black;font-size:47.8503;">';
//    echo$res['kodeorg'];
//    echo'</text>';
    }
//    echo'<g id="'.$unit.'" style="display:inline;fill-rule:evenodd">';
//        echo'<desc>Layer '.$unit.'</desc>';
//        echo'<text id="text'.$unit.'" x="'.$vbx.'" y="'.$vby.'" style="font-family:Arial;fill:black;font-size:'.$modify.';">
//            '.$unit.'
//            </text>';
//    echo'</g>';
    
    echo'</svg>';    





/*    
echo'    
<?xml version="1.0" encoding="iso-8859-1"?>
<svg version="1.1" baseProfile="full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" preserveAspectRatio="xMinYMin meet" width="100%" height="100%" viewBox="541156.5 -79177.7 999.6 1140.5">
<g id="blok_1" style="display:inline;fill-rule:evenodd">
<desc>Layer blok_1</desc>
<path id="1" d="M541216.6,-78364.5 l -2.4,238.9 1.2,27.1 105.9,18.8 37.7,-3.5 84.7,-36.5 41.2,-22.4 62.4,-9.4 67.1,-7.1 40.0,-4.7 31.8,17.6 21.2,0.0 147.1,-4.7 24.7,-14.1 22.4,-21.2 21.2,-2.3 17.7,10.6 42.4,-14.1 17.7,0.0 25.9,15.3 18.8,14.1
 34.1,10.6 30.6,0.0 -4.7,-53.0 -11.8,-28.2 -2.4,-50.6 -9.4,-101.2 0.0,-51.8 -108.3,17.6 -74.1,4.7 -20.0,-4.7 -30.6,-25.9 -60.0,-34.1 -53.0,-11.8 -63.5,33.0 -27.1,10.6 -60.0,3.5 -54.1,3.5 -38.8,10.6 -47.1,7.1 -48.2,2.4 -20.0,-1.2 -94.2,44.7 -56.5,14.1
 -9.4,-1.2 0.0,0.0" style="fill:none;stroke-linejoin:round;stroke:black;stroke-width:2.65856;"/>
</g>
<g id="blok_2" style="display:inline;fill-rule:evenodd">
<desc>Layer blok_2</desc>
<path id="1" d="M541216.4,-78374.2 l 60.6,-8.2 52.3,-27.7 42.1,-20.5 18.5,2.0 39.0,-1.0 74.9,-9.2 31.8,-10.3 83.1,-4.1 40.0,-5.1 35.9,-21.5 43.1,-19.5 54.4,12.3 43.1,27.7 31.8,19.5 23.6,17.4 39.0,1.0 65.7,-7.2 51.3,-10.3 35.9,-8.2 4.1,-40.0 -9.2,-43.1
 -14.4,-52.3 1.0,-60.6 18.5,-32.8 16.4,-10.3 6.2,-7.2 0.0,-5.1 -47.2,-7.2 -38.0,-9.2 -12.3,-4.1 -29.8,1.0 -26.7,13.3 -20.5,4.1 -22.6,0.0 -42.1,-16.4 -35.9,-23.6 -15.4,-18.5 -15.4,-8.2 -17.4,-2.0 -27.7,8.2 -23.6,9.2 -28.7,22.6 -16.4,4.1 -59.5,14.4
 -51.3,10.3 -36.9,27.7 -30.8,14.4 -34.9,11.3 -38.0,0.0 -36.9,-2.1 -45.2,5.1 -19.5,3.1 -30.8,-7.2 -32.8,-16.4 -36.9,-11.3 -21.6,-7.2 -1.0,38.0 6.2,27.7 -4.1,38.0 0.0,68.8 0.0,69.8 4.1,65.7" style="fill:none;stroke-linejoin:round;stroke:rgb(255,0,0);stroke-width:2.65856;"/>
</g>
<g id="blok_3" style="display:inline;fill-rule:evenodd">
<desc>Layer blok_3</desc>
<path id="1" d="M541215.7,-78911.9 l -4.4,218.5 69.4,20.7 22.1,13.3 32.5,8.9 54.6,-7.4 59.1,2.9 36.9,-4.4 48.7,-20.7 38.4,-28.1 79.7,-17.7 48.7,-11.8 39.9,-28.1 36.9,-11.8 32.5,2.9 36.9,34.0 48.7,26.6 39.9,5.9 23.6,-10.3 25.1,-10.3 29.5,1.5 39.9,13.3
 42.8,8.9 -16.2,-29.5 10.3,-155.0 4.4,-73.8 -1.5,-81.2 -1.5,-100.4 -35.4,-1.5 -31.0,23.6 -23.6,31.0 -44.3,26.6 -65.0,5.9 -13.3,8.9 -62.0,0.0 -47.2,-2.9 -56.1,11.8 -88.6,34.0 -22.1,8.9 -32.5,20.7 -29.5,7.4 -60.5,10.3 -32.5,-4.4 -16.2,5.9 -47.2,14.8
 -54.6,8.9 -29.5,7.4 -60.5,17.7 -25.1,-1.5 Z" style="fill:rgb(255,0,0);stroke:black;stroke-width:2.65856;"/>
</g>
<g id="nama_blok" style="display:inline;fill-rule:evenodd">
<desc>Layer nama_blok</desc>
<text x="541494.1" y="-78808.1" style="font-family:Arial;fill:black;font-size:47.8503;">
blok 3
</text>
<text x="541515.4" y="-78539.6" style="font-family:Arial;fill:black;font-size:47.8503;">
blok 2
</text>
<text x="541518.0" y="-78265.8" style="font-family:Arial;fill:black;font-size:47.8503;">
blok 1
</text>
</g>
</svg>';    
 * 
 */

//   class PDF extends FPDF {
//    }
//
//    $pdf=new PDF('L','pt','A4');
//    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
//    $allwidth = $pdf->w;
//    $lmargin=$pdf->lMargin;
//    $tmargin=$pdf->tMargin;
//    $rmargin=$pdf->rMargin;
//    $pdf->SetMargins($lmargin,$tmargin,$rmargin);
//    $height = 10;
//    $pdf->AddPage();
//    $pdf->SetFillColor(255,255,255);
//    $pdf->SetFont('Arial','',7);
//    
//    //crown + monanga group
//    $path='images/lbm_cover1.jpg';
//    $pdf->Image($path,0,0,841);	
//    $path='images/lbm_cover2.jpg';
//    $pdf->Image($path,80,130,470);
//    
//    //nama pt
//    $pdf->SetY(220);   
//    $pdf->SetFont('Arial','',30);
//    $pdf->SetX(80);   
//    $pdf->Cell(140,$height,$optNm[$optPt[$unit]],0,0,'L',1);
//
//    //garis tengah
//    $pdf->SetMargins(0,0,0);
//    $pdf->SetY(270);   
//    $pdf->SetFillColor(255,128,0);
//    $pdf->Cell($allwidth,$height,'',0,0,'L',1);
//    $pdf->SetY(280);   
//    $pdf->SetFillColor(0,192,64);
//    $pdf->Cell($allwidth,$height,'',0,0,'L',1);
//    
//    //LBM
//    $pdf->SetY(300);   
//    $pdf->SetFillColor(255,255,255);
//    $pdf->SetMargins($lmargin,$tmargin,$rmargin);
//    $pdf->SetY(350);   
//    $pdf->Cell($allwidth,$height,strtoupper($_SESSION['lang']['lbm']),0,0,'C',1);
//    
//    //managerial report
//    $pdf->SetY(380);   
//    $pdf->SetFont('Arial','',20);
//    $pdf->Cell($width,$height,$_SESSION['lang']['managerialreport'],0,0,'C',1);
//    
//    //estate, bulan, tahun
//    $pdf->SetY(430);   
//    $pdf->SetX(150);   
//    $pdf->Cell(100,$height,$_SESSION['lang']['unit'],0,0,'L',1);
//    $pdf->Cell(140,$height,' : '.$optNm[$unit].' ('.$unit.')',0,0,'L',1);
//    $pdf->SetY(460);   
//    $pdf->SetX(150);   
//    $pdf->Cell(100,$height,$_SESSION['lang']['bulan'],0,0,'L',1);
//    $pdf->Cell(140,$height,' : '.$optBulan[$bulan],0,0,'L',1);
//    $pdf->SetY(490);   
//    $pdf->SetX(150);   
//    $pdf->Cell(100,$height,$_SESSION['lang']['tahun'],0,0,'L',1);
//    $pdf->Cell(140,$height,' : '.$tahun,0,0,'L',1);
//
//    $pdf->Output();	
    break;
    default:
    break;
}
	





// dz: dec 26, 2011 10:06 - pintu besar utara 6-8
?>