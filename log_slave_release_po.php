<?php
	require_once('master_validation.php');
	include('lib/nangkoelib.php');
	require_once('config/connection.php');
	include_once('lib/zLib.php');
	$method=$_POST['method'];
	$nopo=$_POST['nopo'];
	$user_id=$_SESSION['standard']['userid'];
	$rlse_user_id=$_POST['id_user'];
	$this_date=date("Y-m-d");
	$tglR=$_POST['tglR'];
	$ket=$_POST['ket'];
	$texkKrsi=$_POST['texkKrsi'];
	//$iduser=$_POST['id_user'];
	
	switch ($method)
	{
	
	case 'release_po' :
	//echo "warning:masuk";
	
	$sql="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);
	if(($res['persetujuan1']!='') || ($res['persetujuan2']!='')|| ($res['hasilpersetujuan1']!='') || ($res['hasilpersetujuan2']!='') || ($res['hasilpersetujuan3']!='')) 
	{
		//echo "warning:masuk";
		if(($res['stat_release']==0) && ($res['useridreleasae']==0000000000))
		{		
		//	echo "warning:masuk";
			$unopo="update ".$dbname.".log_poht set stat_release='1', useridreleasae='".$rlse_user_id."',tglrelease='".$this_date."' where nopo='".$nopo."' ";
			$qnopo=mysql_query($unopo) or die(mysql_error());
		}
		else
		{
			echo "warning:PO Sudah Di Release atau sedang koreksi";
			exit();
		}
	}
	else
	{
		echo"warning:Can`t Release The PO Yet";
	}
	break;
	case 'un_release_po' :
	//echo "warning:masuk";
	$sql="select * from ".$dbname.".log_poht where nopo='".$nopo."'";
	$query=mysql_query($sql) or die(mysql_error());
	$res=mysql_fetch_assoc($query);

		if(($res['stat_release']==1) && ($res['useridreleasae']==$rlse_user_id)&&($res['tglrelease']==$this_date))
		{		
			$unopo="update ".$dbname.".log_poht set stat_release='0', useridreleasae='0000000000',tglrelease='0000-00-00' where nopo='".$nopo."' ";
			$qnopo=mysql_query($unopo) or die(mysql_error());
		}
		else
		{
			echo "warning:You Don`t Have Autorize to Unrelease This PO No. ".$nopo;
			exit();
		}
	
	break;
	case 'list_new_data_release_po':
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
		
		$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where statuspo>1  ORDER BY tanggal DESC";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
		
	$str="SELECT * FROM ".$dbname.".log_poht where statuspo>1   ORDER BY tanggal DESC limit ".$offset.",".$limit." ";
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
			echo"<tr id='tr_".$no."' ".($bar['stat_release']==2?"bgcolor='orange'":"class=rowcontent")."  >
				  <td>".$no."</td>
				  <td id=td_".$no.">".$bar['nopo']."</td>
				  <td>".tanggalnormal($bar['tanggal'])."</td>
				  <td align=center>".$kodeorg."</td>";    
//				                     
//                   if($bar['lokalpusat']==1)
//				   {
//					   echo"<td colspan=3 align=center>Local</td>";             
//				   }
//				   else
//                                    {
					   $sKrsi="select catatanrelease from ".$dbname.".log_poht where nopo='".$bar['nopo']."'";
					   $qKrsi=mysql_query($sKrsi) or die(mysql_error($conn));
					   $rKrasi=mysql_fetch_assoc($qKrsi);
						  if($rKrasi['catatanrelease']!='')
						  { $isi=" disabled";}
						  else
						  { $isi="";}
					   $sql="select * from ".$dbname.".datakaryawan where karyawanid='".$bar['persetujuan1']."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);	
					   if(($bar['stat_release']!=1)||($bar['stat_release']==""))
					   {
						
						echo"<td align=center>".$yrs['namakaryawan']."</td>
						 <td align=center valign=\"middle\" onclick=\"undisable(".$no.")\" ><input type=text class=myinputtext style=widht:150px maxlength=150 id=krksiText_".$no." name=krksiText_".$no." value='".$rKrasi['catatanrelease']."' ".$isi." /> <button class=\"mybutton\" id=btnSave_".$no." name=btnSave_".$no." onclick=\"saveKoreksi(".$no.")\" ".$isi." )\"  >".$_SESSION['lang']['save']."</button></td>   
						<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_detail_po',event);\"></td>";
					   }
					   elseif($bar['stat_release']==1)
					   {
						   echo"<td align=center>".$yrs['namakaryawan']."</td><td >&nbsp;</td><td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_detail_po',event);\"></td>";
					   }
					 //for($i=1;$i<4;$i++)
//					 {
//						//echo $bar['hasilpersetujuan'.$i];
//						if($bar['persetujuan'.$i]!='')
//						{	
//							$kr=$bar['persetujuan'.$i];
//							$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$kr."'";
//							$query=mysql_query($sql) or die(mysql_error());
//							$yrs=mysql_fetch_assoc($query);	
//							if($bar['hasilpersetujuan'.$i]==1)
//							{
//									$st="(".$_SESSION['lang']['disetujui'].")";
//							}
//							elseif($bar['hasilpersetujuan'.$i]==2)
//							{
//									$st="(".$_SESSION['lang']['ditolak'].")";
//							}
//							else
//							{
//								$st="";
//							}
//							
//							echo"<td align=center>".$yrs['namakaryawan']."<br /></td>";
//						}
//						else
//						{
//							echo"<td>&nbsp;</td>";
//						}
//					  } 
//				   }
				    
				  if(($bar['statuspo']>1))
				  {
					  if(($bar['stat_release']=='1')&&($bar['useridreleasae']!='0000000000'))
					 { 	$disbled="<td align=center>".tanggalnormal($bar['tglrelease'])."</td>";}
					  else
					  {	$disbled="<td><button class=mybutton onclick=\"release_po('".$bar['nopo']."')\" >".$_SESSION['lang']['release_po']."</button>&nbsp;<img src=images/onebit_33.png class=resicon  title='".$_SESSION['lang']['ditolak']."' onclick=\"get_data_po('".$bar['nopo']."');\" style=\"vertical-align:middle;\"></td>";}
					if(($bar['stat_release']=='0')&&($bar['useridreleasae']=='0000000000'))
					  { 
					 	 $disbled2="<td align=center>".$_SESSION['lang']['un_release_po']."</td>";
					  }
					  else
					  {	if($bar['tglrelease']==$this_date)
					  	{	
					  		$disbled2="<td><button class=mybutton onclick=\"un_release_po('".$bar['nopo']."','".$this_date."') \">".$_SESSION['lang']['un_release_po']."</button></td>";
						}
						else
						{
							$disbled2="<td><button class=mybutton disabled >".$_SESSION['lang']['un_release_po']."</button></td>";
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
			echo" <tr><td colspan=9 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariBast2(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariBast2(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";   	
	  }	
	  else
		{
			echo " Gagal,".(mysql_error($conn));
		}	
	break;
	case 'cari_rpo' :
		//echo "warning:masuk";exit();
		$limit=20;
		$page=0;
		if(isset($_POST['page']))
		{
		$page=$_POST['page'];
		if($page<0)
		$page=0;
		}
		$offset=$page*$limit;
	
			if((isset($_POST['txtSearchrpo']))||(isset($_POST['tglCarirpo'])))
			{
			$txt_search=$_POST['txtSearchrpo'];
			//$txt_tgl=str_replace('-','',$_POST['tglCari']);
			$txt_tgl=tanggalsystem($_POST['tglCarirpo']);
			//$txt_tgl=$txt_tgl);
			$txt_tgl_a=substr($txt_tgl,0,4);
			$txt_tgl_b=substr($txt_tgl,4,2);
			$txt_tgl_c=substr($txt_tgl,6,2);
			$txt_tgl=$txt_tgl_a."-".$txt_tgl_b."-".$txt_tgl_c;
			//echo $txt_tgl_a;
			}
			else
			{
			$txt_search='';
			$txt_tgl='';			
			}//			
			
			if($txt_search!='')
			{
			$where=" and nopo LIKE  '%".$txt_search."%' ";
			
			}
			elseif($txt_tgl!='')
			{
			$where=" and tanggal LIKE '%".$txt_tgl."%' ";
			
			}
			//elseif(($txt_tgl!='')&&($txt_search!=''))
//			{
//			$where="  nopo LIKE '%".$txt_search."%' and tanggal LIKE '%".$txt_tgl."%'"; 
//			}//
			
			
			$strx="select * from ".$dbname.".log_poht where statuspo>1 ".$where."order by tanggal desc";
		
			//echo "warning :".$strx;exit();
			$sql2="select count(*) as jmlhrow from ".$dbname.".log_poht where statuspo>1 ".$where."order by tanggal desc";
		$query2=mysql_query($sql2) or die(mysql_error());
		while($jsl=mysql_fetch_object($query2)){
		$jlhbrs= $jsl->jmlhrow;
		}
			if($res=mysql_query($strx))
			{
				$numrows=mysql_num_rows($res);
				if($numrows<1)
				{
					echo"<tr class=rowcontent><td colspan=9>Not Found</td></tr>";
				}
				else
				{
					while($bar=mysql_fetch_assoc($res))
					{
						$kodeorg=$bar['kodeorg'];
						$spr="select * from  ".$dbname.".organisasi where  kodeorganisasi='".$koderorg."' or induk='".$koderorg."'"; //echo $spr;
						$rep=mysql_query($spr) or die(mysql_error($conn));
						$bas=mysql_fetch_object($rep);
						$no+=1;
						echo"<tr id='tr_".$no."' ".($bar['stat_release']==2?"bgcolor='orange'":"class=rowcontent")." >
						<td>".$no."</td>
						<td id=td_".$no.">".$bar['nopo']."</td>
						<td>".tanggalnormal($bar['tanggal'])."</td>
						<td>".$kodeorg."</td>";                            
						$sKrsi="select catatanrelease from ".$dbname.".log_poht where nopo='".$bar['nopo']."'";
						$qKrsi=mysql_query($sKrsi) or die(mysql_error($conn));
						$rKrasi=mysql_fetch_assoc($qKrsi);
						if($rKrasi['catatanrelease']!='')
						{ $isi=" disabled";}
						else
						{ $isi="";}
						$sql="select * from ".$dbname.".datakaryawan where karyawanid='".$bar['persetujuan1']."'";
						$query=mysql_query($sql) or die(mysql_error());
						$yrs=mysql_fetch_assoc($query);	
						
						echo"<td align=center>".$yrs['namakaryawan']."</td>
						<td align=center valign=\"middle\" onclick=\"undisable(".$no.")\" ><input type=text class=myinputtext style=widht:150px maxlength=150 id=krksiText_".$no." name=krksiText_".$no." value='".$rKrasi['catatanrelease']."' ".$isi." /> <button class=\"mybutton\" id=btnSave_".$no." name=btnSave_".$no." onclick=\"saveKoreksi(".$no.")\" ".$isi." )\"  >".$_SESSION['lang']['save']."</button></td>   
						<td align=center><img src=images/pdf.jpg class=resicon  title='Print' onclick=\"masterPDF('log_poht','".$bar['nopo']."','','log_slave_print_detail_po',event);\"></td>";
						  if(($bar['statuspo']>1))
				  {
					  if(($bar['stat_release']=='1')&&($bar['useridreleasae']!='0000000000'))
					 { 	$disbled="<td align=center>".tanggalnormal($bar['tglrelease'])."</td>";}
					  else
					  {	$disbled="<td><button class=mybutton onclick=\"release_po('".$bar['nopo']."')\" >".$_SESSION['lang']['release_po']."</button></td>";}
					if(($bar['stat_release']=='0')&&($bar['useridreleasae']=='0000000000'))
					  { 
					 	 $disbled2="<td>".$_SESSION['lang']['un_release_po']."</td>";
					  }
					  else
					  {	if($bar['tglrelease']==$this_date)
					  	{	
					  		$disbled2="<td><button class=mybutton onclick=\"un_release_po('".$bar['nopo']."','".$this_date."') \">".$_SESSION['lang']['un_release_po']."</button></td>";
						}
						else
						{
							$disbled2="<td><button class=mybutton disabled \">".$_SESSION['lang']['un_release_po']."</button></td>";
						}
					  }
					  ?>
					<?php echo $disbled; echo $disbled2; ?>
				 <?php } else {?>
				 <td colspan="2" align="center"><?php echo $_SESSION['lang']['wait_approval']?></td>
				 
				 <?
				 }
						echo"</tr><input type=hidden id=nopo_".$no." name=nopo_".$no." value='".$bar['nopo']."' />";
					}//while
					echo" <tr><td colspan=9 align=center>
				".(($page*$limit)+1)." to ".(($page+1)*$limit)." Of ".  $jlhbrs."<br />
				<button class=mybutton onclick=cariPage(".($page-1).");>".$_SESSION['lang']['pref']."</button>
				<button class=mybutton onclick=cariPage(".($page+1).");>".$_SESSION['lang']['lanjut']."</button>
				</td>
				</tr>";  
				 }//else
				
			}//	
			else
			{
			echo "Gagal,".(mysql_error($conn));
			}	
		break;
		case'getFormTolak':
		echo"<br /><div id=rejected_form>
		<fieldset>
		<legend><input type=text readonly=readonly name=rnopo id=rnopo value=".$nopo." class=myinputtext  style=\"width:150px;\" maxlength=\"50\" /></legend>
		<table cellspacing=1 border=0>
		<tr>
		<td colspan=3>
		Apakah Anda Akan Menolak No.PO Di Atas </td></tr>
		<tr><td>".$_SESSION['lang']['keterangan']."</td><td>:</td><td><input type=text class=myinputtext onkeypress=\"return tanpa_kutip(event)\" id=ket name=ket style=\"width:150px;\" /></td></tr>
		<tr><td colspan=3 align=center>
		<button class=mybutton onclick=tolakPo() >".$_SESSION['lang']['yes']."</button>
		<button class=mybutton onclick=cancel_po() >".$_SESSION['lang']['no']."</button>
		</td></tr></table>
		
		</fieldset>
		</div>
		<input type=hidden name=method id=method  /> 
		<input type=hidden name=user_id id=user_id value=".$user_id." />
		<input type=hidden name=nopo id=nopo value=".$nopo."  />
		";
		break;
		case'tolakPo':
		if($ket=="")
		{
			echo"warning:Keterangan Tidak Boleh Kosong";
			exit();
		}
		$sUp="update ".$dbname.".log_poht set hasilpersetujuan2='2',persetujuan2='".$user_id."',tglp2='".$this_date."',keterangan='".$ket."',stat_release='1', useridreleasae='".$user_id."',tglrelease='".$this_date."', tanggal='".$this_date."' where nopo='".$nopo."'";
		//echo"warning:".$sUp;exit();
		if($res=mysql_query($sUp))
		echo"";
		else
		echo $sUp."Gagal,".(mysql_error($conn));
		break;
		case'insertKoreksi':
		$sUpd="update ".$dbname.".log_poht set catatanrelease='".$texkKrsi."',stat_release='2' where nopo='".$nopo."'";
		if(!mysql_query($sUpd))
		{
			echo $sUpd."Gagal,".(mysql_error($conn));
		}
		break;
	default:
	break;
	}
	
	    