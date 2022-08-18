<?php
ob_start();
require $_SERVER['DOCUMENT_ROOT'].'/models/auth.php';

$resultOutput=array();

if (!is_connected()) {
    $resultOutput[] = [
					'error'=>1,
                    'msg' => "vous n'êtes pas connecté"
                ];
    ob_end_clean();
	header("Content-type: application/json; charset=UTF-8"); 
	echo json_encode($resultOutput);
}

require $_SERVER['DOCUMENT_ROOT'].'/models/Classes/Database.php';

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
				$resultOutput[] = [
					'error'=>1,
                    'msg' => "Votre nouveau mot de passe doit contenir entre 8 et 32 caractères"
                ];
				
				
            } else {
                $resultOutput[] = [
					'error'=>0,
                    'userPassword' => password_hash($userNewPassword, PASSWORD_DEFAULT),
                    'user_id' => $_SESSION['id'],
					'msg'=>'Votre mot de passe a bien été mis à jour !'
                ];
                $addInfos = $showInfos->prepare('UPDATE users SET password = :userPassword WHERE id = :user_id', $params, true);
                $passSuccess = true;
            }
        } else {            
			$resultOutput[] = [
					'error'=>1,
                    'msg' => "Votre nouveau mot de passe ne peut pas être identique à l'ancien"
                ];
        }
    }
	else
	$resultOutput[] = [
					'error'=>1,
                    'msg' => "vérification incorrecte"
                ];
	

}

// UPDATE user's infos (phone) :

if (!empty($_POST['tel'])) {
    if (strlen(htmlentities($_POST['tel'])) !== 10) {
		$resultOutput[] = [
					'error'=>1,
                    'msg' => "Le format de votre numéro de téléphone est incorrect"
                ];
		
    } else {
        $userPhone = htmlentities($_POST['tel']);
		$params=[			
            'userPhone' => $userPhone,
            'user_id' => $_SESSION['id']			
        ];        
        $addInfos = $showInfos->prepare('UPDATE user_attributes SET phone = :userPhone WHERE user_id = :user_id', $params);
		
		$resultOutput[] = [
			'error'=>0,            
			'msg'=>'Votre téléphone a été mis à jour'
        ];
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
	
	$resultOutput[] = [
			'error'=>0,            
			'msg'=>'Votre date de naissance a été mise à jour'
        ];
}

// UPDATE user's address

$addressSuccess = false;

if ((isset($_POST['address']) && !empty($_POST['address'])) || (isset($_POST['cp']) && !empty($_POST['cp'])) || (isset($_POST['city']) && !empty($_POST['city']))) {
    if (strlen(htmlentities($_POST['address'])) > 200) {
		$resultOutput[] = [
					'error'=>1,
					'msg'=>"Votre adresse doit contenir moins de 200 caractères"
					];
    } else if (strlen(htmlentities($_POST['cp'])) !== 5) {
		$resultOutput[] = [
					'error'=>1,
					'msg'=>"Votre code postal doit contenir exactement 5 chiffres"
					];
    } else if (strlen(htmlentities($_POST['city'])) > 200) {
        $resultOutput[] = [
					'error'=>1,
					'msg'=>"Votre ville doit contenir moins de 200 caractères"
					];
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
        $resultOutput[] = [
					'error'=>0,
					'msg'=>'Votre adresse a été mise à jour'
					];
    }
}

//--------------------------------------------------------------
ob_end_clean();
header("Content-type: application/json; charset=UTF-8"); 
echo json_encode($resultOutput);

exit();

//-------------------------------
?>