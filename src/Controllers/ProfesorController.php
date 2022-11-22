<?php
namespace App\Controllers;

use App\Api;
use App\Helpers\ErrorHandler;
use App\Helpers\Wrappers;
use App\Items\Review;

class ProfesorController {
    static public function get() {
        if (!(isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL))) {
            ErrorHandler::show(400, 'Tienes que enviar una dirección de correo válida');
        }

        $email = $_GET['email'];
        $api = new Api;
        $profesor = $api->profesor($email);
        if ($profesor) {
            // Get reviews from db
            $reviewDb = new Review;
            $reviews = $reviewDb->getAll($profesor->idnc);
            $stats = $reviewDb->statsOne($profesor->idnc);

            Wrappers::plates('profesor', [
                'title' => $profesor->nombre,
                'profesor' => $profesor,
                'reviews' => $reviews,
                'stats' => $stats
            ]);
        }
    }
}
