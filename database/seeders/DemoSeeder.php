<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Board;
use App\Models\Task;
use App\Models\Label;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Créer le faux compte recruteur
        $recruiter = User::firstOrCreate(
            ['email' => 'recruteur@test.com'],
            [
                'username' => 'recruteur',
                'password' => Hash::make('test_recrut'),
            ]
        );

        // 2. Créer des labels/tags colorés spécifiques
        $labelUrgent = Label::create([
            'name' => 'Urgent', 
            'color' => '#ef4444', 
            'user_id' => $recruiter->id
        ]);
        
        $labelMoyen = Label::create([
            'name' => 'Moyen', 
            'color' => '#ff922c', 
            'user_id' => $recruiter->id
        ]);

        $labelFaible = Label::create([
            'name' => 'Faible', 
            'color' => '#73ee21', 
            'user_id' => $recruiter->id
        ]);

        // ==========================================
        // 3. Board 1 : Refonte Backend API & Sécurité (7 tâches)
        // ==========================================
        $boardBackend = Board::create([
            'name' => 'Refonte Backend & Sécurité API',
            'owner_id' => $recruiter->id,
            'tag' => 'Travail',
            'show_task_tags' => true,
        ]);
        $boardBackend->users()->attach($recruiter->id);


        $t1 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Corriger la faille CSRF sur les requêtes AJAX',]);
        $t1->labels()->attach($labelUrgent->id);

        $t2 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Mettre en place le rate limiting sur l’authentification']);
        $t2->labels()->attach($labelUrgent->id);

        $t3 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Refactoriser les controllers pour utiliser des Form Requests']);
        $t3->labels()->attach($labelMoyen->id);

        $t4 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Écrire les tests unitaires pour l’authentification Sanctum']);
        $t4->labels()->attach($labelMoyen->id);

        $t5 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Mettre à jour Laravel vers la dernière version patch']);
        $t5->labels()->attach($labelFaible->id);

        $t6 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Nettoyer les vieux logs de debug en production']);
        $t6->labels()->attach($labelFaible->id);

        $t7 = Task::create(['board_id' => $boardBackend->id, 'name' => 'Optimiser les requêtes SQL N+1 sur le dashboard']);
        $t7->labels()->attach($labelUrgent->id);


        // ==========================================
        // 4. Board 2 : Interface Utilisateur & UI/UX (6 tâches)
        // ==========================================
        $boardFrontend = Board::create([
            'name' => 'Migration Tailwind & Mode Sombre',
            'owner_id' => $recruiter->id,
            'tag' => 'Travail',
            'show_task_tags' => false,
        ]);
        $boardFrontend->users()->attach($recruiter->id);

        $t8 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Résoudre le bug d’affichage du Kanban sur mobile']);
        $t8->labels()->attach($labelUrgent->id);

        $t9 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Implémenter le toggle pour le mode sombre (Dark Mode)']);
        $t9->labels()->attach($labelMoyen->id);

        $t10 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Ajouter des tooltips sur les boutons de suppression']);
        $t10->labels()->attach($labelFaible->id);

        $t11 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Améliorer le design des cartes de tâches (Drag & Drop)']);
        $t11->labels()->attach($labelMoyen->id);

        $t12 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Corriger les contrastes de couleurs pour l’accessibilité (WCAG)']);
        $t12->labels()->attach($labelUrgent->id);

        $t13 = Task::create(['board_id' => $boardFrontend->id, 'name' => 'Mettre à jour les icônes SVG de la barre latérale']);
        $t13->labels()->attach($labelFaible->id);


        // ==========================================
        // 5. Board 3 : Pipeline DevOps & Déploiement (6 tâches)
        // ==========================================
        $boardDevops = Board::create([
            'name' => 'Pipeline CI/CD & Déploiement Render',
            'owner_id' => $recruiter->id,
            'show_task_tags' => true,
        ]);
        $boardDevops->users()->attach($recruiter->id);

        $t14 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Automatiser le lancement du DemoSeeder au build']);
        $t14->labels()->attach($labelUrgent->id);

        $t15 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Optimiser la taille du bundle Vite pour la production']);
        $t15->labels()->attach($labelMoyen->id);

        $t16 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Configurer les variables d’environnement secrètes sur Render']);
        $t16->labels()->attach($labelUrgent->id);

        $t17 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Mettre en place un script de backup automatique pour SQLite']);
        $t17->labels()->attach($labelMoyen->id);

        $t18 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Rédiger la documentation d’installation locale dans le README']);
        $t18->labels()->attach($labelFaible->id);

        $t19 = Task::create(['board_id' => $boardDevops->id, 'name' => 'Vérifier la compatibilité des assets statiques avec le CDN']);
        $t19->labels()->attach($labelFaible->id);
    }
}