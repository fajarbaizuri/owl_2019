<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
echo open_body();
include('master_mainMenu.php');
echo"<div id=dataAtas>";
OPEN_BOX();
?>
<link rel=stylesheet type=text/css href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_po.js" /></script>
<div id="action_list">
<?php
// <!--td align=center style='width:100px;cursor:pointer;' onclick=show_list_pp()>
	  // <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>-->
echo"<table>
     <tr valign=moiddle>
	
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>"; 
			echo $_SESSION['lang']['carinopo'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariNopo()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>";
echo"<td><fieldset><legend>List Job</legend><div id=notifikasiKerja>";
echo"<script>loadNotifikasi()</script>";
echo"</div>
</fieldset></td>";
echo"</tr>
	 </table></div> "; 
?>
</div>
<?php
CLOSE_BOX(); echo "</div>"; //1 C
echo "<div id=\"list_po\">";
OPEN_BOX(); //2 O
?>
<!--<img src="images/pdf.jpg" onclick="masterPDF('log_poht','','','log_listpo',event)" width="20" height="20" />-->
<fieldset>
    <legend><?php echo $_SESSION['lang']['listpo']?></legend>
    <div id="contain">
    <script>load_new_data()</script>
    </div>
   
</fieldset>
<?php CLOSE_BOX();?>
</div>
<div id="list_pp" name="list_pp" style="display:none;">
   <?php OPEN_BOX();?>
    <fieldset>
        <legend><?php echo $_SESSION['lang']['list_pp'] ?></legend>
    <?php
        $optPt='';
        $sql3="select * from ".$dbname.".organisasi where tipe='PT'";
        $query3=mysql_query($sql3) or die(mysql_error());
        while($res3=mysql_fetch_object($query3))
        {
            $optPt.="<option value='".$res3->kodeorganisasi."'>".$res3->namaorganisasi."</option>";
        }

    ?>
     <table cellspacing="1" border="0">
	 <tr>
	 <td>Please Select Company</td>
	 <td>:</td>
	 <td><select id="kode_pt" name="kode_pt" onchange="cek_pp_pt()">
         <option value=""></option>
        <?php echo $optPt;?>
     </select></td></tr>
     <br />
	 <input type="hidden" id="proses" name="proses" value="insert" />
    <table cellspacing="1" border="0" id="list_pp_table">
        <thead>
        <tr class="rowheader">
            <td>No.</td>
            <td><?php echo $_SESSION['lang']['nopp']?></td>
            <td><?php echo $_SESSION['lang']['kodebarang']?></td>
            <td><?php echo $_SESSION['lang']['namabarang']?></td>
            <td><?php echo $_SESSION['lang']['satuan']?></td>
            <td><?php echo $_SESSION['lang']['jmlhDiminta']?></td>
            <td><?php echo $_SESSION['lang']['jmlh_brg_blm_po']?></td>
            <td><?php echo $_SESSION['lang']['jmlhPesan']?></td>
            <td>Action</td>
        </tr>
        </thead>
       
	    <tbody id="container_pp">		
        
            <tr><td colspan="9" align="center"><button name="proses" id="proses" onclick="process()"><?php echo $_SESSION['lang']['proses']?></button></td></tr>
        </tbody>
    </table>
	<input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION['standard']['userid']?>" />
	</table>
	</fieldset>
      
<?php
CLOSE_BOX();
?>
</div>
<div id="form_po" style="display:none;">
    <?php 

    OPEN_BOX();
	$isiOpt= array(1=>'Tunai',2=>'Transfer',3=>'Giro',4=>'Cek');
	$optTermpay='';
	foreach($isiOpt as $ter => $OptIsi) {
		$optTermpay.="<option value='".$ter."'>".$OptIsi."</option>";
	}
    $optSupplier='';
	$snmkary="select namakaryawan from ".$dbname.".datakaryawan where karyawanid='".$_SESSION['standard']['userid']."'";
	$qnmkary=mysql_query($snmkary) or die(mysql_error());
	$rnmkary=mysql_fetch_assoc($qnmkary);
    $sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
       $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
    }
	$optMt="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$sMt="select kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
	$qMt=mysql_query($sMt) or die(mysql_error());
	while($rMt=mysql_fetch_assoc($qMt))
	{
		$optMt.="<option value=".$rMt['kode'].">".$rMt['kodeiso']."</option>";
	}
	
	$user_id=$_SESSION['standard']['userid'];
	$optPur="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	//$klq="select namakaryawan,karyawanid,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."' and lokasitugas!=''  and tanggalkeluar='0000-00-00' order by namakaryawan asc"; 
	$klq="select namakaryawan,karyawanid,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan='0' and lokasitugas!=''  and tanggalkeluar='0000-00-00' order by namakaryawan asc"; 
	//$klq="select namakaryawan,karyawanid,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan in ('0','6') and karyawanid!='".$user_id."' and lokasitugas!='' and (kodejabatan<6 or kodejabatan=11 or kodejabatan=12) and tanggalkeluar='0000-00-00' order by namakaryawan asc"; 
					//echo $klq;
	$qry=mysql_query($klq) or die(mysql_error());
	while($rst=mysql_fetch_object($qry))
	{
		$sBag="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
		$qBag=mysql_query($sBag) or die(mysql_error());
		$rBag=mysql_fetch_assoc($qBag);
		
		$optPur.="<option value='".$rst->karyawanid."'>".$rst->namakaryawan." [".$rst->lokasitugas."] [".$rBag['nama']."]</option>";
	}
	
	
	/*$optPur2="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
	$klq2="select namakaryawan,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan='0' and karyawanid!='".$user_id."' and lokasitugas!='' and (kodejabatan<6 or kodejabatan=11 or kodejabatan=12 or kodejabatan=7) and tanggalkeluar='0000-00-00' order by namakaryawan asc"; 
	//$klq="select namakaryawan,karyawanid,bagian,lokasitugas from ".$dbname.".`datakaryawan` where tipekaryawan in ('0','6') and karyawanid!='".$user_id."' and lokasitugas!='' and (kodejabatan<6 or kodejabatan=11 or kodejabatan=12) and tanggalkeluar='0000-00-00' order by namakaryawan asc"; 
					//echo $klq;
	$qry2=mysql_query($klq2) or die(mysql_error());
	while($rst2=mysql_fetch_object($qry2))
	{
		$sBag2="select nama from ".$dbname.".sdm_5departemen where kode='".$rst->bagian."'";
		$qBag2=mysql_query($sBag2) or die(mysql_error());
		$rBag2=mysql_fetch_assoc($qBag2);
		
		$optPur2.="<option value='".$rst->namakaryawan."'>".$rst->namakaryawan." [".$rst->lokasitugas."] [".$rBag['nama']."]</option>";
	}*/
	
	
	$sKrm="select id_franco,franco_name from ".$dbname.".setup_franco where status=0 order by franco_name asc";
	$qKrm=mysql_query($sKrm) or die(mysql_error($conn));
	$optKrm='';
	while($rKrm=mysql_fetch_assoc($qKrm))
	{
		$optKrm.="<option value=".$rKrm['id_franco'].">".$rKrm['franco_name']."</option>";
	}
    ?>
    <fieldset><legend><?php echo $_SESSION['lang']['form_po']?></legend>
    <fieldset style='float:left;'>
        <legend>Input <?php echo $_SESSION['lang']['form_po']?></legend>
    <table cellspacing="1" border="0">
        <tr>
            <td><?php echo $_SESSION['lang']['nopo']?></td>
            <td>:</td>
            <td><input type="text" name="no_po" id="no_po" class="myinputtext" style="width:150px;" disabled="disabled" /></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['tanggal']?></td>
            <td>:</td>
            <td><input type="text" name="tgl_po" id="tgl_po" class="myinputtext" value="<?php echo date("d-m-Y");?>"  readonly="readonly" style="width:150px;" /></td>
        </tr>
         <tr>
            <td><?php echo $_SESSION['lang']['namasupplier']?></td>
            <td>:</td>
            <td>
			<select id="supplier_id" name="supplier_id" onchange="get_supplier()" style="width:150px;" >
			<option value=""></option>
			<?php echo $optSupplier; ?>
			</select>
            <img src="images/search.png" class="resicon" title='<?php echo $_SESSION['lang']['findRkn']; ?>' onclick="searchSupplier('<?php echo $_SESSION['lang']['findRkn']; ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']?></legend><?php echo $_SESSION['lang']['find']; ?>&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()><?php echo $_SESSION['lang']['find']; ?></button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);"></td>
        </tr>
		<tr>
            <td><?php echo $_SESSION['lang']['namabank']?></td>
			<td>:</td>
			<td><input type="text" id="bank" name="bank" class="myinputtext" style="width:150px;" disabled="disabled"></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['norekeningbank']?></td>
			<td>:</td>
			<td><input type="text" id="bank_acc" name="bank_acc" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>
        </tr>
		<tr>
            <td><?php echo $_SESSION['lang']['atasnama']?></td>
			<td>:</td>
			<td><input type="text" id="bank_an" name="bank_an" class="myinputtext" style="width:150px;" disabled="disabled"></td>
        </tr>
		<tr>
            <td><?php echo $_SESSION['lang']['npwp']?></td>
			<td>:</td>
			<td><input type="text" id="npwp_sup" name="npwp_sup" class="myinputtext" onkeypress="return angka_doang(event)" style="width:150px;" disabled="disabled"></td>
        </tr>
        <tr>
            <td><?php echo $_SESSION['lang']['matauang']?></td>
			<td>:</td>
			<td><select id="mtUang" name="mtUang" style="width:150px;" onchange="getKurs()"><?php echo $optMt?></select></td>
        </tr>
         <tr>
            <td><?php echo $_SESSION['lang']['kurs']?></td>
			<td>:</td>
			<td><input type="text" class="myinputtext" id="Kurs" name="Kurs" style="width:150px;" onkeypress="return angka_doang(event)"  /></td>
        </tr>
          <tr>
            <td><?php echo $_SESSION['lang']['tandatangan']?></td>
			<td>:</td>
			<td><select id="persetujuan_id" name="persetujuan_id" style="width:150px;" ><?php echo $optPur?></select></td>
        </tr>
		<tr style="display: none">
            <td><?php echo $_SESSION['lang']['tandatangan']?> 2</td>
			<td>:</td>
			<td><select id="persetujuan_id2" name="persetujuan_id2" style="width:150px;" ><?php echo $optPur?></select></td>
        </tr>
        </table>
        </fieldset>
           
           <fieldset style='float:left;'><legend>From input Tujuan dan Pembuat PO</legend>
            <table>
            	<tr>
                	<td>Nama Kontak Pembuat</td>
                    <td>:</td>
                   <!-- <td><select id='pembuat' style="width:150px" /><?php echo $optPur ?></td>-->
                   <td><select id='pembuat' style="width:150px"><?php echo $optPur?></select></td>
                </tr>
            	<tr>
                	<td><?php echo $_SESSION['lang']['divisi'];?></td>
                    <td>:</td>
                    <td><input type='text' id='tujuanpo' class='myinputtext' onkeypress='return tanpa_kutip(event);' style="width:150px"   /></td>
                </tr>
                <tr>
                	<td><?php echo $_SESSION['lang']['peruntukan'];?></td>
                    <td>:</td>
                    <td><input type='text' id='ketbarang' class='myinputtext' onkeypress='return tanpa_kutip(event);' style="width:150px"   /></td>
                </tr>
            </table>
           </fieldset>
        
			<fieldset style="width:60%">
				<legend><?php echo $_SESSION['lang']['daftarbarang']?></legend>
                <table cellspacing="1" border="0" id="detail_content_table" name="detail_content_table">
                    <tbody id="detail_content" name="detail_content">
                        <tr><td><table id='ppDetailTable'>
                        </table>
                        
                        	<table cellspacing='1' border='0'>
        <tr>
            <td><? echo $_SESSION['lang']['tgl_kirim'] ?></td>
			<td>:</td>
			<td><input type="text" class="myinputtext" id="tgl_krm" name="tgl_krm" onmousemove="setCalendar(this.id)" onkeypress="return false";   maxlength="10"  style="width:200px;" /></td>
        </tr>
		<tr>
            <td><? echo $_SESSION['lang']['tgljatuhtempo'] ?></td>
			<td>:</td>
			<td><input type="text" class="myinputtext" id="tgljthtempo" name="tgljthtempo" onmousemove="setCalendar(this.id)" onkeypress="return false";   maxlength="10"  style="width:200px;" /></td>
        </tr>
		  <tr>
            <td><? echo $_SESSION['lang']['almt_kirim'] ?></td>
			<td>:</td>
			<td><select id='tmpt_krm' name='tmpt_krm1' style="width:200px;"><? echo $optKrm ?></select>
			<!--<input type='text'  id='tmpt_krm' name='tmpt_krm' maxlength='45' class='myinputtext' onkeypress='return tanpa_kutip(event);' style=width:200px />--></td>
        </tr>
	 <tr>
            <td><? echo $_SESSION['lang']['syaratPem'] ?></td>
			<td>:</td>
			<td><input type='text' id='term_pay' name='term_pay' class='myinputtext' onkeypress='return tanpa_kutip(event);' style="width:200px"   /></td>
        </tr>
		 <tr>
            <td><? echo $_SESSION['lang']['keterangan'] ?></td>
			<td>:</td>
			<td><textarea id='ketUraian' name='ketUraian' onkeypress='return tanpa_kutip(event);'></textarea></td>
        </tr>
		<tr>
            <td><? echo $_SESSION['lang']['purchaser'] ?></td>
			<td>:</td>
			<td><input type='text' id='purchaser_id' name='purchaser_id' class='myinputtext' disabled='disabled' value='<? echo $_SESSION['empl']['name'] ?>'  style='width:200px;' /></td>
        </tr></table>
                        
                        </td></tr>
                    </tbody>
                </table>
                
           
			
            
            
           
           
            </fieldset>
 
        <table cellspacing="1" border="0">
        <tr>
            <td colspan="3">
                <button class="mybutton" onclick="save_headher()"><?php echo $_SESSION['lang']['save']?></button>
                <button class="mybutton" onclick="cancel_headher()"><?php echo $_SESSION['lang']['cancel']?></button>
				<!--<button class="mybutton"  ><?php echo $_SESSION['lang']['done']?></button>-->
            </td>
        </tr> 

    </table>
        </fieldset>
    <?php CLOSE_BOX(); ?>
</div>

<?
echo close_body();
?>