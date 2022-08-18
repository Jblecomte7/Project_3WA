<?php
ob_start();
require $_SERVER['DOCUMENT_ROOT'].'/models/auth.php';

$resultOutput=array();

if (!is_connected()) {
    $resultOutput = [
					'error'=>1,
                    'msg' => "vous n'êtes pas connecté"
                ];
    ob_end_clean();
	header("Content-type: application/json; charset=UTF-8"); 
	echo json_encode($resultOutput);
}

require $_SERVER['DOCUMENT_ROOT'].'/models/Classes/Database.php';
$dbConn = new Database();
$resultOutput=array();

/*
id produit to add to basket


*/

if (isset($_POST['addToCart']) && !empty($_POST['addToCart'])) {

    // Add in 'panier' table in database the product card : get $_SESSION['id'] and check if the product exists in user's cart : 
    $params = [
        'user_id' => $_SESSION['id'],
        'product_id' => $_POST['addToCart']
    ];
    $checkCart = $dbConn->prepare('SELECT id FROM panier WHERE user_id = :user_id AND product_id = :product_id', $params, true);

    // If product doesn't exist in user's cart, insert product in cart table :
    if ($checkCart === false) {
        $addCart = $dbConn->prepare('INSERT INTO panier (user_id, product_id) VALUES ((SELECT id FROM users WHERE id = :user_id), (SELECT id FROM products WHERE id = :product_id))', $params);
		$resultOutput = [
					'error'=>0,
                    'msg' => 'Ce produit a été ajouté à votre panier !'
                ];
        // Else, display an error message :
    } else {
		$resultOutput = [
					'error'=>1,
                    'msg' => 'Ce produit est déjà dans votre panier !'
                ];
    }
}
//--------------------------------------------------------------
// ajouter le nombre d'éléments dans le panier 
$rslt=$dbConn->query("SELECT COUNT(DISTINCT id) as cnt FROM panier WHERE user_id =".$_SESSION['id']);
$resultOutput['pancount']=$rslt[0]['cnt'];

//--------------------------------------------------------------
ob_end_clean();
header("Content-type: application/json; charset=UTF-8"); 
echo json_encode($resultOutput);

exit();
//-------------------------------
?>