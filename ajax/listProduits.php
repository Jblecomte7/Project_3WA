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

$sql="SELECT `products`.`id`, `categories_id`, `name`, `description`, `price`, `size`, `type` FROM `products` INNER JOIN `categories` ON `categories`.`id`=`categories_id`  WHERE 1";

if (isset($_POST['cat']) && $_POST['cat']<>'all') $sql.=" AND `type`='".$_POST['cat']."'";
/*
cat
size
name
	price
	type
	image[]
	description

*/
$showInfos = new Database();

$rslt=$showInfos->query($sql);

$listIds="-1";
$resultOutput=array();

if (!empty($rslt)) foreach($rslt as $v) {
	$resultOutput[$v['id']]=$v;
	$listIds.=','.$v['id'];
}

$sql="SELECT `image`, `product_id` FROM `product_images` WHERE `product_id` IN(".$listIds.")";
$rslt=$showInfos->query($sql);

if (!empty($rslt)) foreach($rslt as $v) {
	if (!isset($resultOutput[$v['product_id']]['images'])) {
		$resultOutput[$v['product_id']]['images']=array();		
		}
	$resultOutput[$v['product_id']]['images'][]=$v['image'];
}

sort($resultOutput);
	
//--------------------------------------------------------------
ob_end_clean();
header("Content-type: application/json; charset=UTF-8"); 
echo json_encode($resultOutput);

exit();
//-------------------------------
