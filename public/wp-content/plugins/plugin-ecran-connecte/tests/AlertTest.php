<?php

use PHPUnit\Framework\TestCase;
use models\Alert;
use models\User;
use models\CodeAde;
use Mockery\Mock;

class AlertTest extends TestCase
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
        $alerteMock = Mockery::mock(Alert::class)->makePartial();
        $alerteMock->shouldAllowMockingProtectedMethods();
        $alerteMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
                      ->once()
                      ->with($this->stringContains('INSERT INTO ecran_alert'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
                               ->once()
                               ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
                      ->once()
                      ->andReturn(1);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with($this->anything(),
                                   $this->anything(),
                                   $this->anything())
                               ->andReturn(true);

        $alerteMock->shouldReceive('getAuthor')
                         ->andReturn(1);
        $alerteMock->shouldReceive('getContent')
                         ->andReturn('Test content');
        $alerteMock->shouldReceive('getCreationDate')
                         ->andReturn('2025-01-01');
        $alerteMock->shouldReceive('getExpirationDate')
                         ->andReturn('2025-01-10');

        $codeMock = Mockery::mock(CodeAde::class);
        $codeMock->shouldReceive('getCode')->andReturn(123123);
        $codeMock->shouldReceive('getId')->andReturn(2);
        $alerteMock->shouldReceive('getCodes')->andReturn([$codeMock]);

        $this->pdoMock->shouldReceive('prepare')
                      ->once()
                      ->with($this->stringContains('INSERT INTO ecran_code_alert'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
                               ->once()
                               ->andReturn(true);

        $result = $alerteMock->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $alertMock = Mockery::mock(Alert::class)->makePartial();
        $alertMock->shouldAllowMockingProtectedMethods();
        $alertMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('UPDATE ecran_alert'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $alertMock->shouldReceive('getId')->andReturn(1);
        $alertMock->shouldReceive('getContent')->andReturn('Updated content');
        $alertMock->shouldReceive('getExpirationDate')->andReturn('2025-02-01');

        $codeMock = Mockery::mock(CodeAde::class);
        $codeMock->shouldReceive('getCode')->andReturn(123123);
        $codeMock->shouldReceive('getId')->andReturn(2);
        $alertMock->shouldReceive('getCodes')->andReturn([$codeMock]);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_code_alert'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_code_alert'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $result = $alertMock->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $alertMock = Mockery::mock(Alert::class)->makePartial();
        $alertMock->shouldAllowMockingProtectedMethods();
        $alertMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_alert'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $alertMock->shouldReceive('getId')->andReturn(1);

        $result = $alertMock->delete();

        $this->assertEquals(1, $result);
    }

    public function testDatabaseInteraction()
    {

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn($this->pdoStatementMock);


        $this->pdoStatementMock->shouldReceive('bindValue')
            ->once()
            ->with(Mockery::type('string'), Mockery::type('string'))
            ->andReturnSelf();

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $this->pdoStatementMock->shouldReceive('fetch')
            ->once()
            ->andReturn([
                'ID' => 123,
                'user_login' => 'testuser',
                'user_email' => 'test@example.com',
                'dept_id' => 1
            ]);


        $result = $this->pdoMock->prepare("SELECT * FROM wp_users WHERE ID = :id");

        $this->assertTrue($result->execute());
        $this->assertEquals(1, $result->rowCount());
        $this->assertEquals([
            'ID' => 123,
            'user_login' => 'testuser',
            'user_email' => 'test@example.com',
            'dept_id' => 1
        ], $result->fetch());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}