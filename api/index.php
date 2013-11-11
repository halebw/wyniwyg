<?php
//service endpiont

require_once 'require.php';

$service = new wlRestService();
$service ->registerController(new postTemplatesControllerV1());
$service ->registerController(new postUsersControllerV1());

$service->run();

?>
