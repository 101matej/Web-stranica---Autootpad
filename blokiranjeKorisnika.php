<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);

include 'zaglavlje.php';

if (!isset($_SESSION["uloga"])) {
    header("Location: obrasci/prijava.php");
    Sesija::obrisiSesiju();
    exit();
}

if (isset($_SESSION["uloga"]) && ($_SESSION["uloga"] == 1 || $_SESSION["uloga"] == 2 || $_SESSION["uloga"] == 3)) {
    header("Location: obrasci/prijava.php");
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time() - 3600, "/");
    Sesija::obrisiSesiju();
    exit();
}

if (isset($_POST['odblokiraj'])) {
    $veza = new Baza();
    $veza->spojiDB();

    $korisnikId = $_POST['odblokiraj'];

    $sql = "UPDATE korisnik SET status = 0, broj_neuspjesne_prijave = 0 WHERE korisnik_id = {$korisnikId}";
    $rezultat = $veza->updateDB($sql);
    
    $dnevnik = new Dnevnik();
    $dnevnik->odblokiranje($_SESSION['korisnik']);
    $dnevnik->radSBazom($_SESSION['korisnik'], $sql);

    $veza->zatvoriDB();
}

if (isset($_POST['blokiraj'])) {
    $veza = new Baza();
    $veza->spojiDB();

    $korisnikId = $_POST['blokiraj'];

    $sql = "UPDATE korisnik SET status = 1 WHERE korisnik_id = {$korisnikId}";
    $rezultat = $veza->updateDB($sql);
    
    $dnevnik = new Dnevnik();
    $dnevnik->blokiranje($_SESSION['korisnik']);
    $dnevnik->radSBazom($_SESSION['korisnik'], $sql);

    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Blokiranje/odblokiranje korisnika</title>
        <meta charset="utf-8">
        <meta name="author" content="Matej Forjan">
        <meta name="description" content="26.08.2022.">
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
        <link href="css/mforjan.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <header>
            <div class="navigacija">
                <div class="padajuci">
                    <button class="padajuci-gumb">PADAJUĆI IZBORNIK</button>
                    <div class="padajuci-sadrzaj">

                        <?php
                        echo "<a href=\"$putanja/index.php\">Početna</a>";

                        if (!isset($_SESSION["uloga"])) {
                            echo "<a href=\"$putanja/obrasci/prijava.php\">Prijava</a>";
                        }

                        echo "<a href=\"$putanja/obrasci/registracija.php\">Registracija</a>";

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 2) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                            echo "<a href=\"$putanja/pregledDnevnika.php\">Pregled dnevnika</a>";
                            echo "<a href=\"$putanja/markeVozila.php\">Marke vozila</a>";
                            echo "<a href=\"$putanja/kategorijeDijelova.php\">Kategorije dijelova</a>";
                        }
                        
                        echo "<a href=\"$putanja/rangLista.php\">Rang lista</a>";
                        echo "<a href=\"$putanja/galerija.php\">Galerija slika</a>";
                        echo "<a href=\"$putanja/o_autoru.html\">Informacije o autoru</a>";
                        echo "<a href=\"$putanja/dokumentacija.html\">Dokumentacija projekta</a>";

                        if (isset($_SESSION["uloga"])) {
                            echo "<a href=\"$putanja/index.php?obrisi=true\">Odjava</a>";
                        }
                        ?>
                    </div>
                </div> 
            </div>

            <div class = "pozicijaLoga">
                <a href = "index.php">
                    <img class = "logo" src = "materijali/logo.png" alt = "Logo">
                </a>
            </div>

            <h1 class = "naslov" style = "text-align: center">
                BLOKIRANJE/ODBLOKIRANJE KORISNIKA
            </h1>

        </header>

        <section>

            <form name="blokiraniKorisnici" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">BLOKIRANI KORISNICI</caption>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ime</th>
                            <th>Prezime</th>
                            <th>Datum rođenja</th>
                            <th>Email</th>
                            <th>Korisničko ime</th>
                            <th>Status</th>
                            <th>Validiran</th>
                            <th>Tip korisnika</th>
                            <th>Odblokiranje</th>
                        </tr>
                    </thead>
                    <tbody> 

                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();

                        $sql = "SELECT korisnik_id, ime, prezime, datum_rodenja, email, korisnicko_ime, status, validiran, naziv "
                                . "FROM korisnik INNER JOIN tip_korisnika ON korisnik.tip_korisnika = tip_korisnika.tip_korisnika_id AND status = 1 "
                                . "ORDER BY korisnik_id ASC";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><?php echo $red['korisnik_id'] ?></td>
                                <td><?php echo $red['ime'] ?></td>
                                <td><?php echo $red['prezime'] ?></td>
                                <td><?php echo $red['datum_rodenja'] ?></td>
                                <td><?php echo $red['email'] ?></td>
                                <td><?php echo $red['korisnicko_ime'] ?></td>
                                <?php
                                if ($red['status'] == 1) {
                                    ?>
                                    <td>Blokiran</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nije blokiran</td>
                                    <?php
                                }
                                if ($red['validiran'] == 1) {
                                    ?>
                                    <td>Validiran račun</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nevalidiran račun</td>
                                    <?php
                                }
                                ?>
                                <td><?php echo $red['naziv'] ?></td>
                                <td><input id="id" name="odblokiraj" type="submit" value="<?php echo $red['korisnik_id'] ?>"> ODBLOKIRAJ</td>
                            </tr>

                            <?php
                        }
                        $veza->zatvoriDB();
                        ?>
                    </tbody>
                </table>
            </form>

            <form name="odblokiraniKorisnici" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">ODBLOKIRANI KORISNICI</caption>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ime</th>
                            <th>Prezime</th>
                            <th>Datum rođenja</th>
                            <th>Email</th>
                            <th>Korisničko ime</th>
                            <th>Status</th>
                            <th>Validiran</th>
                            <th>Tip korisnika</th>
                            <th>Blokiranje</th>
                        </tr>
                    </thead>
                    <tbody> 

                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();

                        $sql = "SELECT korisnik_id, ime, prezime, datum_rodenja, email, korisnicko_ime, status, validiran, naziv "
                                . "FROM korisnik INNER JOIN tip_korisnika ON korisnik.tip_korisnika = tip_korisnika.tip_korisnika_id AND status = 0 "
                                . "ORDER BY korisnik_id ASC";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><?php echo $red['korisnik_id'] ?></td>
                                <td><?php echo $red['ime'] ?></td>
                                <td><?php echo $red['prezime'] ?></td>
                                <td><?php echo $red['datum_rodenja'] ?></td>
                                <td><?php echo $red['email'] ?></td>
                                <td><?php echo $red['korisnicko_ime'] ?></td>
                                <?php
                                if ($red['status'] == 1) {
                                    ?>
                                    <td>Blokiran</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nije blokiran</td>
                                    <?php
                                }
                                if ($red['validiran'] == 1) {
                                    ?>
                                    <td>Validiran račun</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nevalidiran račun</td>
                                    <?php
                                }
                                ?>
                                <td><?php echo $red['naziv'] ?></td>
                                <td><input id="id" name="blokiraj" type="submit" value="<?php echo $red['korisnik_id'] ?>"> BLOKIRAJ</td>
                            </tr>

                            <?php
                        }
                        $veza->zatvoriDB();
                        ?>
                    </tbody>
                </table>
            </form>

        </section>
        <footer>
            <address><b>Kontakt:</b> 
                <a style="color: white; text-decoration: none;" href="mailto:mforjan@foi.hr">
                    Matej Forjan</a></address>
            <p>&copy; 2022 M. Forjan</p>
            <img style="width: 60px; height: 60px; position: relative; top: -7px;" src="materijali/HTML5.png" alt="Slika">
            <img style="width: 75px; height: 75px; position: relative; top: 0px;" src="materijali/CSS3.png" alt="Slika">
        </footer>
    </body>
</html>