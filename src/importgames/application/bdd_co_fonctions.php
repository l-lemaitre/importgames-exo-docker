<?php
    // Établi une connexion avec la base de données "importgames" pour les fonctions PHP (voir fichier categorie.php ligne 64 pour exemple d'utilisation)
    $host = 'db';
    $db   = 'importgames';
    $user = 'username';
    $pass = 'password';
    $port = "3306";
    $charset = 'utf8mb4';

    $options = [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

    try {
        $pdo = new \PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }