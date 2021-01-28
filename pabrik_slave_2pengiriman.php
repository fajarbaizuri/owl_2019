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

//$arr="##kdPabrik##tgl_1##tgl_2##kdCust##nkntrak##kdBrg";
$kdPabrik=$_POST['kdPabrik'];
$kdCust=$_POST['kdCust'];
$nkntrak=$_POST['nkntrak'];
$kdBrg=$_POST['kdBrg'];
$tgl_1=tanggalsystem($_POST['tgl_1']);
$tgl_2=tanggalsystem($_POST['tgl_2']);
$tgl1=tanggalsystem($_POST['tgl1']);
$tgl2=tanggalsystem($_POST['tgl2']);
$kdCustomer=$_POST['kdCustomer'];


switch($proses)
{
	case'preview':
         if($kdPabrik=='')
         {
             echo"warning:Silahkan Pilih Pabrik Yang Anda Inginkan";
             exit();
         }
         if($tgl_1==''&&$tgl_2=='')
         {
             echo "warning:Tanggal Tidak Boleh Kosong";
             exit();
         }
         if($nkntrak=='' && $kdBrg=='' && $kdCust!='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where nokontrak in (select nokontrak from ".$dbname.".pmn_kontrakjual where koderekanan='".$kdCust."') and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak==''  && $kdBrg=='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where kodebarang like '400000%' and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak=='' && $kdBrg!='')
         {
         $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where kodebarang ='".$kdBrg."' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         else
         {
               $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where nokontrak ='".$nkntrak."' and millcode='".$kdPabrik."' 
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959"; 
         }
            // echo $sTimbangan;

	

	echo"<table cellspacing=1 border=0 class=sortable>
	<thead class=rowheader>
	<tr>
		<td>No.</td>
                <td>".$_SESSION['lang']['materialname']."</td>
		<td>".$_SESSION['lang']['tanggal']."</td>
		<td>".$_SESSION['lang']['vendor']."</td>
		<td>".$_SESSION['lang']['noTiket']."</td>
		<td>".$_SESSION['lang']['kodenopol']."</td>
		<td>".$_SESSION['lang']['beratBersih']."</td>
                <td>".$_SESSION['lang']['beratBersih']." ".substr($_SESSION['lang']['kodecustomer'],5)."</td>
		<td>".$_SESSION['lang']['sopir']."</td>
		<td>".$_SESSION['lang']['nodo']."</td>
		<td>".$_SESSION['lang']['NoKontrak']."</td>
                
	</tr>
	</thead>
	<tbody>";
	//notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
//	$sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
//            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak
//            from ".$dbname.".pabrik_timbangan where kodebarang in ('40000002','40000001') ".$where;
        //echo $sData;
        //echo "warning".$sData;exit();
	$qData=mysql_query($sTimbangan) or die(mysql_error());
	$brs=mysql_num_rows($qData);
	if($brs>0)
	{
		
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
			
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                        $rBrg=mysql_fetch_assoc($qBrg);
                        $sKntrk="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
                        $qKntrk=mysql_query($sKntrk) or die(mysql_error());
                        $rKntrak=mysql_fetch_assoc($qKntrk);
                        $sNama="select namacustomer from ".$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
                        $qNama=mysql_query($sNama) or die(mysql_error($conn));
                        $rNama=mysql_fetch_assoc($qNama);
			
			echo"
			<tr class=rowcontent>
			<td>".$no."</td>
                        <td>".$rBrg['namabarang']."</td>
			<td>".tanggalnormal($rData['tanggal'])."</td>
			<td>".$rNama['namacustomer']."</td>
			<td>".$rData['notransaksi']."</td>
			<td>".$rData['nokendaraan']."</td>
			<td  align=right>".number_format($rData['netto'],2)."</td>
                        <td  align=right>".number_format($rData['kgpembeli'],2)."</td>
			<td>".$rData['supir']."</td>
			<td>".$rData['nodo']."</td>
			<td>".$rData['nokontrak']."</td>
			</tr>";
			$subtota+=$rData['netto'];
			
		}
		echo"<tr class=rowcontent ><td colspan=6 align=right>Total (KG)</td><td align=right>".number_format($subtota,2)."</td><td colspan=4 align=right>&nbsp;</td></tr>";
		
	}
	else
	{
		echo"<tr class=rowcontent><td colspan=11 align=center>Data Kosong</td></tr>";
	}
        echo"</tbody></table>";
	break;
	case'pdf':
        $kdCust=$_GET['kdCust'];
        
	$nkntrak=$_GET['nkntrak'];
	$kdBrg=$_GET['kdBrg'];
	$tglPeriode=explode("-",$periode);
	$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
	$tgl_1=tanggalsystem($_GET['tgl_1']);
	$tgl_2=tanggalsystem($_GET['tgl_2']);
	$kdPabrik=$_GET['kdPabrik'];
	
	$rNmBrg=array();
	 class PDF extends FPDF
        {
            function Header() {
                global $conn;
                global $dbname;
                global $align;
                global $length;
                global $colArr;
                global $title;
				global $kdCust;
				global $nkntrak;
				global $kdBrg;
				global $kdPabrik;
				global $tgl_2;
				global $tgl_1;
				global $tglPeriode;
				global $tanggal;
				global $rNamaSupp;
                                global $rNmBrg;
				
				
				
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
                $this->Image($path,$this->lMargin,$this->tMargin,70);	
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
                $this->Cell($width,$height, $_SESSION['lang']['rPengiriman'],0,1,'C');	
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
				$this->Cell($width,$height, $_SESSION['lang']['rPengiriman']." : ".$kdPabrik." atas ".$rNmBrg[$kdBrg]['namabarang']." ".$_SESSION['lang']['ke']." ".$rNamaSupp[$unit]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1."-".$tgl_2,0,1,'C');	
				}
				else
				{
					$this->Cell($width,$height, $_SESSION['lang']['rPengiriman']." : ".$kdPabrik." atas ".$rNmBrg[$kdBrg]['namabarang']." ".$_SESSION['lang']['ke']." : ".$_SESSION['lang']['all'].", ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)." - ".tanggalnormal($tgl_2),0,1,'C');						
				}
				$this->Ln();$this->Ln();
                $this->SetFont('Arial','B',6);	
                $this->SetFillColor(220,220,220);
					
				$this->Cell(3/100*$width,$height,'No',1,0,'C',1);
                                $this->Cell(15/100*$width,$height,$_SESSION['lang']['materialname'],1,0,'C',1);
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['tanggal'],1,0,'C',1);		
				$this->Cell(15/100*$width,$height,$_SESSION['lang']['vendor'],1,0,'C',1);		
				$this->Cell(8/100*$width,$height,$_SESSION['lang']['noTiket'],1,0,'C',1);	
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['kodenopol'],1,0,'C',1);	
				$this->Cell(9/100*$width,$height,$_SESSION['lang']['beratBersih'],1,0,'C',1);	
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['sopir'],1,0,'C',1);			
				$this->Cell(10/100*$width,$height,$_SESSION['lang']['nodo'],1,0,'C',1);
				$this->Cell(12/100*$width,$height,$_SESSION['lang']['NoKontrak'],1,1,'C',1);
				//$this->Cell(9/100*$width,$height,$_SESSION['lang']['tahuntanam'],1,1,'C',1);	            
            }
                
            function Footer()
            {
                $this->SetY(-15);
                $this->SetFont('Arial','I',8);
                $this->Cell(10,10,'Page '.$this->PageNo(),0,0,'C');
            }
        }
        $pdf=new PDF('P','pt','A4');
        $width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
        $height = 9;
		$pdf->AddPage();
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',6);
	
//	if(($tgl_1!='')&&($tgl_2!=''))
//	{
//		
//                 $where.=" and tanggal >= ".$tgl_1."000001 and tanggal<=".$tgl_2."235959";
//	}
//	else
//	{
//		echo"warning:Tanggal Tidak Boleh Kosong";
//		exit();
//	}
//	
//	if($kdPabrik!='')
//	{
//		$where.=" and millcode='".$kdPabrik."'";
//	}        
//        if($kdBrg!='')
//        {
//            $where.=" and kodebarang='".$kdBrg."'";
//        }
//        if($kdCust!='')
//        {
//           
//                $sGet="select distinct nokontrak from ".$dbname.".pmn_kontrakjual where koderekanan='".$kdCust."' order by nokontrak desc limit 0,1";
//                //echo "warning".$sGet;exit();
//                $qGet=mysql_query($sGet) or die(mysql_error($conn));
//                $rGet=mysql_fetch_assoc($qGet);
//                
//               $sGetKdCustmer="select kodecustomer from ".$dbname.".pabrik_timbangan where nokontrak='".$rGet['nokontrak']."'";
//               echo "warning".$sGetKdCustmer;exit();
//               $qGetKdCustmer=mysql_query($sGetKdCustmer) or die(mysql_error());
//               $rGetKdCustmer=mysql_fetch_assoc($qGetKdCustmer);
//               $where.=" and kodecustomer='".$rGetKdCustmer['kodecustomer']."'";
////            }
////            else
////            {
////              $rCustId=mysql_fetch_assoc($qCustId);  
////              $where.=" and kodecustomer='".$rCustId['kodetimbangan']."'";
////             }
//            //$where.=" and kodecustomer='".$kdCust."'";
//        }
         if($kdPabrik=='')
         {
             echo"warning:Silahkan Pilih Pabrik Yang Anda Inginkan";
             exit();
         }
         if($tgl_1==''&&$tgl_2=='')
         {
             echo "warning:Tanggal Tidak Boleh Kosong";
             exit();
         }
         if($nkntrak=='' && $kdBrg=='' && $kdCust!='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan
                 where nokontrak in (select nokontrak from ".$dbname.".pmn_kontrakjual where koderekanan='".$kdCust."') and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak==''  && $kdBrg=='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan
                 where kodebarang like '400000%' and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak=='' && $kdBrg!='')
         {
         $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan
                 where kodebarang ='".$kdBrg."' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         else
         {
               $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan
                 where nokontrak ='".$nkntrak."' and millcode='".$kdPabrik."' 
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959"; 
         }
		//$sList="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan where nokontrak!='' ".$where;
                
		$qList=mysql_query($sTimbangan) or die(mysql_error());
		while($rData=mysql_fetch_assoc($qList))
		{			
                    $sKntrk="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
                    $qKntrk=mysql_query($sKntrk) or die(mysql_error());
                    $rKntrak=mysql_fetch_assoc($qKntrk);
                    $sNama="select namacustomer from ".$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
                    $qNama=mysql_query($sNama) or die(mysql_error($conn));
                    $rNama=mysql_fetch_assoc($qNama);
                    $sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
                    $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                    $rBrg=mysql_fetch_assoc($qBrg);
				
			
			$no+=1;
			$pdf->Cell(3/100*$width,$height,$no,1,0,'C',1);
                        $pdf->Cell(15/100*$width,$height,$rBrg['namabarang'],1,0,'L',1);
			$pdf->Cell(8/100*$width,$height,tanggalnormal($rData['tanggal']),1,0,'C',1);		
			$pdf->Cell(15/100*$width,$height,$rNama['namacustomer'],1,0,'L',1);		
			$pdf->Cell(8/100*$width,$height,$rData['notransaksi'],1,0,'L',1);	
			$pdf->Cell(12/100*$width,$height,$rData['nokendaraan'],1,0,'L',1);	
			$pdf->Cell(9/100*$width,$height,number_format($rData['netto'],2),1,0,'R',1);	
			$pdf->Cell(10/100*$width,$height,$rData['supir'],1,0,'L',1);			
			$pdf->Cell(10/100*$width,$height,$rData['nodo'],1,0,'L',1);
			$pdf->Cell(12/100*$width,$height,$rData['nokontrak'],1,1,'L',1);
			//$pdf->Cell(9/100*$width,$height,$rData['nokontrak'],1,1,'C',1);
			$subtota+=$rData['netto'];
			$subjjg+=$rData['jjg'];
		}
		$pdf->Cell(61/100*$width,$height,"Total",1,0,'R',1);
		$pdf->Cell(9/100*$width,$height,number_format($subtota,2),1,0,'R',1);
		$pdf->Cell(32/100*$width,$height,"",1,1,'C',1);
		//$pdf->Cell(10/100*$width,$height,number_format($subjjg,2),1,1,'R',1);
		
			
    $pdf->Output();
	break;
	case'excel':
         //   $arr="##kdPabrik##tgl_1##tgl_2##kdCust##nkntrak##kdBrg";
	$kdCust=$_GET['kdCust'];
	$nkntrak=$_GET['nkntrak'];
	$kdBrg=$_GET['kdBrg'];
	$tglPeriode=explode("-",$periode);
	$tanggal=$tglPeriode[1]."-".$tglPeriode[0];
	$tgl_1=tanggalsystem($_GET['tgl_1']);
	$tgl_2=tanggalsystem($_GET['tgl_2']);
	$kdPabrik=$_GET['kdPabrik'];
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
	
	$tab.="<table cellspacing=\"1\" border=0><tr><td colspan=10 align=center>".$_SESSION['lang']['rPengiriman']."</td></tr>
	";
	if(($kdPabrik!='')&&($kdCust!=''))
	{
		$tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['pengirimanBrg']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[$kdBrg]['namabarang']." ".$_SESSION['lang']['ke']." ".$rNamaSupp[$kdCust]['namasupplier']." ".$_SESSION['lang']['periode']." :".$tgl_1."-".$tgl_2."</td></tr>";
	}
	else
	{
		$tab.="<tr><td colspan=2 align=right>".$_SESSION['lang']['pengirimanBrg']."</td><td colspan=8>".$kdPabrik." atas ".$rNmBrg[$kdBrg]['namabarang']." ".$_SESSION['lang']['ke']." ".$_SESSION['lang']['all']." ".$_SESSION['lang']['periode']." :".tanggalnormal($tgl_1)."-".tanggalnormal($tgl_2)."</td></tr>";
	}
	$tab.="</table>";

	
//	if(($tgl_1!='')&&($tgl_2!=''))
//	{
//		
//                 $where.=" and tanggal >= ".$tgl_1."000001 and tanggal<=".$tgl_2."235959";
//	}
//	else
//	{
//		echo"warning:Tanggal Tidak Boleh Kosong";
//		exit();
//	}
//	
//	if($kdPabrik!='')
//	{
//		$where.=" and millcode='".$kdPabrik."'";
//	}        
//        if($kdBrg!='')
//        {
//            $where.=" and kodebarang='".$kdBrg."'";
//        }
//        if($kdCust!='')
//        {
//            //$where.=" and kodecustomer='".$kdCust."'";
//             $SCustId="select kodetimbangan from ".$dbname.".pmn_4customer where kodecustomer='".$kdCust."'";
//            // echo"warning".$SCustId;exit();
//            $qCustId=mysql_query($sCustId) or die(mysql_error($conn));
//            $rCustId=mysql_fetch_assoc($qCustId);
//            $where.=" and kodecustomer='".$rCustId['kodetimbangan']."'";
//        }
        if($kdPabrik=='')
         {
             echo"warning:Silahkan Pilih Pabrik Yang Anda Inginkan";
             exit();
         }
         if($tgl_1==''&&$tgl_2=='')
         {
             echo "warning:Tanggal Tidak Boleh Kosong";
             exit();
         }
        if($nkntrak=='' && $kdBrg=='' && $kdCust!='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where nokontrak in (select nokontrak from ".$dbname.".pmn_kontrakjual where koderekanan='".$kdCust."') and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak==''  && $kdBrg=='')
         {
             $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where kodebarang like '400000%' and kodebarang!='40000003' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         elseif($nkntrak=='' && $kdBrg!='')
         {
         $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where kodebarang ='".$kdBrg."' and millcode='".$kdPabrik."'
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959";
         }
         else
         {
               $sTimbangan="select kodebarang,notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,
            substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak,kgpembeli from ".$dbname.".pabrik_timbangan
                 where nokontrak ='".$nkntrak."' and millcode='".$kdPabrik."' 
                     and  tanggal >= ".$tgl_1."000000 and tanggal<=".$tgl_2."235959"; 
         }
	

	$tab.="<table cellspacing=1 border=1 class=sortable>
	<thead class=rowheader>
	<tr>
		<td bgcolor=#DEDEDE> No.</td>
                <td bgcolor=#DEDEDE>".$_SESSION['lang']['materialname']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['tanggal']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['vendor']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['noTiket']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['kodenopol']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']."</td>
                <td bgcolor=#DEDEDE>".$_SESSION['lang']['beratBersih']." ".substr($_SESSION['lang']['kodecustomer'],5)."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['sopir']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['nodo']."</td>
		<td bgcolor=#DEDEDE>".$_SESSION['lang']['NoKontrak']."</td>
	</tr>
	</thead>
	<tbody>";
	//notransaksi, tanggal, kodeorg, kodecustomer, bjr, jumlahtandan1, kodebarang, jammasuk, beratmasuk, jamkeluar, beratkeluar, nokendaraan, supir, nospb, petugassortasi, timbangonoff, statussortasi, nokontrak, nodo, intex, nosipb, thntm1, thntm2, thntm3, jumlahtandan2, jumlahtandan3, brondolan, username, millcode, beratbersih
	$sData="select notransaksi,kodeorg,jumlahtandan1 as jjg,beratbersih as netto,kodecustomer,substr(tanggal,1,10) as tanggal,supir,nokendaraan,nodo,nokontrak from ".$dbname.".pabrik_timbangan where nokontrak!='' ".$where;
        //echo $sData;
        //echo "warning".$sData;exit();
	$qData=mysql_query($sTimbangan) or die(mysql_error());
	
	$brs=mysql_num_rows($qData);
	if($brs>0)
	{
		
		while($rData=mysql_fetch_assoc($qData))
		{	
			$no+=1;
			$sBrg="select namabarang from ".$dbname.".log_5masterbarang where kodebarang='".$rData['kodebarang']."'";
                        $qBrg=mysql_query($sBrg) or die(mysql_error($conn));
                        $rBrg=mysql_fetch_assoc($qBrg);
			
                        $sKntrk="select koderekanan from ".$dbname.".pmn_kontrakjual where nokontrak='".$rData['nokontrak']."'";
                        $qKntrk=mysql_query($sKntrk) or die(mysql_error());
                        $rKntrak=mysql_fetch_assoc($qKntrk);
                        $sNama="select namacustomer from ".$dbname.".pmn_4customer where kodecustomer='".$rKntrak['koderekanan']."'";
                        $qNama=mysql_query($sNama) or die(mysql_error($conn));
                        $rNama=mysql_fetch_assoc($qNama);
			
			$tab.="
			<tr class=rowcontent>
			<td>".$no."</td>
                        <td>".$rBrg['namabarang']."</td>    
			<td>".tanggalnormal($rData['tanggal'])."</td>
			<td>".$rNama['namacustomer']."</td>
			<td>".$rData['notransaksi']."</td>
			<td>".$rData['nokendaraan']."</td>
			<td  align=right>".number_format($rData['netto'],2)."</td>
                        <td  align=right>".number_format($rData['kgpembeli'],2)."</td>
			<td>".$rData['supir']."</td>
			<td>".$rData['nodo']."</td>
			<td>".$rData['nokontrak']."</td>
			</tr>";
			$subtota+=$rData['netto'];
			
		}
		$tab.="<tr class=rowcontent ><td colspan=6 align=right>Total (KG)</td><td align=right>".number_format($subtota,2)."</td><td colspan=3 align=right>&nbsp;</td></tr>";
		
	}
	else
	{
		$tab.="<tr class=rowcontent><td colspan=10 align=center>Data Kosong</td></tr>";
	}
       
			
			//echo "warning:".$strx;
			//=================================================

			
			$tab.="</tbody></table>Print Time:".date('Y-m-d H:i:s')."<br>By:".$_SESSION['empl']['name'];	
			$tglSkrg=date("Ymd");
			$nop_="LaporanPengiriman".$tglSkrg;
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
        case'getKontrakData':
//         $SCustId="select kodecustomer from ".$dbname.".pmn_4customer where kodetimbangan='".$kdCustomer."'";
//            echo"warning".$SCustId;exit();
//         $qCustId=mysql_query($sCustId) or die(mysql_error($conn));
//         $rCustId=mysql_fetch_assoc($qCustId);
         $sChek="select nokontrak from ".$dbname.".pmn_kontrakjual where koderekanan='".$kdCustomer."' order by nokontrak desc"; //and tanggalkontrak >= ".$tgl1." and tanggalkontrak<=".$tgl2."";
         
//exit("Error".$sChek);
         $qChek=mysql_query($sChek) or die(mysql_error($conn));
         $brs=mysql_num_rows($qChek);
         if($brs>0)
         {
             $optKontrak="<option value=''>".$_SESSION['lang']['all']."</opton>";
             while($rCheck=mysql_fetch_assoc($qChek))
             {
                 $optKontrak.="<option value=".$rCheck['nokontrak'].">".$rCheck['nokontrak']."</option>";
             }
             echo $optKontrak;
         }
         else
         {
             $optKontrak="<option value=''>".$_SESSION['lang']['all']."</opton>";
             echo $optKontrak;
            //exit();
         }
        break;
	default:
	break;
}
?>