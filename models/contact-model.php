<?php

// Form class model data
$formLabel = [
    'name' => 'Nom *',
    'firstName' => 'Prénom *',
    'email' => 'Email *',
    'message' => 'Message *'
];

$className = [
    'labelClass' => 'form__label',
    'inputClass' => 'form__input',
    'textAreaClass' => 'form__textarea',
    'submitClass' => 'btn'
];

$type = [
    'text' => 'text',
    'email' => 'email',
    'textarea' => 'textarea'
];

date_default_timezone_set('Europe/Paris');

$newMessage = new Database();

$formSent = false;

if (!empty($_POST['name']) || !empty($_POST['firstName']) || !empty($_POST['email']) || !empty($_POST['message'])) {
    $name = htmlentities($_POST['name']);
    $firstName = htmlentities($_POST['firstName']);
    $eMail = htmlentities($_POST['email']);
    $message = nl2br(htmlentities($_POST['message']));

    $parameters = [
        'name' => $name,
        'firstname' => $firstName,
        'email' => $eMail,
        'message' => $message,
        'date' => date('Y-m-d H:i:s')
    ];
    $addMessage = $newMessage->prepare('INSERT INTO messages(name, firstname, email, message, date) VALUES(:name, :firstname, :email, :message, :date)', $parameters);

    $_POST['name'] = '';
    $_POST['firstName'] = '';
    $_POST['email'] = '';
    $_POST['message'] = '';
    $formSent = true;
}
