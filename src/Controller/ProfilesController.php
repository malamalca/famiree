<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\App;
use App\Core\Configure;
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
        /*$profiles = $this->paginate($this->Profiles);
        $counts = $this->Profiles->countGenders();
        $posts = TableRegistry::get('Posts')->find()
            ->select()
            ->contain(['Profiles', 'Creators'])
            ->order(['Posts.created DESC'])
            ->limit(5)
            ->all();
        $logs = [];

        $dates = $this->Profiles->withBirthdays();*/

        //App::set(compact('profiles', 'counts', 'posts', 'logs', 'dates'));
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username'])) {
            $profile = (new ProfilesTable())->findByUsername($_POST['username']);

            if (!empty($profile->p) && password_verify($_POST['password'], $profile->p)) {
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['username'])) {
            $profile = (new ProfilesTable())->findByUsername($_POST['username']);
            $profile->p = password_hash($_POST['password'], PASSWORD_DEFAULT);

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
