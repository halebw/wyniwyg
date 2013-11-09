<?php
//service endpiont

require_once 'require.php';

$service = new wlRestService();
$service ->registerController(new getTemplatesControllerV1());

$service->run();

?>
