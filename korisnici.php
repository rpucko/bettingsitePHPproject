<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (!empty($_SESSION)) {
    $sql = "SELECT * FROM korisnik k, tip_korisnika t WHERE k.tip_korisnika_id = t.tip_korisnika_id";

    $rezutat = $baza->upit($sql);
    while ($redak = $rezutat->fetch_assoc()) {
        $korisnici[] = $redak;
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
            <h2>Popis korisnika</h2>
            <a href="noviKorisnik.php"><input type="button" id="dodaj" value="Novi korisnik"/></a>
            <table>
                <thead>
                <tr>
                    <th colspan="2">Korisnicko ime</th>
                    <th>Ime</th>
                    <th>Prezime</th>
                    <th>Email</th>
                    <th>Lozinka</th>
                    <th>Tip korisnika</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($korisnici)) {
                    foreach ($korisnici as $korisnik) { ?>
                        <tr>
                            <td><a href='azurirajKorisnika.php?korisnikId=<?php echo $korisnik['korisnik_id'] ?>'><img src='slike/azurirajKorisnika.png' alt='edit' height=25 width=25/></a>
                            <td><?php echo $korisnik['korisnicko_ime'] ?></td>
                            <td><?php echo $korisnik['ime'] ?></td>
                            <td><?php echo $korisnik['prezime'] ?></td>
                            <td><?php echo $korisnik['email'] ?></td>
                            <td><?php echo $korisnik['lozinka'] ?></td>
                            <td><?php echo $korisnik['naziv'] ?></td>
                        </tr>
                    <?php }
                } else { ?>
                    <tr>
                        <td>Nije pronađen ni jedan korisnik!</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
