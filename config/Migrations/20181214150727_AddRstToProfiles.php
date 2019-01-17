<?php
namespace App;

use Migrations\AbstractMigration;

class AddRstToProfiles extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('profiles');
        $table->addColumn('rst', 'string', [
            'default' => null,
            'limit' => 36,
            'null' => true,
            'after' => 'e'
        ]);
        $table->update();
    }
}
