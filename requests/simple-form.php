<?php
namespace Request;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../src/validation.php';

use Validation\Validation;

$validation = new Validation([
  'firstname' => 'required|between:2,20',
  'lastname'  => 'required|between:2,20',
  'email'     => 'required|email|between:2,255'
], $_POST);

if ($validation->fails()) {
  echo $validation->getResponse();
}
else {
  //PUT YOUR LOGIC CODE HERE

  echo $validation->getResponse();
}
