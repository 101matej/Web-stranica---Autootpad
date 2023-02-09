<?php
error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ERROR | E_PARSE);

$direktorij = getcwd();
$putanja = dirname($_SERVER['REQUEST_URI']);

include 'zaglavlje.php';

?>

<!DOCTYPE html>
<html lang="hr">
    <head>
        <title>Galerija slika</title>
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
                            echo "<a href=\"$putanja/kategorijeDijelova.php\">Kategorije dijelova</a>";
                        }
                        
                        echo "<a href=\"$putanja/rangLista.php\">Rang lista</a>";
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
                GALERIJA SLIKA
            </h1>

        </header>

        <section>

            <form novalidate class="forma" id="formGalerija" method="post" name="formGalerija" action="galerija.php">
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
                <input type="submit" class="submit" form="formGalerija" value="Filtriraj" name="filtrirajGumb">
            </div>
            
            <div style="text-align: center">
                <input type="submit" class="submit3" form="formGalerija" value="Sortiraj po marki vozila uzlazno" name="sortirajPoMarkiUzlaznoGumb">
            </div>
            
            <div style="text-align: center">
                <input type="submit" class="submit3" form="formGalerija" value="Sortiraj po marki vozila silazno" name="sortirajPoMarkiSilaznoGumb">
            </div>
            
            <div style="text-align: center">
                <input type="submit" class="submit3" form="formGalerija" value="Sortiraj po cijeni uzlazno" name="sortirajPoCijeniUzlaznoGumb">
            </div>
            
            <div style="text-align: center">
                <input type="submit" class="submit3" form="formGalerija" value="Sortiraj po cijeni silazno" name="sortirajPoCijeniSilaznoGumb">
            </div>
            
            <div style="text-align: center">
                <input type="submit" class="submit3" form="formGalerija" value="Osvježi prikaz" name="osvjeziGumb">
            </div>
            
            <table class="display" id="tablica">
                <caption id="naslovTablice">GALERIJA SLIKA</caption>
                <thead>
                    <tr>
                        <th>Marka vozila</th>
                        <th>Tip</th>
                        <th>Godina</th>
                        <th>Masa</th>
                        <th>Snaga motora</th>
                        <th>Vrsta goriva</th>
                        <th>Slika</th>
                        <th>Cijena</th>
                    </tr>
                </thead>
                <tbody> 

                    <?php
                    $veza = new Baza();
                    $veza->spojiDB();
                    
                    if(isset($_POST['filtrirajGumb'])){
                        $markaVozilaId = $_POST['markaVozila'];
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                                . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                                . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                                . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id AND marka_vozila.marka_vozila_id = $markaVozilaId;";
                    } else if($_POST['sortirajPoMarkiUzlaznoGumb']){
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                                . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                                . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                                . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id ORDER BY marka_vozila.naziv ASC, zahtjev.tip ASC;";
                    } else if($_POST['sortirajPoMarkiSilaznoGumb']){
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                                . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                                . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                                . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id ORDER BY marka_vozila.naziv DESC, zahtjev.tip DESC;";
                    } else if($_POST['sortirajPoCijeniUzlaznoGumb']){
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                                . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                                . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                                . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id ORDER BY procjena.cijena ASC;";
                    } else if($_POST['sortirajPoCijeniSilaznoGumb']){
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                                . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                                . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                                . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                                . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id ORDER BY procjena.cijena DESC;";
                    } else if($_POST['osvjeziGumb']){
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                            . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                            . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                            . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                            . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id;";
                    }else{
                        $sql = "SELECT marka_vozila.naziv, zahtjev.tip, zahtjev.godina, zahtjev.masa_vozila, "
                            . "zahtjev.snaga_motora, zahtjev.vrsta_goriva, zahtjev.slika, procjena.cijena "
                            . "FROM kupnja_vozila, procjena, zahtjev, marka_vozila "
                            . "WHERE kupnja_vozila.procjena = procjena.procjena_id AND procjena.zahtjev = zahtjev.zahtjev_id "
                            . "AND zahtjev.marka_vozila = marka_vozila.marka_vozila_id;";
                    }
                    
                    $dnevnik = new Dnevnik();
                    $dnevnik->radSBazom($_SESSION['korisnik'], $sql);

                    $rezultat = $veza->selectDB($sql);
                    
                    while ($red = mysqli_fetch_array($rezultat)) {
                        ?>
                        <tr>
                            <td><?php echo $red['naziv'] ?></td>
                            <td><?php echo $red['tip'] ?></td>
                            <td><?php echo $red['godina'] ?></td>
                            <td><?php echo $red['masa_vozila'] ?></td>
                            <td><?php echo $red['snaga_motora'] ?></td>
                            <td><?php echo $red['vrsta_goriva'] ?></td>
                            <td><img src="materijali/<?php echo $red['slika'] ?>" width="160" height="160"></td>
                            <td><?php echo $red['cijena'] ?></td>
                        </tr>

                        <?php
                    }
                    $veza->zatvoriDB();
                    ?>
                </tbody>
            </table>

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