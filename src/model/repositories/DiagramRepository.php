<?php

namespace model\repositories;

use model\entities\auth\Token;
use model\entities\auth\User;
use model\entities\Diagram;
use model\services\Database;

class DiagramRepository {

    /**
     * @var Database
     */
    public $database;

    public function __construct(Database $database) {
        $this->database = $database;
        $this->database->assertTable(Diagram::class);
    }

    public function getById($id) {
        return $this->database->get(Diagram::class, $id);
    }

    /**
     * @param User $user
     * @return Diagram[] All diagrams the user have saved
     */
    public function getByUser(User $user) {
        return $this->database->select(Diagram::class, '`userId` = ?', [$user->getId()]);
    }

    /**
     * @param Diagram $diagram
     * @throws \InvalidArgumentException if the diagram isn't valid
     */
    public function save(Diagram $diagram) {
        if (!$diagram->isValid()) {
            throw new \InvalidArgumentException('Diagram is not valid');
        }

        $this->database->save($diagram);
    }

    public function delete($diagram) {
        $this->database->delete($diagram);
    }
}
