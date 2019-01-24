<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SacetypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('theme')->insert([
            'ThemeName'=> '主人秀',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '约溜',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '配种',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '走失',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '交友',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '二手',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '晒单',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '其他',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '吐槽',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
        DB::table('theme')->insert([
            'ThemeName'=> '活动',
            'CreateTime'=> '2018-12-19',
            'UpdateTime'=> '2018-12-19'
        ]);
    }
}
