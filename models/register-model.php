<?php

$registerConfirm = false;
$registerUser = new Database();

$errors = array();

if (!empty($_POST['name']) || !empty($_POST['firstName']) || !empty($_POST['email']) || !empty($_POST['password'])) {
    if (strlen(htmlentities($_POST['name'])) > 50 || strlen(htmlentities($_POST['name'])) === 0) {
        $errors['name'] = "Votre nom doit contenir entre 1 et 50 caractères !";
    } else if (strlen(htmlentities($_POST['firstName'])) > 50 || strlen(htmlentities($_POST['firstName'])) === 0) {
        $errors['firstName'] = "Votre prénom doit contenir entre 1 et 50 caractères !";
    } else {
        $name = htmlentities($_POST['name']);
        $firstName = htmlentities($_POST['firstName']);
        $email = htmlentities($_POST['email']);
        $password = htmlentities(password_hash($_POST['password'], PASSWORD_DEFAULT));

        $parameters = [
            'role_id' => 2,
            'name' => $name,
            'firstname' => $firstName,
            'email' => $email,
            'password' => $password,
            'date' => date('Y-m-d')
        ];

        if (!filter_var(htmlentities($_POST['email']), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Votre email n'est pas valide";
        } else if (strlen(htmlentities($_POST['email'])) > 50) {
            $errors['email'] = "Votre email est trop long (maximum 50 caractères) !";
        } else {
            $params = [
                'email' => $email,
            ];

            // Check if email exists
            $user = $registerUser->prepare('SELECT id FROM users WHERE email = :email', $params);

            // If email exists : send error message
            if ($user) {
                $errors['email'] = 'Cette adresse mail est déjà utilisée pour un autre compte !';

                // If email doesn't exist : check password's length
            } else if (strlen(htmlentities($_POST['password'])) > 32 || strlen(htmlentities($_POST['password'])) < 8) {
                $errors['password'] = "Votre mot de passe doit contenir entre 8 et 32 caractères !";
            } else {
                $registerUser->prepare('INSERT INTO users (role_id, Name, FirstName, email, password, register_date) VALUES(:role_id, :name, :firstname, :email, :password, :date)', $parameters);
                $registerConfirm = true;
                $params = [
                    'email' => $email
                ];

                // Automatic connexion after register
                $user_connection = $registerUser->prepare('SELECT id FROM users WHERE email = :email', $params, true);
                $_SESSION['id'] = $user_connection['id'];

                // After register, automatic creation of a user_attributes empty line (phone, address and birthday)
                $params = [
                    'user_id' => $_SESSION['id']
                ];
                $registerUser->prepare('INSERT INTO user_attributes (user_id) VALUES(:user_id)', $params, true);
                header('Location: ./index.php?page=Account');
                exit();
            }
        }
    }
}
