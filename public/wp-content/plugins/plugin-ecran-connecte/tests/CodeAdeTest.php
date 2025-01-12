<?php

use PHPUnit\Framework\TestCase;
use models\CodeAde;
use Mockery\Mock;
use PDO;
use PDOStatement;

class CodeAdeTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;

    protected function setUp(): void
    {
        // Mocking the PDO object and PDOStatement for database interactions
        $this->pdoMock = Mockery::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);
    }

    public function testInsert()
    {
        // Create a partial mock of CodeAde
        $codeAdeMock = Mockery::mock(CodeAde::class)->makePartial();
        $codeAdeMock->shouldAllowMockingProtectedMethods();

        // Mock the getDatabase() method to return the PDO mock
        $codeAdeMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        // Expect the PDO prepare method to be called with the correct query
        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_code_ade'))
            ->andReturn($this->pdoStatementMock);

        // Mock the bindValue calls
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        // Mock the execute method to return true (successful query execution)
        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        // Mock the lastInsertId method to return a dummy ID
        $this->pdoMock->shouldReceive('lastInsertId')
            ->once()
            ->andReturn(1);

        // Mock the properties to be inserted
        $codeAdeMock->shouldReceive('getTitle')->andReturn('Test Title');
        $codeAdeMock->shouldReceive('getCode')->andReturn('ABC123');
        $codeAdeMock->shouldReceive('getType')->andReturn('group');
        $codeAdeMock->shouldReceive('getDeptId')->andReturn(101);

        // Call the insert method and assert that the result is the expected ID
        $result = $codeAdeMock->insert();

        // Assert that the result matches the expected last insert ID
        $this->assertEquals(1, $result);
    }

    protected function tearDown(): void
    {
        // Clean up Mockery to avoid memory leaks
        Mockery::close();
    }
}