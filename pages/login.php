<?php
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = 'Yra tusciu lauku';
    }

    $checkEmail = mysqli_query($database, "SELECT * FROM employees WHERE email = '$email'");
    $checkEmail = mysqli_fetch_row($checkEmail);

    if (!$checkEmail) {
        $errors[] = 'Pasto nera';
    } else {
        $get_password = mysqli_query($database, "SELECT password FROM employees WHERE email = '$email'");
        $get_password = mysqli_fetch_array($get_password);
        $get_password = $get_password['password'];

        if ($password != $get_password) {
            $errors[] = 'blogas slaptazodis';
        }
    }

    if (empty($errors)) {
        $_SESSION['email'] = $email;
        header('Location: index.php');
    }
}
?>
<h1>Prisijungimas</h1>
<ul>
    <?php
    if (isset($errors)) {
        foreach ($errors as $error) {
            ?>
            <li>
                <?php echo $error ?>
            </li>
        <?php }
    } ?>
</ul>
<form action="index.php?page=login" method="post">
    <table>
        <tr>
            <td>
                Paštas:
            </td>
            <td>
                <input type="text" name="email" value="<?php echo $_GET['email'] ?? null ?>">
            </td>
        </tr>
        <tr>
            <td>
                Slaptažodis:
            </td>
            <td>
                <input type="password" name="password">
            </td>
        </tr>
    </table>
    <br/><br/>
    <button type="submit">Prisijungti</button>
</form>