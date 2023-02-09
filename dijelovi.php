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

//pritisnut gumb za unos
if (isset($_POST['unesiGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
        
    $naziv = $_POST['nazivDijela'];
    $proizvodac = $_POST['proizvodacDijela'];
    $masa = $_POST['masaDijela'];
    $opis = $_POST['opisDijela'];
    $cijena = $_POST['cijenaDijela'];
    $datumKreiranja = date(Y.m.d);
    $vozilo = $_POST['vozilo'];
    $kategorija_dijela = $_POST['kategorijaDijela'];
    $korime = $_COOKIE['autenticiran'];
    
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    $korisnikId = $red2['korisnik_id'];

    if ($naziv == "" || $proizvodac == "" || $masa == "" || $opis == "" || $cijena == "" || $vozilo == "0" || $kategorija_dijela == "0") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        
        $sql = "INSERT INTO dio (naziv, proizvodac, masa, opis, cijena, datum_kreiranja, vozilo, kategorija_dijela, korisnik) "
                . "VALUES ('$naziv', '$proizvodac', $masa, '$opis', $cijena, '$datumKreiranja', $vozilo, $kategorija_dijela, $korisnikId)";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut id marke vozila
if (isset($_POST['id'])) {
    $azurirajIdGumb = $_POST['id'];
    $veza = new Baza();
    $veza->spojiDB();
    $sql = "SELECT * FROM dio WHERE dio_id ='{$azurirajIdGumb}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $azurirajId = $red['dio_id'];
    $azurirajNaziv = $red['naziv'];
    $azurirajProizvodac = $red['proizvodac'];
    $azurirajMasa = $red['masa'];
    $azurirajOpis = $red['opis'];
    $azurirajCijena = $red['cijena'];
    $azurirajDatumKreiranja = $red['datum_kreiranja'];
    $azurirajRaspolozivo = $red['raspolozivo'];
    
    $veza->zatvoriDB();
}

//pritisnut gumb za azuriranje
if (isset($_POST['azurirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $azurirajId = $_POST['idDijelaAzuriraj'];
    $azurirajNaziv = $_POST['nazivDijelaAzuriraj'];
    $azurirajProizvodac = $_POST['proizvodacDijelaAzuriraj'];
    $azurirajMasa = $_POST['masaDijelaAzuriraj'];
    $azurirajOpis = $_POST['opisDijelaAzuriraj'];
    $azurirajCijena = $_POST['cijenaDijelaAzuriraj'];
    $azurirajDatumKreiranja = $_POST['datumKreiranjaDijelaAzuriraj'];
    $azurirajRaspolozivo = $_POST['raspolozivo'];
    
    if($azurirajRaspolozivo == "on"){
        $azurirajRaspolozivoBaza = 1;
    } else {
        $azurirajRaspolozivoBaza = 0;
    }
    
    $azurirajVozilo = $_POST['vozilo'];
    $azurirajKategorijaDijela = $_POST['kategorijaDijela'];
    
    $sql = "UPDATE dio SET naziv = '{$azurirajNaziv}', proizvodac = '{$azurirajProizvodac}', masa = {$azurirajMasa}, "
    . "opis = '{$azurirajOpis}', cijena = {$azurirajCijena}, datum_kreiranja = '{$azurirajDatumKreiranja}', "
    . "raspolozivo = {$azurirajRaspolozivoBaza}, vozilo = {$azurirajVozilo}, kategorija_dijela = {$azurirajKategorijaDijela} "
    . "WHERE dio_id = {$azurirajId}";
    
    $rezultat = $veza->updateDB($sql);
    
    header("Location: dijelovi.php");
    
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Dijelovi</title>
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
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
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
                DIJELOVI
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formDijelovi" method="post" name="formDijelovi" action="dijelovi.php">
                <label for="nazivDijela">Naziv dijela: </label><br>
                <input class="nazivDijelaTextbox" type="text" name="nazivDijela" size="30" maxlength="30" placeholder="Naziv dijela" autofocus="autofocus" required="required"><br>
                
                <label for="proizvodacDijela">Proizvođač dijela: </label><br>
                <input class="proizvodacDijelaTextbox" type="text" name="proizvodacDijela" size="30" maxlength="30" placeholder="Proizvodac dijela" autofocus="autofocus" required="required"><br>
                
                <label for="masaDijela">Masa dijela: </label><br>
                <input class="masaDijelaTextbox" type="text" name="masaDijela" size="30" maxlength="30" placeholder="Masa dijela" autofocus="autofocus" required="required"><br>
                
                <label for="opisDijela">Opis dijela: </label><br>
                <input class="opisDijelaTextbox" type="text" name="opisDijela" size="30" maxlength="30" placeholder="Opis dijela" autofocus="autofocus" required="required"><br>
                
                <label for="cijenaDijela">Cijena dijela: </label><br>
                <input class="cijenaDijelaTextbox" type="text" name="cijenaDijela" size="30" maxlength="30" placeholder="Cijena dijela" autofocus="autofocus" required="required"><br>
                
                <label for="vozilo">Odaberi vozilo: </label><br>
                <select id="vozilo" name="vozilo">
                    <option value="0" >Odaberi vozilo:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_SESSION['korisnik'];

                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];

                    $sql = "SELECT vozilo.vozilo_id, concat(marka_vozila.naziv, ' ', vozilo.tip) "
                            . "FROM vozilo, marka_vozila WHERE vozilo.marka_vozila = marka_vozila.marka_vozila_id "
                            . "AND vozilo.korisnik = $korisnikId;";

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
                
                <label for="kategorijaDijela">Odaberi kategoriju: </label><br>
                <select id="kategorijaDijela" name="kategorijaDijela">
                    <option value="0" >Odaberi kategoriju:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_SESSION['korisnik'];

                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];

                    $sql = "SELECT kategorija_dijela.kategorija_dijela_id, kategorija_dijela.naziv "
                            . "FROM kategorija_dijela, sadrzava, marka_vozila, moderator "
                            . "WHERE kategorija_dijela.kategorija_dijela_id = sadrzava.kategorija_dijela "
                            . "AND sadrzava.marka_vozila = marka_vozila.marka_vozila_id "
                            . "AND marka_vozila.marka_vozila_id = moderator.marka_vozila AND moderator.korisnik = $korisnikId;";

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
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formDijelovi" value="Unesi" name="unesiGumb">
            </div>
            
            
            <form novalidate class="forma" id="formDijeloviAzuriranje" method="post" name="formDijeloviAzuriranje" action="dijelovi.php">
                <label for="idDijelaAzuriraj">Id dijela: </label><br>
                <input <?php
                global $azurirajId;
                if ($azurirajId != null){
                    echo "value='{$azurirajId}'";
                }
                ?> class="IdDijelaTextbox" type="text" name="idDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                <label for="nazivDijelaAzuriraj">Naziv dijela: </label><br>
                <input <?php
                global $azurirajNaziv;
                if ($azurirajNaziv != null){
                    echo "value='{$azurirajNaziv}'";
                }
                ?> class="nazivDijelaTextbox" type="text" name="nazivDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="proizvodacDijelaAzuriraj">Proizvođač dijela: </label><br>
                <input <?php
                global $azurirajProizvodac;
                if ($azurirajProizvodac != null){
                    echo "value='{$azurirajProizvodac}'";
                }
                ?> class="proizvodacDijelaTextbox" type="text" name="proizvodacDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="masaDijelaAzuriraj">Masa dijela: </label><br>
                <input <?php
                global $azurirajMasa;
                if ($azurirajMasa != null){
                    echo "value='{$azurirajMasa}'";
                }
                ?> class="masaDijelaTextbox" type="text" name="masaDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="opisDijelaAzuriraj">Opis dijela: </label><br>
                <input <?php
                global $azurirajOpis;
                if ($azurirajOpis != null){
                    echo "value='{$azurirajOpis}'";
                }
                ?> class="opisDijelaTextbox" type="text" name="opisDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="cijenaDijelaAzuriraj">Cijena dijela: </label><br>
                <input <?php
                global $azurirajCijena;
                if ($azurirajCijena != null){
                    echo "value='{$azurirajCijena}'";
                }
                ?> class="cijenaDijelaTextbox" type="text" name="cijenaDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="datumKreiranjaDijelaAzuriraj">Datum kreiranja: </label><br>
                <input <?php
                global $azurirajDatumKreiranja;
                if ($azurirajDatumKreiranja != null){
                    echo "value='{$azurirajDatumKreiranja}'";
                }
                ?> class="datumKreiranjaDijelaTextbox" type="text" name="datumKreiranjaDijelaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="raspolozivo">Raspoloživo: </label>
                <input <?php
                global $azurirajRaspolozivo;
                if ($azurirajRaspolozivo == "1"){
                    echo "checked='checked'";
                }
                ?> type="checkbox" id="raspolozivo" name="raspolozivo"><br>
                
                <label for="vozilo">Odaberi vozilo: </label><br>
                <select id="vozilo" name="vozilo">
                    <option value="0" >Odaberi vozilo:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_SESSION['korisnik'];

                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];

                    $sql = "SELECT vozilo.vozilo_id, concat(marka_vozila.naziv, ' ', vozilo.tip) "
                            . "FROM vozilo, marka_vozila WHERE vozilo.marka_vozila = marka_vozila.marka_vozila_id "
                            . "AND vozilo.korisnik = $korisnikId;";

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
                
                <label for="kategorijaDijela">Odaberi kategoriju: </label><br>
                <select id="kategorijaDijela" name="kategorijaDijela">
                    <option value="0" >Odaberi kategoriju:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    $korime = $_SESSION['korisnik'];

                    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
                    $rezultat2 = $veza->selectDB($sql2);
                    $red2 = mysqli_fetch_array($rezultat2);
                    $korisnikId = $red2['korisnik_id'];

                    $sql = "SELECT kategorija_dijela.kategorija_dijela_id, kategorija_dijela.naziv "
                            . "FROM kategorija_dijela, sadrzava, marka_vozila, moderator "
                            . "WHERE kategorija_dijela.kategorija_dijela_id = sadrzava.kategorija_dijela "
                            . "AND sadrzava.marka_vozila = marka_vozila.marka_vozila_id "
                            . "AND marka_vozila.marka_vozila_id = moderator.marka_vozila AND moderator.korisnik = $korisnikId;";

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
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formDijeloviAzuriranje" value="Ažuriraj" name="azurirajGumb">
            </div>
            
            
            <form name="stanjeRacuna" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">DIJELOVI</caption>
                    <thead>
                        <tr>
                            <th>ID dijela</th>
                            <th>Naziv</th>
                            <th>Proizvođač</th>
                            <th>Masa u KG</th>
                            <th>Opis</th>
                            <th>Cijena</th>
                            <th>Datum kreiranja</th>
                            <th>Raspoloživo</th>
                            <th>Kategorija</th>
                            <th>Marka</th>
                            <th>Tip vozila</th>
                            <th>Godina vozila</th>
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

                        $sql = "SELECT dio.dio_id, dio.naziv, dio.proizvodac, dio.masa, dio.opis, dio.cijena, dio.datum_kreiranja, "
                                . "dio.raspolozivo, kategorija_dijela.naziv, marka_vozila.naziv, vozilo.tip, vozilo.godina "
                                . "FROM dio, kategorija_dijela, marka_vozila, vozilo "
                                . "WHERE dio.kategorija_dijela = kategorija_dijela.kategorija_dijela_id "
                                . "AND marka_vozila.marka_vozila_id = vozilo.marka_vozila AND vozilo.vozilo_id = dio.vozilo "
                                . "AND dio.korisnik = $korisnikId ORDER BY dio.dio_id ASC;";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><input id="id" name="id" type="submit" value="<?php echo $red[0] ?>"> AŽURIRAJ</td>
                                <td><?php echo $red[1] ?></td>
                                <td><?php echo $red[2] ?></td>
                                <td><?php echo $red[3] ?></td>
                                <td><?php echo $red[4] ?></td>
                                <td><?php echo $red[5] ?></td>
                                <td><?php echo $red[6] ?></td>
                                <?php
                                if ($red[7] == 1) {
                                    ?>
                                    <td>Raspoloživo</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nije raspoloživo</td>
                                    <?php
                                }
                                ?>
                                <td><?php echo $red[8] ?></td>
                                <td><?php echo $red[9] ?></td>
                                <td><?php echo $red[10] ?></td>
                                <td><?php echo $red[11] ?></td>
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