<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Permissions\V1\Abilities;
use App\Policies\V1\TicketPolicy;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends ApiController
{


    protected $policyClass = TicketPolicy::class;

    /**
     * Get All Tickets
     * 
     * Get all the tickets with pagination, optional sorting and filtering
     * 
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denotes descending sort with a minues sign. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: A,C,X,H. No-example
     * @queryParam filter[title] Filter by title. Wildcards are supported. Example: *fix*
     * 
     */
    public function index(Request $request, TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());

    }

    /**
     * Create a Ticket
     * 
     * Creates a new Ticket. Users can only create a ticket for themselves. Managers can create ticket for any users.
     * 
     * @group Managing Tickets
     * 
     */
    public function store(StoreTicketRequest $request)
    {

        try {
            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes()));

        } catch (AuthorizationException $exception) {

            return $this->error("You are not allowed to create a ticket", Response::HTTP_FORBIDDEN);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {   

            $ticket = Ticket::findOrFail($ticket_id);

            if($this->include('author')){
                return new TicketResource($ticket->load('user'));
            }
    
            return new TicketResource($ticket);

        }catch(ModelNotFoundException $exception){
            return $this->error("The Ticket Does not exists", Response::HTTP_NOT_FOUND);
        }

     
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, $ticket_id)
    {

        try {

            $ticket = Ticket::findOrFail($ticket_id);

            $this->isAble('update', $ticket);
            

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            
            return $this->error("Ticket provided does not exists", Response::HTTP_NOT_FOUND);

        }catch(AuthorizationException $exception){

            return $this->error("You are not allowed to updated the ticket", Response::HTTP_FORBIDDEN);
        }    

    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {

        $ticket = Ticket::find($ticket_id);
        if(!$ticket){
            return $this->error("Ticket provided does not exists", Response::HTTP_NOT_FOUND);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {

            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket Successfully Deleted');

        }catch(ModelNotFoundException $exception){
            return $this->error("The Ticket Does not exists", Response::HTTP_NOT_FOUND);
        }
    }
}
