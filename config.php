<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$database = mysqli_connect('127.0.0.1', 'root', '', 'shop_warehouse');

if (!$database) {
    die("connection failed: " . mysqli_connect_error());
}

$page = $_REQUEST['page'] ?? null;

function isLoged(): bool
{
    if (isset($_SESSION['email'])) {
        return true;
    } else {
        return false;
    }
}

function getEmployeesData($email, $data)
{
        $database = mysqli_connect('127.0.0.1', 'root', '', 'shop_warehouse');
        $get_user = mysqli_query($database, "SELECT * FROM employees WHERE email = '$email'");
        $get_user = mysqli_fetch_object($get_user);
        return $get_user->$data;
}



