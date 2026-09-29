<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'template-category-list',
            'template-category-create',
            'template-category-edit',
            'template-category-delete',
            'animated-template-list',
            'animated-template-create',
            'animated-template-edit',
            'animated-template-delete',
            'quote-category-list',
            'quote-category-create',
            'quote-category-edit',
            'quote-category-delete',
            'quotes-list',
            'quotes-create',
            'quotes-edit',
            'quotes-delete',
            'music-category-list',
            'music-category-create',
            'music-category-edit',
            'music-category-delete',
            'musics-list',
            'musics-create',
            'musics-edit',
            'musics-delete',
            'banner-category-list',
            'banner-category-create',
            'banner-category-edit',
            'banner-category-delete',
            'banners-list',
            'banners-create',
            'banners-edit',
            'banners-delete',
            'settings',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'notification',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete'
        ];  
        
        foreach ($permissions as $key => $value) {
            Permission::create([
                'name' => $value
            ]);
        }
    }
}
