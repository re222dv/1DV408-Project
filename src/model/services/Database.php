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
     * Gets all properties on $object that is marked as a column and private
     *
     * @param object $object
     * @param bool $getId Optional, set to true if the id should be requested even if it's
     *                    not marked as a column.
     * @return array with name => values
     */
    private function getProperties($object, $getId = false) {
        $reflection = new \ReflectionClass($object);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        $vars = [];

        foreach ($properties as $property) {
            preg_match(self::COLUMN_DECLARATION, $property->getDocComment(), $match);

            if ($match or ($getId and $property->getName() === 'id')) {
                $property->setAccessible(true);
                $vars[$property->getName()] = $property->getValue($object);
            }
        }

        return $vars;
    }

    /**
     * Get table name for $class
     *
     * @param mixed $class The name of a class or an object of the class
     * @return string
     */
    private function getTable($class) {
        $reflection = new \ReflectionClass($class);
        return $reflection->getShortName();
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

        $table = $this->getTable($class);
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
     * Deletes $objects from the database
     *
     * @param object|array $objects A single object or an array of objects from the same class
     */
    public function delete($objects) {
        if (!is_array($objects)) {
            $objects = [$objects];
        }

        $table = $this->getTable($objects[0]);
        $ids = [];

        foreach ($objects as $object) {
            $reflection = new \ReflectionClass($object);
            $idProperty = $reflection->getProperty('id');
            $idProperty->setAccessible(true);
            $ids[] = $idProperty->getValue($object);
        }

        // got this from http://stackoverflow.com/a/23641033/2965191
        $in = join(',', array_fill(0, count($ids), '?'));

        $this->connection
            ->prepare("DELETE FROM $table WHERE `id` IN ($in)")
            ->execute($ids);
    }

    /**
     * Get objects of $class
     *
     * @param string $class
     * @param int|int[] $ids One id or one array of ids to get
     * @return object|array The object or objects with the corresponding id
     */
    public function get($class, $ids) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $limit = count($ids);

        $where = '`id` ';
        if ($limit == 1) {
            $where .= "= ?";
        } else {
            $in = join(',', array_fill(0, count($ids), '?'));
            $where .= "IN ($in)";
        }

        return $this->select($class, $where, $ids, $limit);
    }

    /**
     * Insert $object into its corresponding table
     *
     * @param object $object
     * @returns int The id of the created row
     */
    public function insert($object) {
        $reflection = new \ReflectionClass($object);
        $table = $reflection->getShortName();
        $vars = $this->getProperties($object);

        $columns = join('`, `', array_keys($vars));
        $placeholders = join(', ', array_fill (0, count($vars), '?'));

        $this->connection
            ->prepare("INSERT INTO $table (`$columns`) VALUES ($placeholders)")
            ->execute(array_values($vars));

        if ($reflection->hasProperty('id')) {
            $property = $reflection->getProperty('id');
            $property->setAccessible(true);
            $property->setValue($object, $this->connection->lastInsertId());
        }
    }

    /**
     * Saves $object to the database
     * If it exists it's updated or else it's inserted
     *
     * @param object $object
     */
    public function save($object) {
        $table = $this->getTable($object);
        $vars = $this->getProperties($object, true);

        if ($vars['id'] === null) {
            $this->insert($object);
        } else {
            $set = '';
            foreach (array_keys($vars) as $column) {
                if ($column === 'id') {
                    continue;
                }
                $set .= "`$column` = :$column,";
            }
            $set = substr($set, 0, -1);

            $this->connection
                ->prepare("UPDATE $table SET $set WHERE `id` = :id")
                ->execute($vars);
        }
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
        $table = $this->getTable($class);

        if ($where) {
            $where = "WHERE $where";
        }
        if (is_numeric($limit) && $limit > 0) {
            $where .= " LIMIT $limit";
        }

        $query = $this->connection->prepare("SELECT * FROM $table $where");
        $query->execute($values);
        if ($limit == 1) {
            $result = $query->fetch(\PDO::FETCH_ASSOC);
            if ($result) {
                return $this->instantiate($class, $result);
            } else {
                return null;
            }
        } else {
            $objects = [];
            foreach ($query->fetchAll(\PDO::FETCH_ASSOC) as $attributes) {
                $objects[] = $this->instantiate($class, $attributes);
            }
            return $objects;
        }
    }
}
