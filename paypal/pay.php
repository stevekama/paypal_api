<?php

use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

require '../src/start.php';
require '../src/functions.php';
session_start();

if(isset($_GET['approved'])) {

    $approved = $_GET['approved'] === 'true';

    if($approved) {

        $payerId = $_GET['PayerID'];

        // Get payment Id from database
        $paymentId = $db->prepare("
            SELECT payment_id 
            FROM transactions_paypal 
            WHERE hash = :hash

        ");

        $paymentId->execute([
            'hash' => $_SESSION['paypal_hash']
        ]);

        $paymentId = $paymentId->fetchObject()->payment_id;

        $payment = Payment::get($paymentId, $api);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        // Execute Paypal Payment - Charge user
        $payment->execute($execution, $api);

        // update transaction
        $updateTransaction = $db->prepare("
            UPDATE transactions_paypal 
            SET complete = 1 
            WHERE payment_id = :payment_id
        ");

        $updateTransaction->execute([
            "payment_id" => $paymentId
        ]);

        /// set user as  member
        $setMember = $db->prepare("
            UPDATE users 
            SET member = 1
            WHERE id = :user_id
        ");

        $setMember->execute([
            "user_id" => $_SESSION['user_id']
        ]);

        // Unset Paypal Hash 
        unset($_SESSION['paypal_hash']);

        redirect_to('../member/complete.php');
    } else {
        redirect_to('../paypal/cancel.php');
    }

}