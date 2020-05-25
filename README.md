# Hennig Common  

Common files for using as a minimal framework for Web applications 

## Components

- Dynamic Form Builder
- JSON RPC for client server communication

## Usage

```php
<?php

use Hennig\Common\Config;
use Hennig\Common\Database;
use Hennig\Common\ErrorHandling;
use Hennig\Common\Rpc;
use Hennig\Common\Session;

require 'vendor/autoload.php';

try {
    Config::init([
        'timezone' => 'UTC'
    ]);
    Session::init();
    ErrorHandling::init();
    Database::init();
    // Initialize RPC passing the class that will handle authentication
    Rpc::init(new \App\Auth);
    Rpc::handle();
} catch (\Exception $exception) {
    ErrorHandling::output($exception);
}

```  

