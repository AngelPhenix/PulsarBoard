<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Board;
use App\Models\Task;
use App\Models\Label;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'title' => 'Refonte Backend & Sécurité API',
            'user_id' => $recruiter->id,
        ]);

        $t1 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Corriger la faille CSRF sur les requêtes AJAX', 'is_completed' => true]);
        $t1->labels()->attach($labelUrgent->id);

        $t2 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Mettre en place le rate limiting sur l’authentification', 'is_completed' => true]);
        $t2->labels()->attach($labelUrgent->id);

        $t3 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Refactoriser les controllers pour utiliser des Form Requests', 'is_completed' => false]);
        $t3->labels()->attach($labelMoyen->id);

        $t4 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Écrire les tests unitaires pour l’authentification Sanctum', 'is_completed' => false]);
        $t4->labels()->attach($labelMoyen->id);

        $t5 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Mettre à jour Laravel vers la dernière version patch', 'is_completed' => false]);
        $t5->labels()->attach($labelFaible->id);

        $t6 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Nettoyer les vieux logs de debug en production', 'is_completed' => true]);
        $t6->labels()->attach($labelFaible->id);

        $t7 = Task::create(['board_id' => $boardBackend->id, 'title' => 'Optimiser les requêtes SQL N+1 sur le dashboard', 'is_completed' => false]);
        $t7->labels()->attach($labelUrgent->id);


        // ==========================================
        // 4. Board 2 : Interface Utilisateur & UI/UX (6 tâches)
        // ==========================================
        $boardFrontend = Board::create([
            'title' => 'Migration Tailwind & Mode Sombre',
            'user_id' => $recruiter->id,
        ]);

        $t8 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Résoudre le bug d’affichage du Kanban sur mobile', 'is_completed' => true]);
        $t8->labels()->attach($labelUrgent->id);

        $t9 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Implémenter le toggle pour le mode sombre (Dark Mode)', 'is_completed' => true]);
        $t9->labels()->attach($labelMoyen->id);

        $t10 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Ajouter des tooltips sur les boutons de suppression', 'is_completed' => false]);
        $t10->labels()->attach($labelFaible->id);

        $t11 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Améliorer le design des cartes de tâches (Drag & Drop)', 'is_completed' => false]);
        $t11->labels()->attach($labelMoyen->id);

        $t12 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Corriger les contrastes de couleurs pour l’accessibilité (WCAG)', 'is_completed' => false]);
        $t12->labels()->attach($labelUrgent->id);

        $t13 = Task::create(['board_id' => $boardFrontend->id, 'title' => 'Mettre à jour les icônes SVG de la barre latérale', 'is_completed' => true]);
        $t13->labels()->attach($labelFaible->id);


        // ==========================================
        // 5. Board 3 : Pipeline DevOps & Déploiement (6 tâches)
        // ==========================================
        $boardDevops = Board::create([
            'title' => 'Pipeline CI/CD & Déploiement Render',
            'user_id' => $recruiter->id,
        ]);

        $t14 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Automatiser le lancement du DemoSeeder au build', 'is_completed' => true]);
        $t14->labels()->attach($labelUrgent->id);

        $t15 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Optimiser la taille du bundle Vite pour la production', 'is_completed' => true]);
        $t15->labels()->attach($labelMoyen->id);

        $t16 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Configurer les variables d’environnement secrètes sur Render', 'is_completed' => true]);
        $t16->labels()->attach($labelUrgent->id);

        $t17 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Mettre en place un script de backup automatique pour SQLite', 'is_completed' => false]);
        $t17->labels()->attach($labelMoyen->id);

        $t18 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Rédiger la documentation d’installation locale dans le README', 'is_completed' => true]);
        $t18->labels()->attach($labelFaible->id);

        $t19 = Task::create(['board_id' => $boardDevops->id, 'title' => 'Vérifier la compatibilité des assets statiques avec le CDN', 'is_completed' => false]);
        $t19->labels()->attach($labelFaible->id);
    }
}