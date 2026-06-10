<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('role_has_permissions')->truncate();
        DB::table('permissions')->truncate();

        $permissions = [
            ['name' => 'goods.view'],
            ['name' => 'goods.create'],
            ['name' => 'goods.update'],
            ['name' => 'goods.delete'],
            ['name' => 'goods-transaction.view'],
            ['name' => 'goods-transaction.create'],
            ['name' => 'goods-transaction.update'],
            ['name' => 'goods-transaction.delete'],
            ['name' => 'user.view'],
            ['name' => 'user.create'],
            ['name' => 'user.update'],
            ['name' => 'user.delete'],
            ['name' => 'goods-transaction-category.view'],
            ['name' => 'goods-transaction-category.create'],
            ['name' => 'goods-transaction-category.update'],
            ['name' => 'goods-transaction-category.delete'],
            ['name' => 'goods-category.view'],
            ['name' => 'goods-category.create'],
            ['name' => 'goods-category.update'],
            ['name' => 'goods-category.delete'],
            ['name' => 'supplier.view'],
            ['name' => 'supplier.create'],
            ['name' => 'supplier.update'],
            ['name' => 'supplier.delete'],
            ['name' => 'shipper.view'],
            ['name' => 'shipper.create'],
            ['name' => 'shipper.update'],
            ['name' => 'shipper.delete'],
            ['name' => 'subscribe-package.view'],
            ['name' => 'subscribe-package.create'],
            ['name' => 'subscribe-package.update'],
            ['name' => 'subscribe-package.delete'],
            ['name' => 'payment-gateway.manage'],
            ['name' => 'worker.scan'],
        ];

        $superAdmin = Role::where('name', 'Super Admin')->first();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
            $superAdmin->givePermissionTo($permission['name']);
        }

        // Workers only need to view goods and create transactions via the
        // dedicated scan station.
        $worker = Role::where('name', 'Worker')->first();
        if ($worker) {
            $worker->givePermissionTo([
                'goods.view',
                'goods-transaction.view',
                'goods-transaction.create',
                'worker.scan',
            ]);
        }
    }
}
