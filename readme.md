RisePay-PHP -- Simple Payment Payment API wrapper

<hr>
You can request developer credentials from our <a href='http://sales.payment.com/rise-dev-access.html'>Dev Portal</a>.</br> If you would like to certify your application, then submit a <a href='http://sales.payment.com/rise-cert-lab-access.html'>Cert Lab request</a>.
<hr>

### Table of Contents
**[Initialization](#initialization)**

**[Sale Transaction](#sale-transaction)**

**[Auth Transaction](#authorization-transaction)**

**[Void Transaction](#void-transaction)**

**[Return Transaction](#return-transaction)**

**[Capture Transaction](#capture-transaction)**

### Initialization
To utilize this class, first import Payment.php into your project, and require it.

```php
require_once ('Payment.php');
```

After that, create a new instance of the class.

```php
$payment = new Payment ('gatewayApiUser', 'userPassword');
```

To obtain an initialized instance of the class from another class without:
```php
    $db = Payment::getInstance();
```

### Sale Transaction
To make a purchase using a credit card:

Functional API:
```php
$payment = new Payment ("demo","demo");

$data['NameOnCard'] = "John Doe";
$data['CardNum'] = "4111111111111111";
$data['ExpDate'] = "1215";
$data['Amount'] = "10";
$data['CVNum'] = "734";

$resp = $payment->sale($data);
if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}

```

Object API:
```php
$payment = new Payment ("demo","demo");

$payment->NameOnCard = "John Doe";
$payment->CardNum = "4111111111111111";
$payment->ExpDate = "1215";
$payment->Amount = "10";
$payment->CVNum = "734";

$resp = $payment->sale();
...
```

### Authorization Transaction
To make an authorization using a credit card:

Functional API:
```php
$payment = new Payment ("demo","demo");

$data['NameOnCard']= "John Doe";
$data['CardNum']="4111111111111111";
$data['ExpDate']="1215";
$data['Amount']="10";
$data['CVNum']="734";


$resp = $payment->auth($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}
```

### Void Transaction

To void a transaction:

Functional API:
```php
$payment = new Payment ("demo","demo");

$data['NameOnCard'] = "John Doe";
$data['CardNum'] = "4111111111111111";
$data['ExpDate'] = "1215";
$data['Amount'] = "10";
$data['CVNum'] = "734";
$data['PNRef'] = "24324";

$resp = $payment->void($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}
```

### Capture Transaction

To capture a previously Authorized transaction:

Functional API
```php
$payment = new Payment ("demo","demo");

$data['NameOnCard'] = "John Doe";
$data['CardNum'] = "4111111111111111";
$data['ExpDate'] = "1215";
$data['Amount'] = "10";
$data['CVNum'] = "734";
$data['PNRef'] = "24324";

$resp = $payment->capture($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}
```

### Return Transaction

To return a payment for already batched transaction:

Functional API

```php
$payment = new Payment ("demo","demo");

$data['NameOnCard'] = "John Doe";
$data['CardNum'] = "4111111111111111";
$data['ExpDate'] = "1215";
$data['Amount'] = "10";
$data['CVNum'] = "734";
$data['PNRef'] = "24324";

$resp = $payment->returnTrans($data);

if ($resp->Approved) {
    echo "Approved. Transaction ID = " . $resp->PNRef;
    echo "AuthCode = " . $resp->AuthCode;
} else {
    echo "Declined: " . $resp->Message;
    print_r ($resp);
}
```

To see complete list of RisePay API variables, review our <a href='https://secure.suregate.net/vt/nethelp/Documents/processcreditcard.htm'>online documentation</a>. </br>
