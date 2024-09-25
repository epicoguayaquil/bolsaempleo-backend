<?php
require __DIR__.'/../../.environment/enviroment.php';
define("IGNORE_TOKEN", ["/empleabilidad/ofertas_laborales", "/empleabilidad/oferta/.*"]);
define("DEBUG_ERROR", true);
define("PATH_API",'/api_empleabilidad/public/api');
define("NAME_API","api_empleabilidad");