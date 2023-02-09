<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$putanja = dirname($_SERVER['REQUEST_URI']);
$direktorij = dirname(getcwd());

include '../zaglavlje.php';

$poruka = "";
$greska = false;

if (isset($_POST['validirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();

    $uneseniEmail = $_POST['email'];
    $uneseniKljuc = $_POST['kljuc'];

    if ($uneseniEmail == "" || $uneseniKljuc == "") {
        $poruka = "Morate unijeti email i ključ!";
    } else {
        $sql = "SELECT * FROM korisnik WHERE email = '{$uneseniEmail}'";
        $rezultat = $veza->selectDB($sql);
        $red = mysqli_fetch_array($rezultat);
        $email = $red['email'];
        $aktivacijskiKod = $red['aktivacijski_kod'];

        $vrijeme_registracije = $red['vrijeme_registracije'];
        $trenutno_vrijeme = date('Y-m-d H:i:s');
        $sekunde = strtotime($trenutno_vrijeme) - strtotime($vrijeme_registracije);
        
        if ($uneseniEmail == $email) {
            if ($sekunde < 25200) {
                if ($uneseniKljuc == $aktivacijskiKod) {
                    $sql = "UPDATE korisnik SET validiran = '1' WHERE email = '" . $email . "'";
                    $rezultat = $veza->updateDB($sql);

                    header('Location: prijava.php');
                } else {
                    $poruka = "Uneseni aktivacijski ključ nije dobar!";
                }
            } else {
                $poruka = "Prošlo je više od 7 sati nakon što ste se registrirali. Ne možete sada validirati mail!";
            }
        } else {
            $poruka = "Uneseni email nije točan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Validacija računa</title>
        <meta charset="utf-8">
        <meta name="author" content="Matej Forjan">
        <meta name="description" content="26.08.2022.">
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
        <link href="../css/mforjan.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <header>
            <div class="navigacija">
                <div class="padajuci">
                    <button class="padajuci-gumb">PADAJUĆI IZBORNIK</button>
                    <div class="padajuci-sadrzaj">
                        
                        <?php
                        echo "<a href=\"$putanja/../index.php\">Početna</a>";
                        echo "<a href=\"$putanja/registracija.php\">Registracija</a>";
                        echo "<a href=\"$putanja/../rangLista.php\">Rang lista</a>";
                        echo "<a href=\"$putanja/../galerija.php\">Galerija slika</a>";
                        echo "<a href=\"$putanja/../o_autoru.html\">Informacije o autoru</a>";
                        echo "<a href=\"$putanja/../dokumentacija.html\">Dokumentacija projekta</a>";
                        
                        if (isset($_SESSION["uloga"])) {
                            echo "<a href=\"$putanja/index.php?obrisi=true\">Odjava</a>";
                        }
                        ?>
                        
                    </div>
                </div> 
            </div>

            <div class = "pozicijaLoga">
                <a href = "../index.php">
                    <img class = "logo" src = "../materijali/logo.png" alt = "Logo">
                </a>
            </div>

            <h1 class = "naslov" style = "text-align: center">
                VALIDACIJA RAČUNA
            </h1>

            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            <form novalidate class="forma" id="form2" method="post" name="form2" action="validacija.php">
                <label for="email">Email: </label><br>
                <input class="email" type="text" id="email" name="email" size="40" require><br>
                <label for="kljuc">Aktivacijski kod: </label><br>
                <input class="kljuc" type="password" id="kljuc" name="kljuc" size="40" require><br>
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="form2" value="Validiraj" name="validirajGumb">
            </div>
        </section>
        <footer>
            <address><b>Kontakt:</b> 
                <a style="color: white; text-decoration: none;" href="mailto:mforjan@foi.hr">
                    Matej Forjan</a></address>
            <p>&copy; 2022 M. Forjan</p>
            <img style="width: 60px; height: 60px; position: relative; top: -7px;" src="../materijali/HTML5.png" alt="Slika">
            <img style="width: 75px; height: 75px; position: relative; top: 0px;" src="../materijali/CSS3.png" alt="Slika">
        </footer>
    </body>
</html>