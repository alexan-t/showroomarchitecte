</div><!-- #content -->

<footer id="colophon" class="site-footer bg-dark color-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h2 class="font-GildaDisplay bold-100 text-5xl">Trouvez des <br> architectes, des rêves de
                    <br>design ici
                </h2>
            </div>
        </div>
        <hr>
        <div class="row items-center">
            <div class="col-md-6">
                <div class="footer-menu">
                    <?php wp_nav_menu( array( 'theme_location' => 'menu-main', 'menu_id' => 'menu-main' ) ); ?>
                </div>
            </div>
            <div class="col-md-6 mt-1 flex justify-end">
                <p class="color-gray">© Copyright 2025, All Rights Reserved</p>
            </div>
        </div>
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
<?php
    // Inclure le fichier SVG icon
    include get_template_directory() . '/templates/parts/svg-icons.php';
?>
</body>

</html>