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
if (isset($_SESSION["uloga"]) && ($_SESSION["uloga"] == 1 || $_SESSION["uloga"] == 2)) {
    header("Location: obrasci/prijava.php");
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time() - 3600, "/");
    Sesija::obrisiSesiju();
    exit();
}

//pritisnut id zahtjeva
if (isset($_POST['id'])) {
    $idZahtjeva = $_POST['id'];
}

//pritisnut gumb za unos procjene
if (isset($_POST['procijeniGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
        
    $cijena = $_POST['cijena'];
    $stanje = $_POST['stanje'];
    $nedostatak = $_POST['nedostatak'];
    $zahtjev = $_POST['zahtjev'];
    
    $korime = $_COOKIE['autenticiran'];
    
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    $korisnikId = $red2['korisnik_id'];

    if ($cijena == "" || $stanje == "" || $nedostatak == "") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }
    
    $sql3 = "SELECT * FROM stanje_racuna WHERE korisnik = $korisnikId";
    $rezultat3 = $veza->selectDB($sql3);
    $red3 = mysqli_fetch_array($rezultat3);
    $stanjeRacuna = $red3['stanje'];
    
    if($cijena > $stanjeRacuna){
        $poruka = "Nemate dovoljno novaca na računu!";
        $greska = true;
    }

    if (!$greska) {
        
        $sql = "INSERT INTO procjena (cijena, stanje, nedostatak, zahtjev, korisnik) "
                . "VALUES ($cijena, '$stanje', '$nedostatak', $zahtjev, $korisnikId)";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Pregled zahtjeva</title>
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
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
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
                PREGLED ZAHTJEVA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formPregledZahtjeva" method="post" name="formPregledZahtjeva" action="pregledZahtjeva.php">
                <label for="markaVozila">Odaberi marku vozila: </label><br>
                <select id="markaVozila" name="markaVozila">
                    <option value="0" >Odaberi marku vozila:</option>
                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_SESSION['korisnik'];

                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];

                    $sql = "SELECT marka_vozila.marka_vozila_id, marka_vozila.naziv "
                            . "FROM marka_vozila INNER JOIN moderator ON marka_vozila.marka_vozila_id = moderator.marka_vozila "
                            . "AND moderator.korisnik = $korisnikId;";

                    $rezultat = $veza->selectDB($sql);

                    while ($red = mysqli_fetch_array($rezultat)) {
                        ?>
                        <option value="<?php echo $red[0] ?>" ><?php echo $red[1] ?></option>
                        <?php
                    }
                    $veza->zatvoriDB();
                    ?>

                </select>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formPregledZahtjeva" value="Odaberi" name="odaberiGumb">
            </div>
            
            <form novalidate class="forma" id="formProcjena" method="post" name="formProcjena" action="pregledZahtjeva.php">
                <label for="cijena">Cijena: </label><br>
                <input class="cijenaTextbox" type="text" name="cijena" size="30" maxlength="30" placeholder="Cijena" autofocus="autofocus" required="required"><br>
                
                <label for="stanje">Stanje: </label><br>
                <input class="stanjeTextbox" type="text" name="stanje" size="30" maxlength="30" placeholder="Stanje" autofocus="autofocus" required="required"><br>
                
                <label for="nedostatak">Nedostatak: </label><br>
                <input class="nedostatakTextbox" type="text" name="nedostatak" size="30" maxlength="30" placeholder="Nedostatak" autofocus="autofocus" required="required"><br>
                
                <label for="zahtjev">Id zahtjeva: </label><br>
                <input <?php
                global $idZahtjeva;
                if ($idZahtjeva != null){
                    echo "value='{$idZahtjeva}'";
                }
                ?> class="zahtjevTextbox" type="text" name="zahtjev" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formProcjena" value="Procijeni" name="procijeniGumb">
            </div>
            
            <form name="pregledZahtjeva" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">PREGLED ZAHTJEVA</caption>
                    <thead>
                        <tr>
                            <th>Marka</th>
                            <th>ID zahtjeva</th>
                            <th>Tip</th>
                            <th>Godina</th>
                            <th>Masa u KG</th>
                            <th>Snaga motora u KS</th>
                            <th>Vrsta goriva</th>
                            <th>Slika</th>
                            <th>Prodavač</th>
                            <th>Procjena</th>
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

                        if (isset($_POST['odaberiGumb'])) {
                            $markaVozilaId = $_POST['markaVozila'];

                            $sql = "SELECT marka_vozila.naziv, zahtjev.zahtjev_id, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                    . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, concat(korisnik.ime, ' ', korisnik.prezime) "
                                    . "FROM korisnik INNER JOIN zahtjev ON korisnik.korisnik_id = zahtjev.korisnik "
                                    . "INNER JOIN marka_vozila ON zahtjev.marka_vozila = marka_vozila.marka_vozila_id "
                                    . "INNER JOIN moderator ON marka_vozila.marka_vozila_id = moderator.marka_vozila AND moderator.korisnik = $korisnikId "
                                    . "AND marka_vozila.marka_vozila_id = $markaVozilaId;";
                        } else {
                            $sql = "SELECT marka_vozila.naziv, zahtjev.zahtjev_id, zahtjev.tip, zahtjev.godina, "
                                    . "zahtjev.masa_vozila, zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, "
                                    . "concat(korisnik.ime, ' ', korisnik.prezime) "
                                    . "FROM korisnik INNER JOIN zahtjev ON korisnik.korisnik_id = zahtjev.korisnik "
                                    . "INNER JOIN marka_vozila ON zahtjev.marka_vozila = marka_vozila.marka_vozila_id "
                                    . "INNER JOIN moderator ON marka_vozila.marka_vozila_id = moderator.marka_vozila "
                                    . "AND moderator.korisnik = $korisnikId;";
                        }

                        $rezultat = $veza->selectDB($sql);

                        $dnevnik = new Dnevnik();
                        $dnevnik->radSBazom($_SESSION['korisnik'], $sql);

                        while ($red1 = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><?php echo $red1[0] ?></td>
                                <td><?php echo $red1[1] ?></td>
                                <td><?php echo $red1[2] ?></td>
                                <td><?php echo $red1[3] ?></td>
                                <td><?php echo $red1[4] ?></td>
                                <td><?php echo $red1[5] ?></td>
                                <td><?php echo $red1[6] ?></td>
                                <td><img src="materijali/<?php echo $red1[7] ?>" width="160" height="160"></td>
                                <td><?php echo $red1[8] ?></td>
                                
                                <?php
                                $veza = new Baza();
                                $veza->spojiDB();
                                
                                $sql2 = "SELECT procjena.procjena_id "
                                        . "FROM procjena, zahtjev "
                                        . "WHERE procjena.zahtjev = zahtjev.zahtjev_id AND procjena.prihvaceno = 1 "
                                        . "AND zahtjev.zahtjev_id = $red1[1];";
                                $rezultat2 = $veza->selectDB($sql2);
                                $red2 = mysqli_fetch_array($rezultat2);
                                if(mysqli_num_rows($rezultat2) == 0){
                                ?>
                                <td><input id="id" name="id" type="submit" value="<?php echo $red1[1] ?>"> UNESI PROCJENU</td>
                                <?php
                                } else {
                                ?>
                                <td>VOZILO JE PRODANO</td>
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