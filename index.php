<?php
require './models/auth.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['page'])) {
    if ($_GET['page'] === 'Accueil') {
        require './controllers/home-controller.php';
    } else if ($_GET['page'] === 'Apropos') {
        require './controllers/about-controller.php';
    } else if ($_GET['page'] === 'Services') {
        require './controllers/services-controller.php';
    } else if ($_GET['page'] === 'Produits') {
        require './controllers/products-controller.php';
    } else if ($_GET['page'] === 'Contact') {
        require './controllers/contact-controller.php';
    } else if ($_GET['page'] === 'Login') {
        require './controllers/login-controller.php';
    } else if ($_GET['page'] === 'Admin') {
        require './controllers/admin-controller.php';
    } else if ($_GET['page'] === 'Register') {
        require './controllers/register-controller.php';
    } else if ($_GET['page'] === 'Account') {
        require './controllers/account-controller.php';
    } else if ($_GET['page'] === 'MonPanier') {
        require './controllers/cart-controller.php';
    } else {
        require './views/404.phtml';
    }
} else {
    require './controllers/home-controller.php';
}
