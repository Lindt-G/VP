<?php
    require_once "../config.php";

    // loon andmebaasiga uhenduse
    // server, kasutaja, parool, andmebaas
    $db_connection = new mysqli($server_host, $server_user_name, $server_password, $database);

    // maaran suhtlemisel kasutatava kooditabeli
    $db_connection->set_charset("utf8");

    // valmistame ette andmete saatmise SQL käsu
    $stmt = $db_connection->prepare("SELECT comment, grade, added FROM vp_daycomment");
    echo $db_connection->error;

    // seome saadavad andmed muutujatega
    $stmt->bind_result($comment_from_db, $grade_from_db, $added_from_db);

    // taidame kasu
    $stmt->execute();

    // kui saan uhe kirje
    // if ($stmt->fetch()) { }

    // kui tuleb teadmata arv kirjeid
    $comment_html = null;
    while ($stmt->fetch()) { 
        // <p>kommentaar, hinne paevale: 6, lisatud xxxxxxx</p>
        $comment_html .= "<p>" .$comment_from_db .", hinne päevale: " .$grade_from_db;
        $comment_html .= ", lisatud " .$added_from_db .".</p>\n";
    }

    // sulgeme käsu
    $stmt->close();

    // sulgeme andmebaasi uhenduse
    $db_connection->close();
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>Kommentaaride lapang</title>
</head>

<body>

<img src="photos/vp_banner_gs.png" alt="bänner">
<?php echo $comment_html; ?>

</body>

</html>