<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    protected $fillable = [
        'user_id', 'username', 'menu_name', 'table_name', 'data_id', 'ip', 'execution_type'
    ];
}
