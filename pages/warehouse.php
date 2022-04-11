<?php
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
if (isLoged()) {
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
?>

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
                    <option value="dairy">
                        Pieno produktas
                    </option>
                    <option value="vegetables">
                        Darzoves
                    </option>
                    <option value="fruits">
                        Vaisiai
                    </option>
                    <option value="meat">
                        Mesa
                    </option>
                    <option value="drinks">
                        Gerimai
                    </option>
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
            Atnaujinti
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

        if($product_balance != null) {
            $product_balance = $product_balance->product_balance;
        } else {
            $product_balance = 0;
        }
        ?>
        <tr>
            <td>
                <?php echo $name ?>
            </td>
            <td>
                <?php
                switch ($category) {
                    case 'meat':
                        echo 'Mesa';
                        break;
                    case 'dairy':
                        echo 'Pieno produktas';
                        break;
                    case 'vegetables':
                        echo 'Darzoves';
                        break;
                    case 'fruits':
                        echo 'Vaisiai';
                        break;
                    case 'drinks':
                        echo 'Gerimai';
                        break;
                }
                ?>
            </td>
            <td>
                <?php echo $price ?>
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
                <a href="index.php?page=warehouse&action=update&id=<?php echo $id ?>">Atnaujinti</a>
            </td>
        </tr>
    <?php }
    } ?>
</table>