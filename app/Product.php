<?php

namespace App;

use App\Transformers\ProductTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static truncate()
 * @method static create(array $data)
 * @property mixed transactions
 * @property mixed categories
 * @property mixed seller
 * @property mixed quantity
 * @property mixed id
 * @property mixed image
 * @property mixed seller_id
 */
class Product extends Model
{
    use SoftDeletes;

    const PRODUCTO_DISPONIBLE = 'disponible';
    const PRODUCTO_NO_DISPONIBLE = 'no disponible';

    public $transformer = ProductTransformer::class;

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id'
    ];
    protected $dates = ['deleted_at'];
    protected $hidden = [ 'pivot'];

   public function estaDisponible() {
       return $this->status == Product::PRODUCTO_DISPONIBLE;
   }

   public function seller() {
       return $this->belongsTo(Seller::class);
   }

   public function categories() {
       return $this->belongsToMany(Category::class);
   }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }

}
