<?php

use PHPUnit\Framework\TestCase;
use models\Alert;
use models\User;
use models\CodeAde;
use Mockery;
use PDO;

class AlertTest extends TestCase
{
    private $alert;

    protected function setUp(): void
    {
        // Création d'une instance de l'objet Alert avant chaque test
        $this->alert = new Alert();

        // Vous pouvez définir ici des valeurs par défaut si nécessaire
        $user = new User();
        $user->setId(1);
        $this->alert->setAuthor($user);
        $this->alert->setContent('Test Alert Content');
        $this->alert->setCreationDate('2025-01-10');
        $this->alert->setExpirationDate('2025-01-20');

        // Créez des instances de CodeAde (vous devrez adapter selon votre logique)
        $codeAde = new CodeAde();
        $this->alert->setCodes([$codeAde]);

        // Créez un mock de la classe Model
        $modelMock = $this->getMockBuilder('Alert')
            ->setMethods(['getDatabase'])
            ->getMock();

        // Moquez la méthode getDatabase pour qu'elle retourne un mock de PDO
        $databaseMock = "test";
        $modelMock->method('getDatabase')->willReturn($databaseMock);
    }

    public function test1() {
        $this->assertIsString($this->alert->LOL());
    }

    public function testInsert()
    {
        // Simuler l'insertion d'une alerte et vérifier si l'ID est retourné
        $id = $this->alert->insert();

        // Vérifier que l'ID retourné est un entier et supérieur à 0
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testUpdate()
    {
        // Simuler l'insertion pour avoir un ID existant
        $this->alert->insert();

        // Mise à jour du contenu de l'alerte
        $this->alert->setContent('Updated Alert Content');
        $rowsAffected = $this->alert->update();

        // Vérifier que la mise à jour a affecté des lignes
        $this->assertGreaterThan(0, $rowsAffected);
    }

    public function testDelete()
    {
        // Insérer d'abord pour avoir un ID valide
        $this->alert->insert();

        // Suppression de l'alerte
        $rowsAffected = $this->alert->delete();

        // Vérifier que la suppression a affecté une ligne
        $this->assertGreaterThan(0, $rowsAffected);
    }

    public function testGet()
    {
        // Insérer une alerte
        $this->alert->insert();
        $id = $this->alert->getId();

        // Récupérer l'alerte par son ID
        $fetchedAlert = $this->alert->get($id);

        // Vérifier que l'objet récupéré n'est pas nul
        $this->assertNotNull($fetchedAlert);
        $this->assertInstanceOf(Alert::class, $fetchedAlert);
    }

    public function testGetList()
    {
        // Insérer quelques alertes
        $this->alert->insert();
        $this->alert->insert();

        // Récupérer une liste d'alertes
        $alerts = $this->alert->getList(0, 2);

        // Vérifier que la liste des alertes n'est pas vide
        $this->assertNotEmpty($alerts);
        $this->assertCount(2, $alerts);
    }

    public function testJsonSerialize()
    {
        // Sérialisation JSON de l'alerte
        $json = $this->alert->jsonSerialize();

        // Vérifier que le résultat est un tableau associatif
        $this->assertIsArray($json);
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('content', $json);
    }

    // Vous pouvez ajouter d'autres tests pour les méthodes restantes...
}