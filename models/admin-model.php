<?php

if (!is_connected()) {
    header('Location: ./index.php?page=Login');
    exit();
}

/////////////////////////////////
///////// USERS SECTION /////////
/////////////////////////////////

// New instance of Database class : 
$users = new Database();

// Delete user method :

if (isset($_POST['confirmer']) && !empty($_POST['confirmer'])) {
    $params = [
        'id' => htmlentities($_POST['confirmer'])
    ];
    $deleteUser = $users->prepare('DELETE FROM users WHERE id = :id', $params);
}

// Refresh users' table :
$allUsers = $users->prepare('SELECT users.id, users.Name, users.FirstName, users.email, users.register_date, role.name FROM users JOIN role ON role.id = users.role_id', []);


////////////////////////////////////
///////// PRODUCTS SECTION /////////
////////////////////////////////////

$products = new Database();

// Delete product
if (isset($_POST['confirmer']) && !empty($_POST['confirmer'])) {
    $params = [
        'id' => htmlentities($_POST['confirmer'])
    ];
    $deleteProduct = $products->prepare('DELETE FROM products WHERE id = :id', $params);
}

// Show all products

$allProducts = $products->prepare('SELECT products.id, products.name, products.price, products.size, categories.type  FROM products JOIN categories ON products.categories_id = categories.id', []);

// Upload image method :

// $nameOfInputUpload = "image";
$isPost = $_SERVER['REQUEST_METHOD'] === "POST";
$myImage = isset($_FILES) ? $_FILES : null;
$fileLocation = './vendor/images/';

// si il y a un fichier qui a été envoyé sans erreur et qu'il a le name image
$url = [];

foreach ($myImage as $file) {
    if ((isset($file) && $file['error'] == 0)) {
        // If image <= 3mo :
        if ($file['size'] <= 3000000) {
            $informationImage = pathinfo($file['name']);
            $extensionImage = $informationImage['extension'];
            $extensionArray = ['JPG', 'JPEG', 'GIF', 'PNG', 'WEBP'];
            // If image extension is in $extensionArray
            if (in_array(strtoupper($extensionImage), $extensionArray)) {
                // Define file location and file name
                $urlImage  = $fileLocation . time() . rand() . '.' . $extensionImage;
                // Move file in the right folder
                move_uploaded_file($file['tmp_name'], $urlImage);
                $newURL = array('url' => $urlImage);
                array_push($url, $newURL);
                $message = "Votre image est prise en compte ! ";
            } else {
                $message = " Votre fichier n'est pas de type jpeg jpg gif ou png !";
            }
        } else {
            $message = " Votre image depasse les 3mo !";
        }
    }
}


// Send data to database

if (!empty($_POST['category']) && !empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['price']) && !empty($_POST['size']) && !empty($_POST['0'])) {
    $category = htmlentities($_POST['category']);
    $name = htmlentities($_POST['name']);
    $description = nl2br(htmlentities($_POST['description']));
    $price = htmlentities($_POST['price']);
    $size = htmlentities($_POST['size']);

    $params = [
        'category' => $category,
        'name' => $name,
        'description' => $description,
        'price' => $price,
        'size' => $size,
    ];

    $addProduct = $products->prepare('INSERT INTO products (categories_id, name, description, price, size) VALUES (:category, :name, :description, :price, :size)', $params, true);

    foreach ($_POST as $key => $myUrl) {
        if (is_int($key)) {
            $url[$key] = $myUrl;
        }
    }
    $product_id = $products->prepare('SELECT id FROM products ORDER BY id DESC LIMIT 1', [], true);
    foreach ($url as $result) {
        $params = [
            'image' => $result,
            'product_id' => $product_id['id']
        ];
        $products->prepare('INSERT INTO product_images (image, product_id) VALUE (:image, :product_id)', $params);
    }
    $url = [];
}

// EDIT PRODUCTS

$showProducts = new Database();

$productInfos = $showProducts->prepare('SELECT products.id, products.name, products.description, products.price, products.size, categories.type FROM products JOIN categories ON products.categories_id = categories.id', []);

foreach ($productInfos as $key => $value) {
    $params = [
        'id' => $value['id']
    ];
    $queryImg = $showProducts->prepare('SELECT image FROM product_images WHERE product_id = :id', $params);
    $img = [];
    foreach ($queryImg as $productImages) {
        array_push($img, $productImages['image']);
    }
    $productInfos[$key]['image'] = $img;
}
if (((isset($_POST['name']) && !empty($_POST['name'])) || (isset($_POST['description']) && !empty($_POST['description'])) || (isset($_POST['price']) && !empty($_POST['price']))) && isset($_POST['editer'])) {
    $productName = htmlentities($_POST['name']);
    $productDescription = htmlentities($_POST['description']);
    $productPrice = htmlentities($_POST['price']);
    $params = [
        'productName' => $productName,
        'productDescription' => $productDescription,
        'productPrice' => $productPrice,
        'id' => $_POST['editer']
    ];
    $editProduct = $showProducts->prepare('UPDATE products SET name = :productName, description = :productDescription, price = :productPrice WHERE id = :id', $params);
}





////////////////////////////////////
///////// CONTACT SECTION /////////
////////////////////////////////////

$messages = new Database;

// ARCHIVE MESSAGES

if (isset($_POST['confirmer']) && !empty($_POST['confirmer'])) {
    $parameters = [
        ':archive' => '1',
        'id' => htmlentities($_POST['confirmer'])
    ];
    $archiveMsg = $messages->prepare('UPDATE messages SET archive = :archive WHERE id = :id', $parameters);
}


// SHOW MESSAGES
$parameters = [
    ':archive' => '1'
];
$archMessages = $messages->prepare('SELECT id, name, firstname, email, message, date, archive FROM messages WHERE archive = :archive ORDER BY date DESC', $parameters);

$parameters = [
    'archive' => '0'
];
$allMessages = $messages->prepare('SELECT id, name, firstname, email, message, date, archive FROM messages WHERE archive = :archive ORDER BY date DESC', $parameters);
