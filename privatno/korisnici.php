<?php
    $putanja = dirname($_SERVER['REQUEST_URI'],2);
    $direktorij = dirname(getcwd());
    include_once '../zaglavlje.php';
    $veza = new Baza();
    $veza->spojiDB();
    $sql = "SELECT * FROM korisnik";
    $rezultat = $veza->selectDB($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Korisnici</title>
</head>
<body>

    <table border = 1 cellspacing="0" cellpadding="10">
        <tr>
            <th>Ime</th>
            <th>Prezime</th>
            <th>Email</th>
            <th>Korisničko ime</th>
            <th>Lozinka</th>
            <th>Tip korisnika</th>
        </tr>
        <?php
        if (mysqli_num_rows($rezultat) > 0) {
            while($red = mysqli_fetch_assoc($rezultat)) {
            ?>
            <tr>
                <td><?php echo $red['ime']; ?> </td>
                <td><?php echo $red['prezime']; ?> </td>
                <td><?php echo $red['email']; ?> </td>
                <td><?php echo $red['korisnicko_ime']; ?> </td>
                <td><?php echo $red['lozinka']; ?> </td>
                <td><?php echo $red['tip_korisnika']; ?> </td>
            <tr>
        <?php
            }
        }
        ?>
        </table>

</body>
</html>