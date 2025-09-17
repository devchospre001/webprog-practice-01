<?php

/**
 * Host: db1.ibu.edu.ba
 * Database: webfinalmakeup_db_1507
 * User: webfinalmup_db_user2
 * Password: webFinMup1507
 * Port: 3306
 */

class ExamDao
{
  private $conn = null;
  /**
   * constructor of dao class
   */
  public function __construct()
  {
    try {
      $host = "localhost";
      $db = "final-makeup";
      $user = "root";
      $pw = "K3rim123";
      $port = 3306;

      if ($this->conn === null) {
        try {
          $this->conn = new PDO(
            "mysql:host=" . $host . ";dbname=" . $db . ";port=" . $port,
            $user,
            $pw,
            [
              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
          );
          // Removed echo to prevent corrupting JSON output
        } catch (PDOException $e) {
          die("Connection failed: " . $e->getMessage());
        }
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
    }
  }

  public function login($data)
  {
    $stmt = $this->conn->prepare(
      "SELECT id, email, password
      FROM users
      WHERE email = :email LIMIT 1"
    );

    $stmt->execute(["email" => $data["email"]]);
    $user = $stmt->fetch();

    if (!$user) {
      return null;
    }

    unset($user["password"]);
    return $user;

  }

  public function film_performance_report()
  {
    $sql = <<<SQL
      SELECT c.category_id AS id,
        c.name AS name,
        COUNT(fc.film_id) AS total
      FROM category c
      LEFT JOIN film_category fc ON c.category_id = fc.category_id
      GROUP BY c.category_id, c.name
      ORDER BY total DESC
    SQL;

    return $this->conn->query($sql)->fetchAll();
  }

  public function delete_film($film_id)
  {
    $stmt = $this->conn->prepare(
      "DELETE FROM film WHERE film_id = :film_id"
    );

    return $stmt->execute(["film_id" => $film_id]);
  }

  public function edit_film($film_id, $data)
  {
    $fields = [];
    $params = ["film_id" => $film_id];

    foreach ($data as $key => $value) {
      $fields[] = "{$key} = :{$key}";
      $params[$key] = $value;
    }

    if (empty($fields)) {
      throw new RuntimeException("Empty data provided!");
    }

    $sql = "UPDATE film SET " . implode(', ', $fields) . " WHERE film_id = :film_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute($params);

    $stmtTwo = $this->conn->prepare("SELECT * FROM film WHERE film_id = :film_id");
    $stmtTwo->execute(["film_id" => $film_id]);
    return $stmtTwo->fetch();
  }

  public function get_customers_report()
  {
    $sql = <<<SQL
      SELECT c.customer_id, c.first_name, c.last_name, c.email,
        COUNT(r.rental_id) AS total_rentals,
        COALESCE(SUM(p.amount), 0) AS total_spent
      FROM customer c
      LEFT JOIN rental r ON c.customer_id = r.customer_id
      LEFT JOIN payment p ON r.rental_id = p.rental_id
      GROUP BY c.customer_id, c.first_name, c.last_name, c.email
      ORDER BY total_spent DESC
    SQL;

    return $this->conn->query($sql)->fetchAll();
  }

  public function get_customer_rental_details($customer_id)
  {
    $sql = <<<SQL
      SELECT r.rental_id, r.rental_date, r.return_date,
        f.title, p.amount
        FROM rental r
        INNER JOIN inventory i ON r.inventory_id = i.inventory_id
        INNER JOIN film f ON i.film_id = f.film_id
        LEFT JOIN payment p ON r.rental_id = p.rental_id
        WHERE r.customer_id = :customer_id
        ORDER BY r.rental_date DESC
    SQL;

    $stmt = $this->conn->prepare($sql);
    $stmt->execute(["customer_id" => $customer_id]);

    return $stmt->fetchAll();
  }
}
