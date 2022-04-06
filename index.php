<?php
include_once 'config.php';
?>
<hr>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop!</title>
</head>
<body>
<style>
    table {
        padding: 10px;
    }

    td {
        padding: 10px;
    }
</style>

<table>
    <tr>
        <td>
            <a href="index.php">Home</a>
        </td>

        <?php if (isLoged() === false) { ?>
            <td>
                <a href="index.php?page=login">Login</a>
            </td>
            <td>
                <a href="index.php?page=register">Register</a>
            </td>
        <?php } ?>
        <?php if (isLoged() === true) { ?>
            <td>
                <a href="index.php?page=warehouse">Sandelio valdymas</a>
            </td>
            <td>
                <a href="index.php?page=shop">Parduotuves valdymas</a>
            </td>
            <td>
                <a href="index.php?page=logout">Atsijungti</a>
            </td>
        <?php } ?>
    </tr>
</table>

<?php
switch ($page) {
    case null:
        include 'pages/home.php';
        break;
    case 'register':
        include 'pages/registration.php';
        break;
    case 'login':
        include 'pages/login.php';
        break;
    case 'warehouse':
        include 'pages/warehouse.php';
        break;
    case 'shop':
        include 'pages/shop.php';
        break;
}
?>

<br/><br/>
<?php
echo date('Y-m-d H:i:s');
?>
</body>
</html>