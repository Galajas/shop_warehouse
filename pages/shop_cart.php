<h1>Pirkimo puslapis</h1>
<style>
    .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 10px;
    }
</style>

<?php

$get_shops = mysqli_query($database, 'select * from shop');
$get_shops = mysqli_fetch_all($get_shops, MYSQLI_ASSOC);

if (isset($_SESSION['cart_id'])) {
    $cart_id = $_SESSION['cart_id'];
    $get_shop_id = mysqli_query($database, "select shop_id from carts where id = '$cart_id'");
    $get_shop_id = mysqli_fetch_array($get_shop_id, MYSQLI_ASSOC);
    $get_shop_id = $get_shop_id['shop_id'];

    $shop_id = $get_shop_id;
} else {
    $shop_id = $_GET['shopId'] ?? null;
}

if (isset($_POST['amount'])) {
    $id = $_POST['id'];
    $get_shop_product_data = mysqli_query($database, "select * from shop_products where id = '$id'");
    $get_shop_product_data = mysqli_fetch_array($get_shop_product_data, MYSQLI_ASSOC);

    $product_amount = $_POST['amount'];
    $product_id = $_POST['product_id'];
    $product_price = $_POST['product_price'];
    $sum = $product_amount * $product_price;

    $errors = [];

    if (!preg_match('/[0-9]/', $product_amount)) {
        $errors[] = 'kiekis turi buti tik skaicius';
    }
    if ($product_amount < 1) {
        $errors[] = 'kiekis turi buti tik sveikasis skaicius';
    }
    if (empty($product_amount)) {
        $errors[] = 'laukas negali buti tuscias';
    }
    if ($product_amount > $get_shop_product_data['products_amount']) {
        $errors[] = 'Nera sandelyje tokio kiekio';
    }

    if (empty($errors)) {
        if (isset($_SESSION['cart_id'])) {
            $cart_id = $_SESSION['cart_id'];
        } else {
            mysqli_query($database, "insert into carts (shop_id) value ('$shop_id')");
            $cart_id = mysqli_insert_id($database);
            $_SESSION['cart_id'] = $cart_id;
        }

        $get_cart_data = mysqli_query($database, "select * from carts where id = '$cart_id'");
        $get_cart_data = mysqli_fetch_array($get_cart_data, MYSQLI_ASSOC);

        mysqli_query($database, "insert into cart_items (cart_id, product_id, amount, sum) value ('$cart_id', '$id', '$product_amount', '$sum')");
        
        $new_shop_amount = $get_shop_product_data['products_amount'] - $product_amount;
        mysqli_query($database, "update shop_products set products_amount = '$new_shop_amount' where id = '$id'");

        $update_sum = $get_cart_data['paid'] + $sum;
        mysqli_query($database, "update carts set paid = '$update_sum' where id = '$cart_id'");


        if ($new_shop_amount == 0) {
            mysqli_query($database, "update shop_products set sold_out = 1 where id = '$id'");
        }
    } else {
        displayErrors($errors);
    }
}
?>

<?php
if (isset($shop_id)) {
    if (in_array($shop_id, array_column($get_shops, 'id'))) {
        ?>
        <h2><?php echo mysqli_fetch_row(mysqli_query($database, 'select shop_name from shop where id = ' . "$shop_id" . ' '))[0]; ?></h2>
    <?php }
    ?>
    <div style="width: 80%; display: flex; justify-content: space-between">
        <div>
            <h3>Produktai parduotuveje</h3>
            <table class="table">
                <tr>
                    <th>
                        Produktas
                    </th>
                    <th>
                        Produkto kainą
                    </th>
                    <th>
                        Kiekis parduotuveje
                    </th>
                    <th>
                        Iki kada galioja
                    </th>
                    <th>
                        Pirkimo kiekis
                    </th>
                </tr>
                <?php
                $get_shop_products_not_utilized = mysqli_query($database, "select * from shop_products where shop_id = '$shop_id' and utilized = 0 and sold_out = 0");
                $get_shop_products_not_utilized = mysqli_fetch_all($get_shop_products_not_utilized, MYSQLI_ASSOC);
                foreach ($get_shop_products_not_utilized as $product) {
                    $id = $product['id'];
                    $product_id = $product['products_id'];
                    $product_name = mysqli_query($database, "SELECT product_name FROM products where id = '$product_id'");
                    $product_name = mysqli_fetch_object($product_name);
                    $product_name = $product_name->product_name;
                    $product_price = round($product['product_price'], 2);
                    $product_amount = $product['products_amount'];
                    $product_validation = $product['product_expires'];
                    
                    $get_margin_by_shopIdAndValidity_to_end = mysqli_query($database, "select margin_size from shop_margin where shop_id = '$shop_id' and margin_type = 'validity_to_end'");
                    $get_margin_by_shopIdAndValidity_to_end = mysqli_fetch_column($get_margin_by_shopIdAndValidity_to_end);
                    
                    if ($product_validation < date('Y-m-d', strtotime(date('Y-m-d') . '+3 day'))) {
                        if ($get_margin_by_shopIdAndValidity_to_end) {
                            $product_price_with_discount = round($product['product_price'] * $get_margin_by_shopIdAndValidity_to_end, 2);
                        }
                    }

                    ?>

                    <tr>
                        <td>
                            <?php echo $product_name ?>
                        </td>
                        <td>
                            <?php
                            if ($product_validation < date('Y-m-d', strtotime(date('Y-m-d') . '+3 day'))) {
                                if ($get_margin_by_shopIdAndValidity_to_end) {
                                    echo "Akcija ($product_price €)  $product_price_with_discount";
                                    $product_price = $product_price_with_discount;
                                } else {
                                    echo $product_price;
                                }
                            }else {
                                echo $product_price;
                            }
                            ?>€
                        </td>
                        <td>
                            <?php echo $product_amount ?>
                        </td>
                        <td>
                            <?php echo $product_validation ?>
                        </td>

                        <form action="index.php?page=shop_cart&shopId=<?php echo $shop_id ?>"
                              method="post">
                            <td>
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product_price ?>">
                                <input type="number" name="amount">
                            </td>

                            <td>
                                <button type="submit">Pridėti i krepseli</button>
                            </td>

                        </form>

                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
        if (isset($_SESSION['cart_id'])) {
            ?>
            <div>
                <h3>Parduotuves krepselis</h3>
                <table class="table">
                    <tr>
                        <th>
                            Preke
                        </th>

                        <th>
                            Kiekis
                        </th>
                        <th>
                            Suma
                        </th>
                    </tr>
                    <?php
                    $get_cart_items = mysqli_query($database, "select * from cart_items where cart_id = '$cart_id'");
                    $get_cart_items = mysqli_fetch_all($get_cart_items, MYSQLI_ASSOC);

                    foreach ($get_cart_items as $item) {
                        $product_id = $item['product_id'];
                        $get_item_name = mysqli_query($database, "select products_id from shop_products where id = '$product_id'");
                        $get_item_name = mysqli_fetch_column($get_item_name);
                        $get_item_name = mysqli_query($database, "select product_name from products where id = '$get_item_name'");
                        $get_item_name = mysqli_fetch_column($get_item_name);

                        $product_amount = $item['amount'];
                        $product_sum = $item['sum'];

                        ?>
                        <tr>
                            <td>
                                <?php echo $get_item_name ?>
                            </td>
                            <td>
                                <?php echo $product_amount ?>
                            </td>
                            <td>
                                <?php echo round($product_sum, 2) ?>€
                            </td>
                        </tr>

                        <?php
                    }

                    ?>

                    <tr>
                        <td colspan="3" style="text-align: center">
                            Viso: <?php echo round(mysqli_fetch_column(mysqli_query($database, "select SUM(sum) cartSum from cart_items where cart_id = '$cart_id'")), 2); ?>
                            €
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center">
                            <form action="index.php?page=finish_shopping&action=done" method="post">
                                <button type="submit">Apmoketi</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center">
                            <form action="index.php?page=finish_shopping&action=cancel" method="post">
                                <button type="submit">Atsaukti pirkima</button>
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
} else {
    setShop();
}