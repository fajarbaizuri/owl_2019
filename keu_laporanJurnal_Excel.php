<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$pt=$_GET['pt'];
$gudang=$_GET['gudang'];
$periode=$_GET['periode'];
            
if($periode=='' and $gudang=='' and $pt=='')
{               
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
		order by a.nojurnal 
		";
}
else if($periode=='' and $gudang=='' and $pt!='')
{               
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
		and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
                and length(kodeorganisasi)=4)
                order by a.nojurnal 
		";
}
else if($periode=='' and $gudang!='')
{
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal>=".$_SESSION['org']['period']['start']." and  a.tanggal<=".$_SESSION['org']['period']['end']."
		and a.kodeorg='".$gudang."'
                order by a.nojurnal 
		";
}
else if($periode!='' and $gudang=='' and $pt=='')
{
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal like '".$periode."%'
		order by a.nojurnal 
		";
}
else if($periode!='' and $gudang=='' and $pt!='')
{
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal like '".$periode."%'
                and a.kodeorg in(select kodeorganisasi from ".$dbname.".organisasi where induk='".$pt."' 
                and length(kodeorganisasi)=4)                    
		order by a.nojurnal 
		";
}
else if($periode!='' and $gudang!='')
{
               $str="select a.*,b.namaakun from ".$dbname.".keu_jurnaldt_vw a
		left join ".$dbname.".keu_5akun b
		on a.noakun=b.noakun
		where a.tanggal like '".$periode."%'
		and a.kodeorg='".$gudang."'
                order by a.nojurnal 
		";
}

//=================================================
$stream="<table border=1>
	     <thead>
		    <tr>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['nourut']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['nojurnal']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['tanggal']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['organisasi']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['noakun']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['namaakun']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['keterangan']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['debet']."</td>
			  <td bgcolor='#dedede'>".$_SESSION['lang']['kredit']."</td>
                          <td bgcolor='#dedede'>".$_SESSION['lang']['noreferensi']."</td>
                          <td bgcolor='#dedede'>".$_SESSION['lang']['kodevhc']."</td>
                          <td bgcolor='#dedede'>".$_SESSION['lang']['kodeblok']."</td>
                          <td bgcolor='#dedede'>".$_SESSION['lang']['afdeling']."</td>    
			</tr>  
		 </thead>
		 <tbody id=container>";
$res=mysql_query($str);
$no=0;
if(mysql_num_rows($res)<1)
{
       $stream.="<tr class=rowcontent><td colspan=12>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
}
else
{
while($bar=mysql_fetch_object($res))
{
        $no+=1;
        $debet=0;
        $kredit=0;
        if($bar->jumlah>0)
            $debet=$bar->jumlah;
        else
            $kredit=$bar->jumlah*-1;
        
       $stream.="<tr class=rowcontent>
              <td align=center width=20>".$no."</td>
              <td>".$bar->nojurnal."</td>
              <td>".tanggalnormal($bar->tanggal)."</td>
              <td align=center>".$bar->kodeorg."</td>
              <td>".$bar->noakun."</td>
              <td>".$bar->namaakun."</td>
              <td>".$bar->keterangan."</td>
              <td align=right width=100>".number_format($debet,2)."</td>
              <td align=right width=100>".number_format($kredit,2)."</td>
              <td>".$bar->noreferensi." </td>
              <td>".$bar->kodevhc." </td>
              <td>".$bar->kodeblok." </td>
              <td>".substr($bar->kodeblok,0,6)."</td>    
             </tr>"; 		
}	
} 
$stream.="</tbody>
		 <tfoot>
		 </tfoot>		 
	   </table>";

$nop_="LP_JRNL_".$gudang.$periode;
if(strlen($stream)>0)
{
     $gztralala = gzopen("tempExcel/".$nop_.".xls.gz", "w9");
     gzwrite($gztralala, $stream);
     gzclose($gztralala);
     echo "<script language=javascript1.2>
        window.location='tempExcel/".$nop_.".xls.gz';
        </script>";
}
?>