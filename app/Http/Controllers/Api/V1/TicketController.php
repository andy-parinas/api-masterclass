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

        // if($this->include('author')){
        //     return TicketResource::collection(Ticket::with('user')->paginate());
        // }


        // return TicketResource::collection(Ticket::paginate());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            
            $user = User::findOrFail($request->input('data.relationships.user.data.id'));

        } catch (ModelNotFoundException $exception) {
           
            return $this->error("User provided does not exists", Response::HTTP_BAD_REQUEST);
        
        }
        
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.user.data.id'),
        ];

        return new TicketResource(Ticket::create($model));

        
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
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        //PATCH
    }

    public function replace(ReplaceTicketRequest $request, $ticket_id)
    {

        $ticket = Ticket::find($ticket_id);
        if(!$ticket){
            return $this->error("Ticket provided does not exists", Response::HTTP_NOT_FOUND);
        }

        $user = User::find($request->input('data.relationships.user.data.id'));
        if(!$user){
            return $this->error("User Id provided does not exists", Response::HTTP_NOT_FOUND);
        }
            
             
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $request->input('data.relationships.user.data.id'),
        ];

        $ticket->update($model);

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
