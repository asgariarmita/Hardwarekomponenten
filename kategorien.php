<?php
require("includes/config.inc.php");
require("includes/conn.inc.php");
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Hardqwarekomponenten</title>
</head>

<body>
    <h1>Kategorien</h1>
    <ul>
        <li><a href="startseite.php">Startseite</a></li>
        <li><a href="individualpcs.php">Individual PCs</a></li>
    </ul><br>
</body>

</html>
<!-- Geben Sie alle Produktkategorien in einer hierarchischen Liste aus. Bei Klick auf 
eine Produktkategorie sollen sämtliche Informationen zu den zugehörigen Produkten
auf einer eigenen Seite dargestellt werden (inkl. der Verfügbarkeit). -->
<?php
printout();
function printout($fid = null)
{
    global $conn;
    if (is_null($fid)) {
        $status = "FIDKategorie IS NULL";
    } else {
        $status = "FIDKategorie = " . $fid;
    }
    $sql = "
    SELECT 
        * 
    FROM 
        tbl_kategorien
    WHERE 
        tbl_kategorien." . $status . ";
    ";
    $result = $conn->query($sql) or die("Fehler in der Query " . $conn->error . "<br>" . $sql);
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li><a href=kategorie_produkte.php?idKat=" . $row["IDKategorie"] . ">" . $row["Kategorie"] . '</a>';
        printout($row["IDKategorie"]);
        echo "</li>";
    }
    echo "</ul>";
}
?>