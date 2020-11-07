<?php

namespace App;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static truncate()
 * @method static create(array $all)
 * @property mixed products
 * @property mixed id
 */
class Category extends Model
{
    use SoftDeletes;

    public $transformer = CategoryTransformer::class;

    protected $fillable = [
        'name',
        'description'
    ];
    protected $dates = ['deleted_at'];
    protected $hidden = [ 'pivot'];

    // BelongsToMany => pertenece a muchos
    public function products() {
        return $this->belongsToMany(Product::class);
    }
}
