<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$database = mysqli_connect('127.0.0.1', 'root', '', 'shop_warehouse');

if (!$database) {
    die("connection failed: " . mysqli_connect_error());
} else {
    mysqli_query($database, 'update shop_products set utilized = if(product_expires < curdate(), 1, 0)');
}

$page = $_REQUEST['page'] ?? null;

const PRODUCT_CATEGORY_MEAT = ['meat', 'Mesa'];
const PRODUCT_CATEGORY_VEGETABLES = ['vegetables', 'Darzoves'];
const PRODUCT_CATEGORY_FRUITS = ['fruits', 'Vaisiai'];
const PRODUCT_CATEGORY_DAIRY = ['dairy', 'Pieno produktai'];
const PRODUCT_CATEGORY_DRINKS = ['drinks', 'Gerimai'];
const PRODUCT_CATEGORIES = [
    PRODUCT_CATEGORY_MEAT,
    PRODUCT_CATEGORY_VEGETABLES,
    PRODUCT_CATEGORY_FRUITS,
    PRODUCT_CATEGORY_DAIRY,
    PRODUCT_CATEGORY_DRINKS,
];

const MARGIN_CATEGORY_MEAT = ['meat', 'Mesa'];
const MARGIN_CATEGORY_VEGETABLES = ['vegetables', 'Darzoves'];
const MARGIN_CATEGORY_FRUITS = ['fruits', 'Vaisiai'];
const MARGIN_CATEGORY_DAIRY = ['dairy', 'Pieno produktai'];
const MARGIN_CATEGORY_DRINKS = ['drinks', 'Gerimai'];
const MARGIN_CATEGORY_VALIDITY = ['validity_to_end', 'Galiojimas i pabaiga'];
const MARGIN_CATEGORY_COMMON = ['common', 'Bendra'];
const MARGIN_CATEGORIES = [
    MARGIN_CATEGORY_MEAT,
    MARGIN_CATEGORY_VEGETABLES,
    MARGIN_CATEGORY_FRUITS,
    MARGIN_CATEGORY_DAIRY,
    MARGIN_CATEGORY_DRINKS,
    MARGIN_CATEGORY_VALIDITY,
    MARGIN_CATEGORY_COMMON
];

function mutateArray($arrays)
{
    foreach ($arrays as $array) {
        $first = $array[0];
        $second = $array[1];
        $added[$first] = $second;
    }
    return $added;
}


function isLoged(): bool
{
    if (isset($_SESSION['email'])) {
        return true;
    } else {
        return false;
    }
}

function getEmployeesData($email, $data)
{
    $database = mysqli_connect('127.0.0.1', 'root', '', 'shop_warehouse');
    $get_user = mysqli_query($database, "SELECT * FROM employees WHERE email = '$email'");
    $get_user = mysqli_fetch_object($get_user);
    return $get_user->$data;
}

function displayErrors($errors)
{
    if (isset($errors)) {
        ?>
        <ul>
            <?php
            foreach ($errors as $error) {
                ?>
                <li>
                    <?php echo $error ?>
                </li>
            <?php } ?>
        </ul>
        <?php
    }
}

function setShop () {
    $database = mysqli_connect('127.0.0.1', 'root', '', 'shop_warehouse');
    $get_shops = mysqli_query($database, 'select * from shop');
    $get_shops = mysqli_fetch_all($get_shops, MYSQLI_ASSOC);
    $page = $_REQUEST['page'] ?? null;
    ?>
    <form action="index.php" method="get">
        <table class="table">
            <tr>
                <td>Parduotuves pasirinkimas</td>
                <td>
                    <input type="hidden" name="page" value="<?php echo $page ?>">
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



