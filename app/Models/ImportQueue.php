<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportQueue extends Model
{
    use HasFactory;

    protected $table = 'import_queue';
    protected $primaryKey = 'id';

    protected $fillable = [ 
        'contents',
        'status',
        'filename',
        'processed_at'
     ];

     protected $casts = [
        'contents' => 'array'
    ];
}
