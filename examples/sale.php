<?php
include('../Payment.php');
$payment = new Payment ("demo","demo");
$payment->setGatewayUrl = "https://secure.suregate.net/ws/transact.asmx/ProcessCreditCard";

$data['NameOnCard']= "Jhonny";
$data['CardNum']="5149612222222229";
$data['ExpDate']="1215";
$data['Amount']="18";
$data['CVNum']="734";
$data['InvNum']="ABD42";
$data['Zip']="36124";
$data['Street']="Gran vio 25";
$data['Customer']="John Developer";
$data['TipAmt']=1;

$resp = $payment->sale($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "\nAuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
}
echo "\n";
print_r ($resp);
?>
