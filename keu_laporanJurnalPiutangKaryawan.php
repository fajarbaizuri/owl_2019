<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');




$tanggalmulai=$_POST['tanggalmulai'];
$tanggalsampai=$_POST['tanggalsampai'];
$noakun=$_POST['noakun'];

if($tanggalmulai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($tanggalsampai==''){ echo "warning: silakan mengisi tanggal"; exit; }
if($noakun==''){ echo "warning: silakan memilih no akun"; exit; }

$qwe=explode("-",$tanggalmulai); $tanggalmulai=$qwe[2]."-".$qwe[1]."-".$qwe[0];
$qwe=explode("-",$tanggalsampai); $tanggalsampai=$qwe[2]."-".$qwe[1]."-".$qwe[0];

#ambil saldo awal supplier
$str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.kodesupplier,c.namasupplier from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and kodesupplier!='' and kodesupplier is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' group by a.kodesupplier
";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
			if (substr($noakun,0,1)=='2')
			{
				
				$sawal[$bar->kodesupplier]=$bar->sawal * -1;
			}
			else
			{
				$sawal[$bar->kodesupplier]=$bar->sawal ;
			}
    
    $supplier[$bar->kodesupplier]=$bar->namasupplier;
    $akun[$bar->noakun]=$bar->namaakun;
}

#ambil saldo awal  karyawan
$str="select sum(a.debet-a.kredit) as sawal,a.noakun, b.namaakun,a.nik,c.namakaryawan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     
      where a.tanggal<'".$tanggalmulai."'  and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' group by c.namakaryawan
";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $sawalKAR[$bar->nik]=$bar->sawal;
    $supplierKAR[$bar->nik]=$bar->namakaryawan;
    $akunKAR[$bar->noakun]=$bar->namaakun;
}

#ambil  transaksi dalam periode supplier
$str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.kodesupplier,c.namasupplier from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".log_5supplier c on a.kodesupplier = c.supplierid
      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."' 
      and a.noakun = '".$noakun."' and kodesupplier!='' and kodesupplier is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' group by a.kodesupplier
";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $debet[$bar->kodesupplier]=$bar->debet;
    $kredit[$bar->kodesupplier]=$bar->kredit;
    $supplier[$bar->kodesupplier]=$bar->namasupplier;
    $akun[$bar->noakun]=$bar->namaakun;
}

#ambil saldo transaksi  karyawan
$str="select sum(a.debet) as debet,sum(a.kredit) as kredit,a.noakun, b.namaakun,a.nik,c.namakaryawan from ".$dbname.".keu_jurnaldt_vw a
      left join ".$dbname.".keu_5akun b on a.noakun = b.noakun
      left join ".$dbname.".datakaryawan c on a.nik = c.karyawanid     
      where a.tanggal between'".$tanggalmulai."' and '".$tanggalsampai."'  
      and a.noakun = '".$noakun."' and a.nik!='' and a.nik is not null 
      and a.kodeorg='".$_SESSION['empl']['lokasitugas']."' group by c.namakaryawan
";
$res=mysql_query($str);
while($bar=mysql_fetch_object($res))
{
    $debetKAR[$bar->nik]=$bar->debet;
    $kreditKAR[$bar->nik]=$bar->kredit;
    $supplierKAR[$bar->nik]=$bar->namakaryawan;
    $akunKAR[$bar->noakun]=$bar->namaakun;
}


//=================================================
$no=0;
if ($noakun=='1140402' || $noakun=='1140401' || $noakun=='1140501' || $noakun=='1130101' || $noakun=='1140101' || $noakun=='1140276'  ){
if($supplierKAR<1)
{
        echo"<tr class=rowcontent><td colspan=9>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
	
    foreach($supplierKAR as $kdsupp =>$val){
            $no+=1;
			//karyawan
			//1140402 ,1140401,1140501,1130101,1140101
			//if (substr($noakun,0,1)=='2') 
				//$saldoakhir=abs($sawal[$kdsupp]-$debet[$kdsupp]+$kredit[$kdsupp]);
				$saldoakhirKAR=($sawalKAR[$kdsupp]+$debetKAR[$kdsupp]-$kreditKAR[$kdsupp]);
				 echo"<tr class=rowcontent tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailHutangPiutang('".$noakun."','".$kdsupp."','".$tanggalmulai."','".$tanggalsampai."','".$_SESSION['empl']['lokasitugas']."',event);\">";
                  echo"<td align=center width=20>".$no."</td>
                  <td align=center>".$_SESSION['empl']['lokasitugas']."</td>
                  <td>".$noakun."</td>
                  <td>".$akunKAR[$noakun]."</td>
                  <td>".$val."</td>
                   <td align=right width=100>".number_format($sawalKAR[$kdsupp],2)."</td>   
                  <td align=right width=100>".number_format($debetKAR[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($kreditKAR[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($saldoakhirKAR,2)."</td>
                 </tr>"; 
          $tsa+=$sawalKAR[$kdsupp];//<td align=right width=100>".number_format($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp],2)."</td>
          $td+=$debetKAR[$kdsupp];
          $tk+=$kreditKAR[$kdsupp];
         // $tak+=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);  
		 $tak+=$saldoakhirKAR;

			
           
		        
    }	
} 
}else{
if($supplier<1)
{
        echo"<tr class=rowcontent><td colspan=9>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
	
    foreach($supplier as $kdsupp =>$val){
            $no+=1;
		
		
				//$saldoakhir=abs($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);
				$saldoakhir=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);
				 echo"<tr class=rowcontent tr class=rowcontent style='cursor:pointer;' title='Click untuk melihat detail' onclick=\"lihatDetailHutangPiutang('".$noakun."','".$kdsupp."','".$tanggalmulai."','".$tanggalsampai."','".$_SESSION['empl']['lokasitugas']."',event);\">";
                  echo"<td align=center width=20>".$no."</td>
                  <td align=center>".$_SESSION['empl']['lokasitugas']."</td>
                  <td>".$noakun."</td>
                  <td>".$akun[$noakun]."</td>
                  <td>".$val."</td>
                   <td align=right width=100>".number_format($sawal[$kdsupp],2)."</td>   
                  <td align=right width=100>".number_format($debet[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($kredit[$kdsupp],2)."</td>
                  <td align=right width=100>".number_format($saldoakhir,2)."</td>
                 </tr>"; 
          $tsa+=$sawal[$kdsupp];//<td align=right width=100>".number_format($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp],2)."</td>
          $td+=$debet[$kdsupp];
          $tk+=$kredit[$kdsupp];
         // $tak+=($sawal[$kdsupp]+$debet[$kdsupp]-$kredit[$kdsupp]);  
		 $tak+=$saldoakhir;
		
			
           
		        
    }	
} 
}



echo"<tr class=rowcontent>
      <td align=center colspan=5>Total</td>
       <td align=right width=100>".number_format($tsa,2)."</td>   
      <td align=right width=100>".number_format($td,2)."</td>
      <td align=right width=100>".number_format($tk,2)."</td>
      <td align=right width=100>".number_format($tak,2)."</td>
     </tr>"; 	

?>