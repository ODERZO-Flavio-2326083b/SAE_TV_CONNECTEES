<?php

use PHPUnit\Framework\TestCase;
use models\Department;
use Mockery\Mock;
use PDO;
use PDOStatement;

class DepartmentTest extends TestCase
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
        $departmentMock = Mockery::mock(Department::class)->makePartial();
        $departmentMock->shouldAllowMockingProtectedMethods();
        $departmentMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Mocking the database query execution
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_departement'))
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

        $departmentMock->shouldReceive('getName')->andReturn('Sales');

        $result = $departmentMock->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $departmentMock = Mockery::mock(Department::class)->makePartial();
        $departmentMock->shouldAllowMockingProtectedMethods();
        $departmentMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Mocking the database query execution for update
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('UPDATE ecran_departement'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        // Simulating department data for update
        $departmentMock->shouldReceive('getIdDepartment')->andReturn(1);
        $departmentMock->shouldReceive('getName')->andReturn('Marketing');

        $result = $departmentMock->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $departmentMock = Mockery::mock(Department::class)->makePartial();
        $departmentMock->shouldAllowMockingProtectedMethods();
        $departmentMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Mocking the database query execution for delete
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_departement'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $departmentMock->shouldReceive('getIdDepartment')->andReturn(1);

        $result = $departmentMock->delete();

        $this->assertEquals(1, $result);
    }

    public function testGetDepartmentByName()
    {
        $departmentMock = Mockery::mock(Department::class)->makePartial();
        $departmentMock->shouldAllowMockingProtectedMethods();
        $departmentMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Mocking the database query execution for get by name
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('SELECT dept_id, dept_nom'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([['dept_id' => 1, 'dept_nom' => 'Sales']]);

        $result = $departmentMock->getDepartmentByName('Sales');

        $this->assertInstanceOf(Department::class, $result[0]);
        $this->assertEquals('Sales', $result[0]->getName());
    }

    public function testGetAllDepts()
    {
        $departmentMock = Mockery::mock(Department::class)->makePartial();
        $departmentMock->shouldAllowMockingProtectedMethods();
        $departmentMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Mocking the database query execution for get all departments
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('SELECT dept_id, dept_nom'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('fetchAll')
            ->once()
            ->andReturn([
                ['dept_id' => 1, 'dept_nom' => 'Sales'],
                ['dept_id' => 2, 'dept_nom' => 'Marketing']
            ]);

        $result = $departmentMock->getAllDepts();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Department::class, $result[0]);
    }

    protected function tearDown(): void
    {
        // Libération des mocks
        Mockery::close();
    }
}
