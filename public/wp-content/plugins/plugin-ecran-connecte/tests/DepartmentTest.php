<?php

use PHPUnit\Framework\TestCase;
use models\Department;
use Mockery as m;

class DepartmentTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $department;

    protected function setUp(): void
    {
        $this->pdoMock = Mockery::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);

        $this->department = m::mock(Department::class)->makePartial();
        $this->department->shouldAllowMockingProtectedMethods();
        $this->department->shouldReceive('getDatabase')
                         ->andReturn($this->pdoMock);
        $this->department->setIdDepartment(10);
        $this->department->setName("Département");
    }

    public function testInsert()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('INSERT INTO ecran_departement'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':name', "Département");

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->andReturn(1);

        $result = $this->department->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $this->pdoMock->shouldReceive('prepare')
              ->with($this->stringContains('UPDATE ecran_departement'))
              ->once()
              ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':name', 'Département');

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', 10);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->department->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('DELETE FROM ecran_departement'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', 10, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->department->delete();

        $this->assertEquals(1, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
