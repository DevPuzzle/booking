<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarkAsRead extends Model
{
    protected $table = 'markasread';
    protected $primaryKey = ['user_id', 'page_id'];
}
