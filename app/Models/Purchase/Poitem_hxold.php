<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;

class Poitem_hxold extends Model
{
    //
    protected $table = 'vpoitem';
	protected $connection = 'sqlsrv';

	public function item() {
        return $this->hasOne('App\Models\Product\Itemp_hxold', 'goods_id', 'item_id');
    }
}
