<?php
/**
 * Classe de Conexão com Banco de Dados
 * Utiliza PDO para conexão com MySQL
 */

require_once __DIR__ . '/../config/config.php';

class Database {
    private static $instance = null;
    private $connection;

    /**
     * Construtor privado (Singleton Pattern)
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . "; SET time_zone = '-03:00'"
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            error_log("Erro na conexão com o banco de dados: " . $e->getMessage());
            die("Erro interno do servidor. Tente novamente em instantes.");
        }
    }

    /**
     * Retorna a instância única da conexão (Singleton)
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Retorna a conexão PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Previne clonagem da instância
     */
    private function __clone() {}

    /**
     * Previne desserialização da instância
     */
    public function __wakeup() {
        throw new Exception("Não é possível deserializar Singleton");
    }

    /**
     * Executa query SELECT e retorna todos os resultados
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Executa query SELECT e retorna um único resultado
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro na query: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Executa query INSERT, UPDATE ou DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erro ao executar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retorna o ID do último registro inserido
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirma uma transação
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Reverte uma transação
     */
    public function rollback() {
        return $this->connection->rollback();
    }

    /**
     * Verifica se está em uma transação
     */
    public function inTransaction() {
        return $this->connection->inTransaction();
    }
}
