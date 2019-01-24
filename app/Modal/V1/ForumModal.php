<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class ForumModal extends Model{
    //数据库表名
    protected $table = 'forum';

    protected $primaryKey = 'ForumId';

    //数据库字段
    protected $fillable = ['UserId', 'ThemeId', 'Content', 'ImageIds', 'CreateTime', 'UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}