<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);

include 'zaglavlje.php';

$greska = false;

if (isset($_POST['pregledajGumb'])) {
    $pocetak = $_POST['datumPocetka'];
    $kraj = $_POST['datumKraja'];

    $regExp = '/^(3[01]|[12][0-9]|0[1-9])[.](1[0-2]|0[1-9])[.][0-9]{4}[.]$/';
    if (!preg_match($regExp, $pocetak) || !preg_match($regExp, $kraj)) {
        $poruka = "Datum je neispravnog formata! Ispravan format je 'dd.mm.gggg.'!";
        $greska = true;
    } else {
        $sql = "SELECT marka_vozila.naziv, COUNT(*) "
                . "FROM kupnja_dijela INNER JOIN dio ON kupnja_dijela.dio = dio.dio_id "
                . "INNER JOIN vozilo ON dio.vozilo = vozilo.vozilo_id "
                . "INNER JOIN marka_vozila ON vozilo.marka_vozila = marka_vozila.marka_vozila_id "
                . "AND kupnja_dijela.datum_kupnje BETWEEN '$pocetakIspravanFormat' AND '$krajIspravanFormat' "
                . "GROUP BY marka_vozila.marka_vozila_id;";

        $dnevnik = new Dnevnik();
        $dnevnik->radSBazom($_SESSION['korisnik'], $sql);
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Rang lista</title>
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
                            echo "<a href=\"$putanja/blokiranjeKorisnika.php\">Blokiranje korisnika</a>";
                            echo "<a href=\"$putanja/pregledDnevnika.php\">Pregled dnevnika</a>";
                            echo "<a href=\"$putanja/markeVozila.php\">Marke vozila</a>";
                            echo "<a href=\"$putanja/kategorijeDijelova.php\">Kategorije dijelova</a>";
                        }
                        
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
                RANG LISTA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>

            <form novalidate class="forma" id="formRangLista" method="post" name="formRangLista" action="rangLista.php">
                <label for="pocetak">Datum početka: </label><br>
                <input class="datumPocetkaTextbox" type="text" name="datumPocetka" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                <label for="kraj">Datum kraja: </label><br>
                <input class="datumKrajaTextbox" type="text" name="datumKraja" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formRangLista" value="Pregledaj" name="pregledajGumb">
            </div>
            
            <table class="display" id="tablica">
                <caption id="naslovTablice">RANG LISTA</caption>
                <thead>
                    <tr>
                        <th>Marka vozila</th>
                        <th>Broj kupljenih dijelova</th>
                        <th>Datum početka</th>
                        <th>Datum kraja</th>
                    </tr>
                </thead>
                <tbody> 

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    global $pocetak;
                    global $kraj;
                    global $greska;
                    
                    $pocetakIspravanFormat = date("Y-m-d", strtotime($pocetak));
                    $krajIspravanFormat = date("Y-m-d", strtotime($kraj));
                    
                    if($greska == false){
                    
                    $sql = "SELECT marka_vozila.naziv, COUNT(*) "
                            . "FROM kupnja_dijela INNER JOIN dio ON kupnja_dijela.dio = dio.dio_id "
                            . "INNER JOIN vozilo ON dio.vozilo = vozilo.vozilo_id "
                            . "INNER JOIN marka_vozila ON vozilo.marka_vozila = marka_vozila.marka_vozila_id "
                            . "AND kupnja_dijela.datum_kupnje BETWEEN '$pocetakIspravanFormat' AND '$krajIspravanFormat' "
                            . "GROUP BY marka_vozila.marka_vozila_id;";
                    
                    $rezultat = $veza->selectDB($sql);
                    
                    while ($red = mysqli_fetch_array($rezultat)) {
                        ?>
                        <tr>
                            <td><?php echo $red['naziv'] ?></td>
                            <td><?php echo $red[1] ?></td>
                            <td><?php echo "$pocetakIspravanFormat" ?></td>
                            <td><?php echo "$krajIspravanFormat" ?></td>
                        </tr>

                        <?php
                    }
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