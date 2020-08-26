<?php

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Exception\PPConnectionException;

require '../src/start.php';
require '../src/functions.php';

$payer = new Payer();
$details = new Details();
$amount = new Amount();
$transaction = new Transaction();
$payment = new Payment();
$redirectUrls = new RedirectUrls();

// payer
$payer->setPaymentMethod('paypal');

// Details
$details->setShipping('2.00')->setTax('0.00')->setSubtotal('20.00');

// Amount
$amount->setCurrency('USD')->setTotal('22.00')->setDetails($details);

// Transaction
$transaction->setAmount($amount)->setDescription("Membership");

$payment->setIntent('sale')->setPayer($payer)->setTransactions([$transaction]);

// Redirect URLS
$redirectUrls->setReturnUrl(base_url().'paypal/pay.php?approved=true')->setCancelUrl(base_url().'paypal/pay.php?approved=false');

$payment->setRedirectUrls($redirectUrls);

try {

    $payment->create($api);

    session_start();
    // Generate and store hash
    $hash = md5($payment->getId());
    $_SESSION['paypal_hash'] = $hash;

    // Prepare and execute transaction store
    $store = $db->prepare("
        INSERT INTO transactions_paypal (user_id, payment_id, hash, complete)
        VALUES(:user_id, :payment_id, :hash, 0)
    ");

    $store->execute([
        'user_id' => $_SESSION['user_id'],
        'payment_id' => $payment->getId(),
        'hash' => $hash
    ]);

} catch(PPConnectionException $e) {
    // Perhaps log an error
    header('Location: ../paypal/error.php');
}

foreach($payment->getLinks() as $link){
    if($link->getRel() == "approval_url") {
        $redirectUrl = $link->getHref();
    }
}

redirect_to($redirectUrl);