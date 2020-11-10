<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (!empty($_SESSION)) {
    $korisnikId = $_GET['korisnikId'];
    $sql = "SELECT * FROM korisnik WHERE korisnik_id= '$korisnikId'";
    $rezultat = $baza->upit($sql);
    $korisnik = $rezultat->fetch_assoc();


    $sql = "SELECT * FROM tip_korisnika";
    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $tipoviKorisnika [] = $redak;
    }

    if (empty($_POST["korisnickoIme"]) || empty($_POST["ime"]) || empty($_POST["prezime"]) || empty($_POST["email"]) || empty($_POST["lozinka"]) || (!is_numeric($_POST["tipKorisnika"]))) {
        $zabranaAzuriranja = true;
    } else {
        $zabranaAzuriranja = false;
        $ime = $_POST["ime"];
        $prezime = $_POST["prezime"];
        $korisnickoIme = $_POST["korisnickoIme"];
        $email = $_POST["email"];
        $lozinka = $_POST["lozinka"];
        $tipKorisnika = $_POST["tipKorisnika"];

        $sql = "UPDATE korisnik set tip_korisnika_id ='$tipKorisnika', korisnicko_ime='$korisnickoIme', lozinka='$lozinka', ime='$ime', prezime='$prezime', email='$email'
            WHERE korisnik_id ='$korisnikId'";
        $baza->upit($sql);
        header("Location: korisnici.php");
    }
}
$baza->zatvaranje();
?>
<html lang="en">
<head>
    <title>Korisnici</title>
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
        <a href="lige.php">Lige</a>
        <a href="o_autoru.html">O Autoru</a>
        <?php
        if (empty($_SESSION)) {
            echo '<a href = "prijava.php" class = "right">Prijava</a>';
        } else {
            if ($_SESSION["tipKorisnika"] == '1' || $_SESSION["tipKorisnika"] == '0') {
                echo '<a href="statistikaListica.php">Statistika listića</a>';
            }
            if ($_SESSION["tipKorisnika"] == '0') {
                echo '<a href="korisnici.php" class="active">Korisnici</a>';
                echo '<a href="momcadi.php">Momčadi</a>';
            }
            echo '<a href = "odjava.php" class = "right">Odjava</a>';
        }
        ?>
    </div>

    <div class="row">
        <div class="main">
            <h2>Ažuriranje korisnika</h2>
            <div class="greska"> <?php if (!empty($_POST) && $zabranaAzuriranja) echo "Niste unijeli sve potrebne elemente!"; ?></div>
            <br>
            <form method="post" id="noviKorisnik" action="azurirajKorisnika.php?korisnikId=<?php echo $korisnikId; ?>" name="noviKorisnik">
                <label for="korisnickoIme">Korisnicko ime: </label>
                <input type="text" id="korisnickoIme" name="korisnickoIme" placeholder="korisnicko ime" value="<?php echo $korisnik["korisnicko_ime"]; ?>">
                <label for="ime">Ime: </label>
                <input type="text" id="ime" name="ime" placeholder="ime korisnika" value="<?php echo $korisnik["ime"]; ?>">
                <label for="prezime">Prezime: </label>
                <input type="text" id="prezime" name="prezime" placeholder="prezime korisnika" value="<?php echo $korisnik["prezime"]; ?>">
                <label for="email">Email: </label>
                <input type="email" id="email" name="email" placeholder="email korisnika" value="<?php echo $korisnik["email"]; ?>">
                <label for="lozinka">Lozinka: </label>
                <input type="password" id="lozinka" name="lozinka" placeholder="lozinka" value="<?php echo $korisnik["lozinka"]; ?>">
                <label for="tipKorisnika">Tip korisnika: </label>
                <select id="tipKorisnika" name="tipKorisnika">
                    <option value="">----</option>
                    <?php
                    foreach ($tipoviKorisnika as $tip) {
                        if ($tip["tip_korisnika_id"] == $korisnik["tip_korisnika_id"]) {
                            echo "<option value='$tip[tip_korisnika_id]' selected>$tip[naziv]</option>";
                        } else {
                            echo "<option value='$tip[tip_korisnika_id]'>$tip[naziv]</option>";
                        }
                    }
                    ?>
                </select>
                <input type="submit" id="dodaj" value="Ažuriraj korisnika"/>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
