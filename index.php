<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Ands\Page;
use \Ands\PageAdmin;
use \Ands\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {

	$page = new Page();

	$page->setTpl("index");

});

$app->get('/adm', function() {

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});

$app->get('/adm/login', function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("login");
});

$app->post('/adm/login', function() {
	
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /adm");
	exit;

});

$app->get('/adm/logout', function(){

	User::logout();
	header("Location: /adm/login");
	exit;

});

$app->get("/adm/users", function() {

	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array("users"=> $users));
	

});

$app->get("/adm/users/create", function(){
	
	User::verifyLogin();

	$page = new PageAdmin();
	
	$page->setTpl("users-create");

});

$app->get("/adm/users/:iduser/delete", function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /adm/users");
	exit;

});

$app->get("/adm/users/:iduser", function($iduser){
	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});

$app->post("/adm/users/create", function(){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /adm/users");
	exit;

});

$app->post("/adm/users/:iduser", function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /adm/users");
	exit;
});

$app->run();

 ?>