<?php
$page_action = $_GET['action'] ?? null;
$date = date('Y-m-d H:i:s');

if ($page_action == 'null') {
    header('Location: index.php?page=shop_cart');
} elseif ($page_action == 'done') {
    mysqli_query($database, 'update carts set date_of_purchase = "'. $date . '"  where id = '. $_SESSION['cart_id']);
}

unset($_SESSION['cart_id']);
header('Location: index.php?page=shop_cart');