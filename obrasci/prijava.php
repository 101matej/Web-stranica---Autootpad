<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$putanja = dirname($_SERVER['REQUEST_URI']);
$direktorij = dirname(getcwd());

include '../zaglavlje.php';

//prijava putem HTTPS-a
if (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}

if(isset($_COOKIE['zapamtiMe'])){
    $zapamtiMeCookie = $_COOKIE['zapamtiMe'];
} else{
    $zapamtiMeCookie = "";
}

$poruka = "";
$greska = false;

if (isset($_POST['prijavaGumb'])) {
    $veza = new Baza();
    $veza->spojiDB();

    $korime = $_POST['korime'];
    $lozinka = $_POST['lozinka'];
    if($korime == "" || $lozinka == ""){
        $greska = true;
    }
    $upit = "SELECT * FROM korisnik WHERE korisnicko_ime = '{$korime}'";
    $rezultat = $veza->selectDB($upit);
    $prijavljen = false;
    $red = mysqli_fetch_array($rezultat);
    
    if (!$greska) {
        if ($red['korisnicko_ime'] == $korime && $red['status'] != 1) {
            if ($red['validiran'] == 1) {
                if ($red['lozinka'] == $lozinka) {
                $prijavljen = true;
                $tip = $red["tip_korisnika"];
                $sql = "UPDATE korisnik SET broj_neuspjesne_prijave = 0 WHERE korisnik_id = {$red['korisnik_id']}";
                $rezultat = $veza->updateDB($sql);
                if (isset($_POST['zapamtiMe'])) {
                    setcookie("zapamtiMe", $korime, false, '/');
                } else {
                    unset($_COOKIE['zapamtiMe']);
                    setcookie("zapamtiMe", "", time() - 3600, "/");
                }
                setcookie("autenticiran", $korime, false, '/', false);
                Sesija::kreirajKorisnika($korime, $tip);
                
                $dnevnik = new Dnevnik();
                $dnevnik->prijavaKorisnika($_SESSION['korisnik']);
                
                header("Location: ../index.php");
                exit();
            } else {
                $brojPokusaja = $red['broj_neuspjesne_prijave'] + 1;
                if ($brojPokusaja == 3 || $red['status'] == 1) {
                    $sql = "UPDATE korisnik SET status = 1 WHERE korisnik_id = {$red['korisnik_id']}";
                    $rezultat = $veza->updateDB($sql);
                    $poruka = "Upravo ste blokirani!";
                } else {
                    $preostaloPokusaja = 3 - $brojPokusaja;
                    $sql = "UPDATE korisnik SET broj_neuspjesne_prijave = {$brojPokusaja} WHERE korisnik_id = {$red['korisnik_id']}";
                    $rezultat = $veza->updateDB($sql);
                    $poruka = "Krivo unesena lozinka! Broj preostalih pokušaja iznosi {$preostaloPokusaja}!";
                }
            }
            } else{
                $validiranPoruka = "Neuspješna prijava! Niste se validirali!";
            }
            
        } else {
            $poruka = "Blokirani ste ili uneseno korisničko ime nije ispravno!";
        }
    }else{
        $poruka = "Morate unijeti korisničko ime i lozinku!";
    }
    $veza->zatvoriDB();
}

//zaboravljena lozinka
if(isset($_POST['zaboravljenaLozinka'])){
    $veza = new Baza();
    $veza->spojiDB();

    $korisnickoIme = $_POST['korime'];
    $upit = "SELECT * FROM korisnik WHERE korisnicko_ime = '{$korisnickoIme}'";
    $rezultat = $veza->selectDB($upit);
    $generiranaLozinka = bin2hex(random_bytes(4));
    $lozinka256 = hash('sha256', $generiranaLozinka);
    $red = mysqli_fetch_array($rezultat);
    
    if ($red['korisnicko_ime'] == $korisnickoIme) {
        $email = $red['email'];
        $from = "From: no-reply@email.com";
        $subjekt = "Nova lozinka";
        $opis = "Poštovani, generirana Vam je nova lozinka računa: $generiranaLozinka";
        $sql = "UPDATE korisnik SET lozinka = '$generiranaLozinka', lozinka_sha256 = '$lozinka256' WHERE korisnicko_ime = '$korisnickoIme'";
        $rezultat = $veza->updateDB($sql);
        if (mail($email, $subjekt, $opis, $from)) {
            $emailPoruka = "Poruka je poslana na sljedeću adresu: '$email'!";
        } else {
            $emailPoruka = "Neuspješno slanje poruke na sljedeću adresu: '$email'!";
        }
    }else {
        $poruka = "Blokirani ste ili Vam nije dobro korisničko ime!";
    }
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Prijava</title>
        <meta charset="utf-8">
        <meta name="author" content="Matej Forjan">
        <meta name="description" content="26.08.2022.">
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
        <link href="../css/mforjan.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <header>
            
            <div class="navigacija">
                <div class="padajuci">
                    <button class="padajuci-gumb">PADAJUĆI IZBORNIK</button>
                    <div class="padajuci-sadrzaj">
                        <?php
                        echo "<a href=\"$putanja/../index.php\">Početna</a>";
                        echo "<a href=\"$putanja/registracija.php\">Registracija</a>";
                        
                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 2) {
                            echo "<a href=\"$putanja/../zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/../prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/../kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/../stanjeRacuna.php\">Stanje računa</a>";
                            
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/../zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/../prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/../kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/../stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/../dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/../prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/../pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/../zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/../prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/../kupnjaDijelova.php\">Kupnja dijelova</a>";
                            echo "<a href=\"$putanja/../stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/../dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/../prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/../pregledZahtjeva.php\">Pregled zahtjeva</a>";
                            echo "<a href=\"$putanja/../blokiranjeKorisnika.php\">Blokiranje korisnika</a>";
                            echo "<a href=\"$putanja/../pregledDnevnika.php\">Pregled dnevnika</a>";
                            echo "<a href=\"$putanja/../markeVozila.php\">Marke vozila</a>";
                            echo "<a href=\"$putanja/../kategorijeDijelova.php\">Kategorije dijelova</a>";
                        }
                        
                        echo "<a href=\"$putanja/../rangLista.php\">Rang lista</a>";
                        echo "<a href=\"$putanja/../galerija.php\">Galerija slika</a>";
                        echo "<a href=\"$putanja/../o_autoru.html\">Informacije o autoru</a>";
                        echo "<a href=\"$putanja/../dokumentacija.html\">Dokumentacija projekta</a>";
                        
                        if (isset($_SESSION["uloga"])) {
                            echo "<a href=\"$putanja/../index.php?obrisi=true\">Odjava</a>";
                        }
                        ?>
                    </div>
                </div> 
            </div>

            <div class = "pozicijaLoga">
                <a href = "../index.php">
                    <img class = "logo" src = "../materijali/logo.png" alt = "Logo">
                </a>
            </div>

            <h1 class = "naslov" style = "text-align: center">
                PRIJAVA
            </h1>
            
            <?php
            echo "<p class = 'porukaOGresci'>$validiranPoruka</p>";
            echo "<p class = 'porukaOGresci'>$poruka</p>";
            echo "<p class = 'porukaOGresci'>$emailPoruka</p>";
            ?>
            
        </header>

        <section>
            <form novalidate class="forma" id="form2" method="post" name="form2" action="prijava.php">
                <label for="korime">Korisničko ime: </label><br>
                <input class="korimePrijavaTextbox" type="text" id="korime" name="korime" value="<?php print($zapamtiMeCookie) ?>" size="30" maxlength="30" placeholder="Korisničko ime" autofocus="autofocus" required="required"><br>
                <label for="lozinka">Lozinka: </label><br>
                <input class="lozinkaPrijavaTextbox" type="password" id="lozinka" name="lozinka" size="30" maxlength="30" placeholder="Lozinka" required="required"><br>
                <br>
                <input type="checkbox" name="zapamtiMe" value="1">Zapamti me<br><br>
                <div style="text-align: center">
                    <input type="submit" class="submit2" value="Zaboravljena lozinka" name="zaboravljenaLozinka">
                </div>
            </form>
            <div style="text-align: center">
                <input type="submit" class="submit" form="form2" value="Prijavi se" name="prijavaGumb">
            </div>
        </section>
        <footer>
            <address><b>Kontakt:</b> 
                <a style="color: white; text-decoration: none;" href="mailto:mforjan@foi.hr">
                    Matej Forjan</a></address>
            <p>&copy; 2022 M. Forjan</p>
            <img style="width: 60px; height: 60px; position: relative; top: -7px;" src="../materijali/HTML5.png" alt="Slika">
            <img style="width: 75px; height: 75px; position: relative; top: 0px;" src="../materijali/CSS3.png" alt="Slika">
        </footer>
    </body>
</html>