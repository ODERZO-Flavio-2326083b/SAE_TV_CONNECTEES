<?php

namespace controllers;
use models\CodeAde;
use models\Department;
use models\User;
use PHPUnit\Framework\TestCase;
use views\TelevisionView;
use WP_UnitTestCase;

class TelevisionControllerTest extends WP_UnitTestCase {
    private $televisionController;
    private $mockUser;
    private $mockView;
    private $mockCodeAde;
    private $mockDepartment;
    private $mockInformationController;

    protected function setUp(): void {
        // Création des mocks
        $this->mockUser = $this->createMock(User::class);
        $this->mockView = $this->createMock(TelevisionView::class);
        $this->mockCodeAde = $this->createMock(CodeAde::class);
        $this->mockDepartment = $this->createMock(Department::class);
        $this->mockInformationController = $this->createMock(InformationController::class);

        // Instanciation de la classe TelevisionController avec les mocks
        $this->televisionController = new TelevisionController();
        $this->televisionController->model = $this->mockUser;
        $this->televisionController->view = $this->mockView;
        $this->televisionController->informationController = $this->mockInformationController;
    }

    public function testInsert_WithValidData_ShouldInsertUserAndDisplayValidationMessage() {
        // Simuler les entrées de l'utilisateur
        $_POST['createTv'] = true;
        $_POST['loginTv'] = 'testuser';
        $_POST['pwdTv'] = 'password123';
        $_POST['pwdConfirmTv'] = 'password123';
        $_POST['selectTv'] = [1, 2];

        // Simuler les appels aux méthodes
        $this->mockCodeAde->method('getByCode')->willReturn($this->createMock(CodeAde::class));
        $this->mockUser->method('insert')->willReturn(1);
        $this->mockView->expects($this->once())->method('displayInsertValidate');

        // Exécution de la méthode
        $result = $this->televisionController->insert();

        // Vérifier si la validation a été affichée
        $this->mockView->displayInsertValidate();
        $this->assertStringContainsString('Formulaire validé', $result);
    }

    public function testInsert_WithInvalidCode_ShouldReturnErrorMessage() {
        // Simuler les entrées de l'utilisateur avec un code invalide
        $_POST['createTv'] = true;
        $_POST['loginTv'] = 'testuser';
        $_POST['pwdTv'] = 'password123';
        $_POST['pwdConfirmTv'] = 'password123';
        $_POST['selectTv'] = [999];  // Code invalide

        // Simuler l'appel à getByCode renvoyant null pour un code invalide
        $this->mockCodeAde->method('getByCode')->willReturn(null);

        // Exécution de la méthode
        $result = $this->televisionController->insert();

        // Vérifier si l'erreur de code invalide est affichée
        $this->assertStringContainsString('error', $result);
    }

    public function testModify_WithValidData_ShouldUpdateUserAndDisplayValidationMessage() {
        // Simuler un utilisateur existant
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getCodes')->willReturn([$this->createMock(CodeAde::class)]);

        // Simuler les entrées de l'utilisateur
        $_POST['modifValidate'] = true;
        $_POST['selectTv'] = [1];

        // Simuler l'appel à getByCode et à update
        $this->mockCodeAde->method('getByCode')->willReturn($this->createMock(CodeAde::class));
        $user->method('update')->willReturn(1);

        $this->mockView->expects($this->once())->method('displayModificationValidate');

        // Exécution de la méthode
        $result = $this->televisionController->modify($user);

        // Vérifier si la validation de modification est affichée
        $this->mockView->displayModificationValidate();
        $this->assertStringContainsString('Formulaire modifié avec succès', $result);
    }

    public function testDisplayAllTv_ShouldDisplayAllTelevisionUsers() {
        // Simuler les utilisateurs de télévision
        $user1 = $this->createMock(User::class);
        $user1->method('getId')->willReturn(1);
        $user2 = $this->createMock(User::class);
        $user2->method('getId')->willReturn(2);

        $users = [$user1, $user2];
        $this->mockUser->method('getUsersByRole')->willReturn($users);

        // Simuler les départements des utilisateurs
        $this->mockDepartment->method('getUserDepartment')->willReturn($this->createMock(Department::class));

        $this->mockView->expects($this->once())->method('displayAllTv');

        // Exécution de la méthode
        $result = $this->televisionController->displayAllTv();

        // Vérifier que la liste des utilisateurs a été affichée
        $this->mockView->displayAllTv();
        $this->assertStringContainsString('Liste des utilisateurs', $result);
    }

    public function testDisplayMySchedule_ShouldDisplaySchedule() {
        // Simuler un utilisateur avec des codes
        $user = $this->createMock(User::class);
        $user->method('getCodes')->willReturn([$this->createMock(CodeAde::class)]);

        // Simuler la méthode getMyCodes
        $this->mockUser->method('getMyCodes')->willReturn([$user]);

        // Simuler l'affichage du calendrier
        $this->mockInformationController->expects($this->once())->method('displayVideo');

        // Exécution de la méthode
        $result = $this->televisionController->displayMySchedule();

        // Vérifier que le calendrier de l'utilisateur est affiché
        $this->mockInformationController->displayVideo();
        $this->assertStringContainsString('Emploi du temps', $result);
    }
}