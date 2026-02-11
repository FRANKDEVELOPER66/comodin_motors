<?php session_start();

use Dotenv\Dotenv;
use Model\ActiveRecord;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

ini_set('display_errors', $_ENV['DEBUG_MODE'] ?? 1);
ini_set('display_startup_errors', $_ENV['DEBUG_MODE'] ?? 1);
error_reporting($_ENV['DEBUG_MODE'] ?? E_ALL);

require 'funciones.php';
require 'database.php';

ActiveRecord::setDB($db);
