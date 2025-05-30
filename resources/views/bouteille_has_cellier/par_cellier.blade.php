<x-header-nav-sec :pageCourante="$pageCourante" />

<main class="cellier-page">


    <section class="cellier-carte">

        <div class="cellier-carte__image @if ($cellier->teinte == '#F28B82') rouge-framboise
                @elseif ($cellier->teinte == '#FBC4AB') rose-peche
                @elseif ($cellier->teinte == '#FDF6E3') blanc-vanille
                @elseif ($cellier->teinte == '#CDEAC0') sauge-pale
                @elseif ($cellier->teinte == '#E6E6FA') lavande-brume
                @elseif ($cellier->teinte == '#D1F2EB') menthe-douce
                @elseif ($cellier->teinte == '#AEDFF7') bleu-ciel
                @elseif ($cellier->teinte == '#FFF1D0') champagne-pale
                @elseif ($cellier->teinte == '#FFD1DC') corail-pastel
                @elseif ($cellier->teinte == '#E5E5E5') gris-perle
            @endif">


            <div class="cellier-carte__boutons">
                <div class="container"><a href="{{ route('celliers.edit', ['id' => $cellier->id]) }}"
                        class="bouton-warning"> <i class="fa fa-pencil-square-o"></i></a>
                </div>
                <div class="container">
                    <form action="{{ route('celliers.destroy', ['id' => $cellier->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bouton-danger"> <i class="fa fa-trash-o"></i></button>
                    </form>
                </div>
            </div>
            <div class="cellier-carte__texte">
                <picture>
                    <img src="{{asset('images/icons/celliers-icon-01.svg')}}" alt="Image de cellier" />
                </picture>
                <h1 class="celliers-carte__titre">{{ $cellier->nom }}</h1>
            </div>
        </div>
        <section>
            <!-- liens pour modifier & supprimer un cellier -->
            <div class="celliers-carte__actions">


                <div class="container">
                    <a href="{{ route('bouteilles.index') }}" class="bouton"><img
                            src="/images/icons/ajout-bouteille.svg" alt=""> <span> Ajouter des bouteilles</span></a>
                </div>
            </div>
        </section>

        <section class="section-recherche">
            <h2 class="section-recherche__titre">Recherche de bouteilles dans le cellier</h2>
            <x-composante-recherche :pageCourante="$pageCourante" :pays="$listePays" />
        </section>

        <div>
            <a href="{{ route('cellier_bouteilles.cellier.bouteilles', ['cellier_id' => session('id_cellier')]) }}">Tous
                les résultats</a>
        </div>
        @if($bouteilles->isEmpty())
        <section>
            <div>
                <h2>Recherche de : "{{$demande}}"</h2>

                <p>Désolé, aucun résultat trouvé.
                    <br>Essayez une autre recherche
                </p>

            </div>
        </section>
        @else
        @foreach ($bouteilles as $reponse)
        <x-carte-bouteille-saq :bouteille="$reponse->bouteille" :pageCourante="$pageCourante" :reponse="$reponse" />
        <x-formulaire-quantite-bouteille :reponse="$reponse" />
        @endforeach
        @endif
</main>
<x-footer :pageCourante="$pageCourante" />