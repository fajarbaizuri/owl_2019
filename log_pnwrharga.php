<?PHP
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O
?>

<link rel="stylesheet" type="text/css" href="style/zTable.css">
<script language="javascript" src="js/zMaster.js"></script>
<script type="text/javascript" src="js/log_pnwrharga.js" /></script>
<script>
 jdl_ats_0='<?php echo $_SESSION['lang']['find']?>';
// alert(jdl_ats_0);
 jdl_ats_1='<?php echo $_SESSION['lang']['findBrg']?>';
 content_0='<fieldset><legend><?php echo $_SESSION['lang']['findnoBrg']?></legend>Find<input type=text class=myinputtext id=no_brg><button class=mybutton onclick=findBrg()>Find</button></fieldset><div id=container></div>';
 Option_Isi='<?php 
 	$optKurs="<option value=>".$_SESSION['lang']['pilihdata']."</option>";
 $sKurs="select kode,kodeiso from ".$dbname.".setup_matauang order by kode desc";
	$qKurs=mysql_query($sKurs) or die(mysql_error());
	while($rKurs=mysql_fetch_assoc($qKurs))
	{
		$optKurs.="<option value=".$rKurs['kode'].">".$rKurs['kodeiso']."</option>";
	} 
	echo $optKurs;?>';
 isi_option="<?php ?>";
</script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayFormInput()>
	   <img class=delliconBig src=images/newfile.png title='".$_SESSION['lang']['new']."'><br>".$_SESSION['lang']['new']."</td>
	 <td align=center style='width:100px;cursor:pointer;' onclick=displayList()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['find']."</legend>";
			echo $_SESSION['lang']['find'].":<input type=text id=txtsearch size=25 maxlength=30 class=myinputtext>";
			echo $_SESSION['lang']['tanggal'].":<input type=text class=myinputtext id=tgl_cari onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariPnwrn()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> ";
?>
</div>
<?php
CLOSE_BOX();
?>

<div id="list_permintaan" name="list_permintaan">
    <?php OPEN_BOX();?>
    <fieldset>
        <legend><?php echo $_SESSION['lang']['permintaan'];?></legend>
        <div id="dlm_list_permintaan" name="dlm_list_permintaan" style="overflow: scroll; height:420px;">
            <table class="sortable" cellspacing="1" border="0">
            <thead>
            <tr class=rowheader>
            <td>No.</td>
            <td><?php echo $_SESSION['lang']['nopermintaan']?></td>
			<td>No.PB</td>
            <td><?php echo $_SESSION['lang']['tanggal'];?></td>
            <td><?php echo $_SESSION['lang']['purchaser'];?></td>
            <td><?php echo $_SESSION['lang']['namasupplier'];?></td>
            <td align="center">Action</td>
            </tr>
            </thead>
            <tbody id="contain">
                <?php
				$limit=25;
				$page=0;
				if(isset($_POST['page']))
				{
				$page=$_POST['page'];
				if($page<0)
				$page=0;
				}
				$offset=$page*$limit;
				
                    $sql="select * from ".$dbname.".log_perintaanhargaht order by nomor desc LIMIT ".$offset.",".$limit."";
					$sql2="select count(*) as jmlhrow from ".$dbname.".log_perintaanhargaht order by nomor desc";
                    $query=mysql_query($sql) or die(mysql_error());
					$query2=mysql_query($sql2) or die(mysql_error());
					while($jsl=mysql_fetch_object($query2)){
					$jlhbrs= $jsl->jmlhrow;
					}
                    while($res=mysql_fetch_assoc($query))
                    {
                         $no+=1;
						$dtkr="select * from ".$dbname.".datakaryawan where karyawanid='".$res['purchaser']."'";
                        $qdtkr=mysql_query($dtkr) or die(mysql_error());
                        $rdtkr=mysql_fetch_object($qdtkr);

                        $splr="select * from ".$dbname.".log_5supplier where supplierid='".$res['supplierid']."'";
                        $qsuplr=mysql_query($splr) or die(mysql_error());
                        $rsplr=mysql_fetch_object($qsuplr);
                         if($res['ppn']!=0)
                        {
                        $ppn=($res['ppn']/($res['subtotal']-$res['nilaidiskon']))*100;
                        }
                        echo
                        "<tr class=rowcontent>
                            <td>".$no."</td>
                            <td>".$res['nomor']."</td>
							<td>".$res['nopp']."</td>
                            <td>".tanggalnormal($res['tanggal'])."</td>
                            <td>".$rdtkr->namakaryawan."</td>
                            <td>".$rsplr->namasupplier."</td>";
							if($res['purchaser']==$_SESSION['standard']['userid'])
							{
							echo"
                            <td><img src=images/application/application_edit.png class=resicon  title='Edit' onclick=\"fillField('".$res['nomor']."','".tanggalnormal($res['tanggal'])."','".$res['purchaser']."','".$res['supplierid']."','".$res['nopp']."','".$res['sisbayar']."','".$res['id_franco']."','".$res['stock']."','".$ppn."');\">
                            <img src=images/application/application_delete.png class=resicon  title='Delete' onclick=\"delPer('".$res['nomor']."');\">
                            <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                            <img onclick=datakeExcel(event,'".$res['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>    
                            </td>";
							}
							else
							{
								echo"<td>
                                                                    <img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_perintaanhargaht','".$res['nomor']."','','log_slave_print_permintaan_penawaran',event);\">
                                                                    <img onclick=datakeExcel(event,'".$res['nomor']."') src=images/excel.jpg class=resicon title='MS.Excel'>        
                                                                    </td>";
								
							}
							echo"
                        </tr>";
                    }
					echo"
				 <tr><td colspan=6 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr><input type=hidden id=nopp_".$no." name=nopp_".$no." value='".$bar['nopp']."' />"; 
                ?>
            </tbody>
            </table>
        </div>
    </fieldset>
    <?php CLOSE_BOX();?>
</div>

<div id="form_permintaan" name="form_permintaan" style="display:none;">
    <?php
    OPEN_BOX();
    ?>
	<?php
	$tgl=date("d-m-Y");
         $sql="select namasupplier,supplierid from ".$dbname.".log_5supplier order by namasupplier asc";
    $query=mysql_query($sql) or die(mysql_error());
    while($res=mysql_fetch_assoc($query))
    {
       $optSupplier.="<option value='".$res['supplierid']."'>".$res['namasupplier']."</option>";
    }
    $optTermPay="<option value=''>".$_SESSION['lang']['pilihdata']."</option>";
    $optStock=$optTermPay;
    $optKrm=$optTermPay;
    $arrOptTerm=array("1"=>"Tunai","2"=>"Kerdit 2 Minggu","3"=>"Kredit 1 Bulan","4"=>"Termin","5"=>"DP");
    foreach($arrOptTerm as $brsOptTerm =>$listTerm)
    {
        $optTermPay.="<option value='".$brsOptTerm."'>".$listTerm."</option>";
    }
    $sKrm="select id_franco,franco_name from ".$dbname.".setup_franco where status=0 order by franco_name asc";
    $qKrm=mysql_query($sKrm) or die(mysql_error($conn));
    while($rKrm=mysql_fetch_assoc($qKrm))
    {
                    $optKrm.="<option value=".$rKrm['id_franco'].">".$rKrm['franco_name']."</option>";
    }
     $arrStock=array("1"=>"Ready Stock","2"=>"Not Ready");   
     foreach($arrStock as $brsStock => $listStock)
     {
         $optStock.="<option value='".$brsStock."'>".$listStock."</option>";
     }
	?>
    <fieldset>
        <legend><?php echo $_SESSION['lang']['permintaan']?></legend>
        <table cellspacing="1" border="0">
            <tr>
                <td><?php echo $_SESSION['lang']['nopermintaan']?></td>
                <td>:</td>
                <td><input type="text" id="no_prmntan" name="no_prmntan" class="myinputtext"  style="width:200px;" onkeypress="return angka_doang(event);" readonly="readonly"  /> </td>
                <td>(* Auto Generate</td>
            </tr>
            <tr>
                <td><?php echo $_SESSION['lang']['tanggal']?></td>
                <td>:</td>
                <td><input type="text" id="tgl_prmntan" name="tgl_prmntan" class="myinputtext"  disabled="disabled" style="width:200px;" value="<?php echo $tgl;?>" /></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><?php echo $_SESSION['lang']['namasupplier']?></td>
                <td>:</td>
                <td>
                    <select id="id_supplier" name="id_supplier" style="width:200px;" disabled="disabled"><?php echo $optSupplier;?></select>
                </td>
                <td><img src='images/search.png' class=dellicon title='<?php echo $_SESSION['lang']['findRkn']?>' onclick="searchSupplier('<?php echo $_SESSION['lang']['findRkn']; ?>','<fieldset><legend><?php echo $_SESSION['lang']['findRkn']?></legend><?php echo $_SESSION['lang']['namasupplier']; ?>&nbsp;<input type=text class=myinputtext id=nmSupplier><button class=mybutton onclick=findSupplier()><?php echo $_SESSION['lang']['find']; ?></button></fieldset><div id=containerSupplier style=overflow=auto;height=380;width=485></div>',event);"></td>
            </tr>
             <tr>
                <td><?php echo $_SESSION['lang']['nopp']?></td>
                <td>:</td>
                <td><input type="text" id="nopp" name="nopp" class="myinputtext" disabled="disabled"  style="width:200px;"  /></td>
                <td align="left"><div id="criTmbl" style="display:block;"><img  src='images/search.png' class=dellicon title='<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp'];?>' onclick="searchNopp('<?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp']; ?>','<fieldset><legend><?php echo $_SESSION['lang']['find']." ".$_SESSION['lang']['nopp'];?></legend><?php echo $_SESSION['lang']['find']; ?>&nbsp;<input type=text class=myinputtext id=kdNopp><button class=mybutton onclick=findNopp()><?php echo $_SESSION['lang']['find']; ?></button></fieldset><div id=containerNopp style=overflow=auto;height=380;width=485></div>',event);"></div></td>
            </tr>
            <tr>
            <td><? echo $_SESSION['lang']['syaratPem'] ?></td>
            <td>:</td>
            <td><select id='term_pay' name='term_pay' style="width:200px"><? echo $optTermPay; ?></select></td>
            <td>&nbsp;</td>
            </tr>
            <tr>
            <td><? echo $_SESSION['lang']['almt_kirim'] ?></td>
                    <td>:</td>
                    <td><select id='tmpt_krm' name='tmpt_krm' style="width:200px;"><? echo $optKrm ?></select></td>
                    <td>&nbsp;</td>
            </tr>
            <tr>
            <td><? echo substr($_SESSION['lang']['stockdetail'],0,5) ?></td>
            <td>:</td>
            <td><select id='stockId' name='stockId' style="width:200px"><? echo $optStock; ?></select></td>
            <td>&nbsp;</td>
            </tr>
            <tr>
            <td><? echo $_SESSION['lang']['keterangan'] ?></td>
            <td>:</td>
            <td><textarea id='ketUraian' name='ketUraian' onkeypress='return tanpa_kutip(event);'></textarea></td>
            <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4">
                    <div id="dtHeader">  <button class=mybutton id="tmbl_save" onclick=headher_permintaan() ><?php echo $_SESSION['lang']['save']?></button>
                    <button class=mybutton id="tmbl_cancel" onclick=reset_data()><?php echo $_SESSION['lang']['cancel']?></button>
                    </div>
                </td>
            </tr>
            <input type="hidden" id="purchser_id" name="purchaser_id" value="<?php echo $_SESSION['standard']['userid']?>" />
            <input type="hidden" id="method" name="method" value="insert" />
        </table>
        <br />
        <div id="formDetailIsian" style="display: none;">
        <fieldset>
        <legend><?php echo $_SESSION['lang']['log_perintaanhargaht']?></legend><br />
        <div id="detailTable" style="display:none;">
        <!-- content detail pp-->

        </div>
        <div id="tmbl_all" style="display:none;">

        <button class=mybutton onclick=simpanSemua() ><?php echo $_SESSION['lang']['save']?></button>
        <button class=mybutton onclick=reset_data()><?php echo $_SESSION['lang']['cancel']?></button>
        </div>
        </fieldset>
        </div>    
    </fieldset>
     <?php
    CLOSE_BOX();
    ?>
</div>
<?php echo close_body(); ?>