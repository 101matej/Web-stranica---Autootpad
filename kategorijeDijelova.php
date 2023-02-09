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
if (isset($_SESSION["uloga"]) && ($_SESSION["uloga"] == 1 || $_SESSION["uloga"] == 2 || $_SESSION["uloga"] == 3)) {
    header("Location: obrasci/prijava.php");
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time() - 3600, "/");
    Sesija::obrisiSesiju();
    exit();
}

//pritisnut gumb za unos
if (isset($_POST['unesiGumb'])) {
    $nazivKategorije = $_POST['nazivKategorije'];
    $datumKreiranja = date(Y.m.d);

    if ($nazivKategorije == "") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "INSERT INTO kategorija_dijela (naziv, datum_kreiranja) "
                . "VALUES ('$nazivKategorije', '$datumKreiranja')";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut gumb za odabiranje marke za kategoriju
if (isset($_POST['odaberiGumb'])) {
    $kategorijaId = $_POST['kategorija'];
    $markaVozilaId = $_POST['markaVozila'];

    if ($kategorijaId == "0" || $markaVozilaId == "0") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "INSERT INTO sadrzava (kategorija_dijela, marka_vozila) "
                . "VALUES ($kategorijaId, $markaVozilaId)";
        $dnevnik = new Dnevnik();
        $dnevnik->radSBazom($_SESSION['korisnik'], $sql);
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut id marke vozila
if (isset($_POST['id'])) {
    $azurirajIdGumb = $_POST['id'];
    $veza = new Baza();
    $veza->spojiDB();
    $sql = "SELECT * FROM kategorija_dijela WHERE kategorija_dijela_id='{$azurirajIdGumb}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $azurirajId = $red['kategorija_dijela_id'];
    $azurirajNaziv = $red['naziv'];
    $azurirajDatumKreiranja = $red['datum_kreiranja'];
    
    $veza->zatvoriDB();
}

//pritisnut gumb za azuriranje
if (isset($_POST['azurirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $azurirajId = $_POST['idKategorijeAzuriraj'];
    $azurirajNaziv = $_POST['nazivKategorijeAzuriraj'];
    $azurirajDatumKreiranja = $_POST['datumKreiranjaAzuriraj'];
    
    $sql = "UPDATE kategorija_dijela SET naziv = '{$azurirajNaziv}', datum_kreiranja = '{$azurirajDatumKreiranja}' WHERE kategorija_dijela_id = {$azurirajId}";
    
    $rezultat = $veza->updateDB($sql);
    
    header("Location: kategorijeDijelova.php");
    
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Kategorije dijelova</title>
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
                KATEGORIJE DIJELOVA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formKategorijaDijelova" method="post" name="formKategorijaDijelova" action="kategorijeDijelova.php">
                <label for="nazivKategorije">Naziv kategorije: </label><br>
                <input class="nazivKategorijeTextbox" type="text" name="nazivKategorije" size="30" maxlength="30" placeholder="Naziv kategorije" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formKategorijaDijelova" value="Unesi" name="unesiGumb">
            </div>
            
            
            <form novalidate class="forma" id="formSadrzava" method="post" name="formSadrzava" action="kategorijeDijelova.php">
                <label for="kategorija">Odaberi kategoriju: </label><br>
                <select id="kategorija" name="kategorija">
                    <option value="0" >Odaberi kategoriju:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();

                    $sql = "SELECT kategorija_dijela.kategorija_dijela_id, kategorija_dijela.naziv FROM kategorija_dijela;";

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
                
                <label for="markaVozila">Odaberi marku vozila: </label><br>
                <select id="markaVozila" name="markaVozila">
                    <option value="0" >Odaberi marku vozila:</option>
                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();

                    $sql = "SELECT marka_vozila.marka_vozila_id, marka_vozila.naziv FROM marka_vozila";

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
                <input type="submit" class="submit" form="formSadrzava" value="Odaberi" name="odaberiGumb">
            </div>
            
            <form novalidate class="forma" id="formKategorijaDijelaAzuriranje" method="post" name="formKategorijaDijelaAzuriranje" action="kategorijeDijelova.php">
                <label for="idKategorijeDijelaAzuriraj">Id kategorije: </label><br>
                <input <?php
                global $azurirajId;
                if ($azurirajId != null){
                    echo "value='{$azurirajId}'";
                }
                ?> class="idKategorijeTextbox" type="text" name="idKategorijeAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                <label for="nazivKategorijeAzuriraj">Naziv kategorije: </label><br>
                <input <?php
                global $azurirajNaziv;
                if ($azurirajNaziv != null){
                    echo "value='{$azurirajNaziv}'";
                }
                ?> class="nazivKategorijeTextbox" type="text" name="nazivKategorijeAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="datumKreiranjaAzuriraj">Datum kreiranja: </label><br>
                
                <input <?php
                global $azurirajDatumKreiranja;
                if ($azurirajDatumKreiranja != null){
                    echo "value='{$azurirajDatumKreiranja}'";
                }
                ?> class="datumKreiranjaKategorijeTextbox" type="text" name="datumKreiranjaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formKategorijaDijelaAzuriranje" value="Ažuriraj" name="azurirajGumb">
            </div>
            
            
            <form name="kategorijeDijelova" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">KATEGORIJE DIJELOVA</caption>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naziv</th>
                            <th>Datum kreiranja</th>
                            <th>Marka vozila</th>
                        </tr>
                    </thead>
                    <tbody> 

                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();

                        $sql = "SELECT kategorija_dijela_id, kategorija_dijela.naziv, kategorija_dijela.datum_kreiranja, marka_vozila.naziv "
                                . "FROM kategorija_dijela INNER JOIN sadrzava ON kategorija_dijela.kategorija_dijela_id = sadrzava.kategorija_dijela "
                                . "INNER JOIN marka_vozila ON sadrzava.marka_vozila = marka_vozila.marka_vozila_id ORDER BY 1 ASC;";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><input id="id" name="id" type="submit" value="<?php echo $red['kategorija_dijela_id'] ?>"> AŽURIRAJ</td>
                                <td><?php echo $red[1] ?></td>
                                <td><?php echo $red['datum_kreiranja'] ?></td>
                                <td><?php echo $red[3] ?></td>
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