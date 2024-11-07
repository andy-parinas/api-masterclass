<?php

namespace App\Http\Filters\V1;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter 
{
    protected $builder;
    protected $request;
    protected $sortable = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * How this works:
     * the filter method here is to change the way the query parameter for filtering is called
     * example the query parameter is ?filter[status]=C
     * 
     * The apply method will loop into the request query parameter and will encounter the filter. This is
     * what the query parameter looks like [filter => [status => C], include=>user]
     * 
     * When the apply functions loops into the request query parameter, it will encounter the filter,
     * the filter method exist therefore it is called. Inside the filter method, the filter value loops
     * and will encounter the status. the status exists in the subclass (TicketFilter) and is then called
     * 
     * The status function called the query builder.
     * 
     */
    public function filter($arr)
    {
        foreach($arr as $key => $value){
            if(method_exists($this, $key)){
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if(method_exists($this, $key)){
                $this->$key($value);
            }
        }

        return $builder;
    }


    protected function sort($value)
    {
        $sortAttributes = explode(',', $value);
        
        foreach($sortAttributes as $sortAttribute){
            $direction = 'asc';

            if(strpos($sortAttribute, '-') === 0){
                $direction = 'desc';
                $sortAttribute = substr($sortAttribute, 1);
            }

            /**
             * The array_key_exists($sortAttribute, $this->sortable) is for providing a sortable value that is 
             * a key-value pair. example createdAt => created_at
             */
            if(!in_array($sortAttribute, $this->sortable) && !array_key_exists($sortAttribute, $this->sortable)){
                continue;
            }

            $columnName = $this->sortable[$sortAttribute] ?? null;

            if($columnName === null){
                $columnName = $sortAttribute;
            }

            $this->builder->orderBy($columnName, $direction);
        }
    }

}