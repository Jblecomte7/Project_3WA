<?php
session_start();
ob_start();
//---------------------------------------
if (
	!isset($_POST['email']) || empty($_POST['email']) ||
	!isset($_POST['password']) || empty($_POST['password'])
)
	echo json_encode(array("error" => 1, "msg" => "email ou password vide"));
else {
	//---------------------------------------
	// class
	require $_SERVER['DOCUMENT_ROOT'] . '/models/Classes/Database.php';
	$db = new Database();

	$rslt = $db->query("SELECT `id`, `role_id`, `Name`, `FirstName`, `register_date`, `password` FROM `users` WHERE`email`='" . $_POST['email'] . "' LIMIT 1");
	//---------------------------------------
	if (empty($rslt))  echo json_encode(array("error" => 1, "msg" => "ya personne avec ces identifiants"));
	else {

		$passwordVerify = password_verify($_POST['password'], $rslt[0]['password']);
		if ($passwordVerify) {

			$_SESSION['id'] = $rslt[0]['id'];
			setcookie("idCookie", $rslt[0]['id'], time() + 3600);

			echo json_encode(array(
				"error" => 0,
				"msg" => "YES ! :: " . $_SESSION['id'],
				"id" => $rslt[0]['id']
			));
		} else
			echo json_encode(array("error" => 1, "msg" => "password error"));
	}
}


$output = ob_get_contents();
ob_end_clean();

header("Content-type: application/json; charset=UTF-8");

echo $output;
exit();
