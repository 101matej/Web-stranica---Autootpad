<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$putanja = dirname($_SERVER['REQUEST_URI']);
$direktorij = dirname(getcwd());

include '../zaglavlje.php';

$korisnickoIme = $_POST['korime'];
$greska = false;

if(isset($_POST['registrirajGumb']) && $_POST['g-recaptcha-response'] != ""){
    $tajniKljuc = '6LdezHQgAAAAAATPERJafkgU_Xg';
    $odgovor = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $tajniKljuc . '&response=' . $_POST['g-recaptcha-response']);
    $odgovorPodaci = json_decode($odgovor);
    if ($responseData->success){
        $captcha = false;
    }else{
        $captcha = true;
    }
}

//provjera postojanja korisničkog imena
if($korisnickoIme != "" && isset($_POST['registrirajGumb'])){
    $veza = new Baza();
    $veza->spojiDB();

    $korisnickoIme = $_POST['korime'];
    $sql = "SELECT * FROM korisnik WHERE korisnicko_ime = '{$korisnickoIme}'";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    if($red['korisnicko_ime'] == $korisnickoIme){
        $poruka = "Korisnik s unesenim korisničkim imenom već postoji u bazi!";
        $greska = true;
    }
}

//pritisak na gumb registriraj
if (isset($_POST['registrirajGumb'])) {
    if ($captcha == true) {
        //provjera jesu li uneseni svi elementi
        $ime = $_POST['ime'];
        $prezime = $_POST['prez'];
        $datumRodenja = $_POST['danRod'];
        $email = $_POST['email'];
        $korisnickoIme = $_POST['korime'];
        $lozinka = $_POST['lozinka1'];
        $ponovljenaLozinka = $_POST['lozinka2'];

        if ($ime == null) {
            $imePraznoPoruka = "Ime mora biti uneseno!";
            $imePrazno = true;
            $greska = true;
        }
        if ($prezime == null) {
            $prezimePraznoPoruka = "Prezime mora biti uneseno!";
            $prezimePrazno = true;
            $greska = true;
        }
        if ($datumRodenja == null) {
            $datumRodenjaPraznoPoruka = "Datum rođenja mora biti unesen!";
            $datumRodenjaPrazno = true;
            $greska = true;
        }
        if ($email == null) {
            $emailPraznoPoruka = "Email mora biti unesen!";
            $emailPrazno = true;
            $greska = true;
        }
        if ($korisnickoIme == null) {
            $korisnickoImePraznoPoruka = "Korisničko ime mora biti uneseno!";
            $korisnickoImePrazno = true;
            $greska = true;
        }
        if ($lozinka == null) {
            $lozinkaPraznoPoruka = "Lozinka mora biti unesena!";
            $lozinkaPrazno = true;
            $greska = true;
        }
        if ($ponovljenaLozinka == null) {
            $ponovljenaLozinkaPraznoPoruka = "Ponovljena lozinka mora biti unesena!";
            $ponovljenaLozinkaPrazno = true;
            $greska = true;
        }

        //provjera imena
        $imeBroj = false;
        for ($i = 0; $i < strlen($ime); $i++) {
            if (ctype_digit($ime[$i]) ) {
                $postojiBroj = true;
                break;
            }
        }
        if ($postojiBroj == true){
            $neispravnoImePoruka = "Ne smijete unijeti broj u imenu!";
            $neispravnoIme = true;
            $greska = true;
        }

        //provjera prezimena
        $prezimeBroj = false;
        for ($i = 0; $i < strlen($prezime); $i++) {
            if (ctype_digit($prezime[$i]) ) {
                $postojiBroj = true;
                break;
            }
        }
        if ($postojiBroj == true){
            $neispravnoPrezimePoruka = "Ne smijete unijeti broj u prezimenu!";
            $neispravnoPrezime = true;
            $greska = true;
        }
        
        //provjera da lozinka ima barem jedan broj
        $postojiBroj = false;
        for ($i = 0; $i < strlen($lozinka); $i++) {
            if (ctype_digit($lozinka[$i]) ) {
                $postojiBroj = true;
                break;
            }
        }
        if ($postojiBroj == false){
            $neispravnaLozinkaPoruka = "Lozinka mora sadržavati barem jedan broj!";
            $neispravnaLozinka = true;
            $greska = true;
        }

        //provjera datuma
        $regExp = '/^(3[01]|[12][0-9]|0[1-9])[.](1[0-2]|0[1-9])[.][0-9]{4}[.]$/';
        if (!preg_match($regExp, $datumRodenja)) {
            $neispravanFormatPoruka = "Datum je neispravnog formata! Ispravan format je 'dd.mm.gggg.'!";
            $neispravanFormat = true;
            $greska = true;
        }

        //provjera podudaranja lozinki
        if ($lozinka != $ponovljenaLozinka) {
            $neispravneLozinkePoruka = "Lozinke se ne podudaraju!";
            $neispravneLozinke = true;
            $greska = true;
        }

        //registracija korisnika
        if ($greska == false) {
            $veza = new Baza();
            $veza->spojiDB();
            
            $lozinkaSHA = hash('sha256', $ponovljenaLozinka);
            $datum = date("Y-m-d", strtotime($datumRodenja));
            $generiranAktivacijkiKod = bin2hex(random_bytes(4));
            
            $email = $email;
            $from = "From: no-reply@email.com";
            $subjekt = "Aktivacijski ključ";
            $opis = "Aktivacijski ključ je sljedeći: $generiranAktivacijkiKod.https://barka.foi.hr/WebDiP/2021_projekti/WebDiP2021x024/obrasci/validiraj.php";
            
            $sql = "INSERT INTO korisnik (ime, prezime, datum_rodenja, email, korisnicko_ime, lozinka, lozinka_sha256, aktivacijski_kod, vrijeme_registracije, tip_korisnika) "
                    . "VALUES ('$ime', '$prezime', '$datum', '$email', '$korisnickoIme', '$lozinka', '$lozinkaSHA', '$generiranAktivacijkiKod', now(), 2)";
            $rezultat = $veza->updateDB($sql);
            $veza->zatvoriDB();
            
            if (mail($email, $subjekt, $opis, $from)) {
                $emailPoruka = "Poruka je poslana na sljedeću adresu: '$email'!";
            } else {
                $emailPoruka = "Neuspješno slanje poruke na sljedeću adresu: '$email'!";
            }

            header("Location: validacija.php");
        }
    }else{
        $captchaPoruka = "Morate potvrditi da niste robot!";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Registracija</title>
        <meta charset="utf-8">
        <meta name="author" content="Matej Forjan">
        <meta name="description" content="26.08.2022.">
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1">
        <link href="../css/mforjan.css" rel="stylesheet" type="text/css">
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    </head>
    <body>
        <header>
            
            <div class="navigacija">
                <div class="padajuci">
                    <button class="padajuci-gumb">PADAJUĆI IZBORNIK</button>
                    <div class="padajuci-sadrzaj">
                        
                        <?php
                        echo "<a href=\"$putanja/../index.php\">Početna</a>";
                        
                        if (!isset($_SESSION["uloga"])) {
                            echo "<a href=\"$putanja/prijava.php\">Prijava</a>";
                        }
                        
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
                REGISTRACIJA
            </h1>
            
            <?php
                echo "<p class = 'porukaOGresci'>$emailPoruka</p>";
                echo "<p class = 'porukaOGresci'>$captchaPoruka</p>";
                echo "<p class = 'porukaOGresci'>$poruka</p>";
                echo "<p class = 'porukaOGresci'>$imePraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$prezimePraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$datumRodenjaPraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$emailPraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$korisnickoImePraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$lozinkaPraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$ponovljenaLozinkaPraznoPoruka</p>";
                echo "<p class = 'porukaOGresci'>$neispravnoImePoruka</p>";
                echo "<p class = 'porukaOGresci'>$neispravnoPrezimePoruka</p>";
                echo "<p class = 'porukaOGresci'>$neispravnaLozinkaPoruka</p>";
                echo "<p class = 'porukaOGresci'>$neispravanFormatPoruka</p>";
                echo "<p class = 'porukaOGresci'>$neispravneLozinkePoruka</p>";
            ?>
            
        </header>

        <section>
            
            <form novalidate class = "forma" id = "form1" method = "post" name = "form1" action = "registracija.php">

                <label <?php
                    global $imePrazno;
                    if ($imePrazno == true || $neispravnoIme == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "ime" id="imeLabel">Ime: </label><br>
                <input class = "imeRegistracijaTextbox" type = "text" id = "ime" name = "ime" size = "35" placeholder = "Ime" required = "required" autofocus = "autofocus"><br>

                <label <?php
                    global $prezimePrazno;
                    if ($prezimePrazno == true || $neispravnoPrezime == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "prez">Prezime: </label><br>
                <input class = "prezimeRegistracijaTextbox" type = "text" id = "prez" name = "prez" size = "35" placeholder = "Prezime" required = "required"><br>

                <label <?php
                    global $datumRodenjaPrazno;
                    global $neispravanFormat;
                    if ($datumRodenjaPrazno == true || $neispravanFormat == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "danRod" id="datumRodenjaLabel">Datum rođenja: </label><br>
                <input class = "datumRodenjaRegistracijaTextbox" type = "text" id = "danRod" name = "danRod" size="35" required = "required" placeholder="Datum rođenja u formatu dd.mm.gggg."><br>

                <label <?php
                    global $emailPrazno;
                    if ($emailPrazno == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "email">Email adresa: </label><br>
                <input class = "emailRegistracijaTextbox" type = "email" id = "email" name = "email" size = "35" maxlength = "35" placeholder = "ldap@foi.hr" required = "required"><br>

                <label <?php
                    global $korisnickoImePrazno;
                    if ($korisnickoImePrazno == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "korime">Korisničko ime: </label><br>
                <input class = "korimeRegistracijaTextbox" type = "text" id = "korime" name = "korime" size = "35" maxlength = "25" placeholder = "Korisničko ime" required = "required"><br>

                <label <?php
                    global $lozinkaPrazno;
                    if ($lozinkaPrazno == true || $neispravneLozinke == true || $neispravnaLozinka == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "lozinka1" id = "lozinka1Label">Lozinka: </label><br>
                <input class = "lozinkaRegistracijaTextbox" type = "password" id = "lozinka1" name = "lozinka1" size = "35" maxlength = "50" placeholder = "Lozinka" required = "required"><br>

                <label <?php
                    global $ponovljenaLozinkaPrazno;
                    if ($ponovljenaLozinkaPrazno == true || $neispravneLozinke == true) {
                        echo "class='neispravanElement'";
                    }
                    ?> for = "lozinka2" id = "lozinka2Label">Ponovi lozinku: </label><br>
                <input class = "ponovljenaLozinkaRegistracijaTextbox" type = "password" id = "lozinka2" name = "lozinka2" size = "35" maxlength = "50" placeholder = "Lozinka" required = "required"><br>
                
                <br>
                
                <div class="g-recaptcha" data-sitekey="6LdezHQgAAAAAM0kz90fyLtlITXX7IoK7fY3ibuo"></div>
                
                <br>
                
            </form>
            <div style = "text-align: center">
                <input form = "form1" type = "submit" id = "registriraj" name="registrirajGumb" class = "submit" value = "Registriraj se ">
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