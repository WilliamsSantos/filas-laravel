<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $fillable = [ 
        'title',
        'contents',
        'category_id',
        'exercice_year',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
