<h1>Parduotuves valdymas</h1>
<?php

if (isLoged()) { ?>
    <h3>Pasirinkite parduotuve</h3>

    <?php
    $get_shops = mysqli_query($database, 'select * from shop');
    $get_shops = mysqli_fetch_all($get_shops, MYSQLI_ASSOC);


    ?>
    <form action="index.php" method="get">
        <table>
            <td>Parduotuves pavadinimas</td>
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
            <td>
                <button type="submit">Pasirinkti</button>
            </td>
        </table>
    </form>
    <?php

    if (isset($_GET['shopId'])) {
        if (in_array($_GET['shopId'], array_column($get_shops, 'id'))) {
        } else {
            header('Location: index.php?page=shop');
        }
    }
} ?>