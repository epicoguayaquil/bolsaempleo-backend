<?php

$group->group('/empleabilidad', function (Slim\Interfaces\RouteCollectorProxyInterface $group) {
    $group->get('/', App\Empleabilidad\Controllers\EmpleabilidadController::class.':hello');
    $group->get('/oferta/{id}', App\Empleabilidad\Controllers\EmpleabilidadController::class.':getOfertaLaboral');
    $group->get('/ofertas_laborales', App\Empleabilidad\Controllers\EmpleabilidadController::class.':ofertasLaborales');
    $group->post('/postular/{id}',App\Empleabilidad\Controllers\EmpleabilidadController::class.':postular');
});