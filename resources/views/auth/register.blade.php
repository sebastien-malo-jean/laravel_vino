<x-header-nav-sec :pageCourante="$pageCourante" />
<main class="enregistrement-form-page">
    <section class="enregistrement-form">
        <header>
            <h1>Création de compte</h1>
        </header>
        <div class="enregistrement-form__container balise-form">
            <form method="POST" action="{{ route('register') }}" class="enregistrement-form__container__contenu">
                @csrf

                <!-- Nom -->
                <div class="groupe-input balise_courriel">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="name" required>
                    <x-input-error :messages="$errors->get('name')" />
                </div>
                <div class="groupe-input balise_courriel">
                    <!-- Prénom -->
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                    <x-input-error :messages="$errors->get('prenom')" />
                </div>
                <!-- Date de naissance -->
                <div class="groupe-input balise_courriel">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" required>
                    <x-input-error :messages="$errors->get('date_naissance')" />
                </div>
                <!-- Adresse -->
                <div class="groupe-input balise_courriel">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required>
                    <x-input-error :messages="$errors->get('adresse')" />
                </div>
                <!-- Email -->
                <div class="groupe-input balise_courriel">
                    <label for="email">Courriel</label>
                    <input type="email" id="email" name="email" required>
                    <x-input-error :messages="$errors->get('email')" />
                </div>
                <!-- Mot de passe -->
                <div class="groupe-input balise_password">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                    <x-input-error :messages="$errors->get('password')" />
                </div>
                <!-- Confirmation du mot de passe -->
                <div class="groupe-input balise_password">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    <x-input-error :messages="$errors->get('password_confirmation')" />
                </div>
                <!-- Soumettre le formulaire -->
                <div class="groupe-input balise_password">
                    <button type="submit" class="boutons btn btn-primary">Créer un compte</button>
                </div>
                <!-- Lien vers la page de connexion -->
                <div class="groupe-input balise-inscrire">
                    <p>Déjà inscrit ? </p><a href="{{ route('login') }}">Connectez-vous ici</a>
                </div>
            </form>
        </div>
    </section>
</main>
<x-footer :pageCourante="$pageCourante" />