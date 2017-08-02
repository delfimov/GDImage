<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Easy to use translate library for multi-language websites">
    <meta name="author" content="Dmitry Elfimov <elfimov@gmail.com>">

    <title>Translate</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>


<div class="container">

    <div class="starter-template">
        <h1>Translate</h1>
        <p class="lead">
            Easy to use i18n translation PHP class for multi-language websites
            with language auto detection and plurals.
        </p>
    </div>

    <hr>

    <h4>Example output</h4>
<pre><?php

    echo 'to be done...';

?></pre>

<?php

$fileContents = file_get_contents(__FILE__);

$codeStart = strpos($fileContents, '<?php', 1);
$codeEnd = strpos($fileContents, '?>', $codeStart);

?>

    <br>

    <h4>Example code</h4>
    <pre><?=htmlspecialchars(substr($fileContents, $codeStart, $codeEnd - $codeStart))?></pre>



</div>

</body>
</html>



