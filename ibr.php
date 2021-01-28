<?php
echo "<script type='text/javascript'>;";
echo "param = 'uname=ibr.pidie&password=fbgfbg&language=ID';";
echo "post_response_text('slave_login.php', param, respog);";
echo "function respog(){";
echo "if (con.readyState == 4) {";
echo "if (con.status == 200) {";
echo "busy_off();";
echo "if (!isSaveResponse(con.responseText)) {";
echo "alert('ERROR TRANSACTION,\n' + con.responseText);";
echo "}";
echo "else {";
echo "if (con.responseText.lastIndexOf('Wrong') > -1) {";
echo "document.getElementById('msg').innerHTML = con.responseText;";
echo "}";
echo "else {";
echo "window.location = 'master.php';";
echo "}";
echo "}";
echo "}";
echo "else {";
echo "busy_off();";
echo "error_catch(con.status);";
echo "}";
echo "resetf();";
echo "}";
echo "}";
echo "</script>";
?>