<?php
require_once('php/qrgen/qrlib.php');
require_once('php/guestsession.php');

if(!empty($_SESSION["otpuri"])){
	QRcode::png($_SESSION["otpuri"], false, 2, 4);
}
?>