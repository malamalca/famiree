<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Routing\Router;
use Cake\ORM\Behavior;
use Cake\ORM\Locator\LocatorAwareTrait;

class LoggableBehavior extends Behavior
{
    use LocatorAwareTrait;

    protected array $_defaultConfig = [
        'excludedProperties' => [
            'created', 'modified',
        ],
    ];

    /**
     * Returns id of the logged in user or null when there is none (cli, guests).
     *
     * @return int|string|null
     */
    protected function currentUserId(): int|string|null
    {
        /** @var \Authentication\IdentityInterface|null $identity */
        $identity = Router::getRequest()?->getAttribute('identity');

        return $identity?->getIdentifier();
    }

    /**
     * afterSave event handler
     *
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $entityClass = get_class($entity);
        $entityClass = substr($entityClass, strrpos($entityClass, '\\') + 1);

        $action = $entity->isNew() ? 'add' : 'edit';

        $packed = [];
        if ($action == 'add') {
            $changes = $entity->extract($entity->getVisible());
            $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

            foreach ($changes as $property => $value) {
                $packed[$property] = [
                    'value' => $entity->get($property),
                ];
            }
        }

        if ($action == 'edit') {
            $changes = $entity->extractOriginalChanged($entity->getVisible());
            $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

            if (!empty($changes)) {
                foreach ($changes as $property => $value) {
                    $packed[$property] = [
                        'old' => $value,
                        'value' => $entity->get($property),
                    ];
                }
            }
        }
        if (!empty($packed)) {
            $log = $this->fetchTable('Logs')->newEntity([
                'title' => $entity->get($this->table()->getDisplayField()),
                'class' => $entityClass,
                'foreign_id' => $entity->id,
                'action' => $action,
                'user_id' => $this->currentUserId(),
                'change' => serialize($packed),
            ]);

            $this->fetchTable('Logs')->save($log);
        }
    }

    /**
     * afterDelete event handler
     *
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\Datasource\EntityInterface $entity Entity object
     * @param \ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $entityClass = get_class($entity);
        $entityClass = substr($entityClass, strrpos($entityClass, '\\') + 1);

        $action = 'delete';

        $changes = $entity->extract($entity->getVisible());
        $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

        $packed = [];
        foreach ($changes as $property => $value) {
            $packed[$property] = [
                'value' => $entity->get($property),
            ];
        }

        if (!empty($packed)) {
            $log = $this->fetchTable('Logs')->newEntity([
                'title' => $entity->get($this->table()->getDisplayField()),
                'class' => $entityClass,
                'foreign_id' => $entity->id,
                'action' => $action,
                'user_id' => $this->currentUserId(),
                'change' => serialize($packed),
            ]);

            $this->fetchTable('Logs')->save($log);
        }
    }
}
