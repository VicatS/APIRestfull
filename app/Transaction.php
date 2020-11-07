<?php

namespace App;

use App\Transformers\TransactionTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static truncate()
 * @method static create(array $array)
 * @property mixed product
 * @property mixed buyer_id
 * @property mixed product_id
 * @property mixed id
 * @property mixed created_at
 * @property mixed updated_at
 * @property mixed quantity
 */
class Transaction extends Model
{
    use SoftDeletes;

    public $transformer = TransactionTransformer::class;

    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id'
    ];
    protected $dates = ['deleted_at'];

    public function buyer() {
        return $this->belongsTo(Buyer::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

}
