<?php
include 'db.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/tags','getTags');
$app->get('/tags/:tagName','getSimiliarTags');
$app->get('/userTags/:userId','getUserTags');
$app->get('/userTags/add/:userId/:tagId','addUserTag');
$app->get('/userTags/remove/:userId/:tagId','removeUserTag');
$app->get('/info/findByUserId/:userId','getInfoByUserId');
$app->get('/info/findByTagId/:tagId','getInfoByTagId');
$app->get('/info/findByUserTag/:userId','getInfoByUserTag');
$app->get('/info/remove/:userId','removeInfo');

$app->post('/info/add', function () use ($app) {
	$req = $app->request();
	$json = $req->post('json');
	$data = json_decode($json, true); // parse the JSON into an assoc. array
	
	$request = \Slim\Slim::getInstance()->request();
	if(is_null($data)){
		echo '{"response":"0"}';
	}

	$sql = "CALL insertInfo(:userId, :tagId, :tagDesc, :picture)";

	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $data['user_id']);
		$stmt->bindParam("tagId", $data['tag_id']);
		$stmt->bindParam("tagDesc", $data['tag_desc']);
		$stmt->bindParam("picture", $data['picture']);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
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
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
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
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
});

$app->run();

function getTags() {
	$sql = "CALL getTags;";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "tags": ' . json_encode($tags) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getSimiliarTags($tagName) {
	$sql = "CALL getSimiliarTags('" . $tagName . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$tags = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "tags": ' . json_encode($tags) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
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
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getInfoByUserId($userId){
	$sql = "CALL getInfoByUserId('" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$info = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "info": ' . json_encode($info) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getInfoByTagId($tagId){
	$sql = "CALL getInfoByTagId('" . $tagId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$info = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "info": ' . json_encode($info) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getInfoByUserTag($userId){
	$sql = "CALL getInfoByUserTag('" . $userId . "');";
	try {
		$db = getDB();
		$stmt = $db->query($sql);  
		$info = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"response":"1" , "info": ' . json_encode($info) . '}';
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addUserTag($userId, $tagId) {
	$request = \Slim\Slim::getInstance()->request();
	//$body = json_decode($request->getBody());
	$sql = "call insertUserTag(:userId, :tagId);";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $userId);
		$stmt->bindParam("tagId", $tagId);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function removeUserTag($userId, $tagId) {
	$request = \Slim\Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$sql = "call deleteUserTag(:userId, :tagId);";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("userId", $userId);
		$stmt->bindParam("tagId", $tagId);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function removeInfo($infoId) {
	$request = \Slim\Slim::getInstance()->request();
	$body = json_decode($request->getBody());
	$sql = "call deleteInfo(:infoId);";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("infoId", $infoId);
		$stmt->execute();
		$db = null;
		echo '{"response" : "1"}';
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"response":"0" , "error":{"text":'. $e->getMessage() .'}}'; 
	}
}

?>
