<?php

use PHPUnit\Framework\TestCase;
use models\Information;
use models\User;
use Mockery\Mock;

class InformationTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;

    protected function setUp(): void
    {
        $this->pdoMock = Mockery::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);
    }

    public function testInsert()
    {
        $infoMock = Mockery::mock(Information::class)->makePartial();
        $infoMock->shouldAllowMockingProtectedMethods();
        $infoMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_information'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->once()
            ->andReturn(1);

        $infoMock->shouldReceive('getTitle')->andReturn('Test Title');
        $infoMock->shouldReceive('getContent')->andReturn('Test Content');
        $infoMock->shouldReceive('getCreationDate')->andReturn('2025-01-01');
        $infoMock->shouldReceive('getExpirationDate')->andReturn('2025-01-10');
        $infoMock->shouldReceive('getType')->andReturn('Text');
        $infoMock->shouldReceive('getAuthor')->andReturn(Mockery::mock(User::class));
        $infoMock->shouldReceive('getAdminId')->andReturn(1);
        $infoMock->shouldReceive('getIdDepartment')->andReturn(2);

        $result = $infoMock->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $infoMock = Mockery::mock(Information::class)->makePartial();
        $infoMock->shouldAllowMockingProtectedMethods();
        $infoMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('UPDATE ecran_information'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $infoMock->shouldReceive('getId')->andReturn(1);
        $infoMock->shouldReceive('getTitle')->andReturn('Updated Title');
        $infoMock->shouldReceive('getContent')->andReturn('Updated Content');
        $infoMock->shouldReceive('getExpirationDate')->andReturn('2025-02-01');
        $infoMock->shouldReceive('getIdDepartment')->andReturn(2);

        $result = $infoMock->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $infoMock = Mockery::mock(Information::class)->makePartial();
        $infoMock->shouldAllowMockingProtectedMethods();
        $infoMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_information'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $infoMock->shouldReceive('getId')->andReturn(1);

        $result = $infoMock->delete();

        $this->assertEquals(1, $result);
    }

    public function testGet()
    {
        $infoMock = Mockery::mock(Information::class)->makePartial();
        $infoMock->shouldAllowMockingProtectedMethods();
        $infoMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('SELECT id, title, content'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindParam')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $this->pdoStatementMock->shouldReceive('fetch')
            ->once()
            ->andReturn([
                'id' => 1,
                'title' => 'Test Title',
                'content' => 'Test Content',
                'creation_date' => '2025-01-01',
                'expiration_date' => '2025-01-10',
                'author' => 1,
                'type' => 'Text',
                'administration_id' => 1,
                'department_id' => 2,
            ]);

        $result = $infoMock->get(1);

        $this->assertInstanceOf(Information::class, $result);
        $this->assertEquals('Test Title', $result->getTitle());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
