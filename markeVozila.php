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
    $nazivMarke = $_POST['nazivMarke'];
    $datumKreiranja = date(Y.m.d);

    if ($nazivMarke == "") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "INSERT INTO marka_vozila (naziv, datum_kreiranja) "
                . "VALUES ('$nazivMarke', '$datumKreiranja')";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut gumb za dodjeljivanje moderatora
if (isset($_POST['dodijeliGumb'])) {
    $moderatorId = $_POST['moderator'];
    $markaVozilaId = $_POST['markaVozila'];

    if ($moderatorId == "0" || $markaVozilaId == "0") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        $veza = new Baza();
        $veza->spojiDB();
        $sql = "INSERT INTO moderator (korisnik, marka_vozila) "
                . "VALUES ($moderatorId, $markaVozilaId)";
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
    $sql = "SELECT * FROM marka_vozila WHERE marka_vozila_id='{$azurirajIdGumb}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $azurirajId = $red['marka_vozila_id'];
    $azurirajNaziv = $red['naziv'];
    $azurirajDatumKreiranja = $red['datum_kreiranja'];
    
    $veza->zatvoriDB();
}

//pritisnut gumb za azuriranje
if (isset($_POST['azurirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $azurirajId = $_POST['idMarkeAzuriraj'];
    $azurirajNaziv = $_POST['nazivMarkeAzuriraj'];
    $azurirajDatumKreiranja = $_POST['datumKreiranjaAzuriraj'];
    
    $sql = "UPDATE marka_vozila SET naziv = '{$azurirajNaziv}', datum_kreiranja = '{$azurirajDatumKreiranja}' WHERE marka_vozila_id = {$azurirajId}";
    
    $rezultat = $veza->updateDB($sql);
    
    header("Location: markeVozila.php");
    
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Marke vozila</title>
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
                MARKE VOZILA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formMarkaVozila" method="post" name="formMarkaVozila" action="markeVozila.php">
                <label for="nazivMarke">Naziv marke: </label><br>
                <input class="nazivMarkeTextbox" type="text" name="nazivMarke" size="30" maxlength="30" placeholder="Naziv marke" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formMarkaVozila" value="Unesi" name="unesiGumb">
            </div>
            
            
            <form novalidate class="forma" id="formModerator" method="post" name="formModerator" action="markeVozila.php">
                <label for="moderator">Odaberi moderatora: </label><br>
                <select id="moderator" name="moderator">
                    <option value="0" >Odaberi moderatora:</option>

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();

                    $sql = "SELECT korisnik.korisnik_id, CONCAT(korisnik.ime, ' ',korisnik.prezime) FROM korisnik WHERE korisnik.tip_korisnika = 3";

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
                <input type="submit" class="submit" form="formModerator" value="Dodijeli" name="dodijeliGumb">
            </div>
            
            <form novalidate class="forma" id="formMarkaVozilaAzuriranje" method="post" name="formMarkaVozilaAzuriranje" action="markeVozila.php">
                <label for="idMarkeVozilaAzuriraj">Id marke: </label><br>
                <input <?php
                global $azurirajId;
                if ($azurirajId != null){
                    echo "value='{$azurirajId}'";
                }
                ?> class="idMarkeTextbox" type="text" name="idMarkeAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                <label for="nazivMarkeAzuriraj">Naziv marke: </label><br>
                <input <?php
                global $azurirajNaziv;
                if ($azurirajNaziv != null){
                    echo "value='{$azurirajNaziv}'";
                }
                ?> class="nazivMarkeTextbox" type="text" name="nazivMarkeAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="datumKreiranjaAzuriraj">Datum kreiranja: </label><br>
                
                <input <?php
                global $azurirajDatumKreiranja;
                if ($azurirajDatumKreiranja != null){
                    echo "value='{$azurirajDatumKreiranja}'";
                }
                ?> class="datumKreiranjaTextbox" type="text" name="datumKreiranjaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formMarkaVozilaAzuriranje" value="Ažuriraj" name="azurirajGumb">
            </div>
            
            
            <form name="markeVozila" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">MARKE VOZILA</caption>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Naziv</th>
                            <th>Datum kreiranja</th>
                            <th>Ime i prezime moderatora</th>
                        </tr>
                    </thead>
                    <tbody> 

                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();

                        $sql = "SELECT marka_vozila_id, naziv, datum_kreiranja, concat(ime, ' ', prezime) "
                                . "FROM marka_vozila, moderator, korisnik WHERE marka_vozila.marka_vozila_id = moderator.marka_vozila "
                                . "AND moderator.korisnik = korisnik.korisnik_id ORDER BY 1;";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><input id="id" name="id" type="submit" value="<?php echo $red['marka_vozila_id'] ?>"> AŽURIRAJ</td>
                                <td><?php echo $red['naziv'] ?></td>
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