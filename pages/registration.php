<?php

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $name = $_POST['name'];


    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'][] = 'Neteisingas el. pastas';
    }

    if (strlen($password) < 9) {
        $errors['password'][] = 'slaptazodis turi buti ilgesnis nei 9 simboliai';
    }

    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors['password'][] = 'slaptazodyje turi buti raide ir skaicius';
    }

    if (!in_array($role, ['warehouse_worker', 'shop_worker'])) {
        $errors['sex'][] = 'nera tokios pareigybes';
    }

    if (strlen($name) < 3 || strlen($name) > 60) {
        $errors['name'][] = 'vardas yra per ilgas arba per trumpas';
    }

    if ($email == $password) {
        $errors['password'][] = 'slaptazodis ir emailas negali buti vienodi';
    }

    if ($password != $password2) {
        $errors['password2'][] = 'Slaprazodiai nesutampa';
    }

    $checkEmail = mysqli_query($database, "SELECT * FROM employees WHERE email = '$email'");
    $checkEmail = mysqli_fetch_row($checkEmail);
    if ($checkEmail != null) {
        $errors['email'][] = 'Pastas uzimtas';
    }


    if (empty($errors)) {

        $sql = "insert into employees (name, role, password, email) value ('$name', '$role', '$password', '$email')";

        if (mysqli_query($database, $sql) === false) {
            echo 'Nepavyko sukurti vartotojo';
        } else {
            header('Location: index.php?page=login&email=' . $email);
        }
    }
}

?>
<h1>Register</h1>
<form action="index.php?page=register" method="post">
    <table>
        <tr>
            <td>
                Vardas:
            </td>
            <td>
                <input type="text" name="name" value="<?php echo $name ?? null ?>">
            </td>
            <td>
                <?php
                if (isset($errors['name'])) {
                    echo implode(',', $errors['name']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Pareigybe:
            </td>
            <td>
                <select name="role">
                    <option value="">-</option>
                    <option value="warehouse_worker"
                        <?php
                        if (($role ?? null) == 'warehouse_worker') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Sandelininkas
                    </option>
                    <option value="shop_worker"
                        <?php
                        if (($role ?? null) == 'shop_worker') {
                            echo 'selected';
                        }
                        ?>
                    >
                        Parduotuves darbuotojas
                    </option>
                    shop_worker
                    </option>
                </select>
            </td>
            <td>
                <?php
                if (isset($errors['role'])) {
                    echo implode(',', $errors['role']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Paštas:
            </td>
            <td>
                <input type="text" name="email" value="<?php echo $email ?? null ?>">
            </td>
            <td>
                <?php
                if (isset($errors['email'])) {
                    echo implode(',', $errors['email']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Slaptažodis:
            </td>
            <td>
                <input type="password" name="password">
            </td>
            <td>
                <?php
                if (isset($errors['password'])) {
                    echo implode(',', $errors['password']);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>
                Pakartoti slaptažodi:
            </td>
            <td>
                <input type="password" name="password2">
            </td>
            <td>
                <?php
                if (isset($errors['password2'])) {
                    echo implode(',', $errors['password2']);
                }
                ?>
            </td>
        </tr>

        </tr>
    </table>
    <button type="submit">Registruotis</button>
</form>