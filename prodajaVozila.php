<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);

include 'zaglavlje.php';

$greska = false;

if (!isset($_SESSION["uloga"])) {
    header("Location: obrasci/prijava.php");
    Sesija::obrisiSesiju();
    exit();
}
if (isset($_SESSION["uloga"]) && ($_SESSION["uloga"] == 1)) {
    header("Location: obrasci/prijava.php");
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time() - 3600, "/");
    Sesija::obrisiSesiju();
    exit();
}

//pritisnut id dijela
if (isset($_POST['id'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $idProcjene = $_POST['id'];
    
    $sql = "SELECT * FROM procjena WHERE procjena_id = $idProcjene";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $cijenaVozila = $red['cijena'];
    $kupacVozilaId = $red['korisnik'];
    $zahtjevId = $red['zahtjev'];
    
    $korime = $_COOKIE['autenticiran'];
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    
    $prijavljeniKorisnikId = $red2['korisnik_id'];
    
    $sql3 = "SELECT * FROM stanje_racuna WHERE korisnik = $prijavljeniKorisnikId";
    $rezultat3 = $veza->selectDB($sql3);
    $red3 = mysqli_fetch_array($rezultat3);
    
    $prijavljeniKorisnikStanje = $red3['stanje'];
    
    $sql4 = "SELECT * FROM stanje_racuna WHERE korisnik = $kupacVozilaId";
    $rezultat4 = $veza->selectDB($sql4);
    $red4 = mysqli_fetch_array($rezultat4);
    
    $kupacVozilaStanje = $red4['stanje'];
    
    $sqlProvjeraPrihvacenihProcjena = "SELECT kupnja_vozila.kupnja_vozila_id "
            . "FROM kupnja_vozila, procjena, zahtjev "
            . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
            . "AND zahtjev.zahtjev_id = $zahtjevId;";
    $rezultatProvjeraPrihvacenihProcjena = $veza->selectDB($sqlProvjeraPrihvacenihProcjena);
    
    if (mysqli_num_rows($rezultatProvjeraPrihvacenihProcjena) != 0) {
        $poruka = "Već ste prihvatili procjenu za odabrani zahtjev!";
    } else {
        if ($kupacVozilaStanje >= $cijenaVozila) {
            $prijavljeniKorisnikNovoStanje = $prijavljeniKorisnikStanje + $cijenaVozila;
            $kupacVozilaNovoStanje = $kupacVozilaStanje - $cijenaVozila;

            $sql5 = "UPDATE stanje_racuna SET stanje = {$prijavljeniKorisnikNovoStanje} WHERE korisnik = {$prijavljeniKorisnikId}";
            $rezultat5 = $veza->updateDB($sql5);

            $sql6 = "UPDATE stanje_racuna SET stanje = {$kupacVozilaNovoStanje} WHERE korisnik = {$kupacVozilaId}";
            $rezultat6 = $veza->updateDB($sql6);

            $sql7 = "UPDATE procjena SET prihvaceno = 1 WHERE procjena_id = {$idProcjene}";
            $rezultat7 = $veza->updateDB($sql7);

            $datumKupnje = date(Y . m . d);
            $sql8 = "INSERT INTO kupnja_vozila (datum_kupnje, procjena, korisnik) "
                    . "VALUES ('$datumKupnje', $idProcjene, $kupacVozilaId)";
            $rezultat8 = $veza->updateDB($sql8);

            $sql9 = "SELECT zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, zahtjev.snaga_motora, "
                    . "zahtjev.vrsta_goriva, zahtjev.slika, zahtjev.marka_vozila "
                    . "FROM zahtjev, procjena "
                    . "WHERE zahtjev.zahtjev_id = procjena.zahtjev AND procjena.procjena_id = $idProcjene";
            $rezultat9 = $veza->selectDB($sql9);
            $red9 = mysqli_fetch_array($rezultat9);

            $tip = $red9[0];
            $godina = $red9[1];
            $masa = $red9[2];
            $snagaMotora = $red9[3];
            $vrstGoriva = $red9[4];
            $slika = $red9[5];
            $markaVozila = $red9[6];

            $sql10 = "INSERT INTO vozilo (tip, godina, masa_vozila, snaga_motora, vrsta_goriva, slika, marka_vozila, korisnik) "
                    . "VALUES ('$tip', $godina, $masa, $snagaMotora, '$vrstGoriva', '$slika', $markaVozila, $kupacVozilaId)";
            $rezultat10 = $veza->updateDB($sql10);
        } else {
            $poruka = "Kupac nema dovoljno novaca na računu!";
        }
    }

    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Prodaja vozila</title>
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
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
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
                PRODAJA VOZILA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form name="pregledProcjena" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">PREGLED PROCJENA</caption>
                    <thead>
                        <tr>
                            <th>ID procjene</th>
                            <th>ID zahtjeva</th>
                            <th>Marka</th>
                            <th>Tip</th>
                            <th>Godina</th>
                            <th>Stanje</th>
                            <th>Nedostatak</th>
                            <th>Prihvaćeno</th>
                            <th>Cijena</th>
                            <th>Procjenitelj</th>
                            <th>Prodaj</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();
                        
                        $korime = $_SESSION['korisnik'];
                        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                        $rezultat2 = $veza->selectDB($sql2);
                        $red2 = mysqli_fetch_array($rezultat2);
                        $korisnikId = $red2['korisnik_id'];
                        
                        $sql = "SELECT procjena.procjena_id, zahtjev.zahtjev_id, marka_vozila.naziv, zahtjev.tip, zahtjev.godina, "
                                . "procjena.stanje, procjena.nedostatak, procjena.prihvaceno, "
                                . "procjena.cijena, concat(korisnik.ime, ' ', korisnik.prezime) "
                                . "FROM marka_vozila, zahtjev, procjena, korisnik "
                                . "WHERE marka_vozila.marka_vozila_id = zahtjev.marka_vozila AND zahtjev.zahtjev_id = procjena.zahtjev "
                                . "AND procjena.korisnik = korisnik.korisnik_id AND zahtjev.korisnik = $korisnikId ORDER BY procjena.cijena DESC;";
                        
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
                                <?php
                                if ($red[7] == 1) {
                                    ?>
                                    <td>Prihvaćeno</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nije prihvaćeno</td>
                                    <?php
                                }
                                ?>
                                <td><?php echo $red[8] ?></td>
                                <td><?php echo $red[9] ?></td>
                                <?php
                                if ($red[7] == 0) {
                                ?>
                                    <td><input id="id" name="id" type="submit" value="<?php echo $red[0] ?>"> PRODAJ</td>
                                <?php
                                } else {
                                ?>
                                    <td>PROCJENA JE PRIHVAĆENA</td>
                                <?php
                                }
                                ?>
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