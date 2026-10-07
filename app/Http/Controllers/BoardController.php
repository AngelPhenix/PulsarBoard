<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function settings(Board $board)
    {
        // On récupère toutes les infos nécessaires d'un coup
        $boards = Auth::user()->boards;
        $friends = Auth::user()->friends;
        $existingTags = Auth::user()->categories()->pluck('name');

        return view('board.options', [
            'board' => $board,
            'boardList' => $boards, // Indispensable pour la sidebar !
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
            'boardList' => Auth::user()->boards,
            'existingTags' => Auth::user()->categories()->pluck('name'),
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
            $normalizedTagName = mb_strtolower(trim($attributes['tag']));
            
            $category = Auth::user()->categories()->firstOrCreate([
                'name' => $normalizedTagName
            ]);
            $categoryId = $category->id;
        }

        $boardAttributes = [
            'name' => $attributes['name'],
            'category_id' => $categoryId,
            'owner_id' => Auth::id(),
            'invite_token' => Str::uuid(),
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

    public function rename(Request $request, Board $board)
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:80']
        ]);

        $board->update([
            'name' => $attributes['name']
        ]);

        return redirect()->back()->with(['success' => 'Board renamed successfully.']);
    }

    public function updateTag(Request $request, Board $board)
    {
        $request->validate([
            'tag' => 'nullable|string|max:255',
        ]);

        $categoryId = null;

        if ($request->filled('tag')) {
            // Normalisation stricte pour SQLite
            $normalizedName = mb_strtolower(trim($request->tag));

            $category = Auth::user()->categories()->firstOrCreate([
                'name' => $normalizedName
            ]);
            
            $categoryId = $category->id;
        }

        $board->update([
            'category_id' => $categoryId
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'name' => $board->category?->name
            ]);
        }

        return redirect()->back()->with(['success' => 'Tag updated successfully.']);
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

        // Normalisation : on nettoie les espaces et on met tout en minuscules
        $normalizedName = mb_strtolower(trim($request->name));

        $category = Auth::user()->categories()->firstOrCreate(
            ['name' => $normalizedName],
            ['name' => $normalizedName] // Tu peux garder le nom original si tu ajoutes un champ 'display_name', mais tout en minuscules évite 100% des doublons
        );

        return back()->with('success', 'Catégorie créée avec succès !');
    }

    public function updateCategoryManually(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $normalizedName = mb_strtolower(trim($request->name));

        // On vérifie si l'utilisateur a déjà une *autre* catégorie avec ce nom normalisé
        $exists = Auth::user()->categories()
            ->where('name', $normalizedName)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Une catégorie avec ce nom existe déjà.'
            ], 422);
        }

        $category->update([
            'name' => $normalizedName
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

    public function joinBoard($token)
    {
        $board = Board::where('invite_token', $token)->firstOrFail();

        if (!$board->users->contains(Auth::id()) && $board->owner_id !== Auth::id()) {
            $board->users()->attach(Auth::id());
        }

        return redirect()->route('board.show', $board->id)
            ->with('success', 'You have successfully joined the board!');
    }

    public function leaveBoard(Board $board)
    {
        // Sécurité : Un propriétaire ne peut pas "quitter" sa propre board de cette façon
        if ($board->owner_id === Auth::id()) {
            return redirect()->route('board.show', $board->id)
                ->with('error', 'As the owner, you cannot leave your own board.');
        }

        // On retire l'utilisateur de la table pivot
        $board->users()->detach(Auth::id());

        return redirect('/')->with('success', 'You have left the board successfully.');
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return redirect('/boards');
    }
}
