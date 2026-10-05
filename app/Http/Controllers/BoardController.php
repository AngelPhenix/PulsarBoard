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
        $user = Auth::user();
        $boards = $user->boards;

        // 1. Récupère les tags déjà présents sur les boards
        $boardTags = $boards->pluck('tag')->filter()->unique();

        // 2. Récupère les catégories créées manuellement (session)
        $customCategories = collect(session('custom_categories', []));

        // 3. Fusionne le tout pour avoir la liste exhaustive
        $allTags = $boardTags->merge($customCategories)->unique();

        // 4. Reconstruit proprement le $groupedBoards avec les catégories vides
        $groupedBoards = collect();
        
        foreach ($allTags as $tag) {
            $groupedBoards[$tag] = $boards->where('tag', $tag);
        }
        
        // Gère les éléments "Uncategorized" (tag vide ou null)
        $groupedBoards[''] = $boards->filter(fn($b) => empty($b->tag));

        // Optionnel : trie les clés par ordre alphabétique en gardant 'Uncategorized' à la fin si tu veux
        $groupedBoards = $groupedBoards->sortKeysUsing(function ($a, $b) {
            if ($a === '') return 1;
            if ($b === '') return -1;
            return strcasecmp($a, $b);
        });

        return view('board.view', [
            'boards' => $boards,
            'boardList' => $boards,
            'groupedBoards' => $groupedBoards
        ]);
    }

    public function options(Board $board)
    {
        $boards = Auth::user()->boards;
        $friends = Auth::user()->friends;

        // Récupère tous les tags uniques de l'utilisateur (hors null/vides)
        $existingTags = Auth::user()->boards()
            ->whereNotNull('tag')
            ->where('tag', '!=', '')
            ->distinct()
            ->pluck('tag');

        return view('board.options', [
            'board' => $board,
            'boardList' => $boards,
            'friends' => $friends,
            'existingTags' => $existingTags, // On passe les tags à la vue
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

        // Adding the ID of the current logged in user as "owner_id"
        $boardAttributes = [
            'name' => $attributes['name'],
            'tag' => !empty($attributes['tag']) ? trim($attributes['tag']) : null,
            'owner_id' => Auth::id(),
        ];
        
        $board = Board::create($boardAttributes);

        // Attaching the board_id to the user_id in the pivot table "board_user"
        $board->users()->attach(Auth::id());

        // Si des tâches initiales ont été renseignées, on les crée et on les rattache au board
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
        // 1. Validation du tag
        $attributes = $request->validate([
            'tag' => ['nullable', 'string', 'max:50'],
        ]);

        // 2. Mise à jour de la board (nettoyage des espaces ou null si vide)
        $board->update([
            'tag' => !empty($attributes['tag']) ? trim($attributes['tag']) : null,
        ]);

        // 3. Réponse adaptée selon le type de requête
        if ($request->expectsJson()) {
            // Si ça vient du Drag & Drop (AJAX / Fetch)
            return response()->json(['success' => true]);
        }

        // Sinon, si c'est un formulaire classique (ex: page de settings)
        return back()->with('board_renamed', 'Board tag updated successfully!');
    }

    public function settings(Board $board)
    {
        // 1. Récupère la liste des amis de l'utilisateur connecté (pour le select des collaborateurs)
        $friends = auth()->user()->friends; // Adapte selon le nom de ta relation (ex: friends(), or similar)

        // 2. Récupère tous les tags uniques existants pour les suggestions de la vue
        $existingTags = Board::whereNotNull('tag')
            ->where('tag', '!=', '')
            ->distinct()
            ->pluck('tag');

        // 3. Retourne la vue avec toutes les variables nécessaires
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
        $request->validate([
            'tag' => 'required|string|max:255',
        ]);

        // On récupère les catégories en session (ou on initialise un tableau)
        $categories = session('custom_categories', []);

        // Si le tag n'existe pas encore, on l'ajoute
        if (!in_array($request->tag, $categories)) {
            $categories[] = $request->tag;
            session(['custom_categories' => $categories]);
        }

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return redirect('/boards');
    }
}
