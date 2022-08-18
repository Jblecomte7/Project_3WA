<?php

// user infos : initialize new database
$userInfos = new Database();

// $params => online user's id
if (is_connected()) {
    $params = [
        'id' => $_SESSION['id']
    ];

    $showAll = $userInfos->prepare('SELECT * FROM users WHERE id = :id', $params, true);

    // Declare var for name and firstname
    $userName = $showAll['Name'];
    $userFirstName = $showAll['FirstName'];

    // Setting date
    setlocale(LC_TIME, 'fr_FR.utf8', 'French');
    date_default_timezone_set('Europe/Paris');
    $date = strftime("%A %d %B %Y");
}


// NEWSLETTER 

$newsletter = new Database();

$errors = [];

if (isset($_POST['email']) && !empty($_POST['email'])) {
    if (!filter_var(htmlentities($_POST['email']), FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Votre email n'est pas valide";
    } else if (strlen(htmlentities($_POST['email'])) > 50) {
        $errors['email'] = "Votre email est trop long (maximum 50 caractères) !";
    } else {
        $params = [
            'email' => htmlentities($_POST['email'])
        ];

        // Check if email exists
        $checkEmail = $newsletter->prepare('SELECT id FROM newsletter WHERE email = :email', $params);

        // If email exists : send error message
        if ($checkEmail) {
            $errors['email'] = 'Vous êtes déjà abonné à notre newsletter !';
        } else {
            $params = [
                ':email' => htmlentities($_POST['email'])
            ];
            $insertEmail = $newsletter->prepare('INSERT INTO newsletter (email) VALUES (:email)', $params);
            $success = true;
        }
    }
}
