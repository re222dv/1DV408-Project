<?php

namespace model\services;

/**
 * A Simple ORM that uses reflection to automatically build SQL
 *
 * @package model\services
 */
class Database {
    const COLUMN_DECLARATION = '/\[column ([a-z0-9()]+)( null)?\]/';

    private $connectionString = 'mysql:host=127.0.0.1;dbname=project';
    private $user = 'Sven';
    private $password = 'Åke';
    private $connection;

    public function __construct() {
        $this->connection = new \PDO($this->connectionString, $this->user, $this->password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Instantiates a class without calling its constructor and set $attributes even
     * if they are private.
     *
     * @param string $class The class to instantiate from
     * @param array $attributes The attributes to set with name => value
     * @return object The instantiated object
     */
    private function instantiate($class, array $attributes) {
        $reflection = new \ReflectionClass($class);
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($attributes as $name => $value) {
            try {
                $property = $reflection->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($object, $value);
            } catch (\ReflectionException $e) {
                // Swallow
            }
        }

        return $object;
    }

    /**
     * Creates a table that matches $class, if it does not exist.
     * An auto incrementing id will be added.
     *
     * @param string $class
     */
    public function assertTable($class) {
        $reflection = new \ReflectionClass($class);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $table = $reflection->getShortName();
        $columns = '';

        foreach ($properties as $property) {
            preg_match(self::COLUMN_DECLARATION, $property->getDocComment(), $match);

            if ($match) {
                $name = $property->getName();
                $type = $match[1];

                $nullability = '';
                if (count($match) <= 2) {
                    $nullability = 'NOT NULL';
                }

                $columns .= "`$name` $type $nullability, ";
            }
        }

        $this->connection
            ->prepare("CREATE TABLE IF NOT EXISTS `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    $columns
                    PRIMARY KEY (`id`)
                )")
            ->execute();
    }

    /**
     * Insert $object into its corresponding table
     *
     * @param object $object
     */
    public function insert($object) {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $table = $reflection->getShortName();
        $vars = [];

        foreach ($properties as $property) {
            preg_match(self::COLUMN_DECLARATION, $property->getDocComment(), $match);

            if ($match) {
                $property->setAccessible(true);
                $vars[$property->getName()] = $property->getValue($object);
            }
        }

        $columns = join('`, `', array_keys($vars));
        $placeholders = join(', ', array_fill (0, count($vars), '?'));

        $this->connection
            ->prepare("INSERT INTO $table (`$columns`) VALUES ($placeholders)")
            ->execute(array_values($vars));
    }

    /**
     * Select one or more objects
     *
     * @param string $class The class to query for
     * @param string $where An SQL where clause
     * @param array $values An array for parametrized values in the $where clause
     * @param int $limit The maximum number to return or all if zero or negative
     * @return array|object An array of objects of $class or just an object of $class if $limit == 1
     */
    public function select($class, $where = '', array $values = [], $limit = 0) {
        $reflection = new \ReflectionClass($class);
        $table = $reflection->getShortName();

        if ($where) {
            $where = "WHERE $where";
        }
        if (is_numeric($limit) && $limit > 0) {
            $where .= " LIMIT $limit";
        }

        $query = $this->connection->prepare("SELECT * FROM $table $where");
        $query->execute($values);
        if ($limit == 1) {
            return $this->instantiate($class, $query->fetch(\PDO::FETCH_ASSOC));
        } else {
            $objects = [];
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $attributes) {
                $objects[] = $this->instantiate($class, $attributes);
            }
            return $objects;
        }
    }
}
