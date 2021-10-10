<?php
namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Http\Session;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

class LoggableBehavior extends Behavior
{
    protected $_defaultConfig = [
        'excludedProperties' => [
            'created', 'modified'
        ]
    ];

    /**
     * Initialize function
     *
     * @param array $config Config
     * @return void
     */
    public function initialize(array $config)
    {
        // Some initialization code here
    }

    /**
     * afterSave event handler
     *
     * @param Event $event Event object
     * @param EntityInterface $entity Entity object
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $entityClass = get_class($entity);
        $entityClass = substr($entityClass, strrpos($entityClass, '\\') + 1);

        $action = $entity->isNew() ? 'add' : 'edit';

        $packed = [];
        if ($action == 'add') {
            $changes = $entity->extract($entity->visibleProperties());
            $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

            foreach ($changes as $property => $value) {
                $packed[$property] = [
                    'value' => $entity->get($property)
                ];
            }
        }

        if ($action == 'edit') {
            $changes = $entity->extractOriginalChanged($entity->visibleProperties());
            $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

            if (!empty($changes)) {
                foreach ($changes as $property => $value) {
                    $packed[$property] = [
                        'old' => $value,
                        'value' => $entity->get($property)
                    ];
                }
            }
        }
        if (!empty($packed)) {
            $log = TableRegistry::get('Logs')->newEntity([
                'title' => $entity->get($this->getTable()->getDisplayField()),
                'class' => $entityClass,
                'foreign_id' => $entity->id,
                'action' => $action,
                'user_id' => (new Session())->read('Auth.User.id'),
                'change' => serialize($packed)
            ]);

            TableRegistry::get('Logs')->save($log);
        }
    }

    /**
     * afterDelete event handler
     *
     * @param Event $event Event object
     * @param EntityInterface $entity Entity object
     * @param ArrayObject $options Options
     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $entityClass = get_class($entity);
        $entityClass = substr($entityClass, strrpos($entityClass, '\\') + 1);

        $action = 'delete';

        $changes = $entity->extract($entity->visibleProperties());
        $changes = array_diff_key($changes, array_flip($this->_config['excludedProperties']));

        $packed = [];
        foreach ($changes as $property => $value) {
            $packed[$property] = [
                'value' => $entity->get($property)
            ];
        }

        if (!empty($packed)) {
            $log = TableRegistry::get('Logs')->newEntity([
                'title' => $entity->get($this->getTable()->getDisplayField()),
                'class' => $entityClass,
                'foreign_id' => $entity->id,
                'action' => $action,
                'user_id' => (new Session())->read('Auth.User.id'),
                'change' => serialize($packed)
            ]);

            TableRegistry::get('Logs')->save($log);
        }
    }
}
