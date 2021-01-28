<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');
if(isTransactionPeriod())//check if transaction period is normal
{
  $gudang=$_POST['gudang'];
  $notransaksi=$_POST['notransaksi'];
  $tipetransaksi=$_POST['tipe'];
 //================================
 echo " <div  style='width:690px;height:380px;overflow:scroll;'> 
        <table class=sortable cellspacing=1 border=0 width=100%>
        <thead>";
  $num=0;		
 switch($tipetransaksi)
 {
 	case 1:
        $str="select a.kodebarang,a.satuan,a.jumlah,b.tanggal,b.kodept,b.tipetransaksi
		      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
			  on a.notransaksi=b.notransaksi
			  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";       
	    echo"<tr class=rowheader>
		       <td>No</td>
			   <td>".$_SESSION['lang']['tipe']."</td>			   
			   <td>".$_SESSION['lang']['tanggal']."</td>
			   <td>".$_SESSION['lang']['kodebarang']."</td>
			   <td>".$_SESSION['lang']['namabarang']."</td>
			   <td>".$_SESSION['lang']['satuan']."</td>
			   <td>".$_SESSION['lang']['kuantitas']."</td>
			   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
			   <td>".$_SESSION['lang']['kodeblok']."</td>
			 </tr>
			 </thead>
			 <tbody>";
		  $res=mysql_query($str);
		  $num=mysql_num_rows($res);
		  $no=0;
		  while($bar=mysql_fetch_object($res))
		  {
			$no+=1;
			//=======ambil namabarang
			$strc="select namabarang from ".$dbname.".log_5masterbarang where
			       kodebarang='".$bar->kodebarang."'";   
			$resc=mysql_query($strc);
			$namabarang="";
			while($barc=mysql_fetch_object($resc))
			{
				$namabarang=$barc->namabarang;
			}	    
			echo"<tr class=rowcontent id=row".$no.">
			  <td>".$no."</td>
			  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
			  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
			  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
			  <td>".$namabarang."</td>
			  <td id=satuan".$no." >".$bar->satuan."</td>
			  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
			  <td id=kodept".$no." >".$bar->kodept."</td>
			  <td id=kodeblok".$no.">".$bar->kodeblok."</td>
			  </tr>";										
		 }
		    break;
	case 2:
        $str="select a.kodebarang,a.satuan,a.jumlah,b.tanggal,b.kodept,b.tipetransaksi,a.kodeblok
		      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
			  on a.notransaksi=b.notransaksi
			  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";  
	    echo"<tr class=rowheader>
		       <td>No</td>
			   <td>".$_SESSION['lang']['tipe']."</td>			   
			   <td>".$_SESSION['lang']['tanggal']."</td>
			   <td>".$_SESSION['lang']['kodebarang']."</td>
			   <td>".$_SESSION['lang']['namabarang']."</td>
			   <td>".$_SESSION['lang']['satuan']."</td>
			   <td>".$_SESSION['lang']['kuantitas']."</td>
			   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
			   <td>".$_SESSION['lang']['kodeblok']."</td>
			 </tr>
			 </thead>
			 <tbody>";
		  $res=mysql_query($str);
		  $num=mysql_num_rows($res);
		  $no=0;
		  while($bar=mysql_fetch_object($res))
		  {
			$no+=1;
			//=======ambil namabarang
			$strc="select namabarang from ".$dbname.".log_5masterbarang where
			       kodebarang='".$bar->kodebarang."'";   
			$resc=mysql_query($strc);
			$namabarang="";
			while($barc=mysql_fetch_object($resc))
			{
				$namabarang=$barc->namabarang;
			}	    
			echo"<tr class=rowcontent id=row".$no.">
			  <td>".$no."</td>
			  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
			  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
			  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
			  <td>".$namabarang."</td>
			  <td id=satuan".$no." >".$bar->satuan."</td>
			  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
			  <td id=kodept".$no." >".$bar->kodept."</td>
			  <td id=kodeblok".$no.">".$bar->kodeblok."</td>
			  </tr>";										
		 }
		    break;			
	 case 3:
	        $str="select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,
			      b.tanggal,b.kodept,b.tipetransaksi
			      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
				  on a.notransaksi=b.notransaksi
				  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";       
	
			echo"<tr class=rowheader>
			       <td>No</td>
				   <td>".$_SESSION['lang']['tipe']."</td>			   
				   <td>".$_SESSION['lang']['tanggal']."</td>
				   <td>".$_SESSION['lang']['kodebarang']."</td>
				   <td>".$_SESSION['lang']['namabarang']."</td>
				   <td>".$_SESSION['lang']['satuan']."</td>
				   <td>".$_SESSION['lang']['kuantitas']."</td>
				   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
				   <td>".$_SESSION['lang']['sumber']."</td>
				   <td>".$_SESSION['lang']['kodeblok']."</td>
				 </tr>
				 </thead>
				 <tbody>";
			  $res=mysql_query($str);
			  $num=mysql_num_rows($res);
			  $no=0;
			  while($bar=mysql_fetch_object($res))
			  {
				$no+=1;
				//=======ambil namabarang
				$strc="select namabarang from ".$dbname.".log_5masterbarang where
				       kodebarang='".$bar->kodebarang."'";   
				$resc=mysql_query($strc);
				$namabarang="";
				while($barc=mysql_fetch_object($resc))
				{
					$namabarang=$barc->namabarang;
				}	    
				echo"<tr class=rowcontent id=row".$no.">
				  <td>".$no."</td>
				  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
				  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
				  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td id=satuan".$no." >".$bar->satuan."</td>
				  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
				  <td id=kodept".$no." >".$bar->kodept."</td>
				  <td id=gudangx".$no." >".$bar->gudangx."</td>
				  <td id=kodeblok".$no.">".$bar->kodeblok."</td>
				  </tr>";										
			 }		 	
		 	break;
		case 5:
			 $str="select a.kodebarang,a.satuan,a.jumlah,b.untukpt,a.kodeblok,
			      b.tanggal,b.kodept,b.tipetransaksi
			      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
				  on a.notransaksi=b.notransaksi
				  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";  
			echo"<tr class=rowheader>
			       <td>No</td>
				   <td>".$_SESSION['lang']['tipe']."</td>			   
				   <td>".$_SESSION['lang']['tanggal']."</td>
				   <td>".$_SESSION['lang']['kodebarang']."</td>
				   <td>".$_SESSION['lang']['namabarang']."</td>
				   <td>".$_SESSION['lang']['satuan']."</td>
				   <td>".$_SESSION['lang']['kuantitas']."</td>
				   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
				   <td>".$_SESSION['lang']['pt']."</td>
				   <td>".$_SESSION['lang']['kodeblok']."</td>
				 </tr>
				 </thead>
				 <tbody>";
			  $res=mysql_query($str);
			  $num=mysql_num_rows($res);
			  $no=0;
			  while($bar=mysql_fetch_object($res))
			  {
				$no+=1;
				//=======ambil namabarang
				$strc="select namabarang from ".$dbname.".log_5masterbarang where
				       kodebarang='".$bar->kodebarang."'";   
				$resc=mysql_query($strc);
				$namabarang="";
				while($barc=mysql_fetch_object($resc))
				{
					$namabarang=$barc->namabarang;
				}	    
				echo"<tr class=rowcontent id=row".$no.">
				  <td>".$no."</td>
				  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
				  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
				  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td id=satuan".$no." >".$bar->satuan."</td>
				  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
				  <td id=kodept".$no." >".$bar->kodept."</td>
				  <td id=untukpt".$no." >".$bar->untukpt."</td>
				  <td id=kodeblok".$no.">".$bar->kodeblok."</td>
				  </tr>";										
			 }
		 	break;
		case 6:
			$str="select a.kodebarang,a.satuan,a.jumlah,b.untukpt,a.kodeblok,
			      b.tanggal,b.kodept,b.tipetransaksi
			      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
				  on a.notransaksi=b.notransaksi
				  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";  
			echo"<tr class=rowheader>
			       <td>No</td>
				   <td>".$_SESSION['lang']['tipe']."</td>			   
				   <td>".$_SESSION['lang']['tanggal']."</td>
				   <td>".$_SESSION['lang']['kodebarang']."</td>
				   <td>".$_SESSION['lang']['namabarang']."</td>
				   <td>".$_SESSION['lang']['satuan']."</td>
				   <td>".$_SESSION['lang']['kuantitas']."</td>
				   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
				 </tr>
				 </thead>
				 <tbody>";
			  $res=mysql_query($str);
			  $num=mysql_num_rows($res);
			  $no=0;
			  while($bar=mysql_fetch_object($res))
			  {
				$no+=1;
				//=======ambil namabarang
				$strc="select namabarang from ".$dbname.".log_5masterbarang where
				       kodebarang='".$bar->kodebarang."'";   
				$resc=mysql_query($strc);
				$namabarang="";
				while($barc=mysql_fetch_object($resc))
				{
					$namabarang=$barc->namabarang;
				}	    
				echo"<tr class=rowcontent id=row".$no.">
				  <td>".$no."</td>
				  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
				  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
				  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td id=satuan".$no." >".$bar->satuan."</td>
				  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
				  <td id=kodept".$no." >".$bar->kodept."</td>
				  </tr>";										
			 }
		 	break;
		case 7:
			 $str="select a.kodebarang,a.satuan,a.jumlah,b.gudangx,a.kodeblok,
			      b.tanggal,b.kodept,b.tipetransaksi
			      from ".$dbname.".log_transaksidt a left join  ".$dbname.".log_transaksiht b 
				  on a.notransaksi=b.notransaksi
				  where a.notransaksi='".$notransaksi."' and b.kodegudang='".$gudang."'";       
	
			echo"<tr class=rowheader>
			       <td>No</td>
				   <td>".$_SESSION['lang']['tipe']."</td>			   
				   <td>".$_SESSION['lang']['tanggal']."</td>
				   <td>".$_SESSION['lang']['kodebarang']."</td>
				   <td>".$_SESSION['lang']['namabarang']."</td>
				   <td>".$_SESSION['lang']['satuan']."</td>
				   <td>".$_SESSION['lang']['kuantitas']."</td>
				   <td>".$_SESSION['lang']['ptpemilikbarang']."</td>
				   <td>".$_SESSION['lang']['tujuan']."</td>
				   <td>".$_SESSION['lang']['kodeblok']."</td>
				 </tr>
				 </thead>
				 <tbody>";
			  $res=mysql_query($str);
			  $num=mysql_num_rows($res);
			  $no=0;
			  while($bar=mysql_fetch_object($res))
			  {
				$no+=1;
				//=======ambil namabarang
				$strc="select namabarang from ".$dbname.".log_5masterbarang where
				       kodebarang='".$bar->kodebarang."'";   
				$resc=mysql_query($strc);
				$namabarang="";
				while($barc=mysql_fetch_object($resc))
				{
					$namabarang=$barc->namabarang;
				}	    
				echo"<tr class=rowcontent id=row".$no.">
				  <td>".$no."</td>
				  <td id=tipe".$no." title=\"1=Masuk,2=Pengembalian pengeluaran, 3=penerimaan mutasi,5=Pengeluaran,6=Pengembalian penerimaan,7 pengeluaran mutasi\">".$bar->tipetransaksi."</td>
				  <td id=tanggal".$no." >".tanggalnormal($bar->tanggal)."</td>
				  <td id=kodebarang".$no." >".$bar->kodebarang."</td>
				  <td>".$namabarang."</td>
				  <td id=satuan".$no." >".$bar->satuan."</td>
				  <td  id=jumlah".$no." align=right>".$bar->jumlah."</td>
				  <td id=kodept".$no." >".$bar->kodept."</td>
				  <td id=gudangx".$no." >".$bar->gudangx."</td>
				  <td id=kodeblok".$no.">".$bar->kodeblok."</td>
				  </tr>";										
			 }			
		    break;
		default:
          echo" Error: Unknown transaction type"; 				

  }
   echo"</tbody><tfoot></tfoot></table>
   <center>
     <button onclick=\"prosesPosting(".$no.",'".$tipetransaksi."','".$notransaksi."');\" class=mybutton>".$_SESSION['lang']['posting']."</button>
	 <button onclick=closeDialog() class=mybutton>".$_SESSION['lang']['cancel']."</button>
   </center>
   </div>";
}
else
{
	echo " Error: Transaction Period missing";
}
?>