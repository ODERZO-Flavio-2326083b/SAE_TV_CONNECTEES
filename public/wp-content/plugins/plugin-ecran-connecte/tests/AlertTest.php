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

    protected function tearDown(): void
    {
        // Libération des mocks
        Mockery::close();
    }
}