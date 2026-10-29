<?php
declare(strict_types=1);

namespace App\Model\Rule;

use App\Model\Entity\AttachmentsLink;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Inflector;
use RuntimeException;

class ExistsInWithModelRule
{
    use LocatorAwareTrait;

    /**
     * Options for the constructor
     *
     * @var array<string, mixed>
     */
    protected array $_options = [];

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
        if (!($entity instanceof AttachmentsLink)) {
            throw new RuntimeException('The ExistsInWithModelRule is valid only for AttachmentsLink');
        }

        if (!isset($entity->foreign_id) || is_null($entity->foreign_id) || !is_numeric($entity->foreign_id)) {
            return false;
        }

        $_repository = $this->fetchTable(Inflector::pluralize($entity->class));

        return $_repository->exists(['id' => (int)$entity->foreign_id]);
    }
}
