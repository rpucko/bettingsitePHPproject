<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();
if (!empty($_GET)) {
    $utakmicaId = $_GET['utakmicaId'];
    $ligaId = $_GET['ligaId'];

    $sql = "SELECT u.utakmica_id as id, u.datum_vrijeme_pocetka, u.datum_vrijeme_zavrsetka, m1.momcad_id as m1id, m1.naziv as m1, m2.momcad_id as m2id, m2.naziv as m2, u.rezultat_1, u.rezultat_2, u.opis
            FROM utakmica u, momcad m1, momcad m2 
            WHERE u.momcad_1 = m1.momcad_id AND u.momcad_2 = m2.momcad_id
            AND u.utakmica_id = '$utakmicaId'";

    $rezutat = $baza->upit($sql);
    $utakmica = $rezutat->fetch_assoc();
    $utakmica['datum_vrijeme_pocetka'] = date("d.m.Y H:i:s", strtotime($utakmica["datum_vrijeme_pocetka"]));
    $utakmica['vrijeme_pocetka'] = date("H:i:s", strtotime($utakmica["datum_vrijeme_pocetka"]));
    $utakmica['datum_pocetka'] = date("d.m.Y", strtotime($utakmica["datum_vrijeme_pocetka"]));

    $sql = "SELECT * FROM momcad WHERE liga_id='$ligaId'";
    $rezutat = $baza->upit($sql);
    while ($redak = $rezutat->fetch_assoc()) {
        $momcadi[] = $redak;
    }

    if (!empty($_POST)) {
        if (empty($_POST['momcad1']) || empty($_POST['momcad2']) || empty($_POST['datumPocetka']) || empty($_POST['vrijemePocetka']) || empty($_POST['opisUtakmice'])) {
            $porukaGreske = 'Niste unijeli sve potrebne elemente!';
        } else if ($_POST['momcad1'] == $_POST['momcad2']) {
            $porukaGreske = 'Utakmica ne moze biti izmedju istih momčadi!';
        } else if ($_POST['rezultatM1'] == '-1' || $_POST['rezultatM2'] == '-1') {
            $porukaGreske = 'Rezultat mora biti definiran za obje momčadi!';
        } else {
            $momcad1 = $_POST['momcad1'];
            $momcad2 = $_POST['momcad2'];
            $datumPocetka = date("Y-m-d", strtotime($_POST['datumPocetka']));
            $vrijemePocetka = $_POST['vrijemePocetka'];
            $rezultatM1 = $_POST['rezultatM1'];
            $rezultatM2 = $_POST['rezultatM2'];
            $opisUtakmice = $_POST['opisUtakmice'];

            $sqL2 = "SELECT '$datumPocetka $vrijemePocetka' + INTERVAL 90 MINUTE as datum_vrijeme_zavrsetka";
            $rezutat = $baza->upit($sqL2);
            $zavrsetak = $rezutat->fetch_assoc();

            $sql = "UPDATE utakmica SET momcad_1 = '$momcad1', momcad_2 = '$momcad2', datum_vrijeme_pocetka = '$datumPocetka $vrijemePocetka', rezultat_1 = '$rezultatM1', rezultat_2='$rezultatM2', opis='$opisUtakmice', 
                    datum_vrijeme_zavrsetka='$zavrsetak[datum_vrijeme_zavrsetka]'
                    WHERE utakmica_id = '$utakmicaId';";
            $baza->upit($sql);

            $sql = "UPDATE listic SET listic.status = IF((SELECT
                        (CASE WHEN rezultat_1 > rezultat_2 
                        THEN 1 WHEN rezultat_1 < rezultat_2 
                        THEN 2 ELSE 0 END) AS konacni_rezultat 
                        FROM utakmica WHERE utakmica_id='$utakmicaId') = listic.ocekivani_rezultat, 'D', 'N') 
                        WHERE utakmica_id = '$utakmicaId'";
            $baza->upit($sql);

            header("Location: lige.php?id=$ligaId");
        }
    }
}
$baza->zatvaranje();
?>


<html lang="en">
<head>
    <title>Početna</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" media="screen" type="text/css" href="css/robpucko.css"/>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>Kladionica</h1>
        <p>Projektni zadatak iz kolegija Izradnja WEB aplikacija.</p>
    </div>

    <div class="navbar">
        <a href="index.php">Početna</a>
        <a href="lige.php" class="active">Lige</a>
        <a href="o_autoru.html">O Autoru</a>
        <?php
        if (empty($_SESSION)) {
            echo '<a href = "prijava.php" class = "right">Prijava</a>';
        } else {
            if ($_SESSION["tipKorisnika"] == '1' || $_SESSION["tipKorisnika"] == '0') {
                echo '<a href="statistikaListica.php">Statistika listića</a>';
            }
            if ($_SESSION["tipKorisnika"] == '0') {
                echo '<a href="korisnici.php">Korisnici</a>';
                echo '<a href="momcadi.php">Momčadi</a>';
            }
            echo '<a href = "odjava.php" class = "right">Odjava</a>';
        }
        ?>
    </div>

    <div class="row">
        <div clas='main'>
            <h2>Ažuriranje utakmice</h2>
            <div class="greska">
                <?php if (!empty($_POST) && isset($porukaGreske)) {
                    echo $porukaGreske;
                } ?>
            </div>
            </br>
            <form method="POST" id="novaUtakmica" action="azurirajUtakmicu.php?utakmicaId=<?php echo $utakmicaId . "&ligaId=$ligaId"; ?>" name="novaAktivnost">
                <label for="momcad1">1. Momčad: </label>
                <select name="momcad1" id="momcad1">
                    <?php foreach ($momcadi as $momcad) {
                        if ($utakmica['m1id'] == $momcad['momcad_id']) {
                            echo "<option value ='$momcad[momcad_id]' selected>$momcad[naziv]</option>";
                        } else {
                            echo "<option value ='$momcad[momcad_id]'>$momcad[naziv]</option>";
                        }
                    } ?>
                </select>
                <label for="momcad2">2. Momčad: </label>
                <select name="momcad2" id="momcad2">
                    <label for="rezultatM1">Rezultat 1.momčadi: </label>
                    <?php foreach ($momcadi as $momcad) {
                        if ($utakmica['m2id'] == $momcad['momcad_id']) {
                            echo "<option value ='$momcad[momcad_id]' selected>$momcad[naziv]</option>";
                        } else {
                            echo "<option value ='$momcad[momcad_id]'>$momcad[naziv]</option>";
                        }
                    } ?>
                </select>
                <label for="datumPocetka">Datum početka:</label><br>
                <input type="text" id="datumPocetka" name="datumPocetka" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" placeholder="dd.mm.gggg" value="<?php echo $utakmica['datum_pocetka']; ?>">
                <label for="vrijemePocetka">Vrijeme početka: </label><br>
                <input type="text" id="vrijemePocetka" name="vrijemePocetka" pattern="(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}" placeholder="hh:mm:ss" value="<?php echo $utakmica['vrijeme_pocetka']; ?>">
                <label for="rezultatM1">Rezultat 1.momčadi: </label>
                <input type="number" id="rezultatM1" name="rezultatM1" <?php if ($utakmica["rezultat_1"] != -1) {
                    echo "readonly min='0'";
                } ?> value="<?php echo $utakmica["rezultat_1"]; ?>">
                <label for="rezultatM2">Rezultat 2.momčadi: </label>
                <input type="number" id="rezultatM2" name="rezultatM2" <?php if ($utakmica["rezultat_2"] != -1) {
                    echo "readonly min='0'";
                } ?> value="<?php echo $utakmica["rezultat_2"]; ?>">
                <label for="opisUtakmice">Opis utakmice:</label><br>
                <textarea rows="3" id="opisUtakmice" name="opisUtakmice" placeholder="Ovdje unesite opis utakmice"><?php echo $utakmica['opis']; ?></textarea><br>
                <input type="submit" id="dodaj" value="Ažuriraj"/>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
