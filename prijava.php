<!DOCTYPE html>
<?php
include ("baza/bazaPodataka.php");

if (!empty($_POST)) {
    $korIme = $_POST["korIme"];
    $lozinka = $_POST["lozinka"];
    $baza = new bazaPodataka();
    $baza->spajanje();
    $sql = "SELECT * FROM korisnik WHERE korisnicko_ime = '$korIme' AND lozinka = '$lozinka'";
    $rezultat = $baza->upit($sql);
    $korisnik = $rezultat->fetch_assoc();
    if (!empty($korisnik)) {
        session_start();
        $_SESSION["id"] = $korisnik["korisnik_id"];
        $_SESSION["korIme"] = $korisnik["korisnicko_ime"];
        $_SESSION["tipKorisnika"] = $korisnik["tip_korisnika_id"];
        header("Location: index.php");
    }
    $baza->zatvaranje();
}
?>
<html>
    <head>
        <title>Prijava</title>
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

            <div class = "navbar">
                <a href = "index.php">Početna</a>
                <a href = "lige.php">Lige</a>
                <a href = "o_autoru.html">O Autoru</a>
                <?php
                if (empty($_SESSION)) {
                    echo '<a href = "prijava.php" class = "right">Prijava</a>';
                } else {
                    echo '<a href = "odjava.php" class = "right">Odjava</a>';
                }
                ?>
            </div>

            <div class="row">
                <div class="main">
                    <h2>PRIJAVA</h2>
                    <form id="prijava" method="POST" name="prijava" action="prijava.php">
                    <label for="korIme"><b>Korisničko ime:</b></label>
                    <input type="text" id="korIme" name ="korIme" placeholder="korisničko ime">
                    <label for="lozinka"><b>Lozinka:</b></label>
                    <input type="password" id="lozinka" name="lozinka" placeholder="lozinka">
                    <button type="submit">Prijavi se</button>
                    </form>
                </div>
            </div>

            <div class="footer">
                <h2></h2>
            </div>
        </div>
    </body>
</html>
