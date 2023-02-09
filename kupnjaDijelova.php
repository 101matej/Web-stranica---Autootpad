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
    
    $idDijela = $_POST['id'];
    
    $sql = "SELECT * FROM dio WHERE dio_id = $idDijela";
    $rezultat = $veza->selectDB($sql);
    $red = mysqli_fetch_array($rezultat);
    
    $cijenaDijela = $red['cijena'];
    $prodavacDijelaId = $red['korisnik'];
    
    $korime = $_COOKIE['autenticiran'];
    $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
    $rezultat2 = $veza->selectDB($sql2);
    $red2 = mysqli_fetch_array($rezultat2);
    
    $prijavljeniKorisnikId = $red2['korisnik_id'];
    
    $sql3 = "SELECT * FROM stanje_racuna WHERE korisnik = $prijavljeniKorisnikId";
    $rezultat3 = $veza->selectDB($sql3);
    $red3 = mysqli_fetch_array($rezultat3);
    
    $prijavljeniKorisnikStanje = $red3['stanje'];
    
    $sql4 = "SELECT * FROM stanje_racuna WHERE korisnik = $prodavacDijelaId";
    $rezultat4 = $veza->selectDB($sql4);
    $red4 = mysqli_fetch_array($rezultat4);
    
    $prodavacDijelaStanje = $red4['stanje'];
    
    if($prijavljeniKorisnikStanje >= $cijenaDijela) {
        $prijavljeniKorisnikNovoStanje = $prijavljeniKorisnikStanje - $cijenaDijela;
        $prodavacDijelaNovoStanje = $prodavacDijelaStanje + $cijenaDijela;
        
        $sql5 = "UPDATE stanje_racuna SET stanje = {$prijavljeniKorisnikNovoStanje} WHERE korisnik = {$prijavljeniKorisnikId}";
        $rezultat5 = $veza->updateDB($sql5);
        
        $sql6 = "UPDATE stanje_racuna SET stanje = {$prodavacDijelaNovoStanje} WHERE korisnik = {$prodavacDijelaId}";
        $rezultat6 = $veza->updateDB($sql6);
        
        $sql7 = "UPDATE dio SET raspolozivo = 0 WHERE dio_id = {$idDijela}";
        $rezultat7 = $veza->updateDB($sql7);
        
        $datumKupnje = date(Y.m.d);
        $sql8 = "INSERT INTO kupnja_dijela (datum_kupnje, dio, korisnik) "
                . "VALUES ('$datumKupnje', $idDijela, $prijavljeniKorisnikId)";
        $rezultat8 = $veza->updateDB($sql8);
    } else {
        $poruka = "Nemate dovoljno novaca na računu!";
    }
    $veza->zatvoriDB();
}
?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Kupnja dijelova</title>
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
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 3) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
                            echo "<a href=\"$putanja/stanjeRacuna.php\">Stanje računa</a>";
                            echo "<a href=\"$putanja/dijelovi.php\">Dijelovi</a>";
                            echo "<a href=\"$putanja/prihvaceneProcjene.php\">Prihvaćene procjene</a>";
                            echo "<a href=\"$putanja/pregledZahtjeva.php\">Pregled zahtjeva</a>";
                        }

                        if (isset($_SESSION["uloga"]) && $_SESSION["uloga"] == 4) {
                            echo "<a href=\"$putanja/zahtjevi.php\">Zahtjevi za procjenu</a>";
                            echo "<a href=\"$putanja/prodajaVozila.php\">Prodaja vozila</a>";
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
                KUPNJA DIJELOVA
            </h1>
            
            <?php
            echo "<p style = 'color:darkblue; font-weight:bolder; font-size:25px; text-align:center;'>$poruka</p>";
            ?>

        </header>

        <section>
            
            <form novalidate class="forma" id="formKupnjaDijelova" method="post" name="formKupnjaDijelova" action="kupnjaDijelova.php">
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
                <input type="submit" class="submit" form="formKupnjaDijelova" value="Odaberi" name="odaberiGumb">
            </div>
            
            <form name="pregledDijelova" action="" method="post">
                <table class="display" id="tablica">
                    <caption id="naslovTablice">PREGLED DIJELOVA</caption>
                    <thead>
                        <tr>
                            <th>Marka</th>
                            <th>ID dijela</th>
                            <th>Naziv</th>
                            <th>Proizvođač</th>
                            <th>Masa u KG</th>
                            <th>Opis</th>
                            <th>Cijena</th>
                            <th>Datum kreiranja</th>
                            <th>Raspoloživo</th>
                            <th>Prodavač</th>
                            <th>Kupi</th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php
                        $veza = new Baza();
                        $veza->spojiDB();

                        if (isset($_POST['odaberiGumb'])) {
                            $markaVozilaId = $_POST['markaVozila'];
                            
                            $sql = "SELECT marka_vozila.naziv, dio.dio_id, dio.naziv, dio.proizvodac, dio.masa, dio.opis, "
                                    . "dio.cijena, dio.datum_kreiranja, dio.raspolozivo, concat(korisnik.ime, ' ', korisnik.prezime) "
                                    . "FROM korisnik INNER JOIN dio ON korisnik.korisnik_id = dio.korisnik "
                                    . "INNER JOIN vozilo ON dio.vozilo = vozilo.vozilo_id "
                                    . "INNER JOIN marka_vozila ON vozilo.marka_vozila = marka_vozila.marka_vozila_id "
                                    . "AND marka_vozila.marka_vozila_id = $markaVozilaId ORDER BY dio.dio_id;";
                        } else {
                            $sql = "SELECT marka_vozila.naziv, dio.dio_id, dio.naziv, dio.proizvodac, dio.masa, dio.opis, "
                                    . "dio.cijena, dio.datum_kreiranja, dio.raspolozivo, concat(korisnik.ime, ' ', korisnik.prezime) "
                                    . "FROM korisnik INNER JOIN dio ON korisnik.korisnik_id = dio.korisnik "
                                    . "INNER JOIN vozilo ON dio.vozilo = vozilo.vozilo_id "
                                    . "INNER JOIN marka_vozila ON vozilo.marka_vozila = marka_vozila.marka_vozila_id ORDER BY dio.dio_id;";
                        }

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
                                <td><?php echo $red[7] ?></td>
                                <?php
                                if ($red[8] == 1) {
                                    ?>
                                    <td>Raspoloživo</td>
                                    <?php
                                } else {
                                    ?>
                                    <td>Nije raspoloživo</td>
                                    <?php
                                }
                                ?>
                                <td><?php echo $red[9] ?></td>
                                <?php
                                if ($red[8] == 1) {
                                ?>
                                    <td><input id="id" name="id" type="submit" value="<?php echo $red[1] ?>"> KUPI</td>
                                <?php
                                } else {
                                ?>
                                    <td>NIJE DOSTUPNO</td>
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