<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleLog extends Model
{
    use HasFactory;
    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'log_mysql';

    protected $table = 'logging_db';
    protected $guarded = ['id'];
}
