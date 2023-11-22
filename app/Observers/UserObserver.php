<?php

namespace App\Observers;

use App\Models\Actionlog;
use App\Models\User;
use Auth;

class UserObserver
{
    /**
     * Listen to the User updating event. This fires automatically every time an existing asset is saved.
     *
     * @param  User  $user
     * @return void
     */
    public function updating(User $user)
    {

        $changed = [];
        foreach ($user->getRawOriginal() as $key => $value) {

            if ($user->getRawOriginal()[$key] != $user->getAttributes()[$key]) {

                $changed[$key]['old'] = $user->getRawOriginal()[$key];
                $changed[$key]['new'] = $user->getAttributes()[$key];

                // Do not store the hashed password in changes
                if ($key == 'password') {
                    $changed['password']['old'] = '*************';
                    $changed['password']['new'] = '*************';
                }

                // Do not store last login in changes
                if ($key == 'last_login') {
                    unset($changed['last_login']);
                    unset($changed['last_login']);
                }

                if ($key == 'permissions') {
                    unset($changed['permissions']);
                    unset($changed['permissions']);
                }
            }
        }

        $logAction = new Actionlog();
        $logAction->item_type = User::class;
        $logAction->item_id = $user->id;
        $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->target_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->user_id = Auth::id();
        $logAction->log_meta = json_encode($changed);
        $logAction->logaction('update');
    }

    /**
     * Listen to the User created event, and increment
     * the next_auto_tag_base value in the settings table when i
     * a new asset is created.
     *
     * @param  User $user
     * @return void
     */
    public function created(User $user)
    {
        $logAction = new Actionlog();
        $logAction->item_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->item_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->user_id = Auth::id();
        $logAction->logaction('create');
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User $user
     * @return void
     */
    public function deleting(User $user)
    {
        $logAction = new Actionlog();
        $logAction->item_type = User::class;
        $logAction->item_id = $user->id;
        $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->target_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->user_id = Auth::id();
        $logAction->logaction('delete');
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User $user
     * @return void
     */
    public function restoring(User $user)
    {
        $logAction = new Actionlog();
        $logAction->item_type = User::class;
        $logAction->item_id = $user->id;
        $logAction->target_type = User::class; // can we instead say $logAction->item = $asset ?
        $logAction->target_id = $user->id;
        $logAction->created_at = date('Y-m-d H:i:s');
        $logAction->user_id = Auth::id();
        $logAction->logaction('restore');
    }


}
