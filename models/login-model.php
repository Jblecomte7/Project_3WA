<?php

// Form class model data
$formLabel = [
    'email' => 'Email',
    'password' => 'Mot de passe',
];

$className = [
    'labelClass' => 'form__label',
    'inputClass' => 'form__input',
    'submitClass' => 'btn'
];

$type = [
    'email' => 'email',
    'password' => 'password'
];

$login = new Database();

$errors = array();


if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $eMail = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);

    $params = [
        'email' => $eMail
    ];

    $user = $login->prepare('SELECT * FROM users WHERE email = :email', $params, true);
    if ($user !== false && $user['email'] === $eMail) {
        $passwordVerify = password_verify($password, $user['password']);
        if ($passwordVerify) {
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['role'] = $user['role_id'];
            header('Location: ./index.php?page=Account');
            exit();
        } else {
            $errors['password'] = 'Le mot de passe est incorrect !';
        }
    } else {
        $errors['email'] = 'Cette adresse mail est inconnue !';
    }
}


if (is_connected()) {
    header('Location: ./index.php?page=Account');
    exit();
}
