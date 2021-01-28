<?//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
?>
<script language=javascript src=js/zTools.js></script>
<script language=javascript1.2 src='js/log_penerimaan_internal.js'></script>
<?
$arrData="##id_supplier##tglKrm##jlhKoli##kpd##lokPenerimaan##srtJalan##biaya##ket##method##nomor_id";
$sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
    $optSupplier="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
       $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
    }
    $optKary="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $sKary="select  karyawanid,namakaryawan,lokasitugas from ".$dbname.".datakaryawan where tipekaryawan in (0,1,2) order by namakaryawan asc";
    $qKary=mysql_query($sKary) or die(mysql_error());
    while($rKary=mysql_fetch_assoc($qKary))
    {
        $optKary.="<option value='".$rKary['karyawanid']."'>".$rKary['namakaryawan']."-".$rKary['lokasitugas']."</option>";
    }
include('master_mainMenu.php');

OPEN_BOX();

echo"<fieldset><legend>".$_SESSION['lang']['list']."</legend><table class=sortable cellspacing=1 border=0>
     <thead>
	  <tr class=rowheader>
	   <td>No</td>
           <td>".$_SESSION['lang']['suratjalan']."</td>
	   <td>".$_SESSION['lang']['expeditor']."</td>
	   <td>".$_SESSION['lang']['tgl_kirim']."</td>
           <td>".$_SESSION['lang']['kepada']."</td>
	   <td>".$_SESSION['lang']['keterangan']."</td>
           <td>Tanggal Terima</td>
	   <td>Action</td>
	  </tr>
	 </thead>
	 <tbody id=container>";
	 echo"<script>loadData()</script>";
    
echo"</tbody>
     <tfoot>
	 </tfoot>
	 </table></fieldset>";
CLOSE_BOX();
echo close_body();
?>