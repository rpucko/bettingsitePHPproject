<!DOCTYPE html>
<?php
include("baza/bazaPodataka.php");
session_start();
$baza = new bazaPodataka();
$baza->spajanje();
if (!empty($_SESSION)) {

    $sql = "SELECT * FROM liga";
    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $lige [] = $redak;
    }

    $sql = "SELECT * FROM korisnik";
    $rezultat = $baza->upit($sql);
    while ($redak = $rezultat->fetch_assoc()) {
        $korisnici [] = $redak;
    }

    $sql = "SELECT k.korisnicko_ime, liga.naziv,
                SUM(CASE WHEN listic.status = 'D' THEN 1 ELSE 0 END) AS dobitni,
                SUM(CASE WHEN listic.status='N' THEN 1 ELSE 0 END) AS nedobitni 
                FROM  liga, momcad, utakmica u, korisnik k,listic 
                WHERE liga.liga_id=momcad.liga_id 
                AND momcad.momcad_id=u.momcad_1 
                AND u.utakmica_id = listic.utakmica_id 
                and listic.korisnik_id= k.korisnik_id";

    if (!empty($_POST)) {
        if (!empty($_POST['odDatuma'])) {
            $odDatuma = date("Y-m-d H:i:s", strtotime($_POST['odDatuma']));
            $sql .= " AND u.datum_vrijeme_zavrsetka > '$odDatuma'";
        }
        if (!empty($_POST['doDatuma'])) {
            $doDatuma = date("Y-m-d H:i:s", strtotime($_POST['doDatuma']));
            $sql .= " AND u.datum_vrijeme_zavrsetka < '$doDatuma'";
        }
        if (!empty($_POST['korisnik'])) {
            $korisnikId = $_POST['korisnik'];
            $sql .= " AND k.korisnik_id = '$korisnikId'";
        }
        if (!empty($_POST['liga'])) {
            $ligaId = $_POST['liga'];
            $sql .= " AND liga.liga_id = '$ligaId'";
        }

        $sql .= " GROUP BY 1,2";

        if (isset($_POST['ascSort'])) {
            $sql .= " ORDER BY 3 DESC";
        }

        if (isset($_POST['descSort'])) {
            $sql .= " ORDER BY 3 ASC";
        }

        $rezultat = $baza->upit($sql);
        while ($redak = $rezultat->fetch_assoc()) {
            $statistika [] = $redak;
        }
    }
}
$baza->zatvaranje();
?>

<html lang="en">
<head>
    <title>Statistika listića</title>
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
                echo '<a href="statistikaListica.php" class="active">Statistika listića</a>';
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
            <h2>Statistika listića</h2>
            <form id="filtriranje" method="POST" name="filtriranje" action="statistikaListica.php">
                <label for="odVremena">Od datuma:</label>
                <input type="text" id="odDatuma" name="odDatuma"
                       value="<?php if (!empty($_POST['odDatuma'])) {
                           echo $_POST['odDatuma'];
                       } ?>"
                       pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}. (0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}" placeholder="dd.mm.gggg. hh:mm:ss"><br>
                <label for="doVremena">Do datuma:</label>
                <input type="text" id="doDatuma" name="doDatuma"
                       value="<?php if (!empty($_POST['doDatuma'])) {
                           echo $_POST['doDatuma'];
                       } ?>"
                       pattern="(0[1-9]|1[0-9]|2[0-9]|3[01]).(0[1-9]|1[012]).[0-9]{4}. (0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}" placeholder="dd.mm.gggg. hh:mm:ss"><br>
                <label for="liga">Liga: </label>
                <select id="liga" name="liga">
                    <option value="">----</option>
                    <?php foreach ($lige as $liga) {
                        if (!empty($_POST['liga']) && $_POST['liga'] == $liga['liga_id']) {
                            echo "<option value='$liga[liga_id]' selected>$liga[naziv]</option>";
                        } else {
                            echo "<option value='$liga[liga_id]'>$liga[naziv]</option>";
                        }
                    } ?>
                </select>
                <label for="korisnik">Korisnik: </label>
                <select id="korisnik" name="korisnik">
                    <option value="">----</option>
                    <?php foreach ($korisnici as $korisnik) {
                        if (!empty($_POST['korisnik']) && $_POST['korisnik'] == $korisnik['korisnik_id']) {
                            echo "<option value='$korisnik[korisnik_id]' selected>$korisnik[korisnicko_ime]</option>";
                        } else {
                            echo "<option value='$korisnik[korisnik_id]'>$korisnik[korisnicko_ime]</option>";
                        }
                    } ?>
                </select>
                <button id="filtrirajBtn" type="submit">Filtriraj</button>
                <button id="filtrirajBtn" type="submit" name="descSort">Sortiraj po broju dobitnih uzlazno</button>
                <button id="filtrirajBtn" type="submit" name="ascSort">Sortiraj po broju dobitnih silazno</button>
            </form>

            <table>
                <thead>
                <tr>
                    <th>Korisnik</th>
                    <th>Liga</th>
                    <th>Dobitni listici</th>
                    <th>Nedobitni listici</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($statistika)) {
                    foreach ($statistika as $zapis) : ?>
                        <tr>
                            <td><?php echo $zapis['korisnicko_ime'] ?></td>
                            <td><?php echo $zapis['naziv'] ?></td>
                            <td><?php echo $zapis['dobitni'] ?></td>
                            <td><?php echo $zapis['nedobitni'] ?></td>
                        </tr>
                    <?php endforeach;
                } else { ?>
                    <tr>
                        <td>Za trazeni upit nema statistike listića.</td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="footer">
        <h2></h2>
    </div>
</div>
</body>
</html>
