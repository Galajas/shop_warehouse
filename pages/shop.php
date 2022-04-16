<h1>Parduotuves valdymas</h1>

<style>
    .table th, .table td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 10px;
    }
</style>
<?php

if (isLoged()) { ?>
    <h3>Pasirinkite parduotuve</h3>

    <?php
    $get_shops = mysqli_query($database, 'select * from shop');
    $get_shops = mysqli_fetch_all($get_shops, MYSQLI_ASSOC);

    $get_margin = mysqli_query($database, "select * from shop_margin");
    $get_margin = mysqli_fetch_all($get_margin, MYSQLI_ASSOC);

    $get_warehouse_products = mysqli_query($database, "select products.id, products.product_category, products.product_name, products.product_price, products.product_validity_days, warehouse_products.product_balance from products inner join warehouse_products on products.id = warehouse_products.product_id");
    $get_warehouse_products = mysqli_fetch_all($get_warehouse_products, MYSQLI_ASSOC);


    if (isset($_POST['margin_size'])) {
        $shop_id = $_GET['shopId'];
        $product_category = $_POST['product_category'];
        $margin_size = $_POST['margin_size'];

        $errors = [];

        if (!preg_match('/[0-9]/', $margin_size)) {
            $errors[] = 'marza turi buti tik skaicius';
        }

        if ($margin_size <= 1) {
            $errors[] = 'marza negali buti mazesne nei 1';
        }

        if (!in_array($product_category, array_column($get_margin, 'margin_type'))) {
            $errors[] = 'Pasirinkta neteisinga kategorija';
        }

        $get_filtered_margin = mysqli_query($database, "select * from shop_margin where shop_id = '$shop_id'");
        $get_filtered_margin = mysqli_fetch_all($get_filtered_margin, MYSQLI_ASSOC);

        if (empty($errors)) {
            if (in_array($product_category, array_column($get_filtered_margin, 'margin_type'))) {
                $update_margin = mysqli_query($database, "update shop_margin set margin_size = '$margin_size' where margin_type = '$product_category' and shop_id = '$shop_id'");
                echo 'Marza atnaujinta';
            } else {
                $save_margin = mysqli_query($database, "insert into shop_margin (shop_id, margin_type, margin_size) value  ('$shop_id', '$product_category', '$margin_size')");
                echo 'Marza sukurta';
            }
        } else {
            displayErrors($errors);
        }
    }

    if (isset($_POST['product_id'])) {
        $shop_id = $_GET['shopId'];
        $product_id = $_POST['product_id'];
        $products_amount = $_POST['products_amount'];

        $errors = [];

        if (!preg_match('/[0-9]/', $products_amount)) {
            $errors[] = 'kiekis turi buti tik skaicius';
        }
        if ($products_amount <= 1) {
            $errors[] = 'kiekis turi buti tik sveikasis skaicius';
        }

        if (empty($errors)) {


            $get_warehouse_product = mysqli_query($database, "select products.id, products.product_category, products.product_name, products.product_price, products.product_validity_days, warehouse_products.product_balance from products inner join warehouse_products on products.id = warehouse_products.product_id where products.id = '$product_id'");
            $get_warehouse_product = mysqli_fetch_array($get_warehouse_product, MYSQLI_ASSOC);
            $get_warehouse_product_type = $get_warehouse_product['product_category'];

            $get_filtered_margin = mysqli_query($database, "select * from shop_margin where shop_id = '$shop_id' and margin_type = '$get_warehouse_product_type'");
            $get_filtered_margin = mysqli_fetch_array($get_filtered_margin, MYSQLI_ASSOC);
            
            $new_balance = $get_warehouse_product['product_balance'] - $products_amount;
            $date = date('Y-m-d');
            $new_price = $get_warehouse_product['product_price'] * $get_filtered_margin['margin_size'];
            $get_expire_days = $get_warehouse_product['product_validity_days'];
            $expire_date = date('Y-m-d', strtotime("+$get_expire_days days"));

            var_dump($new_price);


            mysqli_query($database, "update warehouse_products set product_balance = '$new_balance' where product_id = '$product_id'");
            mysqli_query($database, "insert into shop_products (shop_id, products_id, products_amount, product_price, product_expires) value ('$shop_id', '$product_id', '$products_amount', '$new_price', '$expire_date')");

        } else {
            displayErrors($errors);
        }

    }


    ?>


    <form action="index.php" method="get">
        <table class="table">
            <tr>
                <td>Parduotuves pasirinkimas</td>
                <td>
                    <input type="hidden" name="page" value="shop">
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

    if (isset($_GET['shopId'])) {
        if (in_array($_GET['shopId'], array_column($get_shops, 'id'))) {
            $shop_id = $_GET['shopId'];
            $get_margin_via_shop = mysqli_query($database, "select * from shop_margin where shop_id = '$shop_id'");
            $get_margin_via_shop = mysqli_fetch_all($get_margin_via_shop, MYSQLI_ASSOC);
            ?>
            <h2><?php echo mysqli_fetch_row(mysqli_query($database, 'select shop_name from shop where id = ' . "$shop_id" . ' '))[0]; ?></h2>

            <div style="display: flex">
                <h3>Marzos pridejimas</h3>
                <form style="margin-bottom: 0px" action="index.php?page=shop&shopId=<?php echo $_GET['shopId'] ?>"
                      method="post">
                    <table class="table">
                        <tr>
                            <td>
                                Maržos kategorija
                            </td>
                            <td>
                                <select name="product_category">
                                    <option value="">-</option>
                                    <?php
                                    foreach (MARGIN_CATEGORIES as $category) {
                                        ?>
                                        <option value="<?php echo $category[0] ?>">
                                            <?php echo $category[1] ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Marzos dydis
                            </td>
                            <td>
                                <input step="0.01" type="number" name="margin_size" placeholder="1.55">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center">
                                <button style="width: 200px; height: 30px" type="submit">Prideti</button>
                            </td>
                        </tr>
                    </table>
                </form>
                <table class="table">
                    <tr>
                        <th>
                            Marzos kategorija
                        </th>
                        <?php
                        foreach ($get_margin_via_shop as $margin) { ?>
                            <td>
                                <?php
                                if (array_key_exists($margin['margin_type'], mutateArray(MARGIN_CATEGORIES))) {
                                    echo mutateArray(MARGIN_CATEGORIES)[$margin['margin_type']];
                                }
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <th>
                            Marzos dydis
                        </th>
                        <?php
                        foreach ($get_margin_via_shop as $margin) { ?>
                            <td>
                                <?php
                                echo $margin['margin_size'];
                                ?>
                            </td>
                        <?php } ?>
                    </tr>
                </table>
            </div>

            <div>
                <h3>Produktu pridejimas</h3>
                <form style="margin-bottom: 0px" action="index.php?page=shop&shopId=<?php echo $_GET['shopId'] ?>"
                      method="post">
                    <table class="table">
                        <tr>
                            <td>
                                Produktas
                            </td>
                            <td>
                                <select name="product_id">
                                    <?php
                                    foreach ($get_warehouse_products as $product) { ?>
                                        <option value="<?php echo $product['id'] ?>">
                                            <?php
                                            echo $product['product_name'] . ' - ' . $product['product_balance'] . '.vnt';
                                            ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Kiek prideti
                            </td>
                            <td>
                                <input type="number" name="products_amount">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center">
                                <button style="width: 200px; height: 30px" type="submit">Prideti</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

            <?php
        } else {
            header('Location: index.php?page=shop');
        }
    }
} ?>