<?php
include 'db.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/tags/:userId','getTags');
$app->get('/tags/findByTagName/:tagName/:userId','getSimiliarTags');
$app->get('/userTags/:userId','getUserTags');
$app->get('/info/findByUserId/:userId/:startRow','getInfoByUserId');
$app->get('/info/findByTagId/:tagId/:userId/:startRow','getInfoByTagId');
$app->get('/info/findByUserTag/:userId/:startRow','getInfoByUserTag');
$app->get('/user/:userId','getUser');

$app->post('/postTest', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	echo $json;
});

$app->post('/userTags/add', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "call insertUserTag(:userId, :tagId);";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("tagId", $data['tag_id']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/userTags/remove', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "call deleteUserTag(:userId, :tagId);";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("tagId", $data['tag_id']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/info/add', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "CALL insertInfo(:userId, :tagId, :infoDetail, :picture)";

	$arr = parse_ini_file("infozimo.ini");
	$picUrl = $arr['image_post_location'];
	
	try {
		$directory = $picUrl . $data['user_id'];
		if(!file_exists($directory)){
			mkdir($directory, 0777, true);
		}

		date_default_timezone_set('Asia/Kolkata'); //<--This will set the timezone to IST

		$picName = date('Y-m-d_H-i-s', time()) . ".jpg";
		
		$pic = $data['info_picture'];
		$picString = base64_decode($pic);
		$picPath = $directory . "/" . $picName;
		file_put_contents($picPath, $picString);
		
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("tagId", $data['tag_id']);
		$stmt->bindParam("infoDetail", $data['info_detail']);
		$stmt->bindParam("picture", $picPath);
		$val = $stmt->execute();
		$stmt->closeCursor();
		$db = null;

		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/info/remove', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "call deleteInfo(:infoId);";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("infoId", $data['info_id']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/like/add', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "CALL insertLike(:userId, :infoId)";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("infoId", $data['info_id']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/like/remove', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "CALL deleteLike(:userId, :infoId)";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("infoId", $data['info_id']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->post('/user/update', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "CALL updateUser(:userId, :userName, :dob, :gender, :picture)";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("userName", $data['user_name']);
		$stmt->bindParam("dob", $data['dob']);
		$stmt->bindParam("gender", $data['gender']);
		$stmt->bindParam("picture", $data['picture']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
});

$app->run();

function getTags($userId) {
	$sql = "CALL getTags('" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);
		$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "tags": ' . json_encode($tags) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}';
	}
}

function getSimiliarTags($tagName, $userId) {
	$sql = "CALL getSimiliarTags('" . $tagName . "', '" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "tags": ' . json_encode($tags) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

function getUserTags($userId) {
	$sql = "CALL getUserTags('" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "tags": ' . json_encode($tags) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

function getInfoByUserId($userId, $startRow){
	$sql = "CALL getInfoByUserId('" . $userId . "', '" . $startRow . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$infoList = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		foreach($infoList as $info){
			$imageLoc = $info->info_picture;
			
			if($imageLoc != null && file_exists($imageLoc)){
				$byte_array = file_get_contents($imageLoc);
				$image = base64_encode($byte_array);
				$info->picture_bytes = $image;
			}
			
		}
		
		echo '{"response":"1" , "info": ' . json_encode($infoList) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

function getInfoByTagId($tagId, $userId, $startRow){
	$sql = "CALL getInfoByTagId('" . $tagId . "', '" . $userId . "', '" . $startRow . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$infoList = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		foreach($infoList as $info){
			$imageLoc = $info->info_picture;
			
			if($imageLoc != null && file_exists($imageLoc)){
				$byte_array = file_get_contents($imageLoc);
				$image = base64_encode($byte_array);
				$info->picture_bytes = $image;
			}
			
		}
		
		echo '{"response":"1" , "info": ' . json_encode($infoList) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

function getInfoByUserTag($userId, $startRow){
	$sql = "CALL getInfoByUserTag('" . $userId . "', '" . $startRow . "');";
	
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$infoList = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		
		foreach($infoList as $info){
			$imageLoc = $info->info_picture;
			
			if($imageLoc != null && file_exists($imageLoc)){
				$byte_array = file_get_contents($imageLoc);
				$image = base64_encode($byte_array);
				$info->picture_bytes = $image;
			}
			
		}
		
		echo '{"response":"1" , "info": ' . json_encode($infoList) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

function getUser($userId) {
	$sql = "CALL getUser('" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$user = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "user": ' . json_encode($user) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":"'. $e->getMessage() .'"}}'; 
	}
}

?>
