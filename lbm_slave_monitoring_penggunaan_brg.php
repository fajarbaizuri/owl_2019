<?
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
require_once('lib/zLib.php');
require_once('lib/fpdf.php');

$proses=$_GET['proses']; 
$_POST['unit']==''?$unit=$_GET['unit']:$unit=$_POST['unit'];
$_POST['berdasarkan']==''?$berdasarkan=$_GET['berdasarkan']:$berdasarkan=$_POST['berdasarkan'];
$_POST['find']==''?$find=$_GET['find']:$find=$_POST['find'];
$_POST['judul']==''?$judul=$_GET['judul']:$judul=$_POST['judul'];

$tab.="<table cellpadding=1 cellspacing=1 border=\"0\" class=sortable style='width:100%;'>
    <thead class=rowheader>
    <tr>
    <td align=center >No.</td>
	<td align=center >No. Transaksi</td>
	<td align=center >Gudang</td>
	<td align=center >Tanggal</td>
	<td align=center >Kode Barang</td>
	<td align=center >Nama Barang</td>
	<td align=center >Jumlah</td>
	<td align=center >Satuan</td>
	<td align=center >Keterangan</td>
	
    </tr>
    </thead>
    <tbody>
";

$SQL="SELECT A.kodegudang,A.notransaksi,A.tanggal,A.kodebarang,A.jumlah,A.satuan,A.keterangan,B.namabarang
FROM ".$dbname.". log_transaksi_vw A LEFT JOIN ".$dbname.".log_5masterbarang B ON A.kodebarang=B.kodebarang WHERE ".$berdasarkan." LIKE '%".$find."%' and A.kodegudang like '".$unit."'  and A.post=1;" ;
$no=1;

$qOrg=mysql_query($SQL) or die(mysql_error($conn));
while($rOrg=mysql_fetch_assoc($qOrg))
{
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=center>".$no."</td>"; 
	$tab.= "<td align=center>".$rOrg['kodegudang']."</td>"; 
    $tab.= "<td align=center>".$rOrg['notransaksi']."</td>"; 
	$tab.= "<td align=center>".$rOrg['tanggal']."</td>"; 
    $tab.= "<td align=center>".$rOrg['kodebarang']."</td>"; 
    $tab.= "<td align=left>".$rOrg['namabarang']."</td>"; 
	$tab.= "<td align=right>".number_format($rOrg['jumlah'],2)."</td>"; 
	$tab.= "<td align=center>".$rOrg['satuan']."</td>"; 
	$tab.= "<td align=left>".$rOrg['keterangan']."</td>"; 
	
    $tab.= "</tr>";
	$no++;
}
    


  /*      
    $dummy='';
    $no=1;
// excel array content =========================================================================
    $tab.= "<tr class=rowcontent>";
    $tab.= "<td align=center>".$no."</td>"; 
    $tab.= "<td align=center>".$rOrg['kodegudang']."</td>"; 
	$tab.= "<td align=center>".$rOrg['kodebarang']."</td>"; 
    $tab.= "<td align=center>".$rOrg['namabarang']."</td>"; 
	$tab.= "<td align=center>".$rOrg['satuan']."</td>"; 
    $tab.= "<td align=center>".number_format($rOrg['saldoqty'],2)."</td>"; 
    $tab.= "</tr>";
	
    
	*/
	
    $tab.="</tbody></table>";
			
switch($proses)
{
    case'preview':
    echo $tab;
    break;
}
	
?>
