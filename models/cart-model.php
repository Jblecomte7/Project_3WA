<?php

$cart = new Database();


// If user isn't connected, redirection to login page :
if (!is_connected()) {
    header('Location: ./index.php?page=Login');
    exit();
}

// Delete cart product method :

if (isset($_POST['confirmer']) && !empty($_POST['confirmer'])) {
    $params = [
        'id' => htmlentities($_POST['confirmer'])
    ];
    $deleteCartProduct = $cart->prepare('DELETE FROM panier WHERE product_id = :id', $params);
}



$params = [
    'user_id' => $_SESSION['id']
];

$showCart = $cart->prepare('SELECT product_id FROM panier WHERE user_id = :user_id', $params);


$cartProduct = [];
foreach ($showCart as $product) {
    $params = [
        'id' => $product['product_id']
    ];
    $productCartSQL = $cart->prepare('SELECT products.id, products.name, products.price, products.size, product_images.image FROM products JOIN product_images WHERE products.id = :id AND products.id = product_images.product_id ORDER BY product_images.id LIMIT 1', $params);

    $cartProduct = array_merge($productCartSQL, $cartProduct);
};

// Display total price :
$totalPrice = 0;
foreach ($cartProduct as $cart) {
    $totalPrice += $cart['price'];
}
