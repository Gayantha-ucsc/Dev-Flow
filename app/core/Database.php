<?php

class DB {
    private static ?DB $instance = null;
    private mysqli $conn;

    // Only one database instance is possible
    private function __construct() {
        $config = require __DIR__ . '/../../config/database.php';

        $this->conn = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );

        if ($this->conn->connect_error) {
            // Fail loudly
            die('Database connection failed: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset($config['charset'] ?? 'utf8mb4');
    }

    public static function getInstance(): DB {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }


    // Insert a row. $data = ['column' => value, ...]
    // Returns the new row's auto-increment ID.
     
    public function insert(string $table, array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode( ', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $params = array_values($data);
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($this->buildTypes($params), ...$params);
        }
        $stmt->execute();

        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }


    // Update rows matching $conditions
    // $data = columns to change, $conditions = WHERE clause values.
    // Returns number of affected rows.
     
    public function update(string $table, array $data, array $conditions): int {
        if (empty($conditions)) {
            throw new InvalidArgumentException('update() require at least one condition.');
        }

        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        $params = array_merge(array_values($data), array_values($conditions));
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }


    // Delete rows matching $conditions

    public function delete(string $table, array $conditions): int {
        if (empty($conditions)) {
            throw new InvalidArgumentException('delete() require at least one condition.');
        }

        $where = implode(' AND ', array_map( fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "DELETE FROM {$table} WHERE {$where}";

        $params = array_values($conditions);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    
    // Find one row matching $conditions. Returns assoc array or null.
    // e.g. find('User', ['email' => $email]) for login lookup.

    public function select(string $table, array $conditions): ?array {
        $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "SELECT * FROM {$table} WHERE {$where} LIMIT 1";

        $params = array_values($conditions);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }


    // Find all rows matching $conditions. 
    // [] return every row in the table.

    public function selectAll(string $table, array $conditions = []): array {
        if (empty($conditions)) {
            $sql = "SELECT * FROM {$table}";

            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "SELECT * FROM {$table} WHERE {$where}";

        $params = array_values($conditions);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }


    // Return 'True' if at least one row matches $conditions.

    public function exists(string $table, array $conditions): bool {
        $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "SELECT 1 FROM {$table} WHERE {$where} LIMIT 1";

        $params = array_values($conditions);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $found;
    }


    // Count rows matching $conditions. 
    // [] returns the count of whole table.
    
    public function count(string $table, array $conditions = []): int {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) AS total FROM {$table}";

            $result = $this->conn->query($sql);
            return (int) $result->fetch_assoc()['total'];
        }

        $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));

        $sql = "SELECT COUNT(*) AS total FROM {$table} WHERE {$where}";

        $params = array_values($conditions);
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($this->buildTypes($params), ...$params);
        $stmt->execute();

        $total = (int) $stmt->get_result()->fetch_assoc()['total'];
        $stmt->close();
        return $total;
    }


    // Generic — any SQL statement

    public function query(string $sql, array $params = []): array {
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($this->buildTypes($params), ...$params);
        }
        $stmt->execute();

        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }


    // Run any non-SELECT (UPDATE/DELETE/INSERT)
    // Returns affected row count.
     
    public function execute(string $sql, array $params = []): int {
        $stmt = $this->conn->prepare($sql);
        if ($params) {
            $stmt->bind_param($this->buildTypes($params), ...$params);
        }
        $stmt->execute();

        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    public function lastInsertId(): int {
        return $this->conn->insert_id;
    }


    // TRANSACTIONS

    public function beginTransaction(): void {
        $this->conn->begin_transaction();
    }

    public function commit(): void {
        $this->conn->commit();
    }

    public function rollback(): void {
        $this->conn->rollback();
    }

    private function buildTypes(array $params): string {
        $types = '';
        foreach ($params as $p) {
            if (is_int($p)) {
                $types .= 'i';
            } elseif (is_float($p)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        return $types;
    }
}