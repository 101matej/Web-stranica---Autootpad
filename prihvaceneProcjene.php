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
if (isset($_SESSION["uloga"]) && ($_SESSION["uloga"] == 1 || $_SESSION["uloga"] == 2)) {
    header("Location: obrasci/prijava.php");
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time() - 3600, "/");
    Sesija::obrisiSesiju();
    exit();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Prihvaćene procjene</title>
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
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                            echo "<a href=\"$putanja/blokiranjeKorisnika.php\">Blokiranje korisnika</a>";
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
                PRIHVAĆENE PROCJENE
            </h1>

        </header>

        <section>

            <table class="display" id="tablica">
                <caption id="naslovTablice">PRIHVAĆENE PROCJENE</caption>
                <thead>
                    <tr>
                        <th>Marka</th>
                        <th>Tip</th>
                        <th>Godina</th>
                        <th>Masa u KG</th>
                        <th>Snaga motora u KS</th>
                        <th>Vrsta goriva</th>
                        <th>Stanje</th>
                        <th>Nedostatak</th>
                        <th>Cijena</th>
                    </tr>
                </thead>
                <tbody> 
                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_COOKIE['autenticiran'];
                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];
                    
                    $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                            . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, procjena.stanje, procjena.nedostatak, procjena.cijena "
                            . "FROM procjena, zahtjev, marka_vozila WHERE procjena.zahtjev = zahtjev.zahtjev_id "
                            . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id AND procjena.prihvaceno = 1 "
                            . "AND procjena.korisnik = $korisnikId ORDER BY procjena.procjena_id ASC;";
                    
                    $rezultat = $veza->selectDB($sql);
                    
                    $dnevnik = new Dnevnik();
                    $dnevnik->radSBazom($_SESSION['korisnik'], $sql);
                    
                    while ($red = mysqli_fetch_array($rezultat)) {
                        ?>
                        <tr>
                            <td><?php echo $red[0] ?></td>
                            <td><?php echo $red[1] ?></td>
                            <td><?php echo $red[2] ?></td>
                            <td><?php echo $red[3] ?></td>
                            <td><?php echo $red[4] ?></td>
                            <td><?php echo $red[5] ?></td>
                            <td><?php echo $red[6] ?></td>
                            <td><?php echo $red[7] ?></td>
                            <td><?php echo $red[8] ?></td>
                        </tr>

                        <?php
                    }
                    $veza->zatvoriDB();
                    ?>
                </tbody>
            </table>

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