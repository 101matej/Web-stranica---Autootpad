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
        
    $tip = $_POST['tip'];
    $godina = $_POST['godina'];
    $masa = $_POST['masa'];
    $snagaMotora = $_POST['snagaMotora'];
    $vrstaGoriva = $_POST['vrstaGoriva'];
    $slika = $_POST['slika'];
    $markaVozila = $_POST['markaVozila'];
    
    $korime = $_SESSION['korisnik'];
    
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    $korisnikId = $red2['korisnik_id'];

    if ($tip == "" || $godina == "" || $masa == "" || $snagaMotora == "" || $vrstaGoriva == "" || $slika == "" || $markaVozila == "0") {
        $poruka = "Neispravno uneseni podaci!";
        $greska = true;
    }

    if (!$greska) {
        
        $sql = "INSERT INTO zahtjev (tip, godina, masa_vozila, snaga_motora, vrsta_goriva, slika, marka_vozila, korisnik) "
                . "VALUES ('$tip', $godina, $masa, $snagaMotora, '$vrstaGoriva', '$slika', $markaVozila, $korisnikId)";
        
        $rezultat = $veza->updateDB($sql);
        $veza->zatvoriDB();
    }
}

//pritisnut id marke vozila
if (isset($_POST['id'])) {
    $azurirajIdGumb = $_POST['id'];
    $veza = new Baza();
    $veza->spojiDB();
    $sql = "SELECT * FROM zahtjev WHERE zahtjev_id ='{$azurirajIdGumb}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $azurirajId = $red['zahtjev_id'];
    $azurirajTip = $red['tip'];
    $azurirajGodina = $red['godina'];
    $azurirajMasa = $red['masa_vozila'];
    $azurirajSnaga = $red['snaga_motora'];
    $azurirajVrstaGoriva = $red['vrsta_goriva'];
    
    $veza->zatvoriDB();
}

//pritisnut gumb za azuriranje
if (isset($_POST['azurirajGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();
    
    $azurirajId = $_POST['idZahtjevaAzuriraj'];
    $azurirajTip = $_POST['tipVozilaAzuriraj'];
    $azurirajGodina = $_POST['godinaVozilaAzuriraj'];
    $azurirajMasa = $_POST['masaVozilaAzuriraj'];
    $azurirajSnaga = $_POST['snagaMotoraAzuriraj'];
    $azurirajVrstaGoriva = $_POST['vrstaGorivaAzuriraj'];
    $azurirajSlika = $_POST['slikaAzuriraj'];
    $azurirajMarkaVozila = $_POST['markaVozilaAzuriraj'];
    
    $sql2 = "SELECT * FROM procjena WHERE zahtjev = $azurirajId";
    $rezultat2 = $veza->selectDB($sql2);
    
    if (mysqli_num_rows($rezultat2) != 0){
        $poruka = "Ne možete ažurirati odabrani zahtjev, već postoji procjena za njega!";
    } else {
        $sql = "UPDATE zahtjev SET tip = '{$azurirajTip}', godina = {$azurirajGodina}, masa_vozila = {$azurirajMasa}, "
            . "snaga_motora = {$azurirajSnaga}, vrsta_goriva = '{$azurirajVrstaGoriva}', "
            . "slika = '{$azurirajSlika}', marka_vozila = {$azurirajMarkaVozila} WHERE zahtjev_id = {$azurirajId}";
    }
    
    $rezultat = $veza->updateDB($sql);
    
    //header("Location: zahtjevi.php");
    
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Zahtjevi</title>
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
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
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
                ZAHTJEVI
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formZahtjevi" method="post" name="formZahtjevi" action="zahtjevi.php">
                <label for="tip">Tip vozila: </label><br>
                <input class="tipVozilaTextbox" type="text" name="tip" size="30" maxlength="30" placeholder="Tip vozila" autofocus="autofocus" required="required"><br>
                
                <label for="godina">Godina vozila: </label><br>
                <input class="godinaVozilaTextbox" type="text" name="godina" size="30" maxlength="30" placeholder="Godina vozila" autofocus="autofocus" required="required"><br>
                
                <label for="masa">Masa vozila: </label><br>
                <input class="masaVozilaTextbox" type="text" name="masa" size="30" maxlength="30" placeholder="Masa vozila" autofocus="autofocus" required="required"><br>
                
                <label for="snagaMotora">Snaga motora: </label><br>
                <input class="snagaMotoraTextbox" type="text" name="snagaMotora" size="30" maxlength="30" placeholder="Snaga motora" autofocus="autofocus" required="required"><br>
                
                <label for="vrstaGoriva">Vrsta goriva: </label><br>
                <input class="vrstaGorivaTextbox" type="text" name="vrstaGoriva" size="30" maxlength="30" placeholder="Vrsta goriva" autofocus="autofocus" required="required"><br>
                <br>
                <label for="slika" id="slika">Slika: </label><br>
                <input class="slikaButton" type="file" id="slika" name="slika">
                <br><br>
                <label for="markaVozila">Odaberi marku vozila: </label><br>
                <select id="markaVozilaZahtjev" name="markaVozila">
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
                <input type="submit" class="submit" form="formZahtjevi" value="Unesi" name="unesiGumb">
            </div>
            
            
            <form novalidate class="forma" id="formZahtjeviAzuriranje" method="post" name="formZahtjeviAzuriranje" action="zahtjevi.php">
                <label for="idZahtjevaAzuriraj">Id zahtjeva: </label><br>
                <input <?php
                global $azurirajId;
                if ($azurirajId != null){
                    echo "value='{$azurirajId}'";
                }
                ?> class="idZahtjevaTextbox" type="text" name="idZahtjevaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required" readonly="true"><br>
                
                <label for="tipVozilaAzuriraj">Tip vozila: </label><br>
                <input <?php
                global $azurirajTip;
                if ($azurirajTip != null){
                    echo "value='{$azurirajTip}'";
                }
                ?> class="tipVozilaTextbox" type="text" name="tipVozilaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="godinaVozilaAzuriraj">Godina vozila: </label><br>
                <input <?php
                global $azurirajGodina;
                if ($azurirajGodina != null){
                    echo "value='{$azurirajGodina}'";
                }
                ?> class="godinaVozilaTextbox" type="text" name="godinaVozilaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="masaVozilaAzuriraj">Masa vozila: </label><br>
                <input <?php
                global $azurirajMasa;
                if ($azurirajMasa != null){
                    echo "value='{$azurirajMasa}'";
                }
                ?> class="masaVozilaTextbox" type="text" name="masaVozilaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="snagaMotoraAzuriraj">Snaga motora: </label><br>
                <input <?php
                global $azurirajSnaga;
                if ($azurirajSnaga != null){
                    echo "value='{$azurirajSnaga}'";
                }
                ?> class="snagaMotoraTextbox" type="text" name="snagaMotoraAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                
                <label for="vrstaGorivaAzuriraj">Vrsta goriva: </label><br>
                <input <?php
                global $azurirajVrstaGoriva;
                if ($azurirajVrstaGoriva != null){
                    echo "value='{$azurirajVrstaGoriva}'";
                }
                ?> class="vrstaGorivaTextbox" type="text" name="vrstaGorivaAzuriraj" size="30" maxlength="30" autofocus="autofocus" required="required"><br>
                <br>
                <label for="slikaAzuriraj" id="slika">Slika: </label><br>
                <input class="slikaButton" type="file" id="slika" name="slikaAzuriraj"><br>
                <br>
                <label for="markaVozila">Odaberi marku vozila: </label><br>
                <select id="markaVozilaZahtjev" name="markaVozilaAzuriraj">
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
                <input type="submit" class="submit" form="formZahtjeviAzuriranje" value="Ažuriraj" name="azurirajGumb">
            </div>
            
            
            <form name="stanjeRacuna" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">ZAHTJEVI</caption>
                    <thead>
                        <tr>
                            <th>ID zahtjeva</th>
                            <th>Marka</th>
                            <th>Tip</th>
                            <th>Godina</th>
                            <th>Masa u KG</th>
                            <th>Snaga motora u KS</th>
                            <th>Vrsta goriva</th>
                            <th>Slika</th>
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

                        $sql = "SELECT zahtjev.zahtjev_id, marka_vozila.naziv, zahtjev.tip, zahtjev.godina, "
                                . "zahtjev.masa_vozila, zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika "
                                . "FROM zahtjev INNER JOIN marka_vozila ON zahtjev.marka_vozila = marka_vozila.marka_vozila_id "
                                . "AND zahtjev.korisnik = $korisnikId;";

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
                                <td><img src="materijali/<?php echo $red[7] ?>" width="160" height="160"></td>
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