<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

$proses=$_GET['proses'];
switch ($proses){
        case 'preview':
                $param=$_POST; 
            break;
         case 'excel':
                $param=$_GET;    
             break;
}

        //ambil  tahun tanam
        $str="select kodeorg,tahuntanam,kodeorg from ".$dbname.".setup_blok where kodeorg like '".$param['idKebun']."%'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $tt[$bar->kodeorg]=$bar->tahuntanam;
            $blok[]=$bar->kodeorg;
        }
        
        //ambil  jjg panen
        $str="select sum(hasilkerja) as jjgpanen,kodeorg,tanggal from ".$dbname.".kebun_prestasi_vw where tanggal like '".$param['periode']."%'
                  and kodeorg like '".$param['idKebun']."%' group by tanggal,kodeorg";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $jjgpanen[$bar->tanggal][$bar->kodeorg]=$bar->jjgpanen;
        }
        //ambil janjang spb
        $str="select sum(jjg) as jjgangkut,blok,sum(totalkg) as kgwb, tanggal,sum(brondolan) as brd from ".$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'
                  and kodeorg = '".$param['idKebun']."' group by tanggal,blok";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $jjgangkut[$bar->tanggal][$bar->blok]=$bar->jjgangkut;
            $brdkbn[$bar->tanggal][$bar->blok]=$bar->brd;
        }        
        //======================================
        //ambil spb per tiket
        $str="select blok,jjg,tanggal,notiket from ".$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'
                  and kodeorg = '".$param['idKebun']."'";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res)){
            $spbk[$bar->notiket][$bar->tanggal][$bar->blok]=$bar->jjg;
            $spbktg[$bar->notiket]=$bar->tanggal;
        }
        //ambil brondolan per no tiket dari timbangan
        $str="select notransaksi,brondolan as bb from ".$dbname.".pabrik_timbangan
                  where notransaksi in(select notiket from ".$dbname.".kebun_spb_vw where tanggal like '".$param['periode']."%'
                  and kodeorg = '".$param['idKebun']."')";
        $res=mysql_query($str);
        while($bar=mysql_fetch_object($res))
        {
            $tiket[$bar->notransaksi]=$bar->bb;
        }        
        //kalkulasi brondolan per blok spb;
        foreach($tiket as $tik =>$nx)
        {
               foreach($spbk[$tik] as $tg){
                   $tjg=array_sum($tg);
                   foreach($tg as $bl=>$jg)
                   {
                      $brd[$spbktg[$tik]][$bl]+=$jg/$tjg*$tiket[$tik];
                   }
               }    
            
        }
 $stream.="Brondolan Harian ".$param['idKebun']." Periode:".$param['periode']."
         <table class=sortable border=0 cellspacing=1>
          <thead>
          <tr class=rowheader>
             <td>No</td>
             <td>Tanggal</td>
             <td>Blok</td>
             <td>Thn.Tanam</td>
             <td>JJG PANEN</td>
             <td>JJG ANGKUT</td>
             <td>KG BRD SPB</td>
             <td>KG BRD TIMBANGAN</td>             
          </tr></thead><tbody>
          ";
      //jumlah hari
      $mk=mktime(0,0,0,substr($param['periode'],5,2),15,substr($param['periode'],0,4));
      $jhari=date('j',$mk);
      $a=0;
      for($x=1;$x<=$jhari;$x++){
          foreach($blok as $ki=>$bl){
            $tttt=str_pad($x, 2, "0", STR_PAD_LEFT);
            
            if($jjgpanen[$param['periode']."-".$tttt][$bl]>0 or $jjgangkut[$param['periode']."-".$tttt][$bl]>0 or $brdkbn[$param['periode']."-".$tttt][$bl]>0 or $brd[$param['periode']."-".$tttt][$bl]>0)
            {
                $a++;
                $stream.="<tr class=rowcontent>
                           <td>".$a."</td>
                           <td>".$param['periode']."-".$tttt."</td>
                           <td>".$bl."</td>
                           <td>".$tt[$bl]."</td>
                            <td align=right>".number_format($jjgpanen[$param['periode']."-".$tttt][$bl])."</td>
                            <td align=right>".number_format($jjgangkut[$param['periode']."-".$tttt][$bl])."</td>    
                           <td align=right>".number_format($brdkbn[$param['periode']."-".$tttt][$bl],2)."</td>
                           <td align=right>".number_format($brd[$param['periode']."-".$tttt][$bl],2)."</td>    
                     </tr>";
                $tjp+=$jjgpanen[$param['periode']."-".$tttt][$bl];
                $tja+=$jjgangkut[$param['periode']."-".$tttt][$bl];
                $tbk+=$brdkbn[$param['periode']."-".$tttt][$bl];
                $tb+=$brd[$param['periode']."-".$tttt][$bl];
            }
          }
      }
      $stream.="</tbody><tfoot>
                    <tr class=rowcontent>
                       <td colspan=4>TOTAL</td>
                       <td align=right>".number_format($tjp,2)."</td>
                       <td align=right>".number_format($tja,2)."</td>
                       <td align=right>".number_format($tbk,2)."</td>
                        <td align=right>".number_format($tb,2)."</td>   
                       </tr align=right>
                 </tfoot></table>";
        //========================================
switch ($proses){
        case 'preview':
                echo $stream;
            break;
         case 'excel':
            $nop_="BRONDOLAN_HARIAN_".$param['unit']."_".$param['periode'];
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
}

?>
