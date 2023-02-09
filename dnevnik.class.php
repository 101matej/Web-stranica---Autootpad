<?php

class Dnevnik {
    public function prijavaKorisnika($korisnickoIme) {
        $veza = new Baza();
        $veza->spojiDB();
        
        $radnja = "Prijava korisnika";
        
        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korisnickoIme'";
        $rezultat2 = $veza->selectDB($sql2);
        $red = mysqli_fetch_array($rezultat2);
        $korisnikId = $red['korisnik_id'];
        
        $sql = "INSERT INTO dnevnik_rada (korisnik, tip_radnje, radnja, datum_vrijeme) "
                . "VALUES ($korisnikId, 1, '$radnja', now())";
        
        $rezultat = $veza->updateDB($sql);
        
        $veza->zatvoriDB();
    }
    
    public function odjavaKorisnika($korime) {
        $veza = new Baza();
        $veza->spojiDB();
        
        $radnja = "Odjava korisnika";
        
        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
        $rezultat2 = $veza->selectDB($sql2);
        $red = mysqli_fetch_array($rezultat2);
        $korisnikId = $red['korisnik_id'];
        
        $sql = "INSERT INTO dnevnik_rada (korisnik, tip_radnje, radnja, datum_vrijeme) "
                . "VALUES ($korisnikId, 2, '$radnja', now())";
        
        $rezultat = $veza->updateDB($sql);
        
        $veza->zatvoriDB();
    }
    
    public function blokiranje($korime) {
        $veza = new Baza();
        $veza->spojiDB();
        
        $radnja = "Blokiranje korisnika";
        
        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
        $rezultat2 = $veza->selectDB($sql2);
        $red = mysqli_fetch_array($rezultat2);
        $korisnikId = $red['korisnik_id'];
        
        $sql = "INSERT INTO dnevnik_rada (korisnik, tip_radnje, radnja, datum_vrijeme) "
                . "VALUES ($korisnikId, 4, '$radnja', now())";
        
        $rezultat = $veza->updateDB($sql);
        
        $veza->zatvoriDB();
    }
    
    public function odblokiranje($korime) {
        $veza = new Baza();
        $veza->spojiDB();
        
        $radnja = "Odblokiranje korisnika";
        
        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
        $rezultat2 = $veza->selectDB($sql2);
        $red = mysqli_fetch_array($rezultat2);
        $korisnikId = $red['korisnik_id'];
        
        $sql = "INSERT INTO dnevnik_rada (korisnik, tip_radnje, radnja, datum_vrijeme) "
                . "VALUES ($korisnikId, 4, '$radnja', now())";
        
        $rezultat = $veza->updateDB($sql);
        
        $veza->zatvoriDB();
    }
    
    public function radSBazom($korime, $upit) {
        $veza = new Baza();
        $veza->spojiDB();
        
        $sql2 = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korime'";
        $rezultat2 = $veza->selectDB($sql2);
        $red = mysqli_fetch_array($rezultat2);
        $korisnikId = $red['korisnik_id'];
        
        $sql = "INSERT INTO dnevnik_rada (korisnik, tip_radnje, upit, datum_vrijeme) "
                . "VALUES ($korisnikId, 3, '$upit', now())";
        
        $rezultat = $veza->updateDB($sql);
        
        $veza->zatvoriDB();
    }
}
?>