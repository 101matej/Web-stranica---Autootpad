<?php

require "$direktorij/baza.class.php";
require "$direktorij/sesija.class.php";
require "$direktorij/dnevnik.class.php";

Sesija::kreirajSesiju();


if(isset($_GET["obrisi"])){
    unset($_COOKIE['autenticiran']);
    setcookie("autenticiran", "", time()-3600, "/");
    
    $dnevnik = new Dnevnik();
    $dnevnik->odjavaKorisnika($_SESSION['korisnik']);
    
    Sesija::obrisiSesiju();
}
?>