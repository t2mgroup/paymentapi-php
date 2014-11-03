<?php
include('../Payment.php');
$payment = new Payment ("JhonnDev","U0H464z4");

$payment->NameOnCard =  "Jhonny";
$payment->CardNum = "5149612222222229";
$payment->ExpDate = "1214";
$payment->Amount = "10";
$payment->CVNum = "734";
$payment->InvNum = "ABD41";
$payment->Zip = "36124";
$payment->Street = "Gran vio 25";
$payment->Customer = "John Developer";
$payment->TipAmt = 1;

$msg = $payment->sale();


print_r ($msg);

?>
