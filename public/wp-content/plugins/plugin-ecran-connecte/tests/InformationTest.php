<?php

use PHPUnit\Framework\TestCase;
use models\Information;
use Mockery as m;
use models\User;

class InformationTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $information;

    protected function setUp(): void
    {
        $this->pdoMock = Mockery::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);

        $this->information = m::mock(Information::class)->makePartial();
        $this->information->shouldAllowMockingProtectedMethods();
        $this->information->shouldReceive('getDatabase')
                          ->andReturn($this->pdoMock);

        $user = new User();
        $user->setId(10);

        $this->information->setId(1);
        $this->information->setTitle("Titre de test");
        $this->information->setAuthor($user);
        $this->information->setCreationDate("2025-01-13");
        $this->information->setExpirationDate("2025-12-31");
        $this->information->setContent(
            "Test content");
        $this->information->setType("texte");
        $this->information->setAdminId(42);
        $this->information->setIdDepartment(5);
        $this->information->setDuration(3600000);

    }

    public function testInsert()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('INSERT INTO ecran_information'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':title', 'Titre de test');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':content', 'Test content');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':creationDate', '2025-01-13');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':expirationDate', '2025-12-31');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':type', 'texte');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':userId', 10, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':department_id', 5, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':duration', 3600000, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':administration_id', 42, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->andReturn(1);

        $result = $this->information->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $this->pdoMock->shouldReceive('prepare')
                      ->with($this->stringContains('UPDATE ecran_information'))
                      ->once()
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':title', 'Titre de test');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':content', 'Test content');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':expirationDate', '2025-12-31');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', '1', PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':deptId', 5, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':duration', 3600000, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->information->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('DELETE FROM ecran_information'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', '1', PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->information->delete();

        $this->assertEquals(1, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
