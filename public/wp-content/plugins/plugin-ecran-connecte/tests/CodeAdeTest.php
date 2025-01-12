<?php

use PHPUnit\Framework\TestCase;
use models\CodeAde;
use Mockery\Mock;

class CodeAdeTest extends TestCase
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
        $codeAdeMock = Mockery::mock(CodeAde::class)->makePartial();
        $codeAdeMock->shouldAllowMockingProtectedMethods();
        $codeAdeMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_code_ade'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->once()
            ->andReturn(1);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $codeAdeMock->shouldReceive('getTitle')->andReturn('Sample Title');
        $codeAdeMock->shouldReceive('getCode')->andReturn(12345);
        $codeAdeMock->shouldReceive('getType')->andReturn('group');
        $codeAdeMock->shouldReceive('getDeptId')->andReturn(1);

        $result = $codeAdeMock->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $codeAdeMock = Mockery::mock(CodeAde::class)->makePartial();
        $codeAdeMock->shouldAllowMockingProtectedMethods();
        $codeAdeMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('UPDATE ecran_code_ade'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $codeAdeMock->shouldReceive('getId')->andReturn(1);
        $codeAdeMock->shouldReceive('getTitle')->andReturn('Updated Title');
        $codeAdeMock->shouldReceive('getCode')->andReturn(67890);
        $codeAdeMock->shouldReceive('getType')->andReturn('year');
        $codeAdeMock->shouldReceive('getDeptId')->andReturn(2);

        $result = $codeAdeMock->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $codeAdeMock = Mockery::mock(CodeAde::class)->makePartial();
        $codeAdeMock->shouldAllowMockingProtectedMethods();
        $codeAdeMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_code_ade'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $codeAdeMock->shouldReceive('getId')->andReturn(1);

        $result = $codeAdeMock->delete();

        $this->assertEquals(1, $result);
    }

    public function testGetByCode()
    {
        $codeAdeMock = Mockery::mock(CodeAde::class)->makePartial();
        $codeAdeMock->shouldAllowMockingProtectedMethods();
        $codeAdeMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('SELECT id, title, code, type, dept_id FROM ecran_code_ade WHERE code = :code'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('fetch')
            ->once()
            ->andReturn([
                'id' => 1,
                'title' => 'Sample Title',
                'code' => 12345,
                'type' => 'group',
                'dept_id' => 1
            ]);

        $result = $codeAdeMock->getByCode(12345);

        $this->assertInstanceOf(CodeAde::class, $result);
        $this->assertEquals('Sample Title', $result->getTitle());
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
