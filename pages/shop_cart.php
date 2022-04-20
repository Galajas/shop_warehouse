<h1>Pirkimo puslapis</h1>
<style>
    .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 10px;
    }
</style>

<?php

var_dump(isset($_SESSION['cart_id']));

$get_shops = mysqli_query($database, 'select * from shop');
$get_shops = mysqli_fetch_all($get_shops, MYSQLI_ASSOC);

if (isset($_GET['shopId'])) {
    $shop_id = $_GET['shopId'];
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

        mysqli_query($database, "insert into cart_items (cart_id, product_id, amount, sum) value ('$cart_id', '$product_id', '$product_amount', '$sum')");
        $new_shop_amount = $get_shop_product_data['products_amount'] - $product_amount;
        mysqli_query($database, "update shop_products set products_amount = '$new_shop_amount'");

        if ($get_shop_product_data['products_amount'] == 0) {
            mysqli_query($database, "update shop_products set sold_out = 1");
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
                    $product_price = $product['product_price'];
                    $product_amount = $product['products_amount'];
                    $product_validation = $product['product_expires'];
                    ?>

                    <tr>
                        <td>
                            <?php echo $product_name ?>
                        </td>
                        <td>
                            <?php echo round($product_price, 2) ?>€
                        </td>
                        <td>
                            <?php echo $product_amount ?>
                        </td>
                        <td>
                            <?php echo $product_validation ?>
                        </td>

                        <form action="index.php?page=shop_cart&shopId=<?php echo $_GET['shopId'] ?>"
                              method="post">
                            <td>
                                <input type="hidden" name="id" value="<?php echo $id ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product_id ?>">
                                <input type="hidden" name="product_price" value="<?php echo $product_price ?>">
                                <input type="number" name="amount">
                            </td>

                            <td>
                                <button>Pridėti i krepseli</button>
                            </td>

                        </form>

                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>

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
                <tr>
                    <td>
                        Desreles
                    </td>
                    <td>
                        3 vnt.
                    </td>
                    <td>
                        23.45€
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center">
                        <button>Apmoketi</button>
                    </td>
                </tr>
            </table>

        </div>

    </div>

    <?php
} else {
    ?>
    <form action="index.php" method="get">
        <table class="table">
            <tr>
                <td>Parduotuves pasirinkimas</td>
                <td>
                    <input type="hidden" name="page" value="shop_cart">
                    <select name="shopId">
                        <option value="">-</option>
                        <?php
                        foreach ($get_shops as $shop) {
                            ?>
                            <option value="<?php echo $shop['id'] ?>">
                                <?php echo $shop['shop_name'] ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center">
                    <button style="width: 200px; height: 30px" type="submit">Pasirinkti</button>
                </td>
            </tr>
        </table>
    </form>
    <?php
}