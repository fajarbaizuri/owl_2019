<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX('','<b>'.strtoupper($_SESSION['lang']['riwayatPP']).'</b>'); //1 O
?>
<!--<script type="text/javascript" src="js/log_2keluarmasukbrg.js" /></script>
-->
<script type="text/javascript" src="js/log_2riwayatPP.js" /></script>
<script language="javascript" src="js/zMaster.js"></script>
<div id="action_list">
<?php
$arrPil=array("1"=>"Proses Persetujuan PP","2"=>"Proses Purchsing","3"=>"Sudah PO","4"=>"Blm PO");
foreach($arrPil as $id =>$isi)
{
	$optPil.="<option value=".$id.">".$isi."</option>";
}



$optLokal="<option value=''>".$_SESSION['lang']['all']."</option>";
$arrPo=array("0"=>"Pusat","1"=>"Lokal");
foreach($arrPo as $brsLokal =>$isiLokal)
{
    $optLokal.="<option value=".$brsLokal.">".$isiLokal."</option>";
}
$optper="<option value=''>".$_SESSION['lang']['all']."</option>";
$sTgl="select distinct substr(tanggal,1,7) as periode from ".$dbname.".log_prapoht order by tanggal desc";
$qTgl=mysql_query($sTgl) or die(mysql_error());
while($rTgl=mysql_fetch_assoc($qTgl))
{
   if(substr($rTgl['periode'],5,2)=='12')
   {
         $optper.="<option value='".substr($rTgl['periode'],0,4)."'>".substr($rTgl['periode'],0,4)."</option>";
   }
   $optper.="<option value='".$rTgl['periode']."'>".substr($rTgl['periode'],5,2)."-".substr($rTgl['periode'],0,4)."</option>";
}
	 echo"<table>
     <tr valign=moiddle>
	  <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	   
	  <td><fieldset><legend><b><img class=icon src=images/info.png></b></legend>List data awal mengikut periode bulan ini sebagai data awal. Silahkan melalukan pencarian untuk mencari data lain. Periode sekarang adalah ".$tglSkrng=date("Y-m")."</fieldset></td>
	   
		 <td><fieldset><legend>".$_SESSION['lang']['pilihdata']."</legend>"; 
	            echo $_SESSION['lang']['nopp']." : <input type='text' id='txtNopp' name='txtNopp' onkeypress='return tanpa_kutip(event)' style='width:150px' class=myinputtext />";
		    echo $_SESSION['lang']['tanggal']." PB : <input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;   maxlength=10 style=width:150px />&nbsp;";
		    echo $_SESSION['lang']['periode']."  : <select id=periode name=periode style='width:150px;'>".$optper."</select>&nbsp;";
                    echo $_SESSION['lang']['lokasiBeli']."  : <select id=lokBeli name=lokBeli style='width:150px;'>".$optLokal."</select>&nbsp;";
                    echo $_SESSION['lang']['status']." PP : <select id=statPP name=statPP style='width:150px;'><option value=''>".$_SESSION['lang']['all']."</option>".$optPil."</select>";
		    echo"<button class=mybutton onclick=savePil()>".$_SESSION['lang']['find']."</button>";
			
			
echo"</fieldset></td> 
     </tr>
	 </table> "; 
?>
</div>
<?php 
CLOSE_BOX();
OPEN_BOX();

?>

    <fieldset>
    <legend><?php echo $_SESSION['lang']['list']?></legend>
     <img onclick=dataKeExcel(event,'log_slave_2riwayatPPExcel.php') src=images/excel.jpg class=resicon title='MS.Excel'> 
	 <img onclick=dataKePDF(event) title='PDF' class=resicon src=images/pdf.jpg>

        <div style="overflow:scroll; height:400px; width:1000px;">
		<table class="sortable" cellspacing="1" border="0" width="1500px">
				<thead>
				<tr class=rowheader>
					<td>No.</td>
                    <td><?php echo $_SESSION['lang']['nopp'] ?></td>
                    <td><?php echo $_SESSION['lang']['tanggal'] ?></td>
					<td><?php echo $_SESSION['lang']['namabarang'] ?></td>
                    <td><?php echo $_SESSION['lang']['jumlah']; ?></td>
                    <td>Stok Awal</td>
                    <td><?php echo $_SESSION['lang']['satuan']; ?></td>
                    
					<td><?php echo $_SESSION['lang']['status']; ?></td>
					<td><?php echo "O.Std";?></td>
                    <td><?php echo $_SESSION['lang']['chat']; ?></td>
					<td><?php echo $_SESSION['lang']['nopo']; ?></td>
					<td><?php echo $_SESSION['lang']['tgl_po']; ?></td>
					<td><?php echo $_SESSION['lang']['status']." PO";?></td>
					
					<td><?php echo $_SESSION['lang']['namasupplier'] ?></td>
					<td><?php echo $_SESSION['lang']['rapbNo'] ?></td>
					<td><?php echo $_SESSION['lang']['tanggal'] ?></td>
                    <td>Action</td>
				</tr>
				</thead>
				<tbody  id="contain">
        <script>loadData()</script>
        </tbody>
    </table>
    </div>
    </fieldset>

<?php
CLOSE_BOX();
?>
<?php
echo close_body();
?>