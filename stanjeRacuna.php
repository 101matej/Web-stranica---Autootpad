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

//pritisnut gumb za unos
if (isset($_POST['unesiGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
        
    $stanje = $_POST['stanje'];
    $banka = $_POST['banka'];
    $korime = $_COOKIE['autenticiran'];
    
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    $korisnikId = $red2['korisnik_id'];

    if ($stanje == "" || $banka == "") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        
        $sql = "INSERT INTO stanje_racuna (stanje, banka, korisnik) "
                . "VALUES ($stanje, '$banka', $korisnikId)";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut id marke vozila
if (isset($_POST['id'])) {
    $azurirajIdGumb = $_POST['id'];
    $veza = new Baza();
    $veza->spojiDB();
    $sql = "SELECT * FROM stanje_racuna WHERE stanje_racuna_id ='{$azurirajIdGumb}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $azurirajId = $red['stanje_racuna_id'];
    $azurirajStanje = $red['stanje'];
    $azurirajBanka = $red['banka'];
    
    $veza->zatvoriDB();
}

//pritisnut gumb za azuriranje
if (isset($_POST['azurirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $azurirajId = $_POST['idStanjaAzuriraj'];
    $azurirajStanje = $_POST['stanjeAzuriraj'];
    $azurirajBanka = $_POST['bankaAzuriraj'];
    
    $sql = "UPDATE stanje_racuna SET stanje = {$azurirajStanje}, banka = '{$azurirajBanka}' WHERE stanje_racuna_id = {$azurirajId}";
    
    $rezultat = $veza->updateDB($sql);
    
    header("Location: stanjeRacuna.php");
    
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Stanje računa</title>
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
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
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
                STANJE RAČUNA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formStanjeRacuna" method="post" name="formStanjeRacuna" action="stanjeRacuna.php">
                <label for="stanje">Stanje računa: </label><br>
                <input class="stanjeRacunaTextbox" type="text" name="stanje" size="30" maxlength="30" placeholder="Stanje računa" autofocus="autofocus" required="required"><br>
                
                <label for="banka">Banka: </label><br>
                <input class="bankaTextbox" type="text" name="banka" size="30" maxlength="30" placeholder="Banka" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formStanjeRacuna" value="Unesi" name="unesiGumb">
            </div>
            
            
            <form novalidate class="forma" id="formStanjeRacunaAzuriranje" method="post" name="formStanjeRacunaAzuriranje" action="stanjeRacuna.php">
                <label for="idStanjaAzuriraj">Id stanja računa: </label><br>
                <input <?php
                global $azurirajId;
                if ($azurirajId != null){
                    echo "value='{$azurirajId}'";
                }
                ?> class="idStanjaTextbox" type="text" name="idStanjaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                <label for="stanjeAzuriraj">Stanje računa: </label><br>
                <input <?php
                global $azurirajStanje;
                if ($azurirajStanje != null){
                    echo "value='{$azurirajStanje}'";
                }
                ?> class="stanjeRacunaTextbox" type="text" name="stanjeAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="bankaAzuriraj">Banka: </label><br>
                <input <?php
                global $azurirajBanka;
                if ($azurirajBanka != null){
                    echo "value='{$azurirajBanka}'";
                }
                ?> class="bankaTextbox" type="text" name="bankaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <br>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="formStanjeRacunaAzuriranje" value="Ažuriraj" name="azurirajGumb">
            </div>
            
            
            <form name="stanjeRacuna" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">STANJE RAČUNA</caption>
                    <thead>
                        <tr>
                            <th>ID stanja računa</th>
                            <th>Ime i prezime</th>
                            <th>Datum rođenja</th>
                            <th>Stanje računa</th>
                            <th>Banka</th>
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

                        $sql = "SELECT stanje_racuna.stanje_racuna_id, concat(korisnik.ime, ' ', korisnik.prezime), korisnik.datum_rodenja, "
                                . "stanje_racuna.stanje, stanje_racuna.banka "
                                . "FROM stanje_racuna INNER JOIN korisnik ON stanje_racuna.korisnik = korisnik.korisnik_id "
                                . "AND korisnik.korisnik_id = $korisnikId;";

                        $rezultat = $veza->selectDB($sql);

                        while ($red = mysqli_fetch_array($rezultat)) {
                            ?>
                            <tr>
                                <td><input id="id" name="id" type="submit" value="<?php echo $red['stanje_racuna_id'] ?>"> AŽURIRAJ</td>
                                <td><?php echo $red[1] ?></td>
                                <td><?php echo $red['datum_rodenja'] ?></td>
                                <td><?php echo $red['stanje'] ?></td>
                                <td><?php echo $red['banka'] ?></td>
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