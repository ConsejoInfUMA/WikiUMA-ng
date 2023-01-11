<?php
namespace App\Controllers;

use App\Api;
use App\Helpers\MsgHandler;
use App\Helpers\Wrappers;

class TitulacionesController {
    static public function get(int $id) {
        $api = new Api;
        $res = $api->titulaciones($id);
        if (!$res->data) {
            MsgHandler::showApi($res);
            return;
        }

        $titulaciones = $res->data;

        Wrappers::plates('titulaciones', [
            'title' => $titulaciones[0]->CENTRO,
            'titulaciones' => $titulaciones
        ]);
    }
}
