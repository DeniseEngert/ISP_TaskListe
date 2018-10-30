<?php

$aarr = array(); // assoziiertes Array für Tasks
$iarr = array(); // indiziertes Array mit assoziierten Arrays als Elemente

// pruefen, ob File schon existent, Inhalt auslesen und als Array zur Verfuegung stellen
if (file_exists("ToDo.txt")) {
    $datei = fopen("ToDo.txt", "r");

    $decode = fgets($datei, 1000);
    $iarr = json_decode($decode, true);
}

?>

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>ISP - Gerüst der Einsendeaufgabe 2</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" media="screen" href="./css/main.css"/>
    </head>
    <body>

    <header>
        <h2>EA2 - Aufgabenliste</h2>
    </header>
    <main>
        <ul id="todolist">
            <?php
            // Übertragen des ind. Array $iarr in HTML
            if (empty($iarr)) {
                echo "<h3 style='color: maroon'>Du hast derzeit keine offenen Tasks. Yay!</h3>";
            } else {
                foreach ($iarr as $task) {
                    echo "<li>";
                    // check/ unchecked integrieren
                    if ($task['done'] == 0) {
                        echo "<a href='index.php?Task={$task["Task"]}&Action=done' class='done'></a>";
                    } elseif ($task['done'] == 1) {
                        echo "<a href='index.php?Task={$task["Task"]}&Action=done' class='done checked'></a>";
                    }
                    echo "<span>" . $task['Task'] . "</span>";
                    echo "<a href='index.php?Task={$task["Task"]}&Action=del' class='delete'>löschen</a>";
                    echo "</li>";
                }
            }
            ?>
        </ul>
        <div class="spacer"></div>
        <form id="add-todo" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="text" placeholder="Text für neue Aufgabe" name="text">
            <input type="submit" value="hinzufügen">
        </form>
    </main>
    <footer>
        <p>Engert, Denise - TH-Brandenburg</p>
    </footer>
    </body>
    </html>

<?php

$content = @$_POST["text"]; // nimmt Task auf
$check = @$_GET['Task']; // nimmt Task auf
$action = @$_GET['Action']; // nimmt Aktion auf
$index = 0;

// Tasks in Datei ablegen
if (isset($content)) {
    $datei = fopen("ToDo.txt", "w+");
    $aarr['done'] = 0;
    $aarr['Task'] = $content;

    array_push($iarr, $aarr);

    // Array zu JSON
    $json_arr = json_encode($iarr);

    // Daten in File schreiben
    fwrite($datei, $json_arr);

    fclose($datei);

    echo "<meta http-equiv='refresh' content='0'>";
}

// Tasks modifizieren: done/ undone und loeschen
if (isset($check)) {

    // Index ermitteln
    $i = 0;
    foreach ($iarr as $item) {
        if ($item['Task'] == $check) {
            $index = $i;
            break;
        } else {
            $i += 1;
        }
    }

    // Aktion ausfuehren
    if ($action == 'done') { // setzt Tasks done resp. undone
        if ($iarr[$index]['done'] == 0) {
            $iarr[$index]['done'] = 1;
        } elseif ($iarr[$index]['done'] == 1) {
            $iarr[$index]['done'] = 0;
        }
    } elseif ($action == 'del') { // loescht Element, ordnet Index neu
        array_splice($iarr, $index, 1);
    } else { // My kind of Fehlerbehandlung
        print("Ich weiß nicht was soll es bedeuten ...");
    }

    $datei = fopen("ToDo.txt", "w+");

    // Array zu JSON
    $json_arr = json_encode($iarr);

    // Daten in File schreiben
    fwrite($datei, $json_arr);

    fclose($datei);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
}

?>