<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\V1\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorTicketsController extends ApiController
{
    public function index($user_id, TicketFilter $filters)
    {
        return TicketResource::collection(
            Ticket::where('user_id',$user_id)
                ->filter($filters)
                ->paginate()
        );    
    }

    public function store(User $user, StoreTicketRequest $request)
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $user->id,
        ];

        return new TicketResource(Ticket::create($model));
    }


    public function destroy($user_id, $ticket_id)
    {
        try {
            
            $user = User::findOrFail($user_id);
            $ticket = Ticket::findOrFail($ticket_id);

            if($ticket->user_id === $user->id){

                $ticket->delete();
                return $this->ok('Ticket Successfully Deleted');
            }

            return $this->error("The Ticket Does not exists", Response::HTTP_NOT_FOUND);

        }catch(ModelNotFoundException $exception){
            return $this->error("The Ticket Does not exists", Response::HTTP_NOT_FOUND);
        }
    }
}
