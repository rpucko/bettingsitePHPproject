<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (!empty($_GET)) {
    $utakmicaId = $_GET["utakmicaId"];
    $ligaId = $_GET["ligaId"];
    $sql = "SELECT  u.utakmica_id, u.datum_vrijeme_pocetka, u.datum_vrijeme_zavrsetka, m1.naziv as m1, m2.naziv as m2, u.rezultat_1, u.rezultat_2, u.opis "
        . "FROM momcad m1, momcad m2, utakmica u "
        . "WHERE m1.momcad_id = u.momcad_1 AND m2.momcad_id = u.momcad_2 "
        . "AND utakmica_id= '$utakmicaId'";

    $rezultat = $baza->upit($sql);
    $utakmica = $rezultat->fetch_assoc();
} else if (!empty($_POST)) {
    $utakmicaId = $_POST["utakmicaId"];
    $procjena = $_POST["procjena"];
    $korisnikId = $_SESSION["id"];

    $sql = "INSERT  INTO listic (korisnik_id, utakmica_id, ocekivani_rezultat, status) VALUES ('$korisnikId', '$utakmicaId', '$procjena', 'P')";
    $baza->upit($sql);
}


$baza->zatvaranje();
?>
<html lang="en">
<head>
    <title>Kreiranje listića</title>
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
                echo '<a href="korisnici.php">Korisnici</a>';
                echo '<a href="momcadi.php">Momčadi</a>';
            }
            echo '<a href = "odjava.php" class = "right">Odjava</a>';
        }
        ?>
    </div>

    <div class="row">
        <div class="main">
            <h2>Kreiranje listića</h2>
            <?php if (!empty($_GET["utakmicaId"])) { ?>
                <table>
                    <thead>
                    <tr>
                        <th>Momčad 1</th>
                        <th>Momčad 2</th>
                        <th>Vrijeme početka</th>
                        <th>Vrijeme završetka</th>
                        <th>R1</th>
                        <th>R2</th>
                        <th>Opis</th>
                        <th>Procjena</th>
                        <th>Listić</th>
                    </tr>
                    </thead>
                    <tbody>
                    <form id="kreirajListic" method="POST" action="kreirajListic.php">
                        <tr>
                            <td hidden><input name="utakmicaId" value="<?php echo $utakmicaId ?>"</td>
                            <td hidden><input name="ligaId" value="<?php echo$ligaId ?>"</td>
                            <td><?php echo $utakmica['m1'] ?></td>
                            <td><?php echo $utakmica['m2'] ?></td>
                            <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_pocetka'])) ?></td>
                            <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_zavrsetka'])) ?></td>
                            <td><?php echo $utakmica['rezultat_1'] ?></td>
                            <td><?php echo$utakmica['rezultat_2'] ?></td>
                            <td><?php echo $utakmica['opis'] ?></td>
                            <td>
                                <select name="procjena" id="procjena" name="procjena">
                                    <option value="1">Pobjeda 1.momčadi</option>
                                    <option value="2">Pobjeda 2.momčadi</option>
                                    <option value="0">Nerješeno</option>
                                </select>
                            </td>
                            <td><input type="submit" id="kreiraj" value="Kreiraj"/></td>
                        </tr>
                    </form>
                    </tbody>
                </table>
            <?php } else if (!empty($_POST)) { ?>
                Uspješno ste kreirali listić za odabranu utakmicu. Odaberite jednu od mogućih opcija:
                <a href='lige.php?id=<?php echo $_POST['ligaId'] ?>'><input type="button" value="Stranica lige"/></a>
                <a href='index.php'><input type="button" value="Pregled listića"/></a>
            <?php } ?>

        </div>
    </div>
    <div class="footer">
        <h2></h2>
    </div>
</div>
</div>
</body>
</html>