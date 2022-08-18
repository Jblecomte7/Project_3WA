<?php

$cartCounter = new Database();

if (isset($_SESSION['id'])) {
    $parameters = [
        ':id' => $_SESSION['id']
    ];
    $userCart = $cartCounter->prepare('SELECT id FROM panier WHERE user_id = :id', $parameters);

    $totalProducts = 0;
    foreach ($userCart as $cart) {
        $totalProducts += 1;
    }
}
