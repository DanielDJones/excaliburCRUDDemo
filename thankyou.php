<?php

include('Framework/ViewChainVars.php');

$ARR_Chain['MyColour'] ?? 'Not Found';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <link rel="stylesheet" href="CSS/Main.css">
</head>
<body>
<div class="Column AlignCenter">
    <Section class="w80 Shadow Pad Round BGSecondary">
        <h1>Thank You</h1>
        <p>Thank you for submiting the form. Your colour is: <?= $ARR_Chain['MyColour'] ?></p>
    </Section>
</body>
</html>