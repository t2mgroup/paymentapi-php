<?php
include('../Payment.php');
$payment = new Payment ("JhonnDev","U0H464z4");

$data['NameOnCard']= "Jhonny";
$data['CardNum']="5149612222222229";
$data['ExpDate']="1214";
$data['Amount']="10";
$data['CVNum']="734";
//$data['InvNum']="ABD41";
$data['Zip']="36124";
$data['Street']="Gran vio 25";
$data['Customer']="John Developer";
$data['TipAmt']=1;

$resp = $payment->sale($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}

?>
