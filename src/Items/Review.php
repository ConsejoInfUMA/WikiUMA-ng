<?php
namespace App\Items;

use App\Helpers\Misc;

class Review extends BaseItem {
    const PER_PAGE = 10;

    public function getAll(int $page = 1, string $sort = "created_at", string $order = "desc"): array {
        return $this->__commonGet('', $page, $sort, $order);
    }

    public function getAllFrom(string $data, int $page = 1, string $sort = "created_at", string $order = "desc"): array {
        return $this->__commonGet($data, $page, $sort, $order);
    }

    public function get(int $id): ?object {
        $sql = <<<SQL
        SELECT
            `id`,
            `username`,
            `note`,
            `message`,
            `votes`,
            `created_at`
        FROM
            reviews
        WHERE
            id=:id
        LIMIT 1
        SQL;

        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':id' => $id
        ]);
        if ($success) {
            $review = $stmt->fetchObject();
            // Parse
            $tag = new Tag($this->conn);
            $review->tags = $tag->getFrom($review->id);
            return $review;
        }
        return null;
    }

    public function add(string $data, string $username, float $note, string $message, int $subject): bool {
        $stmt = $this->conn->prepare('INSERT INTO reviews (`data`, username, note, `message`, `subject`) VALUES (:data, :username, :note, :message, :subject)');
        $success = $stmt->execute([
            ':data' => $data,
            ':username' => $username,
            ':note' => $note,
            ':message' => $message,
            ':subject' => $subject
        ]);
        return $success;
    }

    public function delete(int $id): bool {
        $stmt = $this->conn->prepare('DELETE FROM reviews WHERE id=:id');
        $success = $stmt->execute([
            ':id' => $id
        ]);
        return $success;
    }

    public function statsOne(string $data): object {
        $stats = new \stdClass;
        $stats->med = 0;
        $stats->min = 0;
        $stats->max = 0;
        $stats->total = 0;

        $sql = <<<SQL
        SELECT
            COUNT(note) AS `total`,
            ROUND(AVG(note), 2) AS med,
            MAX(note) as max,
            MIN(note) as min
        FROM
            reviews
        WHERE
            `data` =:data
        SQL;

        $stmt = $this->conn->prepare($sql);
        $success = $stmt->execute([
            ':data' => $data
        ]);

        if ($success) {
            $stats = $stmt->fetchObject();
        }

        return $stats;
    }

    public function statsTotal(): array {
        $stats = [];

        $query = $this->conn->query('SELECT COUNT(note) AS `total`, ROUND(AVG(note), 2) AS med FROM reviews GROUP BY `subject`');

        if ($query) {
            $stats = $query->fetchAll(\PDO::FETCH_OBJ);
        }

        return $stats;
    }

    public function vote(int $id, bool $more = false): bool {
        $val = $more ? 1 : -1;
        $stmt = $this->conn->prepare("UPDATE reviews SET votes = (votes + :change) WHERE id=:id");
        $success = $stmt->execute([
            ':id' => $id,
            ':change' => $val
        ]);
        return $success;
    }

    private function __calcOffset(int $page): int {
        return self::PER_PAGE * ($page - 1);
    }

    private function __commonGet(string $data, int $page = 1, string $sort = "created_at", string $order = "desc"): array {
        $isValidSort = Misc::sanitizeSort($sort, $order);
        if ($isValidSort) {
            $offset = $this->__calcOffset($page);
            $per = self::PER_PAGE;
            $where = $data !== '' ? 'WHERE `data`=:data' : '';
            $sql = <<<SQL
            SELECT
                `id`,
                `data`,
                `username`,
                `note`,
                `message`,
                `votes`,
                `subject`,
                `created_at`
            FROM
                reviews
            $where
            ORDER BY
                $sort $order
            LIMIT
                $offset, $per
            SQL;

            $stmt = $this->conn->prepare($sql);
            $success = $stmt->execute($where !== '' ? [
                ':data' => $data
            ] : []);

            $reviews = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if ($success) {
                // Parse
                $tag = new Tag($this->conn);
                foreach ($reviews as $review) {
                    $review->tags = $tag->getFrom($review->id);
                }
            }
            return $reviews;
        }

        return [];
    }
}
