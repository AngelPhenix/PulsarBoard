<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\delete;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Auth::user()->boards;

        return view('board.view', [
            'boards' => $boards,
            'boardList' => $boards
        ]);
    }

    public function options(Board $board)
    {
        $boards = Auth::user()->boards;
        $friends = Auth::user()->friends;

        return view('board.options', [
            'board' => $board,
            'boardList' => $boards,
            'friends' => $friends
        ]);
    }

    public function welcome()
    {
        // On récupère l'utilisateur proprement, et on utilise ?-> pour éviter tout plantage si null
        $user = Auth::user();
        $boards = $user ? $user->boards()->get() : collect();

        return view('welcome', [
            'boards' => $boards,
            'boardList' => $boards
        ]);
    }

    public function create()
    {
        return view('board.create', [
            'boardList' => Auth::user()->boards
        ]);
    }

    // When board is created, add the owner to the board_user pivot table
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => ['required', 'max:80']
        ]);

        // Adding the ID of the current logged in user as "owner_id"
        $attributes['owner_id'] = Auth::id();
        
        $board = Board::create($attributes);

        // Attaching the board_id to the user_id in the pivot table "board_user"
        $board->users()->attach(Auth::id());

        return redirect('/boards');
    }

    // When adding new collaborator, check for his email and add it to the board_user pivot table
    public function addFriend(Request $request, Board $board)
    {
        $attributes = $request->validate([
            'mail' => ['required', 'email']
        ]);

        $user = User::where('email', $attributes['mail'])->first();

        if(!$user){
            return redirect()->back()->with(['user_added' => "The mail doesn't corresponds to any user."]);
        }

        if($board->users->doesntContain($user)) {
            $board->users()->attach($user);
        }else{
            return redirect()->back()->with(['user_added' => "This user is already a collaborator on this board"]);
        }

        return redirect('/board/'. $board->id);
    }

    public function rename(Request $request, Board $board)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:80']
        ]);

        $board->update([
            'name' => $attributes['name']
        ]);

        return redirect()->back()->with(['board_renamed' => 'Board renamed successfully.']);
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return redirect('/boards');
    }
}
