<?php
namespace presentapp\model;

class Item extends \Illuminate\Database\Eloquent\Model
{

    protected 	$table		= 'Item';
    protected 	$primaryKey	= 'id';
    public		$timestamps	= false;

    
    public function liste_item(){

        return $this->belongsTo('presentapp\model\ListeItem', 'id');

    }
}