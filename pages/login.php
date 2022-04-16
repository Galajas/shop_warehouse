<?php
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = 'Yra tusciu lauku';
    }

    $checkEmail = getEmployeesData($email, 'email');

    if (!$checkEmail) {
        $errors[] = 'Pasto nera';
    } else {
        $get_password = getEmployeesData($email, 'password');

        if ($password != $get_password) {
            $errors[] = 'blogas slaptazodis';
        }
    }

    if (empty($errors)) {
        $_SESSION['email'] = $email;
        header('Location: index.php');
    } else {
        displayErrors($errors);
    }
}
?>
<h1>Prisijungimas</h1>
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