<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Core\Configure;
use App\Model\Table\PostsTable;
use App\Model\Table\ProfilesTable;

class ProfilesController
{
    /**
     * Dashboard action
     *
     * @return void
     */
    public function dashboard()
    {
        $ProfilesTable = new ProfilesTable();
        $PostsTable = new PostsTable();

        //$profiles = $this->paginate($this->Profiles);
        $counts = $ProfilesTable->countGenders();

        $posts = $PostsTable->query("SELECT * FROM posts ORDER BY posts.created DESC LIMIT 5");
        $postsLinks = $ProfilesTable->query('SELECT posts.* FROM posts_links ' .
            'LEFT JOIN posts ON posts_links.foreign_id = profiles.id ' .
            'WHERE posts_links.class="Profile" AND posts.id IN (:posts);',
            
        );

        dd($postsLinks);

        /*$posts = TableRegistry::get('Posts')->find()
            ->select()
            ->contain(['Profiles', 'Creators'])
            ->order(['Posts.created DESC'])
            ->limit(5)
            ->all();
        $logs = [];

        $dates = $this->Profiles->withBirthdays();*/

        $logs = [];

        App::set(compact('counts', 'logs', 'posts', 'postsLinks'));
    }

    /**
     * Change user password
     *
     * @return void
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['password']) && !empty($_POST['old_password'])) {
            $checkPasswd = $this->getAdminPassword();

            if (password_verify($_POST['old_password'], $checkPasswd)) {
                if ($_POST['repeat_password'] === $_POST['password']) {
                    $SettingsTable = new SettingsTable();
                    $setting = $SettingsTable->get('passwd');
                    if ($setting === null) {
                        $setting = $SettingsTable->newEntity(['id' => 'passwd']);
                    }
                    $setting->value = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    if ($SettingsTable->save($setting)) {
                        App::setFlash('Admin password has been changed.');
                        App::redirect('/');
                    } else {
                        App::setFlash('Password change failed.', 'error');
                    }
                } else {
                    App::setFlash('New passwords are not equal.', 'error');
                }
            } else {
                App::setFlash('Old password is invalid.', 'error');
            }
        }
    }

    /**
     * User login function
     *
     * @return void
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['u'])) {
            $profile = (new ProfilesTable())->findByU($_POST['u']);

            if (!empty($profile->p) && password_verify($_POST['p'], $profile->p)) {
                $_SESSION['user'] = $profile;
                App::redirect('/');
            }


            App::setFlash('Invalid username or password', 'error');
        }
    }

    /**
     * User reset password
     *
     * @return void
     */
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['u'])) {
            $profile = (new ProfilesTable())->findByU($_POST['u']);
            $profile->p = password_hash($_POST['p'], PASSWORD_DEFAULT);

            dd((new ProfilesTable())->save($profile));

            App::redirect('/');
        }
    }

    /**
     * Logs user out.
     *
     * @return void
     */
    public function logout()
    {
        unset($_SESSION['user']);

        App::redirect('/');
    }
}
