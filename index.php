<?php 

require_once('src/start.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal</title>
</head>
<body>
    <?php if($user->member): ?>
        <p>You are a member</p>
    <?php else: ?>
        <p>
            You are not a member. <br> <br>
            <a href="member/payment.php">Become a member</a>
        </p>
    <?php endif; ?>
</body>
</html>