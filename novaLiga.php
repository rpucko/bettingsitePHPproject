<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (!empty($_SESSION)) {
    $sql = "SELECT * FROM korisnik WHERE tip_korisnika_id=1;";
    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $moderatori [] = $redak;
    }

    if (empty($_POST["naziv"]) || empty($_POST["slika"]) || (empty($_POST["moderator"]))) {
        $zabranaKreiranja = true;
    } else {
        $zabranaKreiranja = false;
        $naziv = $_POST["naziv"];
        $slikaURL = $_POST["slika"];
        $moderator = $_POST["moderator"];
        $videoURL = $_POST["video"];
        $opis = $_POST["opis"];

        $sql = "INSERT INTO liga VALUES (DEFAULT, '$moderator','$naziv','$slikaURL', '$videoURL', '$opis');";
        $baza->upit($sql);
        header("Location: lige.php");
    }
}
$baza->zatvaranje();
?>
<html lang="en">
<head>
    <title>Nova liga</title>
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
        <div class="main">
            <h2>Kreiranje lige</h2>
            <div class="greska"> <?php if (!empty($_POST) && $zabranaKreiranja) echo "Niste unijeli sve potrebne elemente!"; ?></div>
            <br>
            <form method="post" id="novaLiga" action="novaLiga.php" name="novaLiga">
                <label for="naziv">Naziv: </label>
                <input type="text" id="naziv" name="naziv" placeholder="naziv lige">
                <label for="ime">Slika: </label>
                <input type="text" id="slika" name="slika" placeholder="URL slike">
                <label for="moderator">Moderator: </label>
                <select id="moderator" name="moderator">
                    <option value="">----</option>
                    <?php
                    foreach ($moderatori as $moderator) {
                        echo "<option value='$moderator[korisnik_id]'>$moderator[korisnicko_ime] - $moderator[ime] $moderator[prezime]</option>";
                    }
                    ?>
                </select>
                <label for="prezime">Video: </label>
                <input type="text" id="video" name="video" placeholder="URL videa">
                <label for="opis">Opis lige:</label><br>
                <textarea rows="3" id="opisLige" name="opis" placeholder="Ovdje unesite opis lige."></textarea><br>
                <input type="submit" id="dodaj" value="Kreiraj ligu"/>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
