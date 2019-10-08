<?php

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::where('name', 'admin')->firstOrFail();

        $permissions = Permission::all();
        $moderper = $permissions->pluck('id')->all();
        \array_splice($moderper, 40, 2);
        \array_splice($moderper, 24, 8);
        \array_splice($moderper, 19, 3);
        \array_splice($moderper, 12, 3);
        
        $role->permissions()->sync(
            $permissions->pluck('id')->all()
        );
        $role3 = Role::where('name', 'Moderator')->firstOrFail();
        // $role3->permissions()->sync($moderper);
        $role3->permissions()->sync(
            $moderper
        );
        // try{
        // }catch (Exception $e){

        // };
    }
}
