<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../tests/fixtures.php';


$exporter = new \Trismegiste\SerialIcer\Exporter();
$factory = new \Trismegiste\SerialIcer\Factory();

$obj = new \tests\Trismegiste\SerialIcer\Company(new \tests\Trismegiste\SerialIcer\Employee('toto', 13));
$export = $exporter->export($obj);
$newObj = $factory->create($export);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>

        <script>
            var myObj = <?php echo json_encode($export); ?>
        </script>

    </body>
</html>

