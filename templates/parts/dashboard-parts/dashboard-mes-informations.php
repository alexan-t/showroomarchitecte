<h2 class="color-blue uppercase text-center">Mes informations</h2>
<p class="text-center color-gray-dark">Ces informations seront visibles :
    <br>- pour les professionnels que vous contactez
    <br>- pour les professionnels qui ont activé un compte Gold souhaitant vous contacter suite à la lecture de votre
    projet.
</p>
<div class="row">
    <div class="col-md-12">
        <form id="update-profile-form" class="px-2" enctype="multipart/form-data">
            <!-- Champ caché pour définir l'action (utile pour la compatibilité) -->
            <input type="hidden" name="action" value="update_mes_informations">

            <!-- Champ nonce (retiré si nécessaire, selon les recommandations précédentes) -->
            <?php // wp_nonce_field( 'update_mes_informations_nonce', 'mes_informations_nonce' ); ?>

            <div class="row">
                <div class="col-md-6">
                    <label for="name">Nom*</label>
                    <input type="text" id="name" name="name" class="custom-input" placeholder="Nom*" required
                        value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'last_name', true ) ); ?>">
                </div>
                <div class="col-md-6">
                    <label for="email">E-mail*</label>
                    <input type="email" id="email" name="email" class="custom-input" placeholder="E-mail*" required
                        value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="firstname">Prénom*</label>
                    <input type="text" id="firstname" name="firstname" class="custom-input" placeholder="Prénom*"
                        required value="<?php echo esc_attr( wp_get_current_user()->first_name ); ?>">
                </div>
                <div class="col-md-6">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="custom-input" placeholder="Téléphone"
                        pattern="^0[1-9](\s?\d{2}){4}$"
                        title="Entrez un numéro de téléphone français valide, par exemple : 06 12 34 56 78"
                        value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'telephone', true ) ); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="adress">Adresse</label>
                    <input type="text" id="adress" name="adress" class="custom-input" placeholder="Adresse"
                        value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'adress', true ) ); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="city">Ville*</label>
                    <input type="text" id="city" name="city" class="custom-input" placeholder="Ville*" required
                        value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'city', true ) ); ?>">
                    <label for="postalcode">Code Postale*</label>
                    <input type="text" id="postalcode" name="postalcode" class="custom-input"
                        placeholder="Code Postale*" required
                        value="<?php echo esc_attr( get_user_meta( get_current_user_id(), 'postalcode', true ) ); ?>">
                </div>
                <div class="col-md-6">
                    <div class="ml-2">
                        <label for="imageUpload">Changer ma photo de profil :</label>
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <input type='file' id="imageUpload" name="profile_image" accept=".png, .jpg, .jpeg" />
                                <label for="imageUpload"></label>
                            </div>
                            <div class="avatar-preview">
                                <?php
                            $profile_image = get_user_meta( get_current_user_id(), 'profile_image', true );
                            if ( $profile_image ) {
                                $background_image = esc_url( $profile_image );
                            } else {
                                $background_image = get_template_directory_uri() . '/assets/img/blue-circle.svg';
                            }
                            ?>
                                <div id="imagePreview"
                                    style="background-image: url('<?php echo $background_image; ?>');">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="custom-input custom-textarea"
                    placeholder="Décrivez vos envies générales, vos contraintes…."
                    required><?php echo esc_textarea( get_user_meta( get_current_user_id(), 'description', true ) ); ?></textarea>
            </div>
            <!-- Bouton de soumission -->
            <div class="col-md-12 mt-3 text-center">
                <button type="submit" class="btn btn-blue uppercase bold">Mettre à jour</button>
            </div>
            <p class="mt-1 text-sm color-gray-dark italic">* Champ obligatoire pouvant être communiqués aux
                professionnels que vous contactez ou qui ont activé un profil Gold.</p>
        </form>
        <!-- La div #ajax-response a été supprimée -->
    </div>

</div>