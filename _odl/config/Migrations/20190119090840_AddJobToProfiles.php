<?php
use Migrations\AbstractMigration;

class AddJobToProfiles extends AbstractMigration
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
        $table->addColumn('job', 'string', [
            'default' => null,
            'limit' => 250,
            'null' => true,
            'after' => 'plobu'
        ]);
        $table->addColumn('edu', 'string', [
            'default' => null,
            'limit' => 250,
            'null' => true,
            'after' => 'job'
        ]);
        $table->update();
    }
}
