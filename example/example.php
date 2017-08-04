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
        <h1>GDImage</h1>
        <p class="lead">

        </p>
    </div>

    <hr>

    <h4>Example output</h4>
<pre><?php

    include '../src/GDImage.php';

    $logo = new DElfimov\GDImage\GDImage('../tests/test.gif');
    $logo
        ->resize(300, 300, true)
        ->opacity(30);

    $image = new DElfimov\GDImage\GDImage('../tests/test.jpg');
    $image->resize(600, 400, true)
        ->merge($logo, 'right', 'bottom')
        ->save(__DIR__ . '/' . 'example.jpg');

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



