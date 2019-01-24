<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class ThemeModal extends Model{
    //数据库表名
    protected $table = 'theme';

    protected $primaryKey = 'ThemeId';

    //数据库字段
    protected $fillable = ['ThemeName', 'CreateTime', 'UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}