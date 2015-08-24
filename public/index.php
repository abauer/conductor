<?php
require '../vendor/autoload.php';
require_once '../app/middleware/APIResponseMiddleware.php';

$config = require_once '../app/config.php';
$app = new \Slim\Slim($config['slim']);
$app->add(new \middleware\APIResponseMiddleware($config));
$m = mysqli_connect("localhost","root","teotauy18","secondEnsemble");
//$m->query("") or die($m->error);
if (!$m) {
    echo json_encode(array("code" => 500, "message" => "Could not connect", "description" => "Unable to locate requested resource."));
}

$app->get('/', function() use($app, $config) {
    $app->response->setStatus(200);
    echo json_encode(array("name" => "conductor", "version" => $config['conductor']['version']));
});

$app->get('/users', function() use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `userData` ORDER BY `userId`") or die($m->error);
	$users = array();
	while($arr = $s->fetch_array(MYSQLI_ASSOC)){
	//	unset($arr['password']);
	//	$users[] = $arr;
		$u = array();
		$u['userId'] = $arr['userId'];
		$u['username']= $arr['username'];
		$users[] = $u;
	}
	echo json_encode($users);
});	

$app->get('/users/:id', function($id) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `userData` WHERE `userId`='$id' ORDER BY `userId`") or die($m->error);
	if($s->num_rows==1){
		$arr = $s->fetch_array(MYSQLI_ASSOC);
		$u = array();
		$u['userId'] = $arr['userId'];
		$u['username']= $arr['username'];
		echo json_encode($u);
	} else {
		echo json_encode(array("code" => 204, "message" => "User doesn't exist", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/rooms', function() use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomData` ORDER BY `roomId`") or die($m->error);
	$rooms = array();
	while($arr = $s->fetch_array(MYSQLI_ASSOC)){
		unset($arr['password']);
		$rooms[] = $arr;
	}
	echo json_encode($rooms);
});

$app->get('/rooms/:id', function($id) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomData` WHERE `roomId`='$id' ORDER BY `roomId`") or die($m->error);
	if($s->num_rows==1){
		$arr = $s->fetch_array(MYSQLI_ASSOC);
		unset($arr['password']);
		echo json_encode($arr);
	} else {
		echo json_encode(array("code" => 204, "message" => "Room doesn't exist", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/users/:user/rooms', function($user) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomPerms` WHERE `userId`='$user' ORDER BY `roomId`") or die($m->error);
	if($s->num_rows>=1){
		$rooms = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$rooms[] = $arr;
		}
		echo json_encode($rooms);
	} else {
		echo json_encode(array("code" => 204, "message" => "User does not belong to any rooms", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/playlists', function() use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistData` ORDER BY `playlistId`") or die($m->error);
	$pls = array();
	while($arr = $s->fetch_array(MYSQLI_ASSOC)){
		$pls[] = $arr;
	}
	echo json_encode($pls);
});

$app->get('/playlists/:id', function($id) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistData` WHERE `playlistId`='$id' ORDER BY `playlistId`") or die($m->error);
	if($s->num_rows==1){
		$arr = $s->fetch_array(MYSQLI_ASSOC);
		echo json_encode($arr);
	} else {
		echo json_encode(array("code" => 204, "message" => "Playlist doesn't exist", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/users/:user/playlists', function($user) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistPerms` WHERE `userId`='$user' ORDER BY `playlistId`") or die($m->error);
	if($s->num_rows>=1){
		$pls = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$pls[] = $arr;
		}
		echo json_encode($pls);
	} else {
		echo json_encode(array("code" => 204, "message" => "User does not belong to any playlists", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/rooms/:room/songs', function($room) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomSongs` WHERE `roomId`='$room' ORDER BY `position`") or die($m->error);
	if($s->num_rows>=1){
		$songs = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$songs[] = $arr;
		}
		echo json_encode($songs);
	} else {
		echo json_encode(array("code" => 204, "message" => "There are no songs in this room", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/rooms/:room/songs/:id', function($room,$id) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomSongs` WHERE `roomId`='$room'AND`songID`='$id' ORDER BY `position`") or die($m->error);
	if($s->num_rows>=1){
		$songs = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$songs[] = $arr;
		}
		echo json_encode($songs);
	} else {
		echo json_encode(array("code" => 204, "message" => "There are no specified songs in this room", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/playlists/:playlist/songs', function($pl) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistSongs` WHERE `playlistId`='$pl' ORDER BY `position`") or die($m->error);
	if($s->num_rows>=1){
		$songs = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$songs[] = $arr;
		}
		echo json_encode($songs);
	} else {
		echo json_encode(array("code" => 204, "message" => "There are no songs in this playlist", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/playlists/:playlist/songs/:id', function($pl,$id) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistSongs` WHERE `playlistId`='$pl'AND`songID`='$id' ORDER BY `position`") or die($m->error);
	if($s->num_rows>=1){
		$songs = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$songs[] = $arr;
		}
		echo json_encode($songs);
	} else {
		echo json_encode(array("code" => 204, "message" => "There are no specified songs in this playlist", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/rooms/:room/permissions', function($room) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomPerms` WHERE `roomId`='$room' ORDER BY `userId`") or die($m->error);
	if($s->num_rows>=1){
		$u = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$u[] = $arr;
		}
		echo json_encode($u);
	} else {
		echo json_encode(array("code" => 204, "message" => "That room has no users associated with it", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/rooms/:room/permissions/:user', function($room,$user) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `roomPerms` WHERE `roomId`='$room'AND`userId`='$user' ORDER BY `userId`") or die($m->error);
	if($s->num_rows>=1){
		$u = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$u[] = $arr;
		}
		echo json_encode($u);
	} else {
		echo json_encode(array("code" => 204, "message" => "That room has no permissions for the specified user", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/playlists/:playlist/permissions', function($pl) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistPerms` WHERE `playlistId`='$pl' ORDER BY `userId`") or die($m->error);
	if($s->num_rows>=1){
		$u = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$u[] = $arr;
		}
		echo json_encode($u);
	} else {
		echo json_encode(array("code" => 204, "message" => "That playlist has no users associated with it", "description" => "Unable to locate requested resource.")); 
	}
});

$app->get('/playlists/:playlist/permissions/:user', function($pl,$user) use($app, $config, $m) {
	$app->response->setStatus(200);
	$s = $m->query("SELECT * FROM `playlistPerms` WHERE `playlistId`='$pl'AND`userId`='$user' ORDER BY `userId`") or die($m->error);
	if($s->num_rows>=1){
		$u = array();
		while($arr = $s->fetch_array(MYSQLI_ASSOC)){
			unset($arr['uniqueId']);
			$u[] = $arr;
		}
		echo json_encode($u);
	} else {
		echo json_encode(array("code" => 204, "message" => "That playlist has no permissions for the specified user", "description" => "Unable to locate requested resource.")); 
	}
});

$app->post('/rooms', function () use ($app) {
    $name = $app->request->post('name');
	$password = $app->request->post('password');
	$userId = $app->request->post('userId');
    $m->query("INSERT INTO `roomData` (`name`,`password`,`userId`) VALUES ('$name','$password','$userId')");
});

$app->post('/logout', function() use($app) {
	// TODO: Revoke session key
    $app->response->setStatus(200);
});

$app->notFound(function () use ($app) {
    $app->response->setStatus(404);
    echo json_encode(array("code" => 404, "message" => "404 Not Found", "description" => "Unable to locate requested resource."));
});

$app->run();