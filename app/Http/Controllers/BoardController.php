<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\delete;

class BoardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Récupère toutes les catégories de l'utilisateur avec leurs boards associés
        $categories = $user->categories()->with('boards')->get();

        // Récupère les boards qui n'ont aucune catégorie (les "Uncategorized")
        $uncategorizedBoards = $user->boards()->whereNull('category_id')->get();

        return view('board.view', [
            'categories' => $categories,
            'uncategorizedBoards' => $uncategorizedBoards,
            'boardList' => $user->boards,
        ]);
    }

    public function options(Board $board)
    {
        $boards = Auth::user()->boards;
        $friends = Auth::user()->friends;

        // On récupère les noms des catégories de l'utilisateur pour les suggestions
        $existingTags = Auth::user()->categories()->pluck('name');

        return view('board.options', [
            'board' => $board,
            'boardList' => $boards,
            'friends' => $friends,
            'existingTags' => $existingTags,
        ]);
    }

    public function welcome()
    {
        // Si l'utilisateur est connecté, on le redirige directement vers sa page de boards
        if (Auth::check()) {
            return redirect()->route('board.view'); // Assure-toi que c'est bien le nom de ta route pour les boards
        }

        // Pour un visiteur non connecté, on affiche la page de bienvenue simple (sans besoin de récupérer les boards)
        return view('welcome');
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
            'name' => ['required', 'max:80'],
            'tag' => ['nullable', 'string', 'max:50'],
            'tasks' => ['nullable', 'array'],
            'tasks.*' => ['nullable', 'string', 'max:255'],
        ]);

        // Gestion de la catégorie à partir du champ "tag" du formulaire de création
        $categoryId = null;
        if (!empty($attributes['tag'])) {
            $category = Auth::user()->categories()->firstOrCreate([
                'name' => trim($attributes['tag'])
            ]);
            $categoryId = $category->id;
        }

        $boardAttributes = [
            'name' => $attributes['name'],
            'category_id' => $categoryId,
            'owner_id' => Auth::id(),
        ];
        
        $board = Board::create($boardAttributes);

        // Attaching the board_id to the user_id in the pivot table "board_user"
        $board->users()->attach(Auth::id());

        // Si des tâches initiales ont été renseignées
        if (!empty($attributes['tasks'])) {
            foreach ($attributes['tasks'] as $taskName) {
                if (!empty(trim($taskName))) {
                    $board->tasks()->create([
                        'name' => trim($taskName),
                        'is_completed' => false,
                    ]);
                }
            }
        }

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

    public function updateTag(Request $request, Board $board)
    {
        $request->validate([
            'tag' => 'nullable|string|max:255',
        ]);

        $categoryId = null;

        if ($request->filled('tag')) {
            // On cherche ou on crée la catégorie pour l'utilisateur connecté
            $category = Auth::user()->categories()->firstOrCreate([
                'name' => trim($request->tag)
            ]);
            $categoryId = $category->id;
        }

        // On met à jour le board avec le category_id
        $board->update([
            'category_id' => $categoryId
        ]);

        return response()->json(['success' => true]);
    }

    public function settings(Board $board)
    {
        $friends = Auth::user()->friends;

        // Idem ici
        $existingTags = Auth::user()->categories()->pluck('name');

        return view('board.options', compact('board', 'friends', 'existingTags'));
    }

    public function toggleBoardTags()
    {
        $user = Auth::user();
        $user->update([
            'show_board_tags' => ! $user->show_board_tags
        ]);

        return back();
    }

    public function toggleTaskTags(Board $board)
    {
        $board->update([
            'show_task_tags' => ! $board->show_task_tags
        ]);

        return back();
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Auth::user()->categories()->create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Catégorie créée avec succès !');
    }

    public function updateCategoryManually(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'name' => $category->name
        ]);
    }

    public function destroyCategory(Category $category)
    {
        // Sécurité : Vérifie que la catégorie appartient bien à l'utilisateur connecté
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        // Empêche la suppression si elle contient des boards
        if ($category->boards()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient encore des boards.');
        }

        $category->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return redirect('/boards');
    }
}
