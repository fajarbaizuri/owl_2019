<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');
require_once('lib/terbilang.php');

if(isset($_POST['proses']))
{
	$proses=$_POST['proses'];
}
else
{
	$proses=$_GET['proses'];
}

//$arr="##periode##tipeIntex##unit";
$periode=$_POST['periode'];
$tipeIntex=$_POST['tipeIntex'];
$unit=$_POST['unit'];//echo $unit;exit();
$kodeOrg=$_POST['kodeOrg'];
$brsKe=$_POST['brsKe'];
$tgl_1=tanggalsystem($_POST['tgl_1']);
$tgl_2=tanggalsystem($_POST['tgl_2']);
$kdBlok=$_POST['kdBlok'];
$nospb=$_POST['nospb'];
$kdPabrik=$_POST['kdPabrik'];



$pt='';
$ptPabrik="select induk from ".$dbname.".organisasi where kodeorganisasi='".$kdPabrik."'";
$res=mysql_query($ptPabrik);
while($bar=mysql_fetch_object($res))
{
  $pt=$bar->induk;
}


switch($proses)
{
    case'preview':
    if($tipeIntex!='')
    {
            $where.=" and intex='".$tipeIntex."'";
    }
    else
    {
            echo"warning:Pilih salah satu Sumber TBS";
            exit();
    }
	if($tipeIntex==0)
	{
			if($unit==''){
			     $where.=" and kodeorg not in (select kodeorganisasi from ".$dbname.".organisasi where tipe='KEBUN')"; 			  
			}
			else{
			    $where.=" and kodecustomer='".$unit."'";
			}
	}
	else if($tipeIntex!=0)
	{
			if($unit!=''){
				$where.=" and kodeorg='".$unit."' ";
			}
			else
			{
			  if($tipeIntex==1)
			  {
			     $where.=" and kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
				 and tipe='KEBUN')"; 
			  }
			  else
			  {
			     $where.=" and kodeorg in (select kodeorganisasi from ".$dbname.".organisasi where induk!='".$pt."' 
				 and tipe='KEBUN')"; 
			  }
			}
	}
	
    if($kdPabrik!='')
    {
            $where.=" and millcode='".$kdPabrik."'";
    }        
    if(($tgl_1!='')&&($tgl_2!=''))
    {

             $where.=" and tanggal between '".$tgl_1."000000' and '".$tgl_2."235959'";
    }
    else
    {
            echo"warning:Tanggal Tidak Boleh Kosong";
            exit();
    }
	
//print_r($_POST);
echo $_SESSION['lang']['rPenerimaanTbs'];	

    echo"<table cellspacing=1 border=0 class=sortable>
    <thead class=rowheader>
    <tr>
            <td align=center>No.</td>
            <td align=center>".$_SESSION['lang']['tanggal']."</td>
            <td align=center>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>
            <td align=center>".$_SESSION['lang']['noTiket']."</td>
            <td align=center>".$_SESSION['lang']['kodenopol']."</td>
            <td align=center>".$_SESSION['lang']['sopir']."</td>
            <td align=center>".$_SESSION['lang']['nospb']."</td>
            <td align=center>".$_SESSION['lang']['jmlhTandan']."</td>
            <td align=center>".$_SESSION['lang']['beratBersih']."</td>                
            <td align=center>Kg Potongan</td>
            <td align=center>Berat Normal</td>
            <td align=center>".$_SESSION['lang']['bjr']."</td>
    </tr>
    </thead>
    <tbody>";
    //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
    $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,kgpotsortasi from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where." order by tanggal";
    //echo $sData;
    //print_r($_POST);
	//echo "warning".$sData;exit();
    $qData=mysql_query($sData) or die(mysql_error());

    $brs=mysql_num_rows($qData);
    if($brs>0)
    {

            while($rData=mysql_fetch_assoc($qData))
            {	
                    $no+=1;

                    if($tipeIntex!=0)
                    {
                            $sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                            $qNm=mysql_query($sNm) or die(mysql_error());
                            $rNm=mysql_fetch_assoc($qNm);
                            $nm=$rNm['namaorganisasi'];
                            $kd=$rData['kodeorg'];
                            $isi=" value=".$kd."";
                    }
                    else
                    {
                            ##indra
                            $a="select * from ".$dbname.".log_5supplier";
                            $b=mysql_query($a) or die(mysql_error());
                            $c=mysql_fetch_assoc($b);

                            if($c['kodetimbangan']=='')
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
                            else
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
							//echo $sNm."Error";
                            $nm=$rNm['namasupplier'];	
                            $stat="";	
                            $isi="";
                    }

                    $bjr=$rData['netto']/$rData['jjg'];

                    echo"
                    <tr class=rowcontent>
                    <td>".$no."</td>
                    <td>".tanggalnormal($rData['tanggal'])."</td>
                    <td>".$nm."</td>
                    <td>".$rData['notransaksi']."</td>
                    <td>".$rData['nokendaraan']."</td>
                    <td>".$rData['supir']."</td>
                    <td>".$rData['nospb']."</td>
                    <td align=right>".number_format($rData['jjg'],2)."</td>
                    <td  align=right>".number_format($rData['netto'],2)."</td>                        
                    <td align=right>".number_format($rData['kgpotsortasi'],2)."</td>
                    <td align=right>".number_format($rData['netto']-$rData['kgpotsortasi'],2)."</td>
                    <td align=right>".number_format($bjr,2)."</td>
                    </tr>";
                    $subtotbjr+=$bjr;
                    $subtota+=$rData['netto'];
                    $subTnandn+=$rData['jjg'];
                    $tpot+=$rData['netto']-$rData['kgpotsortasi'];
                    $subtotkgsor+=$rData['kgpotsortasi'];
            }
            echo"<thead>
                    <tr class=rowcontent >
                            <td colspan=5 align=center>Total (KG)</td>
                            <td colspan=2 align=center>Total (JJG)</td>
                            <td align=right>".number_format($subTnandn,2)."</td>
                            <td align=right>".number_format($subtota,2)."</td>                                
                            <td align=right>".number_format($subtotkgsor,2)."</td>
                            <td align=right>".number_format($tpot,2)."</td>
                            <td align=right>".number_format($subtotbjr,2)."</td>
                    </tr>";

    }
    else
    {
            echo"<tr class=rowcontent><td colspan=10 align=center>Data Kosong</td></tr>";
    }
    break;

    case'pdf':
    $periode=$_GET['periode'];
    $tipeIntex=$_GET['tipeIntex'];
    $unit=$_GET['unit'];
    $tglPeriode=explode("-",$periode);
    $tanggal=$tglPeriode[1]."-".$tglPeriode[0];
    $tgl_1=tanggalsystem($_GET['tgl_1']);
    $tgl_2=tanggalsystem($_GET['tgl_2']);
    $kdPabrik=$_GET['kdPabrik'];


     class PDF extends FPDF
    {
        function Header() {
            global $conn;
            global $dbname;
            global $align;
            global $length;
            global $colArr;
            global $title;
                            global $tipeIntex;
                            global $periode;
                            global $unit;//echo $unit;
                            global $kdPabrik;
                            global $tgl_2;
                            global $tgl_1;
                            global $tglPeriode;
                            global $tanggal;
                            global $rNamaSupp;



                            $tglPeriode=explode("-",$periode);
                            $tanggal=$tglPeriode[1]."-".$tglPeriode[0];
            # Alamat & No Telp
   /*         $query = selectQuery($dbname,'organisasi','namaorganisasi,alamat,telepon',
                "kodeorganisasi='".$kdPt."'");
            $orgData = fetchData($query);*/
                            $sAlmat="select namaorganisasi,alamat,telepon from ".$dbname.".organisasi where kodeorganisasi='".$_SESSION['org']['kodeorganisasi']."'";
                            $qAlamat=mysql_query($sAlmat) or die(mysql_error());
                            $rAlamat=mysql_fetch_assoc($qAlamat);

            $width = $this->w - $this->lMargin - $this->rMargin;
            $height = 11;
            $path='images/logo.jpg';
            //$this->Image($path,$this->lMargin,$this->tMargin,70);	
            $this->Image($path,30,5,60);
                            $this->SetFont('Arial','B',9);
            $this->SetFillColor(255,255,255);	
            $this->SetX(100);   
            $this->Cell($width-100,$height,$rAlamat['namaorganisasi'],0,1,'L');	 
            $this->SetX(100); 		
            $this->Cell($width-100,$height,$rAlamat['alamat'],0,1,'L');	
            $this->SetX(100); 			
            $this->Cell($width-100,$height,"Tel: ".$rAlamat['telepon'],0,1,'L');	
            $this->Line($this->lMargin,$this->tMargin+($height*4),
                $this->lMargin+$width,$this->tMargin+($height*4));
            $this->Ln();	
            $this->Ln();
                            $this->Ln();
            $this->SetFont('Arial','B',11);
            $this->Cell($width,$height, $_SESSION['lang']['rPenerimaanTbs'],0,1,'C');	
                            $this->SetFont('Arial','',8);
                            $sNm="select namasupplier,kodetimbangan from ".$dbname.".log_5supplier order by namasupplier asc";
                            $qNm=mysql_query($sNm) or die(mysql_error());
                            while($rNm=mysql_fetch_assoc($qNm))
                            {
                                    $rNamaSupp[$rNm['kodetimbangan']]=$rNm;
                            }
                            $sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
                            $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                            while($rBrg=mysql_fetch_assoc($qBrg))
                            {
                                    $rNmBrg[$rBrg['kodebarang']]=$rBrg;
                            }
                            if(($kdPabrik!='')&&($unit!=''))
                            {
                            $this->Cell($width,$height, $_SESSION['lang']['terimaTbs']." : ".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$rNamaSupp[$unit]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1."-".$tgl_2,0,1,'C');	
                            }
                            else
                            {
                                    $this->Cell($width,$height, $_SESSION['lang']['terimaTbs']." : ".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." : ".$_SESSION['lang']['all'].", ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)." - ".tanggalnormal($tgl_2),0,1,'C');						
                            }
                            $this->Ln();$this->Ln();
            $this->SetFont('Arial','B',7);	
            $this->SetFillColor(220,220,220);

                            $this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                            $this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
                            $this->Cell(15/100*$width,$height,$_SESSION['lang']['namasupplier'],1,0,'C',1);		
                            $this->Cell(8/100*$width,$height,$_SESSION['lang']['noTiket'],1,0,'C',1);	
                            $this->Cell(12/100*$width,$height,$_SESSION['lang']['kodenopol'],1,0,'C',1);	
                            $this->Cell(9/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);	
                            $this->Cell(10/100*$width,$height,$_SESSION['lang']['sopir'],1,0,'C',1);			
                            $this->Cell(13/100*$width,$height,$_SESSION['lang']['nospb'],1,0,'C',1);
                            $this->Cell(10/100*$width,$height,$_SESSION['lang']['jmlhTandan'],1,0,'C',1);
                            $this->Cell(9/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,0,'C',1);	  
                            $this->Cell(5/100*$width,$height,$_SESSION['lang']['bjr'],1,1,'C',1);          
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial','I',8);
            $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
        }
    }
    $pdf=new PDF('L','pt','A4');
    $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
    $height = 9;
            $pdf->AddPage();
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','',7);
    if($tipeIntex!='')
    {
            $where.=" and intex='".$tipeIntex."'";
    }
    else
    {
            echo"warning:Pilih salah satu Sumber TBS";
            exit();
    }
    if($unit!="")
    {
            if($tipeIntex==0)
            {
                    $where.=" and kodecustomer='".$unit."'";
            }
            elseif($tipeIntex!=0)
            {
                    $where.=" and kodeorg='".$unit."' ";
            }
    }
    if(($tgl_1!='')&&($tgl_2!=''))
    {
            $where.=" and tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
    }
    else
    {
            echo"warning:Tanggal Tidak Boleh Kosong";
            exit();
    }

    if($kdPabrik!='')
    {
            $where.=" and millcode='".$kdPabrik."'";

    }		
            $sList="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,kgpotsortasi from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where;
            $qList=mysql_query($sList) or die(mysql_error());
            while($rData=mysql_fetch_assoc($qList))
            {			
                    if($tipeIntex!=0)
                    {
                            $sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                            $qNm=mysql_query($sNm) or die(mysql_error());
                            $rNm=mysql_fetch_assoc($qNm);
                            $nm=$rNm['namaorganisasi'];
                            $kd=$rData['kodeorg'];
                    }
                    else
                    {


                            ##indra
                            $a="select * from ".$dbname.".log_5supplier";
                            $b=mysql_query($a) or die(mysql_error());
                            $c=mysql_fetch_assoc($b);

                            if($c['kodetimbangan']=='')
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
                            else
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
                            $nm=$rNm['namasupplier'];	

                            /*$sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                            $qNm=mysql_query($sNm) or die(mysql_error());
                            $rNm=mysql_fetch_assoc($qNm);*/
                            //$nm=$rNamaSupp[$rData['kodecustomer']]['namasupplier'];	

                    }



                    $bjr=$rData['netto']/$rData['jjg'];

                    $no+=1;
                    $pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                    $pdf->Cell(8/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
                    $pdf->Cell(15/100*$width,$height,$nm,1,0,'L',1);		
                    $pdf->Cell(8/100*$width,$height,$rData['notransaksi'],1,0,'L',1);	
                    $pdf->Cell(12/100*$width,$height,$rData['nokendaraan'],1,0,'L',1);	
                    $pdf->Cell(9/100*$width,$height,number_format($rData['netto'],2),1,0,'R',1);	
                    $pdf->Cell(10/100*$width,$height,$rData['supir'],1,0,'L',1);			
                    $pdf->Cell(13/100*$width,$height,$rData['nospb'],1,0,'L',1);
                    $pdf->Cell(10/100*$width,$height,number_format($rData['jjg'],2),1,0,'R',1);
                    $pdf->Cell(9/100*$width,$height,$rData['thntm1'],1,0,'C',1);
                    $pdf->Cell(5/100*$width,$height,number_format($bjr,2),1,1,'R',1);
                    /*$pdf->Cell(18/100*$width,$height,$nm,1,0,'C',1);		
                    $pdf->Cell(12/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
                    $pdf->Cell(10/100*$width,$height,number_format($rData['jjg']),1,0,'R',1);			
                    $pdf->Cell(15/100*$width,$height,number_format($rData['netto'],2),1,1,'R',1);*/
                    $subtota+=$rData['netto'];
                    $subjjg+=$rData['jjg'];
                    $subtotbjr+=$bjr;
            }
            $pdf->SetFillColor(220,220,220);
            $pdf->Cell(46/100*$width,$height,"Total",1,0,'C',1);
            $pdf->Cell(9/100*$width,$height,number_format($subtota,2),1,0,'R',1);
            $pdf->Cell(23/100*$width,$height,"Total",1,0,'C',1);
            $pdf->Cell(10/100*$width,$height,number_format($subjjg,2),1,0,'R',1);
            $pdf->Cell(9/100*$width,$height,'--------',1,0,'C',1);
            $pdf->Cell(5/100*$width,$height,number_format($subtotbjr,2),1,0,'R',1);

$pdf->Output();
    break;
    case'excel':
    $periode=$_GET['periode'];
    $tipeIntex=$_GET['tipeIntex'];
    $unit=$_GET['unit'];
    $tglPeriode=explode("-",$periode);
    $tanggal=$tglPeriode[1]."-".$tglPeriode[0];
    $tgl_1=tanggalsystem($_GET['tgl_1']);
    $tgl_2=tanggalsystem($_GET['tgl_2']);
    $kdPabrik=$_GET['kdPabrik'];
    if($tipeIntex!='')
    {
            $where.=" and intex='".$tipeIntex."'";
    }
    else
    {
            echo"warning:Pilih salah satu Sumber TBS";
            exit();
    }
    if($unit!="")
    {
            if($tipeIntex==0)
            {
                    $where.=" and kodecustomer='".$unit."'";
            }
            elseif($tipeIntex!=0)
            {
                    $where.=" and kodeorg='".$unit."' ";
            }
    }
    if(($tgl_1!='')&&($tgl_2!=''))
    {
            $where.=" and tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
    }
    else
    {
            echo"warning:Tanggal Tidak Boleh Kosong";
            exit();
    }

    if($kdPabrik!='')
    {
            $where.=" and millcode='".$kdPabrik."'";

    }
    $sNm="select namasupplier,kodetimbangan from ".$dbname.".log_5supplier order by namasupplier asc";
    $qNm=mysql_query($sNm) or die(mysql_error());
    while($rNm=mysql_fetch_assoc($qNm))
    {
            $rNamaSupp[$rNm['kodetimbangan']]=$rNm;
    }
    $sBrg="select kodebarang,namabarang from ".$dbname.".log_5masterbarang where kelompokbarang='400'";
    $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
    while($rBrg=mysql_fetch_assoc($qBrg))
    {
            $rNmBrg[$rBrg['kodebarang']]=$rBrg;
    }

    $tab.="<table cellspacing=\"1\" border=0><tr><td colspan=10 align=center>".$_SESSION['lang']['rPenerimaanTbs']."</td></tr>
    ";
    if(($kdPabrik!='')&&($unit!=''))
    {
            $tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['terimaTbs']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$rNamaSupp[$unit]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1."-".$tgl_2."</td></tr>";
    }
    else
    {
            $tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['terimaTbs']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[40000003]['namabarang']." ".$_SESSION['lang']['dari']." ".$_SESSION['lang']['all']." ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)."-".tanggalnormal($tgl_2)."</td></tr>";
    }
    $tab.="</table>";

    $tab.="<table cellspacing=1 border=1 class=sortable>
    <thead class=rowheader>
    <tr>
            <td align=center bgcolor=#DEDEDE>No.</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['namasupplier']."/".$_SESSION['lang']['unit']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['noTiket']."</td>
            <td align=center align=center bgcolor=#DEDEDE>".$_SESSION['lang']['kodenopol']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['sopir']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['nospb']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['jmlhTandan']."</td>
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']."</td>                
            <td align=center bgcolor=#DEDEDE>Potongan</td>
            <td align=center bgcolor=#DEDEDE>Normal(Kg)</td>            
            <td align=center bgcolor=#DEDEDE>".$_SESSION['lang']['bjr']."</td>
    </tr>
    </thead>
    <tbody>";
    //notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
    $sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nospb,thntm1,kgpotsortasi from ".$dbname.".pabrik_timbangan where kodebarang='40000003' ".$where;
    //echo "warning".$sData;exit();
    $qData=mysql_query($sData) or die(mysql_error());

    $brs=mysql_num_rows($qData);
    if($brs>0)
    {

            while($rData=mysql_fetch_assoc($qData))
            {	
                    $no+=1;

                    if($tipeIntex!=0)
                    {
                            $sNm="select namaorganisasi from ".$dbname.".organisasi where kodeorganisasi='".$rData['kodeorg']."'";
                            $qNm=mysql_query($sNm) or die(mysql_error());
                            $rNm=mysql_fetch_assoc($qNm);
                            $nm=$rNm['namaorganisasi'];
                            $kd=$rData['kodeorg'];
                            $isi=" value=".$kd."";

                            $stat=" onclick=getAfd(".$no.") style=\"cursor: pointer;\"";

                    }
                    else
                    {
                            ##indra
                            $a="select * from ".$dbname.".log_5supplier";
                            $b=mysql_query($a) or die(mysql_error());
                            $c=mysql_fetch_assoc($b);

                            if($c['kodetimbangan']=='')
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where supplierid='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
                            else
                            {
                                    $sNm="select namasupplier from ".$dbname.".log_5supplier where kodetimbangan='".$rData['kodecustomer']."'";
                                    $qNm=mysql_query($sNm) or die(mysql_error());
                                    $rNm=mysql_fetch_assoc($qNm);
                            }
                            $nm=$rNm['namasupplier'];			
                    }
                    $bjr=$rData['netto']/$rData['jjg'];
                    $tab.="
                    <tr class=rowcontent id=row_".$no." ".$stat.">
                    <td>".$no."</td>
                    <td>".substr($rData['tanggal'],0,10)."</td>
                    <td>".$nm."</td>
                    <td>".$rData['notransaksi']."</td>
                    <td>".$rData['nokendaraan']."</td>
                    <td>".$rData['supir']."</td>
                    <td>".$rData['nospb']."</td>
                    <td align=right>".number_format($rData['jjg'],2)."</td>
                    <td  align=right>".number_format($rData['netto'],2)."</td>                        
                    <td>".number_format($rData['kgpotsortasi'],2)."</td>
                    <td>".number_format($rData['netto']-$rData['kgpotsortasi'],2)."</td>    
                    <td align=right>".number_format($bjr,2)."</td>
                    </tr>";
                    $subtota+=$rData['netto'];
                    $subTnandn+=$rData['jjg'];
                    $subtotbjr+=$bjr;
                    $tpot+=$rData['kgpotsortasi'];
                    $tnor+=$rData['netto']-$rData['kgpotsortasi'];
            }
            $tab.="<tr class=rowcontent bgcolor=#DEDEDE><td colspan=7 align=center>Total (KG)</td>
                         <td align=right>".number_format($subTnandn,2)."</td>
                         <td align=right>".number_format($subtota,2)."</td>
                          <td align=right>".number_format($tpot,2)."</td>
                          <td align=right>".number_format($tnor,2)."</td>    
                          <td align=right>".number_format($subtotbjr,2)."</td></tr>";
    }
    else
    {
            $tab.="<tr class=rowcontent><td colspan=10 align=center>Data Kosong</td></tr>";
    }


                    //echo "warning:".$strx;
                    //=================================================


                    $tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
                    $tglSkrg=date("Ymd");
                    $nop_="LaporanPenerimaanTbs".$tglSkrg;
                    if(strlen($tab)>0)
                    {
                    if ($handle = opendir('tempExcel')) {
                    while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                    @unlink('tempExcel/'.$file);
                    }
                    }	
                    closedir($handle);
                    }
                    $handle=fopen("tempExcel/".$nop_.".xls",'w');
                    if(!fwrite($handle,$tab))
                    {
                    echo "<script language=javascript1.2>
                    parent.window.alert('Can't convert to excel format');
                    </script>";
                    exit;
                    }
                    else
                    {
                    echo "<script language=javascript1.2>
                    window.location='tempExcel/".$nop_.".xls';
                    </script>";
                    }
                    closedir($handle);
                    }
    break;
    default:
    break;
}
?>