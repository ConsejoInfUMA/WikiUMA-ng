<?php
namespace App\Helpers;

use App\Constants\Links;

class Wrappers {
    static public function db(): \PDO {
        $driver = Misc::env('DB_DRIVER', 'mysql');
        $host = Misc::env('DB_HOST', 'localhost');
        $port = Misc::env('DB_PORT', 3306);
        $db = Misc::env('DB_NAME', 'wikiuma');
        $username = Misc::env('DB_USERNAME', '');
        $password = Misc::env('DB_PASSWORD', '');
        $dns = $driver .
        ':host=' . $host .
        ';port=' . $port .
        ';dbname=' . $db . ';charset=utf8mb4';
        return new \PDO($dns, $username, $password);
    }

    /**
     * Render template with Plates
     */
    static public function plates(string $view, array $data = []): void {
        $engine = new \League\Plates\Engine(__DIR__ . '/../../templates');
        $engine->registerFunction('url', function(string $endpoint, array $query = []): string {
            return Misc::url($endpoint, $query);
        });
        $engine->registerFunction('current_url', function(): string {
            return Misc::url(router()->getCurrentUri());
        });
        $engine->registerFunction('version', function (): string {
            return \Composer\InstalledVersions::getVersion('pablouser1/wikiuma-ng');
        });
        $engine->registerFunction('links', function (): array {
            return Links::list;
        });
        $engine->registerFunction('isAdmin', function (): bool {
            return Misc::isLoggedIn();
        });
        $engine->registerFunction('color', function (float $note, bool $isComment = false): string {
            $type = '';
            if ($isComment) {
                if ($note < 0) $type = 'danger';
                if ($note === 0) $type = 'black';
                if ($note > 0) $type = 'success';
            } else {
                if ($note < 5) $type = 'danger';
                if ($note === 5) $type = 'warning';
                if ($note > 5) $type = 'success';
            }
            return $type;
        });
        $engine->registerFunction('page', function (): int {
            return Misc::getPage();
        });
        $engine->registerFunction('selected', function (string $needle, string $key, array $arr = []): string {
            if (empty($arr)) {
                $arr = $_GET;
            }
            return isset($arr[$key]) && $arr[$key] === $needle ? 'selected' : '';
        });
        $engine->registerFunction('url_to', function (string $data, int $subject): string {
            $isSubject = boolval($subject);
            if ($isSubject) {
                // Asignatura (TODO)
                return Misc::url('/');
            }

            return Misc::url('/profesores', ['idnc' => $data]);
        });

        $template = $engine->make($view);
        echo $template->render($data);
    }
}
