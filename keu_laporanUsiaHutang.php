<?php
require_once('master_validation.php');
require_once('config/connection.php');
require_once('lib/nangkoelib.php');

	$pt=$_POST['pt'];
	$gudang=$_POST['gudang'];
	$tanggalpivot=$_POST['tanggalpivot'];
if($gudang!='')
{
		$str="select * from ".$dbname.".aging_sch_vw
		where kodeorg = '".$gudang."' and (nilaiinvoice > dibayar or dibayar is NULL)
		";
}else
if($pt!='')
{
		$str="select * from ".$dbname.".aging_sch_vw
		where kodeorg = '".$pt."' and (nilaiinvoice > dibayar or dibayar is NULL)
		";
}else
{
		$str="select * from ".$dbname.".aging_sch_vw 
		where nilaiinvoice > dibayar or dibayar is NULL
		";
}
//echo "qwe:".$str;

function tanggalbiasa($_q)
{
 $_q=str_replace("-","",$_q);
 $_retval=substr($_q,4,4)."-".substr($_q,2,2)."-".substr($_q,0,2);
 return($_retval);
}

//=================================================
	$res=mysql_query($str);
	$no=0;
	if(@mysql_num_rows($res)<1)
	{
		echo"<tr class=rowcontent><td colspan=13>".$_SESSION['lang']['tidakditemukan']."</td></tr>";
	}
	else
	{
            $total0=$total15=$total30=$total45=$total100=$totaldibayar=0;
            $totalinvoice=0;
		while($bar=mysql_fetch_object($res))
		{
			$namasupplier	=$bar->namasupplier;
			if($namasupplier=='')$namasupplier='&nbsp;';
			$noinvoice	=$bar->noinvoice;
			$tanggal	=$bar->tanggal; 
			$jatuhtempo 	=$bar->jatuhtempo;
                        $nopokontrak    =$bar->nopo;
                        $nilaipo        =$bar->kurs*$bar->nilaipo;
                        $nilaikontrak   =$bar->kurs*$bar->nilaikontrak;
			$nilaiinvoice 	=$bar->kurs*$bar->nilaiinvoice;
                        $totalinvoice+=$nilaiinvoice;
			$dibayar 	=$bar->kurs*$bar->dibayar;
                        $sisainvoice    =$nilaiinvoice-$dibayar;
                        $nilaipokontrak =$nilaipo;
                        if($nilaikontrak>0)$nilaipokontrak=$nilaikontrak;
//			$date1=date('Y-m-d');
			$date1=tanggalbiasa($tanggalpivot);
			$diff =(strtotime($jatuhtempo)-strtotime($date1));
			$outstd =floor(($diff)/(60*60*24));
			//if($outstd<1)$outstd=0;
			$flag0=$flag15=$flag30=$flag45=$flag100=0;
			if($outstd!=0)$outstd*=-1;
			if($outstd<=0)$flag0=1; 
			if(($outstd>=1)and($outstd<=15))$flag15=1;
			if(($outstd>=16)and($outstd<=30))$flag30=1;
			if(($outstd>=31)and($outstd<=45))$flag45=1;
			if($outstd>45)$flag100=1;
                        if($flag0==1)$total0+=$sisainvoice;
                        if($flag15==1)$total15+=$sisainvoice;
                        if($flag30==1)$total30+=$sisainvoice;
                        if($flag45==1)$total45+=$sisainvoice;
                        if($flag100==1)$total100+=$sisainvoice;
                        $totaldibayar+=$dibayar;
			if($jatuhtempo=='0000-00-00'){ $outstd=''; $jatuhtempo=''; }else{ $jatuhtempo=tanggalnormal($jatuhtempo); }
//			if($dibayar>=$nilaiinvoice)continue;
			$no+=1;
//				  <td align=right width=100>".number_format($nilaiinvoice,2)."</td>
			
		echo"<tr class=rowcontent>
				  <td rowspan=2 align=center width=20>".$no."</td>
				  <td rowspan=2 nowrap>".$tanggal."</td> 
				  <td nowrap>".$noinvoice."</td> 
				  <td rowspan=2 align=center>".$jatuhtempo."</td>
				  <td rowspan=2 align=center>".$nopokontrak."</td>
				  <td rowspan=2 align=right>".number_format($nilaipokontrak,2)."</td>
				  <td rowspan=2 align=right>".number_format($nilaiinvoice,2)."</td>
				  <td rowspan=2 align=right>";
				  if($flag0==1)echo number_format($sisainvoice,2); echo"</td>
				  <td rowspan=2 align=right>";
				  if($flag15==1)echo number_format($sisainvoice,2); echo"</td>
				  <td rowspan=2 align=right>";
				  if($flag30==1)echo number_format($sisainvoice,2); echo"</td>
				  <td rowspan=2 align=right>";
				  if($flag45==1)echo number_format($sisainvoice,2); echo"</td>
				  <td rowspan=2 align=right>";
				  if($flag100==1)echo number_format($sisainvoice,2); echo"</td>
				  <td rowspan=2 align=right width=100>".number_format($dibayar,2)."</td>
				  <td rowspan=2 align=right>".$outstd."</td>
			</tr><tr class=rowcontent>
				  <td nowrap>".$namasupplier."</td> 
			</tr>"; 		
		}
		echo"<tr class=rowtitle>
				  <td colspan=6 align=center width=20>TOTAL</td>
				  <td align=right>";
				  echo number_format($totalinvoice,2); echo"</td>
				  <td align=right>";
				  echo number_format($total0,2); echo"</td>
				  <td align=right>";
				  echo number_format($total15,2); echo"</td>
				  <td align=right>";
				  echo number_format($total30,2); echo"</td>
				  <td align=right>";
				  echo number_format($total45,2); echo"</td>
				  <td align=right>";
				  echo number_format($total100,2); echo"</td>
				  <td align=right width=100>".number_format($totaldibayar,2)."</td>
				  <td align=right>&nbsp;</td>
			</tr>";                 
	}


?>