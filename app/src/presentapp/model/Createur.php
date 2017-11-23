<?php
namespace presentapp\model;

class Createur extends \Illuminate\Database\Eloquent\Model
{

    protected 	$table		= 'Createur';
    protected 	$primaryKey	= 'id';
    public		$timestamps	= false;

    public function listes(){

        return $this->hasMany('presentapp\model\Liste', 'createur');

    }

}