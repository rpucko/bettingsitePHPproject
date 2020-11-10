<?php
class bazaPodataka {

    const server = "localhost";
    const korisnik = "iwa_2018";
    const lozinka = "foi2018";
    const baza = "iwa_2018_vz_projekt";

    private $veza = null;

    function spajanje() {
        $this->veza = new mysqli(self::server, self::korisnik, self::lozinka, self::baza);
        if ($this->veza->connect_errno) {
            echo "Neuspješno spajanje na bazu: " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
        }
        $this->veza->set_charset("utf8");
        if ($this->veza->connect_errno) {
            echo "Neuspješno postavljanje znakova za bazu: " . $this->veza->connect_errno . ", " .
            $this->veza->connect_error;
        }
        return $this->veza;
    }

    function zatvaranje() {
        $this->veza->close();
    }

    function upit($upit) {
        $rezultat = $this->veza->query($upit);
        if (!$rezultat) {
            $rezultat = null;
        }
        return $rezultat;
    }
}
?>