<?php
// à placer tout en haut si besoin
?>
<div class="row card card-inscription">
    <div
        class="col-md-6 bg-blue-dark color-white flex justify-center items-center rounded-top-left rounded-bottom-left p-3">
        <div class="flex flex-col justify-center text-center">
            <ion-icon class="text-6xl w-100" name="people-circle-outline"></ion-icon>
            <h3>Trouvez votre architecte idéal</h3>
            <p>Protégez vos données avec notre système de sécurité avancé</p>
        </div>
    </div>

    <div class="col-md-6">
        <div class="p-1">
            <div class="text-center">
                <ion-icon name="people-outline" class="text-6xl "></ion-icon><br>
                <h3 class="bold-500">Se créer un compte</h3>
                <p class="text-sm">Connectez-vous avec des architectes de premier ordre pour le projet de vos rêves</p>
            </div>

            <form method="post" action="" enctype="multipart/form-data" id="registrationForm"
                class="connexion-form border-bottom">
                <input type="hidden" name="action" value="showroom_register_user">
                <!-- step 1 -->
                <div id="step1">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="last_name" class="custom-input" placeholder="Nom*" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="first_name" class="custom-input" placeholder="Prenom*" required>
                        </div>
                    </div>

                    <input type="email" name="username" class="custom-input" placeholder="E-mail*" required>
                    <br>
                    <input type="password" name="password" class="custom-input" placeholder="Mot de passe*" required>

                    <div class="mb-2 mt-2">
                        <div class="flex gap-2 items-center py-1">
                            <input type="radio" id="type_particulier" name="user_type" value="particulier" required>
                            <label for="type_particulier">Je suis un client à la recherche d'un professionnel</label>
                        </div>

                        <div class="flex gap-2 items-center py-1">
                            <input type="radio" id="type_professionnel" name="user_type" value="professionnel">
                            <label for="type_professionnel">Je suis un architecte ou une agence qui propose ses
                                services</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button id="continueBtn" class="btn justify-center rounded-5 w-100 btn-dark">Poursuivre
                            l'inscription</button>
                    </div>
                </div>
                <!--fin step 1-->

                <!--debut step 2-->
                <!-- Champs communs supplémentaires -->
                <div id="step2" class="none">
                    <div id="common-fields" class="none">
                        <div class="row">
                            <div class="col-md-6"><input type="tel" name="telephone" class="custom-input"
                                    placeholder="Téléphone*"></div>
                            <div class="col-md-6"><input type="date" name="birthdate" class="custom-input"
                                    placeholder="Date de naissance*"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><input type="text" name="address" class="custom-input"
                                    placeholder="Adresse"></div>
                            <div class="col-md-6"><input type="text" name="city" class="custom-input"
                                    placeholder="Ville*"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6"><input type="text" name="postalcode" class="custom-input"
                                    placeholder="Code postal*"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="profile_image">Photo de profil :</label>
                                <input type="file" name="profile_image" accept="image/jpeg,image/png"
                                    class="custom-input">
                            </div>
                        </div>

                    </div>

                    <!-- Champs spécifiques aux pros -->
                    <div id="professional-fields" class="none">
                        <div class="row">
                            <div class="col-md-6 p-relative"><input type="text" name="siren" id="siren"
                                    class="custom-input" placeholder="Numéro SIREN*">
                            </div>

                            <div class="col-md-6"><input type="text" id="ape" name="ape" class="custom-input"
                                    placeholder="Code APE*"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12"><input type="text" id="entreprise" name="company_name"
                                    class="custom-input" placeholder="Nom de l'entreprise*"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label for="kbis_file">Fichier KBIS (PDF ou JPG)* :</label>
                                <input type="file" name="kbis_file" accept="application/pdf,image/jpeg,image/png"
                                    class="custom-input">
                            </div>
                        </div>


                    </div>

                    <div class="col-md-12 py-2">
                        <div class="flex gap-1 items-center">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms" class="pt-1">
                                J'accepte <a class="underline color-dark" href="#">les conditions</a> et <a href="#"
                                    class="underline color-dark">la politique de confidentialité*</a>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn justify-center rounded-5 w-100 btn-dark">S'inscrire</button>
                    </div>
                </div>
                <!--fin step 2-->


            </form>

            <div class="text-center pt-2">
                <p class="text-sm color-dark">Vous avez déjà un compte ? <span class="underline">
                        <a class="color-dark" href="<?php echo site_url('/connexion/'); ?>?type=connexion">Se
                            connecter</a></span>
                </p>
            </div>
            <div class="mt-3">
                <p class="bold color-dark text-sm">* Requis</p>
            </div>
        </div>
    </div>
</div>