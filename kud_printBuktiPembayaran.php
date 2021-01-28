<?
//@Copy nangkoelframework
require_once('master_validation.php');
include('lib/nangkoelib.php');
include_once('lib/zLib.php');
echo open_body();
include('master_mainMenu.php');
?>
<script type="text/javascript" src="js/zMaster.js" /></script>
<script type="text/javascript" src="js/kud_printBuktiPembayaran.js" /></script>
    
<div id="result">
<script>loadData()</script>
</div>



<?php
echo close_body();
?>