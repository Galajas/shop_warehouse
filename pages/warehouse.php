<h1>Sandelio valdymas</h1>

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
            echo 'Klaida';
        }
    }

    ?>
    <fieldset>
        <h3>Produktu pridejimas</h3>
        <form action="index.php?page=warehouse" method="post">
            <table>
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
            </table>
            <button type="submit">Prideti</button>
        </form>
    </fieldset>
<?php } ?>
