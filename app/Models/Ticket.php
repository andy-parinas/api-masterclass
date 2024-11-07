<?php

namespace App\Models;

use App\Http\Filters\V1\QueryFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\TicketFactory> */
    use HasFactory;
    
    protected $fillable = ['title', 'description', 'status', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * How this function works:
     * The QueryFilter is a parameter for the Local Scope filter. 
     * The apply method is where the Query is Build
     * 
     * In the QueryFilter abstract class, it takes in a Request from the contructor via Dependency Injections.
     * the apply methods loops into the request parameters and check if the Key exist as a method of the QueryFilter and
     * its subclasses. Example is TicketFilter, it has a method of status. If a status a passed a parameter 
     * from the request it is called. The status uses the builder to build the query.
     * 
     */
    public function scopeFilter(Builder $builder, QueryFilter $filters)
    {
        return $filters->apply($builder);
    }

    
}
