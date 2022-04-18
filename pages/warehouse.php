<?php
if (isLoged()) {

    if (getEmployeesData($_SESSION['email'], 'role') == 'warehouse_worker') {
        $action = $_GET['action'] ?? null;
        ?>
        <h1>Sandelio valdymas</h1>
        <br>
        <style>
            .table th, .table td {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 10px;
            }
        </style>
        <?php
        if ($action === 'update') {
            $id = $_GET['id'];

            if (isset($_POST['product_name'])) {
                $product_name = $_POST['product_name'];
                $product_category = $_POST['product_category'];
                $product_price = $_POST['product_price'];
                $product_validity_days = $_POST['product_validity_days'];

                $get_product_name = mysqli_query($database, "SELECT * FROM products where product_name = '$product_name'");
                $get_product_name = mysqli_fetch_row($get_product_name);

                if (!empty($product_name) && !empty($product_category) && !empty($product_price) && !empty($product_validity_days)) {
                    if ($get_product_name == $product_name) {
                        $update_product = mysqli_query($database, "update products set product_category = '$product_category', product_name = '$product_name', product_price = '$product_price', product_validity_days = '$product_validity_days' where id = '$id'");
                        header('Location: index.php?page=warehouse');
                    } elseif ($get_product_name != null) {
                        echo 'Toks produktas jau yra';
                    } else {
                        $update_product = mysqli_query($database, "update products set product_category = '$product_category', product_name = '$product_name', product_price = '$product_price', product_validity_days = '$product_validity_days' where id = '$id'");
                        header('Location: index.php?page=warehouse');
                    }
                } else {
                    echo 'Kazkuris laukas tuscias prasome uzpapildyti';
                }
            }
        } else {
            if (isset($_POST['product_name'])) {
                $product_name = $_POST['product_name'];
                $product_category = $_POST['product_category'];
                $product_price = $_POST['product_price'];
                $product_validity_days = $_POST['product_validity_days'];

                $errors = [];

                $get_product_name = mysqli_query($database, "SELECT * FROM products where product_name = '$product_name'");
                $get_product_name = mysqli_fetch_row($get_product_name);

                if ($get_product_name != null) {
                    $errors[] = 'Toks produktas jau yra';
                }

                if (empty($errors)) {
                    $sql = "insert into products (product_category, product_name, product_price, product_validity_days) value ('$product_category', '$product_name', '$product_price', '$product_validity_days')";
                    mysqli_query($database, $sql);
                    echo 'Produktas pridetas';
                } else {
                    if (isset($errors)) {
                        foreach ($errors as $error) {
                            ?>
                            <li>
                                <?php echo $error ?>
                            </li>
                        <?php }
                    }
                }
            }
        }

        if (isset($_POST['product_balance'])) {
            $product_id = $_POST['product_id'];
            $balance = $_POST['product_balance'];

            $errors = [];

            if (!preg_match('/[0-9]/', $balance)) {
                $errors[] = 'kiekis turi buti tik skaicius';
            }
            if ($balance <= 1) {
                $errors[] = 'kiekis turi buti tik sveikasis skaicius';
            }

            if (empty($errors)) {
                $check_warehouse_product = mysqli_query($database, "SELECT * FROM warehouse_products where product_id = '$product_id'");
                $check_warehouse_product = mysqli_fetch_array($check_warehouse_product, MYSQLI_ASSOC);

                if ($check_warehouse_product != null) {
                    $update_balance = $check_warehouse_product['product_balance'] + $balance;

                    $sql = "update warehouse_products set product_balance = '$update_balance' where product_id = '$product_id'";
                    echo 'Sandelis papildytas';
                } else {
                    $sql = "insert into warehouse_products (product_id, product_balance) value ('$product_id', '$balance')";
                    echo 'Sandelis papildytas';
                }
                mysqli_query($database, $sql);
            } else {
                displayErrors($errors);
            }
        }
        ?>

        <?php
        if ($action === 'update') { ?>
            <h3>Produkto redagavimas</h3>
            <?php
            $get_product = mysqli_query($database, "select * from products where id = '$id'");
            $get_product = mysqli_fetch_array($get_product, MYSQLI_ASSOC);

            $product_name = $get_product['product_name'];
            $product_category = $get_product['product_category'];
            $product_price = $get_product['product_price'];
            $product_validity_days = $get_product['product_validity_days'];
            ?>

            <form action="index.php?page=warehouse&action=update&id=<?php echo $id ?>" method="post">
                <table class="table">
                    <tr>
                        <td>
                            Produktas:
                        </td>
                        <td>
                            <input value="<?php echo $product_name ?>" type="text" name="product_name">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Produkto kategorija:
                        </td>
                        <td>
                            <select name="product_category">
                                <option value=""></option>
                                <?php
                                foreach (PRODUCT_CATEGORIES as $category) {
                                    ?>
                                    <option value="<?php echo $category[0] ?>"
                                        <?php
                                        if ($category[0] == $product_category) {
                                            echo 'selected';
                                        }
                                        ?>
                                    >
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
                            Kaina:
                        </td>
                        <td>
                            <input value="<?php echo $product_price ?>" type="number" name="product_price">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kiek dienu galioja:
                        </td>
                        <td>
                            <input value="<?php echo $product_validity_days ?>" type="number"
                                   name="product_validity_days"
                                   placeholder="ivesti dienu skaiciu">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <button style="width: 200px; height: 30px" type="submit">Atnaujinti</button>
                        </td>
                    </tr>
                </table>
            </form>

        <?php } else { ?>
            <h3>Produkto pridejimas</h3>

            <form action="index.php?page=warehouse" method="post">
                <table class="table">
                    <tr>
                        <td>
                            Produktas:
                        </td>
                        <td>
                            <input type="text" name="product_name">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Produkto kategorija:
                        </td>
                        <td>
                            <select name="product_category">
                                <option value="">-</option>
                                <?php
                                foreach (PRODUCT_CATEGORIES as $category) {
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
                            Kaina:
                        </td>
                        <td>
                            <input type="number" name="product_price">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Kiek dienu galioja:
                        </td>
                        <td>
                            <input type="number" name="product_validity_days" placeholder="ivesti dienu skaiciu">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <button style="width: 200px; height: 30px" type="submit">Prideti</button>
                        </td>
                    </tr>
                </table>
            </form>

        <?php }
        ?>

        <h3>
            Esami produktai
        </h3>
        <table class="table">
            <tr>
                <th>
                    Produktas
                </th>
                <th>
                    Produkto kategorija
                </th>
                <th>
                    Produkto kainą
                </th>
                <th>
                    Kiek dienų galioja
                </th>
                <th>
                    Kiekis sandelyje
                </th>
                <th>
                    Pridėti kiekį
                </th>
                <th>
                    Produkto redagavimas
                </th>
            </tr>
            <?php
            $get_products = mysqli_query($database, "SELECT * FROM products");
            $get_products = mysqli_fetch_all($get_products, MYSQLI_ASSOC);

            foreach ($get_products as $product) {
                $id = $product["id"];
                $category = $product["product_category"];
                $name = $product["product_name"];
                $price = $product["product_price"];
                $validity = $product["product_validity_days"];

                $product_balance = mysqli_query($database, "SELECT product_balance FROM warehouse_products where product_id = '$id'");
                $product_balance = mysqli_fetch_object($product_balance);
                $product_balance = $product_balance->product_balance;
                ?>
                <tr>
                    <td>
                        <?php echo $name ?>
                    </td>
                    <td>
                        <?php
                        for ($i = 0; $i < count(PRODUCT_CATEGORIES); $i++) {
                            if (in_array($category, PRODUCT_CATEGORIES[$i])) {
                                echo PRODUCT_CATEGORIES[$i][1];
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php echo $price ?>€
                    </td>
                    <td>
                        <?php echo $validity ?>
                    </td>
                    <td>
                        <?php echo $product_balance ?>
                    </td>
                    <td>
                        <?php
                        if ($action === "fillWarehouse" && $_POST['product_id'] === $id) {
                            ?>
                            <form action="index.php?page=warehouse" method="post"
                                  style="display: flex; flex-direction: column">
                                <input type="hidden" name="product_id" value="<?php echo $id ?>">
                                <span> Prideti i sandeli:</span>
                                <input style="width: 110px" type="number" name="product_balance" placeholder="kiekis">
                                <button style="width: 110px" type="submit">Prideti i sandeli</button>
                            </form>
                            <?php
                        } else {
                            ?>
                            <form action="index.php?page=warehouse&action=fillWarehouse" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $id ?>">
                                <button type="submit">Papildyti sandeli</button>
                            </form>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="index.php?page=warehouse&action=update&id=<?php echo $id ?>">Redaguoti</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php
    } else { ?>
        <h1>Jums cia negalima - prasome jungtis prie savo sistemos</h1>
        <?php
    }
} ?>
