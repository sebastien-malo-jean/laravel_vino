<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bouteille;
use App\Models\Cellier;
use App\Models\BouteilleHasCellier;


class BouteilleHasCellierController extends Controller
{
    // Page d'accueil de la liste des bouteilles dans les celliers
    public function index()
    {
        // Page courante :
        $pageCourante = 'bouteilleHasCellierIndex';
        $bouteillesHasCelliers = BouteilleHasCellier::all();

        return view('bouteille_has_cellier.index', compact('bouteillesHasCelliers', 'pageCourante'));
    }

    // Page de création d'une bouteille dans un cellier
    public function create()
    {
        // Page courante :
        $pageCourante = 'bouteilleHasCellierCreate';
        $bouteilles = Bouteille::all();
        // $les cellier de l'utilisateur
        $celliers = Cellier::where('user_id', auth()->id())->get();

        if ($celliers->isEmpty()) {
            return redirect()->route('celliers.index')->with('error', 'Vous devez d\'abord créer un cellier.');
        }

        return view('bouteille_has_cellier.create', compact('bouteilles', 'celliers', 'pageCourante'));
    }

    // Fonction pour stocker une bouteille dans un cellier
    public function store(Request $request)
    {
        $request->validate([
            'bouteille_id' => 'required|exists:bouteilles,id',
            'cellier_id' => 'required|exists:celliers,id',
            'quantite' => 'required|integer|min:1',
            'favoris' => 'boolean',
        ]);

        // Vérification si la bouteille est déjà dans ce cellier
        $existing = BouteilleHasCellier::where('bouteille_id', $request->bouteille_id)
            ->where('cellier_id', $request->cellier_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Cette bouteille est déjà dans ce cellier.');
        }

        // Création de l'enregistrement
        BouteilleHasCellier::create($request->all());

        // Redirige vers la page du cellier spécifique après l'ajout
        return redirect()->route('cellier_bouteilles.cellier.bouteilles', ['cellier_id' => $request->cellier_id])
            ->with('success', 'Bouteille ajoutée au cellier avec succès.');
    }

    // Page d'édition d'une bouteille dans un cellier
    public function edit($cellier_id, $bouteille_id)
    {
        // Page courante :
        $pageCourante = 'bouteilleHasCellierEdit';
        $bouteilleHasCellier = BouteilleHasCellier::where('bouteille_id', $bouteille_id)
            ->where('cellier_id', $cellier_id)
            ->first();

        $bouteilles = Bouteille::all();
        $celliers = Cellier::all();

        return view('bouteille_has_cellier.edit', compact('bouteilleHasCellier', 'bouteilles', 'celliers', 'pageCourante'));
    }

    // Fonction pour mettre à jour une bouteille dans un cellier
    public function update(Request $request, $cellier_id, $bouteille_id)
    {
        $request->validate([
            'quantite' => 'required|integer|min:1',
            'favoris' => 'boolean',
        ]);

        $bouteilleHasCellier = BouteilleHasCellier::where('bouteille_id', $bouteille_id)
            ->where('cellier_id', $cellier_id)
            ->first();

        if (!$bouteilleHasCellier) {
            return redirect()->route('cellier_bouteilles.index')->with('error', 'Cette bouteille n\'existe pas dans ce cellier.');
        }

        // Mise à jour
        $bouteilleHasCellier->update($request->all());

        return redirect()->route('cellier_bouteilles.index')->with('success', 'Bouteille mise à jour avec succès.');
    }

    // Fonction pour supprimer une bouteille dans un cellier
    public function destroy($cellier_id, $bouteille_id)
    {
        $deleted = BouteilleHasCellier::where('bouteille_id', $bouteille_id)
            ->where('cellier_id', $cellier_id)
            ->delete(); // <- ici : on supprime directement

        if ($deleted) {
            return redirect()->route('cellier_bouteilles.cellier.bouteilles', ['cellier_id' => $cellier_id])
                ->with('success', 'Bouteille supprimée du cellier avec succès.');
        } else {
            return redirect()->route('cellier_bouteilles.cellier.bouteilles', ['cellier_id' => $cellier_id])
                ->with('error', 'Cette bouteille n\'existe pas dans ce cellier.');
        }
    }

    // Fonction pour montrer toutes les bouteilles d'un cellier
    public function bouteillesDansCellier($cellier_id, Request $request)
    {
        $user = Auth::user();
        $demande = $request->input('requete');
        $type = $request->input('type');
        $format = $request->input('format');
        $pays = $request->input('pays');
        $pageCourante = 'bouteillesParCellier';

        // Vérifie si ce cellier appartient bien à l'utilisateur connecté
        $cellier = Cellier::where('id', $cellier_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$cellier) {
            return redirect()->route('accueil')->with('error', 'Ce cellier ne vous appartient pas.');
        }

        $bouteillesQuery = BouteilleHasCellier::with(['bouteille', 'cellier'])
            ->where('cellier_id', $cellier_id)
            ->join('bouteilles', 'bouteille_id', '=', 'bouteilles.id')
            ->select('bouteille_has_celliers.*'); // Pour éviter de surcharger les attributs

        // Si recherche textuelle
        if (!empty($demande)) {
            $champs = ['nom', 'format', 'pays', 'type', 'code_saq'];
            $bouteillesQuery->where(function ($query) use ($demande, $champs) {
                foreach ($champs as $champ) {
                    $query->orWhere($champ, 'like', "%{$demande}%");
                }
            });
        }

        // Filtres
        if ($type) {
            $bouteillesQuery->where('type', $type);
        }

        if ($format) {
            $bouteillesQuery->where('format', $format);
        }

        if ($pays) {
            $bouteillesQuery->where('pays', $pays);
        }

        // Pagination
        $bouteilles = $bouteillesQuery->paginate(50)->withQueryString();

        session()->put('id_cellier', $cellier_id);

        // Pour générer la liste de pays dynamiquement (utile pour ton select dans la vue)
        $listePays = Bouteille::trouveNomDePays();

        return view('bouteille_has_cellier.par_cellier', compact(
            'bouteilles',
            'cellier_id',
            'pageCourante',
            'cellier',
            'demande',
            'listePays'
        ));
    }

    // Fonction pour montrer toutes les bouteilles de l'utilisateur
    public function bouteillesUtilisateur($user_id)
    {
        // Page courante :
        $pageCourante = 'bouteillesParUtilisateur';
        $bouteillesUtilisateur = BouteilleHasCellier::with('bouteille')
            ->whereHas('cellier', function ($query) use ($user_id) {
                $query->where('user_id', $user_id);
            })
            ->get();

        return view('bouteille_has_cellier.BouteillesUtilisateur', compact('bouteillesUtilisateur', 'user_id', 'pageCourante'));
    }

    //fonction pour changer le nombre de bouteilles dans un cellier
    public function changerQuantite(Request $request, $cellier_id, $bouteille_id)
    {
        // Validation de la quantité
        $request->validate([
            'quantite' => 'required|integer|min:1',
        ]);
        // $request->quantite = (int)$request->quantite;

        // dd($request->quantite);
        // Récupérer l'enregistrement correspondant dans la table 'BouteilleHasCellier'
        $bouteilleHasCellier = BouteilleHasCellier::where('cellier_id', $cellier_id)
            ->where('bouteille_id', $bouteille_id)
            ->first();

        if (!$bouteilleHasCellier) {
            return redirect()->back()->with('error', 'Cette bouteille n\'existe pas dans ce cellier.');
        }

        // Mise à jour de la quantité
        $bouteilleHasCellier = BouteilleHasCellier::where('cellier_id', $cellier_id)
            ->where('bouteille_id', $bouteille_id)
            ->update(['quantite' => $request->quantite]);


        return redirect()->route('cellier_bouteilles.cellier.bouteilles', ['cellier_id' => $cellier_id])
            ->with('success', 'La quantité a été mise à jour.');
    }
}
