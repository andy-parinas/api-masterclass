<?php

namespace App\Http\Filters\V1;

class TicketFilter extends QueryFilter
{

    protected $sortable = [
        'title', 
        'status', 
        'createdAt' => 'created_at'
    ];

    public function status($value)
    {
        return $this->builder->whereIn('status', explode(",", $value));
    }

    public function title($value)
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeStr);
    }

    /**
     * This needs correction.
     * For single date should be whereBetween $value 00:00:00 $value 23:59:59
     * For two dates entered, the query should be $value[0] 00:00:00 $value[1] 23:59:59
     */
    public function createdAt($value)
    {
        $dates = explode(",", $value);

        if(count($dates) > 1){
            return $this->builder->whereBetween('created_at', $dates);
        }
     
        return $this->builder->where('created_at', $value);
    }

    public function updatedAt($value)
    {
        $dates = explode(",", $value);

        if(count($dates) > 1){
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->where('updated_at', $value);
    }

    public function include($value)
    {
        return $this->builder->with($value);
    }


}