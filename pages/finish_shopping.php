<?php
$page_action = $_GET['action'] ?? null;
$date = date('Y-m-d H:i:s');

if ($page_action == 'null') {
    header('Location: index.php?page=shop_cart');
} elseif ($page_action == 'done') {
    mysqli_query($database, 'update carts set date_of_purchase = "'. $date . '"  where id = '. $_SESSION['cart_id']);
} elseif ($page_action == 'cancel') {

    $get_cart_items = mysqli_query($database, 'select * from cart_items where cart_id = '. $_SESSION['cart_id']);
    $get_cart_items = mysqli_fetch_all($get_cart_items, MYSQLI_ASSOC);
    foreach ($get_cart_items as $item) {
        $amount = $item['amount'];
        $product_id = $item['product_id'];

        $get_shop_product_amount = mysqli_query($database, "select products_amount from shop_products where id = '$product_id'");
        $get_shop_product_amount = mysqli_fetch_column($get_shop_product_amount);

        if ($get_shop_product_amount == 0) {
            mysqli_query($database, "update shop_products set sold_out = 0 where id = '$product_id'");
            mysqli_query($database, "update shop_products set products_amount = '$amount' where id = '$product_id'");
        }

        $shop_amount = $get_shop_product_amount + $amount;
        mysqli_query($database, "update shop_products set products_amount = '$shop_amount' where id = '$product_id'");
        
    }
    
    mysqli_query($database, 'delete from carts where id = '. $_SESSION['cart_id']);
    mysqli_query($database, 'delete from cart_items where cart_id = '. $_SESSION['cart_id']);
}

unset($_SESSION['cart_id']);
header('Location: index.php?page=shop_cart');