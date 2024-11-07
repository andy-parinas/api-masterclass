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
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     * 
     * Notes: The TicketFilter here is instantiated via Dependency Injections
     */
    public function index(Request $request, TicketFilter $filters)
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {

        return new TicketResource(Ticket::create($request->mappedAttributes()));
        
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

        $ticket = Ticket::find($ticket_id);
        if(!$ticket){
            return $this->error("Ticket provided does not exists", Response::HTTP_NOT_FOUND);
        }

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);

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
