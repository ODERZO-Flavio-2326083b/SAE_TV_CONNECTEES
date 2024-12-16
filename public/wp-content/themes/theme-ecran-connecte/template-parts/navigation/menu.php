<?php

use Models\CodeAde;

if (function_exists('downloadFileICS_func')) {
    $current_user = wp_get_current_user();
    if (class_exists(CodeAde::class)) {
        $model = new CodeAde();
        $years = $model->getAllFromType('year');
    }

    // Url of all pages of Wordpress
    $page = get_page_by_title_custom('Emploi du temps');
    $linkEDT = get_permalink($page->ID);

    $page = get_page_by_title_custom('Création des comptes');
    $linkAddUser = get_permalink($page->ID);

    $page = get_page_by_title_custom('Gestion des utilisateurs');
    $linkManageUser = get_permalink($page->ID);

    $page = get_page_by_title_custom('Créer une alerte');
    $linkAddAlert = get_permalink($page->ID);

    $page = get_page_by_title_custom('Gérer les alertes');
    $linkManageAlert = get_permalink($page->ID);

    $page = get_page_by_title_custom('Créer une information');
    $linkAddInfo = get_permalink($page->ID);

    $page = get_page_by_title_custom('Gérer les informations');
    $linkManageInfo = get_permalink($page->ID);

    $page = get_page_by_title_custom('Gestion codes ADE');
    $linkCode = get_permalink($page->ID);

    $page = get_page_by_title_custom('Mon compte');
    $linkAccount = get_permalink($page->ID);

    ?>
    <nav class="menu" id="myMenu">
        <ul>
            <?php if (!is_user_logged_in()) { ?>
                <li><a class="menu-item" href="<?php echo wp_login_url(home_url()); ?>">Connexion</a></li>
            <?php } elseif (is_user_logged_in() && !in_array("technician", $current_user->roles)) { ?>
                <li class="menu-item_dropdown menu-item">
                    <button class="dropbtn">Emploi du temps
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="menu-item_dropdown-content">
                        <?php if (isset($years)) {
                            foreach ($years as $year) { ?>
                                <a href="<?php echo $linkEDT . $year->getId(); ?>/"> <?php echo $year->getTitle() ?></a>
                            <?php }
                        } ?>
                    </div>
                </li>
                <?php if (in_array('secretaire', $current_user->roles) || in_array('administrator', $current_user->roles)) { ?>
                    <li class="menu-item_dropdown menu-item">
                        <button class="dropbtn">Utilisateurs
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="menu-item_dropdown-content">
                            <a href="<?php echo $linkAddUser; ?>"> Création des comptes</a>
                            <a href="<?php echo $linkManageUser; ?>">Gestion des utilisateurs</a>
                        </div>
                    </li>
                <?php }
                if (in_array('secretaire', $current_user->roles) || in_array('administrator', $current_user->roles)) { ?>
                    <li class="menu-item_dropdown menu-item">
                        <button class="dropbtn">Alertes
                            <i class="fa fa-caret-down"></i>
                        </button>
                        <div class="menu-item_dropdown-content">
                            <a href="<?php echo $linkAddAlert; ?>">Créer une alerte</a>
                            <a href="<?php echo $linkManageAlert; ?>">Gestion des alertes</a>
                        </div>
                    </li>
                    <?php if (in_array('secretaire', $current_user->roles) || in_array('administrator', $current_user->roles)) { ?>
                        <li class="menu-item_dropdown menu-item">
                            <button class="dropbtn">Informations
                                <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="menu-item_dropdown-content">
                                <a href="<?php echo $linkAddInfo; ?>">Créer une information</a>
                                <a href="<?php echo $linkManageInfo; ?>">Gestion des informations</a>
                            </div>
                        </li>
                    <?php } ?>
                    <?php if (in_array('administrator', $current_user->roles)) { ?>
                        <li><a class="menu-item" href="<?php echo $linkCode; ?>"> Codes ADE</a></li>
                    <?php }
                } ?>
                <li><a class="menu-item" href="<?php echo $linkAccount; ?>">Mon compte</a></li>
                <li><a class="menu-item" href="<?php echo wp_logout_url(home_url()); ?>">Déconnexion</a></li>
            <?php } ?>
        </ul>
    </nav>
<?php } ?>