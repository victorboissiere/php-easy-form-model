<?php

namespace Validation;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use Exception;

class Validation
{

  private $rules;

  private $request;

  private $errors = [];

  public function __construct(Array $rules, Array $request)
  {
    $this->rules = $rules;
    $this->request = $request;
  }

  public function pass() : bool
  {
    foreach($this->rules as $name => $validationRules)
    {
      $validationRules = explode('|', $validationRules);

      foreach($validationRules as $validationRule)
      {
        $validationPass = true;

        //  Check rules. This is where you can add your own rules
        if ($validationRule === 'required')
          $validationPass = $this->validateRequire($name, $this->request[$name] ?? '');
        else if ($validationRule === 'email')
          $validationPass = $this->validateEmail($name, $this->request[$name] ?? '');
        else if (substr($validationRule, 0, 7)  === 'between') {
          if (!isset($this->request[$name]))
            continue;
          preg_match_all('!\d+!', $validationRule, $matches);
          if (count($matches[0]) != 2)
            throw new Exception('Invalid between rule. Expect 2 numbers (min and max)');
          $validationPass = $this->validateBetween($name, $this->request[$name] ?? '', $matches[0]);
        }
        else
          throw new Exception('Undefined validation rule ' . $validationRule);

        if (!$validationPass)
          break;
      }
    }

    return count($this->errors) === 0;
  }

  public function fails() : bool
  {
    return !$this->pass();
  }

  public function getResponse() : String
  {
    if (count($this->errors) === 0)
      return json_encode(['status' => 'success'], JSON_PRETTY_PRINT);

    return json_encode([
      'status' => 'error',
      'errors' => $this->errors
    ]);
  }

  private function validateRequire(String $name, String $value) : bool
  {
    if (strlen($value) === 0) {
      $this->error('required', $name, $name);
      return false;
    }

    return true;
  }

  private function validateBetween(String $name, String $value, Array $bounds) : bool
  {
    if (strlen($value) < $bounds[0] || strlen($value) > $bounds[1]) {
      $this->error('between', $name, $name, $bounds[0], $bounds[1]);
      return false;
    }

    return true;
  }

  private function validateEmail(String $name, String $value) : bool
  {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->error('email', $name, $name);
      return false;
    }

    return true;
  }


  private function error(String $rule, String $name, String... $values)
  {
    $errors = $this->getErrorMessages();
    if (!isset($errors[$rule]))
      throw new Exception('Undefined error message');

    $this->errors[$name] = vsprintf($errors[$rule], $values); 

  }

  private function getErrorMessages()
  {
    return [
      'required' => '%s is required',
      'email' => '%s must be a valid email address',
      'between' => '%s must be between %d and %d'
    ];
  }

}

?>
