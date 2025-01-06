<?php

use PHPUnit\Framework\TestCase;
use controllers\UserController;
use models\User;
use models\Alert;
use models\Information;
use views\UserView;

class UserControllerTest extends TestCase {
    private $userController;
    private $mockUser;
    private $mockView;
    private $mockAlert;
    private $mockInformation;

    protected function setUp(): void {
        // Création des mocks
        $this->mockUser = $this->createMock(User::class);
        $this->mockView = $this->createMock(UserView::class);
        $this->mockAlert = $this->createMock(Alert::class);
        $this->mockInformation = $this->createMock(Information::class);

        // Instanciation de la classe UserController avec les mocks
        $this->userController = new UserController();
        $this->userController->model = $this->mockUser;
        $this->userController->view = $this->mockView;
    }

    public function testDeleteAccount_WithCorrectPassword_ShouldSendEmailAndDisplayMessage() {
        // Simuler un utilisateur avec un mot de passe correct
        $mockCurrentUser = $this->createMock(\WP_User::class);
        $mockCurrentUser->method('ID')->willReturn(1);
        $mockCurrentUser->method('user_pass')->willReturn('correct_password');
        $mockCurrentUser->method('user_email')->willReturn('user@example.com');

        // Simuler la fonction wp_get_current_user pour renvoyer l'utilisateur mocké
        $this->mockFunction('wp_get_current_user', $mockCurrentUser);

        // Simuler les entrées de l'utilisateur
        $_POST['deleteMyAccount'] = true;
        $_POST['verifPwd'] = 'correct_password';

        // Simuler la création du code de désinscription
        $this->mockUser->method('createCode')->willReturn(true);

        // Attendez-vous à ce que l'email soit envoyé
        $this->mockView->expects($this->once())
            ->method('displayMailSend')
            ->willReturn(true);

        // Exécution de la méthode
        $this->userController->deleteAccount();

        // Vérifiez que le bon message a été affiché
        $this->mockView->displayMailSend();
    }

    public function testDeleteAccount_WithIncorrectPassword_ShouldDisplayError() {
        // Simuler un utilisateur avec un mot de passe incorrect
        $mockCurrentUser = $this->createMock(\WP_User::class);
        $mockCurrentUser->method('ID')->willReturn(1);
        $mockCurrentUser->method('user_pass')->willReturn('incorrect_password');

        // Simuler la fonction wp_get_current_user pour renvoyer l'utilisateur mocké
        $this->mockFunction('wp_get_current_user', $mockCurrentUser);

        // Simuler les entrées de l'utilisateur
        $_POST['deleteMyAccount'] = true;
        $_POST['verifPwd'] = 'incorrect_password';

        // Attendez-vous à ce que l'erreur de mot de passe soit affichée
        $this->mockView->expects($this->once())
            ->method('displayWrongPassword')
            ->willReturn(true);

        // Exécution de la méthode
        $this->userController->deleteAccount();

        // Vérifiez que le bon message d'erreur a été affiché
        $this->mockView->displayWrongPassword();
    }

    public function testChooseModif_ShouldReturnModificationOptions() {
        // Simuler un utilisateur connecté
        $mockCurrentUser = $this->createMock(\WP_User::class);
        $mockCurrentUser->method('ID')->willReturn(1);

        // Simuler la fonction wp_get_current_user pour renvoyer l'utilisateur mocké
        $this->mockFunction('wp_get_current_user', $mockCurrentUser);

        // Attendez-vous à ce que la méthode retourne des options de modification
        $result = $this->userController->chooseModif();

        // Vérifier que le résultat contient la vue correcte
        $this->assertStringContainsString('Modifier mon mot de passe', $result);
        $this->assertStringContainsString('Supprimer mon compte', $result);
    }

    public function testModifyPwd_WithCorrectPassword_ShouldUpdatePassword() {
        // Simuler un utilisateur avec un mot de passe correct
        $mockCurrentUser = $this->createMock(\WP_User::class);
        $mockCurrentUser->method('ID')->willReturn(1);
        $mockCurrentUser->method('user_pass')->willReturn('correct_password');

        // Simuler la fonction wp_get_current_user pour renvoyer l'utilisateur mocké
        $this->mockFunction('wp_get_current_user', $mockCurrentUser);

        // Simuler les entrées de l'utilisateur
        $_POST['modifyMyPwd'] = true;
        $_POST['verifPwd'] = 'correct_password';
        $_POST['newPwd'] = 'new_password';

        // Simuler l'appel à wp_set_password pour changer le mot de passe
        $this->mockFunction('wp_set_password', true);

        // Attendez-vous à ce que la méthode retourne un message de succès
        $this->mockView->expects($this->once())
            ->method('displayModificationPassValidate')
            ->willReturn(true);

        // Exécution de la méthode
        $this->userController->modifyPwd();

        // Vérifiez que le bon message a été affiché
        $this->mockView->displayModificationPassValidate();
    }

    // Méthode d'aide pour simuler des fonctions globales (comme wp_get_current_user)
    protected function mockFunction($functionName, $returnValue) {
        runkit_function_rename($functionName, $functionName . '_real');
        runkit_function_add($functionName, '', 'return ' . var_export($returnValue, true) . ';');
    }

    // Méthode pour restaurer les fonctions simulées
    protected function restoreFunction($functionName) {
        runkit_function_rename($functionName . '_real', $functionName);
    }
}
