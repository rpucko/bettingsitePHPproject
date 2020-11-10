<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();

if (empty($_GET)) {
    $sql = "SELECT l.* FROM liga l LEFT JOIN korisnik k ON l.moderator_id = k.korisnik_id";
    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $lige[] = $redak;
    }
    $moderator = false;
} else {
    $ligaId = $_GET["id"];
    $sql = "SELECT u.utakmica_id as id, u.datum_vrijeme_pocetka, u.datum_vrijeme_zavrsetka, m1.naziv as m1, m2.naziv as m2, u.rezultat_1, u.rezultat_2, u.opis 
        FROM momcad m1, momcad m2, utakmica u 
        WHERE m1.momcad_id = u.momcad_1 AND m2.momcad_id = u.momcad_2 
        AND m1.liga_id = '$ligaId' AND u.datum_vrijeme_zavrsetka < NOW()";

    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $zavrseneUtakmice[] = $redak;
    }
    if (!empty($_SESSION)) {
        $sql = "SELECT u.utakmica_id as id, u.datum_vrijeme_pocetka, u.datum_vrijeme_zavrsetka, m1.naziv as m1, m2.naziv as m2, u.rezultat_1, u.rezultat_2, u.opis 
            FROM momcad m1, momcad m2, utakmica u 
            WHERE m1.momcad_id = u.momcad_1 AND m2.momcad_id = u.momcad_2
            AND m1.liga_id = '$ligaId' AND u.datum_vrijeme_zavrsetka > NOW()";

        $rezultat = $baza->upit($sql);
        while ($redak = $rezultat->fetch_assoc()) {
            $neZavrseneUtakmice[] = $redak;
        }

        $sql = "select * from liga l, korisnik k where l.moderator_id = k.korisnik_id AND k.korisnik_id = '$_SESSION[id]' AND l.liga_id = '$ligaId'";
        $rezultat = $baza->upit($sql);
        $moderator = $rezultat->fetch_assoc();

        $sql = "SELECT * FROM listic where korisnik_id = '$_SESSION[id]'";
        $rezultat = $baza->upit($sql);
        $listiciKorisnika = [];
        while ($redak = $rezultat->fetch_assoc()) {
            $listiciKorisnika[] = $redak['utakmica_id'];
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
        <div class="main">
            <?php
            if (empty($_GET)) { ?>
                <h2>Popis liga</h2>
                <?php if (!empty($_SESSION) && $_SESSION['tipKorisnika'] == '0') { ?>
                    <a href="novaLiga.php"><input type="button" id="dodaj" value="Kreiraj ligu"/></a><br>
                <?php } ?>
                <table>
                    <thead>
                    <tr>
                        <th <?php if (!empty($_SESSION) && $_SESSION['tipKorisnika'] == 0) {
                            echo "colspan='2'";
                        } ?>
                        <th>Naziv</th>
                        <th>Slika</th>
                        <th>Opis</th>
                        <th>Video</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lige as $liga) : ?>
                        <tr>
                            <?php if (!empty($_SESSION) && $_SESSION['tipKorisnika'] == 0) {
                                echo "<td><a href='azurirajLigu.php?ligaId=$liga[liga_id]'><img src='slike/edit.png' alt='edit' height=25 width=25/> </td></a>";
                            } ?>
                            <td>
                                <a class='tablica' href='lige.php?id=<?php echo $liga['liga_id'] ?>'>
                                    <?php echo $liga['naziv'] ?> <?php if (!empty($_SESSION) && $_SESSION['tipKorisnika'] == '1') {
                                        if ($liga['moderator_id'] == $_SESSION['id']) {
                                            echo "<img style='float: right ' src='slike/moderator.png' alt='moderator' height=25 width=25/>";
                                        }
                                    } ?><a/>
                            </td>
                            <td><img src='<?php echo $liga['slika'] ?>' alt='' border=0 height=75 width=75></img></td>
                            <td><?php echo $liga['opis'] ?></td>
                            <td>
                                <?php if (strpos($liga['video'], 'youtube') !== false) { ?>
                                    <iframe width='300' height='auto' src='<?php echo $liga['video'] ?>'></iframe>
                                <?php } else { ?>
                                    <video width='300' height='auto' controls>
                                        <source src='<?php echo $liga['video'] ?>' type='video/webm'>
                                    </video>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <?php
            } else { ?>
            <h2>Završene utakmice</h2>
        <?php if (empty($zavrseneUtakmice)) {
            echo "<br>Liga nema završenih utakmica.";
        } else { ?>
        <table>
        <thead>
        <tr>
            <th <?php if (isset($moderator)) {
                echo "colspan='2'";
            } ?> >Momčad 1
            </th>
            <th>Momčad 2</th>
            <th>Vrijeme početka</th>
            <th>Vrijeme završetka</th>
            <th>R1</th>
            <th>R2</th>
            <th>Opis</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($zavrseneUtakmice as $utakmica) { ?>
            <tr>
                <?php if (isset($moderator)) {
                    echo "<td><a href='azurirajUtakmicu.php?utakmicaId=$utakmica[id]&ligaId=$ligaId'><img src='slike/edit.png' alt='edit' height=20 width=20></img> </td></a>";
                } ?>
                <td><?php echo $utakmica['m1'] ?></td>
                <td><?php echo $utakmica['m2'] ?></td>
                <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_pocetka'])) ?></td>
                <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_zavrsetka'])) ?></td>
                <td><?php echo $utakmica['rezultat_1'] ?></td>
                <td><?php echo $utakmica['rezultat_2'] ?></td>
                <td><?php echo $utakmica['opis'] ?></td>
            </tr>
            <?php
        }
        }
        ?>
        </table>

        <?php if (empty($_SESSION)) {
            echo "<h4><a href='prijava.php'>Prijavite se</a> kako bi ste mogli kreirati listić.</h4>";
        } else {
        echo "<h2>Aktivne utakmice</h2>";
        if (empty($neZavrseneUtakmice)) {
            echo "<br>Liga nema utakmica koje nisu završile.";
        } else { ?>
            <table>
                <thead>
                <tr>
                    <th <?php if (isset($moderator)) {
                        echo "colspan='2'";
                    } ?> >Momčad 1
                    </th>
                    <th>Momčad 2</th>
                    <th>Vrijeme početka</th>
                    <th>Vrijeme završetka</th>
                    <th>R1</th>
                    <th>R2</th>
                    <th>Opis</th>
                    <th>Listić</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($neZavrseneUtakmice as $utakmica) { ?>
                <tr>
                    <?php if (isset($moderator)) {
                        echo "<td><a href='azurirajUtakmicu.php?utakmicaId=$utakmica[id]&ligaId=$ligaId'><img src='slike/edit.png' alt='edit' height=20 width=20></img> </td></a>";
                    } ?>
                    <td><?php echo $utakmica['m1'] ?></td>
                    <td><?php echo $utakmica['m2'] ?></td>
                    <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_pocetka'])) ?></td>
                    <td><?php echo date('d.m.Y. H:i:s', strtotime($utakmica['datum_vrijeme_zavrsetka'])) ?></td>
                    <td><?php echo $utakmica['rezultat_1'] ?></td>
                    <td><?php echo $utakmica['rezultat_2'] ?></td>
                    <td><?php echo $utakmica['opis'] ?></td>
                    <td>
                        <?php if (!in_array($utakmica['id'], $listiciKorisnika)) {
                            echo "<a href='kreirajListic.php?utakmicaId=$utakmica[id]&ligaId=$ligaId'><input type='button' id='kreirajListic' value='Kreiraj listić'/></a>";
                        } else {
                            echo "Listic je već kreiran!";
                        }
                        }
                        ?>
                    </td>
                </tr>
                <?php }
                }
                }
                ?>
                </tbody>
            </table>
            <?php if (!empty($_SESSION) && $moderator && !empty($_GET['id'])) { ?>
                <a href="novaUtakmica.php?ligaId=<?php echo $ligaId ?>"><input type="button" id="dodaj" value="Kreiraj utakmicu"/></a><br><br>
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