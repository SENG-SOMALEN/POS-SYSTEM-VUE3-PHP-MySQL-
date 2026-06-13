<?php
require_once __DIR__ . "/../models/User.php";

$userModel = new User();

$result = $userModel->findAll();

echo"<pre>";
print_r($result);