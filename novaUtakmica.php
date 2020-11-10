<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();
if (!empty($_GET)) {
    $ligaId = $_GET['ligaId'];

    $sql = "SELECT * FROM momcad WHERE liga_id='$ligaId'";
    $rezutat = $baza->upit($sql);
    while ($redak = $rezutat->fetch_assoc()) {
        $momcadiLige[] = $redak;
    }

    if (!empty($_POST)) {
        if (empty($_POST['momcad1']) || empty($_POST['momcad2']) || empty($_POST['datumPocetka']) || empty($_POST['vrijemePocetka']) || empty($_POST['opisUtakmice'])) {
            $porukaGreske = 'Niste unijeli sve potrebne elemente!';
        } else if ($_POST['momcad1'] == $_POST['momcad2']) {
            $porukaGreske = 'Utakmica ne moze biti izmedju istih momčadi!';
        } else {
            $momcad1 = $_POST['momcad1'];
            $momcad2 = $_POST['momcad2'];
            $datumPocetka = date("Y-m-d", strtotime($_POST['datumPocetka']));
            $vrijemePocetka = $_POST['vrijemePocetka'];
            $rezultatM1 = $_POST['rezultatM1'];
            $rezultatM2 = $_POST['rezultatM2'];
            $opisUtakmice = $_POST['opisUtakmice'];

            $sql = "SELECT '$datumPocetka $vrijemePocetka' + INTERVAL 90 MINUTE as datum_vrijeme_zavrsetka";
            $rezutat = $baza->upit($sql);
            $zavrsetak = $rezutat->fetch_assoc();

            $sql = "INSERT INTO utakmica VALUES (DEFAULT, '$momcad1','$momcad2','$datumPocetka $vrijemePocetka', '$zavrsetak[datum_vrijeme_zavrsetka]', '$rezultatM1','$rezultatM2', '$opisUtakmice')";
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
            <h2>Kreiranje nove utakmice</h2>
            <div class="greska">
                <?php if (!empty($_POST) && isset($porukaGreske)) {
                    echo $porukaGreske;
                } ?>
            </div>
            </br>
            <form method="POST" id="novaUtakmica" action="novaUtakmica.php?ligaId=<?php echo $ligaId; ?>" name="novaUtakmica">
                <label for="momcad1">1. Momčad: </label>
                <select name="momcad1" id="momcad1">
                    <option value=""> --------</option>
                    <?php foreach ($momcadiLige as $momcad) {
                        echo "<option value=$momcad[momcad_id]>$momcad[naziv]</option>";
                    } ?>
                </select>
                <label for="momcad2">2. Momčad: </label>
                <select name="momcad2" id="momcad2">
                    <option value=""> --------</option>
                    <?php foreach ($momcadiLige as $momcad) {
                        echo "<option value=$momcad[momcad_id]>$momcad[naziv]</option>";
                    } ?>
                </select>
                <label for="datumPocetka">Datum početka:</label><br>
                <input type="text" id="datumPocetka" name="datumPocetka" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}" placeholder="dd.mm.gggg">
                <label for="vrijemePocetka">Vrijeme početka: </label><br>
                <input type="text" id="vrijemePocetka" name="vrijemePocetka" pattern="(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}" placeholder="hh:mm:ss">
                <label for="rezultatM1">Rezultat 1.momčadi: </label>
                <input type="number" id="rezultatM1" name="rezultatM1" readonly min="-1" value="-1">
                <label for="rezultatM2">Rezultat 2.momčadi: </label>
                <input type="number" id="rezultatM2" name="rezultatM2" readonly min="-1" value="-1">
                <label for="opisUtakmice">Opis utakmice:</label><br>
                <textarea rows="3" id="opisUtakmice" name="opisUtakmice" placeholder="Ovdje unesite opis utakmice"></textarea><br>
                <input type="submit" id="dodaj" value="Kreiraj utakmicu"/>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
