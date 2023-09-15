<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class Bank
 *
 * @package App\Models
 *
 * @property int $id
 * @property string $bank_name
 * @property string $branch_name
 * @property string $account_name
 * @property string $account_number
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Bank extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
}
