<?php get_header(); ?>
    <!-- MAIN -->
    <main>
        <div class="container">
            <div class="row">
                <div class="col-6 mx-auto col-md-6 order-md-1">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/media/background.png" alt="Logo Amu" class="img-fluid mb-3 mb-md-0">
                </div>
                <div class="col-md-6 order-md-2 text-center text-md-left pr-md-5">
                    <h1 class="mb-3 bd-text-purple-bright"><?php bloginfo('name'); ?></h1>
                    <p class="lead">
                        Créez des informations pour toutes les télévisions connectées, les informations seront affichées sur chaque télévision en plus des informations déjà publiées.
                        Les informations des télévisions peuvent contenir du texte, des images et même des pdf.
                    </p>
                    <p class="lead mb-4">
                        Vous pouvez faire de même avec les alertes des télévisions connectées.
                    </p>
                    <p class="lead mb-4">
                        Les informations seront affichées dans la partie de droite des télévisions et les alertes dans la partie rouge en bas des téléviseurs.
                    </p>
                    <div class="row mx-n2">
                        <div class="col-md px-2">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_title('Créer une information'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une information</a>
                        </div>
                        <div class="col-md px-2">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_title('Créer une alerte'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une alerte</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="masthead-followup row m-0 border border-white">
                <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                    <h3><img src="<?php echo get_template_directory_uri(); ?>/assets/media/+.png" alt="Ajouter une information/alerte" class="logo">Ajouter</h3>
                    <p>Ajouter une information ou une alerte. Elles seront affichées le lendemain sur toutes les télévisions</p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_title('Créer une information'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une information</a>
                    <hr class="half-rule">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_title('Créer une alerte'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer une alerte</a>
                </div>
                <div class="col-md-6 p-3 p-md-5 bg-light border border-white">
                    <h3><img src="<?php echo get_template_directory_uri(); ?>/assets/media/gestion.png" alt="voir les informations/alertes" class="logo">Gérer</h3>
                    <p>Voir toutes les informations et alertes déjà publiées. Vous pouvez les supprimer, les modifier ou bien seulement les regarder</p>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_title('Gestion des informations'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes informations</a>
                    <hr class="half-rule">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_title('Gestion des alertes'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Voir mes alertes</a>
                </div>
            </div>
            <div class="row">
                <div class="col-6 mx-auto col-md-6 order-md-2">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/media/user.png" alt="Logo utilisateur" class="img-fluid mb-3 mb-md-0">
                </div>
                <div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
                    <h2 class="mb-3 bd-text-purple-bright">Les utilisateurs</h2>
                    <p class="lead">
                        Vous pouvez ajouter des utilisateurs qui pourront à leur tour ajouter des informations et des alertes.
                    </p>
                    <p class="lead mb-4">
                        Ils pourront aussi gérer leurs informations et leurs alertes.
                    </p>
                    <div class="row mx-n2">
                        <div class="col-md px-2">
                            <a href="<?php echo esc_url(get_permalink(get_page_by_title('Créer un utilisateur'))); ?>" class="btn btn-lg button_presentation_ecran w-100 mb-3">Créer un utilisateur</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php //get_sidebar(); ?>
    </main>
<?php get_footer(); ?>
