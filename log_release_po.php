<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
OPEN_BOX(); //1 O

?>
<script language="javascript" src="js/zMaster.js"></script>
<!--<script type="text/javascript" src="js/log_persetujuan_po.js"></script>
-->
<script type="text/javascript" src="js/log_release_po.js"></script>
<div id="action_list">
<?php
echo"<table>
     <tr valign=moiddle>
	 <td align=center style='width:100px;cursor:pointer;' onclick=refresh_data_release_po()>
	   <img class=delliconBig src=images/orgicon.png title='".$_SESSION['lang']['list']."'><br>".$_SESSION['lang']['list']."</td>
	 <td><fieldset><legend>".$_SESSION['lang']['carinopo']."</legend>"; 
			echo $_SESSION['lang']['nopo'].":<input type=text id=txtsearch_rpo size=25 maxlength=30 class=myinputtext>&nbsp;";
			echo $_SESSION['lang']['tgl_po'].":<input type=text class=myinputtext id=tgl_cari_rpo onmousemove=setCalendar(this.id) onkeypress=return false;  size=10 maxlength=10 />";
			echo"<button class=mybutton onclick=cariRpo()>".$_SESSION['lang']['find']."</button>";
echo"</fieldset></td>
     </tr>
	 </table> "; 
?>
</div>
<?php
CLOSE_BOX(); //1 C //2 O
?>
<div id=list_pp_verication>
<?php OPEN_BOX();?>
<fieldset>
<legend><?php echo $_SESSION['lang']['list_po'];?></legend>
<div style="overflow:scroll; height:420px;">
	 <table class="sortable" cellspacing="1" border="0">
	 <thead>
	 <tr class=rowheader>
	 <td>No.</td>
	 <td><?php echo $_SESSION['lang']['nopo']?></td>
	 <td><?php echo $_SESSION['lang']['tgl_po'];?></td> 
	 <td><?php echo $_SESSION['lang']['namaorganisasi'];?></td>
	 <td><?php echo $_SESSION['lang']['tandatangan']; ?></td>
     <td><?php echo $_SESSION['lang']['koreksi']; ?></td>
     <td><?php echo $_SESSION['lang']['detail']; ?></td>
	  <?php		
				for($i=1;$i<4;$i++)
				 {
				//	echo"<td align=center>Persetujuan".$i."</td>";
				 }
	   ?>
	   <td colspan="2" align="center">Release</td>
	   
	
	 </tr>
	 </thead>
	 <tbody id="contain">
     <script>refresh_data_release_po()</script>
	 <?php 
		/*
		$limit=10;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where statuspo='2'  ORDER BY nopo DESC";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
                }
		
		$str="SELECT * FROM ".$dbname.".log_poht  where statuspo='2' ORDER BY nopo DESC  LIMIT ".$offset.",".$limit." ";
			//echo $str;
	  if($res=mysql_query($str))
	  {
		while($bar=mysql_fetch_assoc($res))
		{
			$this_date=date("Y-m-d");
			$kodeorg=$bar['kodeorg'];
			$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$kodeorg."' or induk='".$kodeorg."'"; //echo $spr;
			$rep=mysql_query($spr) or die(mysql_error($conn));
			$bas=mysql_fetch_object($rep);
			$no+=1;
			echo"<tr class=rowcontent id='tr_".$no."'>
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopo']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td align=center>".$kodeorg."</td>
				  <!--<td align=center><img src=images/pdf.jpg class=resicon width='30' height='30' title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_log_po',event);\"></td>-->";                            
                                
				 for($i=1;$i<4;$i++)
				 {
				 	//echo $bar['hasilpersetujuan'.$i];
					if($bar['persetujuan'.$i]!='')
					{	
						$kr=$bar['persetujuan'.$i];
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);	
						if($bar['hasilpersetujuan'.$i]=='1')
						{
								$st=$_SESSION['lang']['approve'];
						}
						elseif($bar['hasilpersetujuan'.$i]=='2')
						{
								$st=$_SESSION['lang']['ditolak'];
						}
						else
						{
							$st=$_SESSION['lang']['wait_approve'];
						}
						
						echo"<td align=center>".$yrs['namakaryawan']."<br />(".$st.")</td>";
					}
					else
					{
						echo"<td>&nbsp;</td>";
					}
				  } 
				  if(($bar['statuspo']=='2'))
				  {
					  if(($bar['stat_release']=='1')&&($bar['useridreleasae']!='0000000000'))
					 { 	$disbled="<td align=center>".tanggalnormal($bar['tglrelease'])."</td>";}
					  else
					  {	$disbled="<td><button class=mybutton onclick=\"release_po('".$bar['nopo']."')\" >".$_SESSION['lang']['release_po']."</button></td>";}
					if(($bar['stat_release']=='0')&&($bar['useridreleasae']=='0000000000'))
					  { 
					 	 $disbled2="<td>&nbsp;</td>";
					  }
					  else
					  {	if($bar['tglrelease']==$this_date)
					  	{	
					  		$disbled2="<td><button class=mybutton onclick=\"un_release_po('".$bar['nopo']."') \">".$_SESSION['lang']['un_release_po']."</button></td>";
						}
						else
						{
							$disbled2="<td>&nbsp;</td>";
						}
					  }
					  ?>
					<?php echo $disbled; echo $disbled2; ?>
				 <?php } else {?>
				 <td colspan="2" align="center"><?php echo $_SESSION['lang']['wait_approval']?></td>
				 
				 <?
				 }
				 echo"</tr><input type=hidden id=nopo_".$no." name=nopo_".$no." value='".$bar['nopo']."' />";
		}	 	
		echo" <tr><td colspan=8 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}	
		*/
	 ?>
	  </tbody>
	 <tfoot>
	 </tfoot>
	 </table>* Tanggal yang tampil adalah tanggal Dirilisnya PO</div>
</fieldset
><?php
CLOSE_BOX();
?>
</div>
<input type="hidden" name="method" id="method"  /> 
<input type="hidden" id="no_po" name="no_po" />
<input type="hidden" name="user_login" id="user_login" value="<?php echo $_SESSION['standard']['userid']?>" />

<?
echo close_body();
?>