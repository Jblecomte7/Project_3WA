<?php

// If user isn't connected, redirection to login page :
if (!is_connected()) {
    header('Location: ./index.php?page=Login');
    exit();
}

// Errors : array of all error messages :
$errors = array();


////////////////////////////////////////////////////
////////// METHODS - Display informations //////////
////////////////////////////////////////////////////

$showInfos = new Database();

// Method to pick user's informations (name, firstname, email, password) from database (table -> users) :
$params = [
    'id' => $_SESSION['id']
];
$showAll = $showInfos->prepare('SELECT Name, FirstName, email,password FROM users WHERE id = :id', $params, true);

// Var creation to show them in user account :
$userName = $showAll['Name'];
$userFirstName = $showAll['FirstName'];
$userEmail = $showAll['email'];
$userPassword = isset($showAll['password']) ? $showAll['password'] : '?';

// Method to pick user's other informations (phone, birthday, address) from database (table -> user_attributes) :
$params = [
    'id' => $_SESSION['id']
];
$userInfos = $showInfos->prepare('SELECT * FROM user_attributes WHERE user_id = :id', $params, true);
$userPhone = $userInfos ? $userInfos['phone'] : '';
$userBirth = $userInfos ? $userInfos['birthday'] : '';
$userAddress = $userInfos ? $userInfos['address'] : '';
$userCP = $userInfos ? $userInfos['CP'] : '';
$userCity = $userInfos ? $userInfos['city'] : '';


///////////////////////////////////////////////////
////////// METHODS - Update informations //////////
///////////////////////////////////////////////////

// UPDATE user's password : 

// Password change default state : false;
$passSuccess = false;

if (!empty($_POST['password'])) {
    $userCurrentPassword = htmlentities($_POST['actualPassword']);
    $userNewPassword = htmlentities($_POST['password']);
    
    if (password_verify($userCurrentPassword, $userPassword)) {
        if (!password_verify($userNewPassword, $userPassword)) {
            if (strlen($userNewPassword) > 32 || strlen($userNewPassword) < 8) {
                $errors['password'] = "Votre nouveau mot de passe doit contenir entre 8 et 32 caractères";
            } else {
                $params = [
                    'userPassword' => password_hash($userNewPassword, PASSWORD_DEFAULT),
                    'user_id' => $_SESSION['id']
                ];
                $addInfos = $showInfos->prepare('UPDATE users SET password = :userPassword WHERE id = :user_id', $params, true);
                $passSuccess = true;
            }
        } else {
            $errors['password'] = "Votre nouveau mot de passe ne peut pas être identique à l'ancien";
        }
    }
}

// UPDATE user's infos (phone) :

if (!empty($_POST['tel'])) {
    if (strlen(htmlentities($_POST['tel'])) !== 10) {
        $errors['tel'] = "Le format de votre numéro de téléphone est incorrect";
    } else {
        $userPhone = htmlentities($_POST['tel']);
        $params = [
            'userPhone' => $userPhone,
            'user_id' => $_SESSION['id']
        ];
        $addInfos = $showInfos->prepare('UPDATE user_attributes SET phone = :userPhone WHERE user_id = :user_id', $params);
    }
}

// UPDATE user's infos (birthday) :

if (!empty($_POST['birth'])) {
    $userBirth = htmlentities(date($_POST['birth']));
    $params = [
        'userBirth' => $userBirth,
        'user_id' => $_SESSION['id']
    ];
    $addInfos = $showInfos->prepare('UPDATE user_attributes SET birthday = :userBirth WHERE user_id = :user_id', $params);
}

// UPDATE user's address

$addressSuccess = false;

if ((isset($_POST['address']) && !empty($_POST['address'])) || (isset($_POST['cp']) && !empty($_POST['cp'])) || (isset($_POST['city']) && !empty($_POST['city']))) {
    if (strlen(htmlentities($_POST['address'])) > 200) {
        $errors['address'] = "Votre adresse doit contenir moins de 200 caractères";
    } else if (strlen(htmlentities($_POST['cp'])) !== 5) {
        $errors['CP'] = "Votre code postal doit contenir exactement 5 chiffres";
    } else if (strlen(htmlentities($_POST['city'])) > 200) {
        $errors['city'] = "Votre ville doit contenir moins de 200 caractères";
    } else {
        $userAddress = htmlentities($_POST['address']);
        $userCP = htmlentities($_POST['cp']);
        $userCity = htmlentities($_POST['city']);
        $params = [
            'userAddress' => $userAddress,
            'userCP' => $userCP,
            'userCity' => $userCity,
            'user_id' => $_SESSION['id'],
        ];
        $addInfos = $showInfos->prepare('UPDATE user_attributes, users SET address = :userAddress, CP = :userCP, city = :userCity WHERE user_id = :user_id', $params);
        $addressSuccess = true;
    }
}

///////////////////////////////////////////////////
/////////////// METHODS - Whishlist ///////////////
///////////////////////////////////////////////////

$fav = new Database();

// Delete cart product method :
if (isset($_POST['confirmer']) && !empty($_POST['confirmer'])) {
    
    $params = [
        'id' => htmlentities($_POST['confirmer'])
    ];
    $deleteFavProduct = $fav->prepare('DELETE FROM wishlist WHERE product_id = :id', $params);
}


// Show wishlist products :
$params = [
    'user_id' => $_SESSION['id']
];
$showFav = $fav->prepare('SELECT wishlist.product_id, products.id, products.name, products.size, products.price FROM wishlist JOIN products ON products.id = wishlist.product_id WHERE user_id = :user_id', $params);

foreach ($showFav as $key => $value) {
    $params = [
        'id' => $value['id']
    ];
    $queryImg = $fav->prepare('SELECT image FROM product_images WHERE product_id = :id', $params);
    $img = [];
    foreach ($queryImg as $productImages) {
        array_push($img, $productImages['image']);
    }
    $showFav[$key]['image'] = $img;
}

// Add products to Cart : 
$showProducts = new Database();

$productInfos = $showProducts->prepare('SELECT products.id, products.name, products.description, products.price, products.size, categories.type FROM products JOIN categories ON products.categories_id = categories.id', []);
if (isset($_GET['addToCart']) && !empty($_GET['addToCart'])) {
    // If user isn't connected, redirection to login page :
    if (!is_connected()) {
        header('Location: ./index.php?page=Login');
        exit();
    }
    $params = [
        'user_id' => $_SESSION['id'],
        'product_id' => $_GET['addToCart']
    ];
    $checkCart = $showProducts->prepare('SELECT id FROM panier WHERE user_id = :user_id AND product_id = :product_id', $params, true);

    if ($checkCart === false) {
        $addCart = $showProducts->prepare('INSERT INTO panier (user_id, product_id) VALUES ((SELECT id FROM users WHERE id = :user_id), (SELECT id FROM products WHERE id = :product_id))', $params);
        header('Location: ./index.php?page=MonCompte');
        exit();
    } else {
        $errors['addToCart'] = 'Ce produit est déjà dans votre panier !';
    }
}
