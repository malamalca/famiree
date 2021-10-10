<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Core\Table;

class EventsTable extends Table
{
    public $entityName = '\App\Model\Entity\Event';
    public $tableName = 'events';
    public $fieldList = ['id', 'kind', 'datestamp'];

    /**
     * @param \App\Model\Entity\Event $event Event Entity.
     * @return bool
     */
    public function validate($event)
    {
        $event->hasErrors = false;

        $event->hasErrors = empty($event->id);

        return !$event->hasErrors;
    }
}
