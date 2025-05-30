<?php

namespace App\Http\Controllers;

use App\Models\Bouteille;

use Illuminate\Http\Request;

class BouteilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $demande = $request->input('requete');

        // champs de filtre
        $type = $request->input('type');
        $format = $request->input('format');
        $pays = $request->input('pays');

        if ($demande == '') {
            $bouteilles = Bouteille::query();

            // Appliquer les filtres si présents
            if ($type) {
                $bouteilles->where('type', $type);
            }

            if ($format) {
                $bouteilles->where('format', $format);
            }

            if ($pays) {
                $bouteilles->where('pays', $pays);
            }

            $bouteilles = $bouteilles->paginate(50);
        } else {
            $champs = ['nom', 'format', 'pays', 'type'];
            $bouteilles = Bouteille::where(function ($query) use ($demande, $champs) {
                foreach ($champs as $champ) {
                    $query->orWhere($champ, 'like', "%{$demande}%");
                }
            });

            // Appliquer les filtres après la recherche textuelle
            if ($type) {
                $bouteilles->where('type', $type);
            }

            if ($format) {
                $bouteilles->where('format', $format);
            }

            if ($pays) {
                $bouteilles->where('pays', $pays);
            }

            $bouteilles = $bouteilles->paginate(50)->withQueryString();
        }

        $listePays = Bouteille::trouveNomDePays();

        $pageCourante = 'bouteilles';

        return view('bouteilles.index', compact('bouteilles', 'pageCourante', 'demande', 'listePays'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //page courante
        $pageCourante = 'bouteilles';
        //montrer le formulaire de création d'une bouteille

        // récupérer l'ID de l'utilisateur authentifié
        $user_id = auth()->id();

        // Boucle pour générer un code unique
        // Vérifier si le code existe déjà dans la base de données
        do {
            //Combiner la date, l'heure, la minute et les secondes actuelles avec l'ID de l'utilisateur pour créer un code unique
            $code_saq = now()->format('ymdHis') . $user_id;
        } while (Bouteille::where('code_saq', $code_saq)->exists());


        return view('bouteilles.create', compact('pageCourante', 'code_saq'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validee = $request->validate([
            'nom' => 'required|string|max:255',
            'pays' => 'required|string|max:255',
            'format' => 'required|string|max:255',
            'degre_alcool' => 'required|numeric|min:0|max:100',
            'region' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        // Générer un code unique
        do {
            $code_saq = now()->format('ymdHis') . auth()->id();
        } while (Bouteille::where('code_saq', $code_saq)->exists());

        // Créer une nouvelle bouteille
        $bouteille = new Bouteille();
        $bouteille->nom = $request->nom;
        $bouteille->pays = $request->pays;
        $bouteille->format = $request->format;
        $bouteille->degre_alcool = $request->degre_alcool;
        $bouteille->region = $request->region;
        $bouteille->type = $request->type;
        $bouteille->code_saq = $code_saq;
        $bouteille->user_id = auth()->id();
        $bouteille->url_image = "https://www.saq.com/media/catalog/product/1/2/12824197-1_1578411313.png?width=367&height=550&canvas=367,550&optimize=high&fit=bounds&format=jpeg";
        $bouteille->save();

        return redirect()->route('bouteilles.index')->with('success', 'La bouteille a été ajoutée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pageCourante = "Présentation de bouteille";
        $bouteille = Bouteille::find($id);
        if (!$bouteille) {
            return redirect()->route('bouteilles.index')->with('error', 'La bouteille n\'existe pas');
        }
        return view('bouteilles.show', compact('bouteille', 'pageCourante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //page courante
        $pageCourante = 'bouteilles';
        //montrer le formulaire d'édition d'un bouteille
        $bouteille = Bouteille::find($id);

        return view('bouteilles.edit', compact('bouteille', 'pageCourante'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
