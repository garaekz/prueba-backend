<?php

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use function Pest\Faker\fake;

abstract class TestCase extends BaseTestCase
{
    public function createUser($pdo, $quantity = 1)
    {
        $query = "INSERT INTO user (fullname, email, pass, openid) VALUES (:fullname, :email, :pass, :openid)";
        $stmt = $pdo->prepare($query);

        for ($i = 0; $i < $quantity; $i++) {
            $stmt->execute([
                'fullname' => fake()->name(),
                'email' => fake()->email(),
                'pass' => fake()->password(8),
                'openid' => fake()->randomNumber(6),
            ]);
        }

        if ($quantity === 1) {
            return $this->getById($pdo, 'user', $pdo->lastInsertId());
        }

        return null;
    }

    public function createComment($pdo, $userId, $quantity = 1)
    {
        $query = "INSERT INTO user_comment (user, coment_text, likes) VALUES (:user, :coment_text, :likes)";
        $stmt = $pdo->prepare($query);

        for ($i = 0; $i < $quantity; $i++) {
            $stmt->execute([
                'user' => $userId,
                'coment_text' => fake()->sentence(),
                'likes' => fake()->randomNumber(2),
            ]);
        }

        if ($quantity === 1) {
            return $this->getById($pdo, 'user_comment', $pdo->lastInsertId());
        }

        return null;
    }

    public function getById($pdo, $table, $id)
    {
        $query = "SELECT * FROM $table WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTableCount($pdo, $table)
    {
        $query = "SELECT COUNT(*) FROM $table";
        $stmt = $pdo->query($query);
        return $stmt->fetchColumn();
    }
}
