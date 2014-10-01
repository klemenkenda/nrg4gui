<?PHP
//---------------------------------------------------------------------
// FILE: config.inc.php
// AUTHOR: Klemen Kenda
// DESCRIPTION: EnStreamM GUI config file
// DATE: 16/12/2011
// HISTORY:
//  2012/08/31: Added resource module config, EPS and SR config, SOS
//              config
//---------------------------------------------------------------------

// mysql config -------------------------------------------------------
$mysql_user = "root";
$mysql_pass = "";
$mysql_host = "localhost";
$mysql_dbase = "nrg4cast";

// mail config --------------------------------------------------------
$webmaster_mail = "klemen.kenda@ijs.si";

// filesystem config --------------------------------------------------
$filesystem_root = "D:\\Demos\\nrg4cast\\www\\";

// miner config -------------------------------------------------------
$miner["url"] = "http://127.0.0.1";
$miner["port"] = 9889;
$miner["stream_timeout"] = 20;
$miner["socket_timeout"] = 10;

?>