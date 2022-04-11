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

//enum('dairy', 'vegetables', 'fruits', 'meat', 'drinks')

const PRODUCT_CATEGORY_MEAT = ['meat', 'Mesa'];
const PRODUCT_CATEGORY_VEGETABLES = ['vegetables', 'Darzoves'];
const PRODUCT_CATEGORY_FRUITS = ['fruits', 'Vaisiai'];
const PRODUCT_CATEGORY_DAIRY = ['dairy', 'Pieno produktai'];
const PRODUCT_CATEGORY_DRINKS = ['drinks', 'Gerimai'];
const PRODUCT_CATEGORIES = [
    PRODUCT_CATEGORY_MEAT,
    PRODUCT_CATEGORY_VEGETABLES,
    PRODUCT_CATEGORY_FRUITS,
    PRODUCT_CATEGORY_DAIRY,
    PRODUCT_CATEGORY_DRINKS,
];

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



