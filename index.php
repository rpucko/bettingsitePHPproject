<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();
if (!empty($_SESSION)) {
    $sql = "SELECT l.listic_id, m1.naziv as m1, m2.naziv as m2, u.opis, l.ocekivani_rezultat, l.status 
        FROM listic l, utakmica u, momcad m1, momcad m2 
        WHERE l.utakmica_id = u.utakmica_id and u.momcad_1 = m1.momcad_id and u.momcad_2 = m2.momcad_id 
        AND l.korisnik_id = '$_SESSION[id]'";

    $rezutat = $baza->upit($sql);
    if (!empty($rezutat)) {
        while ($redak = $rezutat->fetch_assoc()) {
            $listici[] = $redak;
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
        <a href="index.php" class="active">Početna</a>
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
            <h2 align="center">Dobrodošli</h2>
            <h5 align="center">na stranicu moje kladionice!</h5>
            <?php
            if (empty($_SESSION)) {
                echo "<h4 align='center'><a href='prijava.php'>Prijavite se</a> kako bi vidjeli svoje listiće.</h4>";
            } else { ?>
                <h2>Popis listića</h2>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Momcad 1</th>
                        <th>Momcad 2</th>
                        <th>Opis</th>
                        <th>Ocekivani rezultat</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($listici)) {
                        foreach ($listici as $listic) : ?>
                            <tr>
                                <td><?php echo $listic['listic_id'] ?></td>
                                <td><?php echo $listic['m1'] ?></td>
                                <td><?php echo $listic['m2'] ?></td>
                                <td><?php echo $listic['opis'] ?></td>
                                <td><?php echo $listic['ocekivani_rezultat'] ?></td>
                                <td><?php echo $listic['status'] ?></td>
                            </tr>
                        <?php endforeach;
                    } else { ?>
                        <tr>
                            <td>Nemate aktivnih listića.</td>
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
