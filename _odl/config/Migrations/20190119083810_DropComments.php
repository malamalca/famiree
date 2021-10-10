<?php
use Migrations\AbstractMigration;

class DropComments extends AbstractMigration
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
        $table = $this->table('comments');
        $table->drop()->save();

        $table = $this->table('posts');
        $table->removeColumn('slug');
        $table->removeColumn('no_comments');
        $table->removeColumn('allow_comments');
        $table->update();
    }
}
