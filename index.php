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
        <td>
            <a href="index.php?page=login">Prisijungti</a>
        </td>
        <td>
            <a href="index.php?page=register">Registruotis</a>
        </td>
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
}
?>

<br/><br/>
<?php
echo date('Y-m-d H:i:s');
?>
</body>
</html>