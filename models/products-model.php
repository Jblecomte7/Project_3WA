<?php

$showProducts = new Database();

//////////////////////
/// CARDS CREATION ///
//////////////////////

// Select all product's informations needed to create a product card (associative array)
$productInfos = $showProducts->prepare('SELECT products.id, products.name, products.description, products.price, products.size, categories.type FROM products JOIN categories ON products.categories_id = categories.id', []);

// Method (foreach loop) to create as cards as products in the database
foreach ($productInfos as $key => $value) {
    // $key => product line in database
    $params = [
        'id' => $value['id']
    ];
    // Select all images from product_images's table linked to product_id
    $queryImg = $showProducts->prepare('SELECT image FROM product_images WHERE product_id = :id', $params);
    // Ceate an array to add all product_id's images :
    $img = [];
    // Add all images : for each product, insert into $img array all the images linked by product_id :
    foreach ($queryImg as $url) {
        array_push($img, $url['image']);
    }
    $productInfos[$key]['image'] = $img;
}

//////////////////////////
// ADD PRODUCTS TO CART //
//////////////////////////

if (isset($_GET['addToCart']) && !empty($_GET['addToCart'])) {

    // If user isn't connected, redirection to login page :
    if (!is_connected()) {
        header('Location: ./index.php?page=Login');
        exit();
    }

    // Add in 'panier' table in database the product card : get $_SESSION['id'] and check if the product exists in user's cart : 
    $params = [
        'user_id' => $_SESSION['id'],
        'product_id' => $_GET['addToCart']
    ];
    $checkCart = $showProducts->prepare('SELECT id FROM panier WHERE user_id = :user_id AND product_id = :product_id', $params, true);

    // If product doesn't exist in user's cart, insert product in cart table :
    if ($checkCart === false) {
        $addCart = $showProducts->prepare('INSERT INTO panier (user_id, product_id) VALUES ((SELECT id FROM users WHERE id = :user_id), (SELECT id FROM products WHERE id = :product_id))', $params);
        header('Location: ./index.php?page=Produits');
        exit();
        // Else, display an error message :
    } else {
        $errors['addToCart'] = 'Ce produit est déjà dans votre panier !';
    }
}

//////////////////////////////
// ADD PRODUCTS TO WISHLIST //
//////////////////////////////

// Same method as 'Add to cart' :

if (isset($_GET['addToFav']) && !empty($_GET['addToFav'])) {

    // If user isn't connected, redirection to login page :
    if (!is_connected()) {
        header('Location: ./index.php?page=Login');
        exit();
    }

    $params = [
        'user_id' => $_SESSION['id'],
        'product_id' => $_GET['addToFav']
    ];
    $checkFav = $showProducts->prepare('SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id', $params, true);

    if ($checkFav === false) {
        $addFav = $showProducts->prepare('INSERT INTO wishlist (user_id, product_id) VALUES ((SELECT id FROM users WHERE id = :user_id), (SELECT id FROM products WHERE id = :product_id))', $params);
        header('Location: ./index.php?page=Produits');
        exit();
    } else {
        $errors['addToFav'] = 'Ce produit est déjà dans votre liste d\'envies !';
    }
}

// Method to delete URL values after closing modal :

function strip_param_from_url($param)
{
    $stripedURL = '';
    foreach ($_GET as $key => $value) {
        if (is_array($param)) {
            if (!in_array($key, $param)) {
                $stripedURL .= '&' . $key . '=' . $value;
            }
        } else {
            if ($key !== $param) {
                $stripedURL .= '&' . $key . '=' . $value;
            }
        }
    }
    return $stripedURL;
}

// FILTER

$filterResult = [];
foreach ($_POST as $elementK => $elementV) {
    $filterResult = [...$filterResult, $elementK];
}
