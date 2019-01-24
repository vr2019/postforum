<?php

namespace App\Modal\V1;
use Illuminate\Database\Eloquent\Model;

class RaiseModal extends Model{
    //数据库表名
    protected $table = 'raise';

    protected $primaryKey = 'RaiseId';

    //数据库字段
    protected $fillable = ['ForumId', 'UserId', 'CreateTime', 'UpdateTime'];

    //去除update_at等字段
    public $timestamps = false;
    
}