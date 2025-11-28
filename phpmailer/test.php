<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require './src/PHPMailer.php';
require './src/SMTP.php';
require './src/Exception.php';

$mail = new PHPMailer(true);
echo "PHPMailer cargado correctamente ✅";
