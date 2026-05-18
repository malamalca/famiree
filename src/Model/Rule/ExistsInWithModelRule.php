<?php

namespace App\Model\Rule;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

class ExistsInWithModelRule
{
    /**
     * Options for the constructor
     *
     * @var array<string, mixed>
     */
    protected $_options = [];

    /**
     * Performs the existence check
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity from where to extract the fields
     * @param array<string, mixed> $options Options passed to the check,
     * where the `repository` key is required.
     * @throws \RuntimeException When the rule refers to an undefined association.
     * @return bool
     */
    public function __invoke(EntityInterface $entity, array $options): bool
    {
        if (!($entity instanceof \App\Model\Entity\AttachmentsLink)) {
            throw new \RuntimeException('The ExistsInWithModelRule is valid only for AttachmentsLink');
        }

        if (!isset($entity->foreign_id) || is_null($entity->foreign_id) || !is_numeric($entity->foreign_id)) {
            return false;
        }

        $_repository = TableRegistry::get(Inflector::pluralize($entity->class));

        return $_repository->exists(['id' => (int)$entity->foreign_id]);
    }

}