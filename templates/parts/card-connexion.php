<div class="row card card-connexion">
    <div
        class="col-md-6 bg-blue-dark color-white flex justify-center items-center rounded-top-left rounded-bottom-left p-3">
        <div class="flex flex-col justify-center text-center">
            <div class="text-center">
                <ion-icon class="text-6xl " name="shield-outline"></ion-icon>
            </div>
            <h3>Accès sécurisé</h3>
            <p>Protégez vos données avec notre système de sécurité avancé</p>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-1">
            <div class="text-center">
                <ion-icon name="lock-closed-outline" class="text-6xl"></ion-icon><br>
                <h3 class="bold-500">Se connecter à mon compte</h3>
                <p class="text-sm">Entrer vos informations d'identification pour vous connecter</p>
            </div>
            <form method="post" action="" id="connexionForm" class="connexion-form border-bottom">
                <input type="text" id="inputUsername" name="username" class="custom-input" placeholder="E-mail"
                    required>
                <br />
                <input type="password" name="password" class="custom-input" placeholder="Mot de passe" required>
                <div class="row mt-1 items-center">
                    <div class="col-md-6">
                        <div class="flex gap-1 items-center">
                            <input type="checkbox" id="remember-me">
                            <label for="remember-me" class="pt-1">Se souvenir de moi</label>
                        </div>
                    </div>
                    <div class="col-md-6 flex justify-end">
                        <a href="#" id="forgot-password-link" class="color-dark flex pt-1 underline">Mot de passe
                            oublié</a>
                    </div>
                </div>
                <div class="col-md-12 ">
                    <button class="btn justify-center rounded-5 w-100 btn-dark">Connexion</button>
                </div>
            </form>
            <div class="text-center pt-2">
                <p class="text-sm color-dark">Vous n'avez pas de compte de compte ? <span class="underline"><a
                            class="color-dark"
                            href="<?php echo site_url('/connexion/'); ?>?type=inscription">Inscrivez-vous</a></span></p>
            </div>
        </div>
    </div>
</div>