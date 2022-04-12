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
        if (in_array($_GET['shopId'], array_column($get_shops, 'id'))) {?>


            <h3>Marzos pridejimas</h3>
            <form action="index.php?page=shop&shopId=<?php echo $_GET['shopId']?>" method="post">
                <table class="table">
                    <tr>
                        <td>
                            Maržos kategorija
                        </td>
                        <td>
                            <select name="margin_size">
                                <option>
                                    kategorija
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Marzos dydis
                        </td>
                        <td>
                            <input type="number" name="margin_size">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center">
                            <button style="width: 200px; height: 30px" type="submit">Prideti</button>
                        </td>
                    </tr>

                </table>
            </form>


            <?php
        } else {
            header('Location: index.php?page=shop');
        }
    }
} ?>