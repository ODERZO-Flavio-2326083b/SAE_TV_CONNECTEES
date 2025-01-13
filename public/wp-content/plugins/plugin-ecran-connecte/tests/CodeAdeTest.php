<?php

use PHPUnit\Framework\TestCase;
use models\CodeAde;
use Mockery as m;

class CodeAdeTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $codeAde;

    protected function setUp(): void
    {
        $this->pdoMock = m::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);

        $this->codeAde = m::mock(CodeAde::class)->makePartial();
        $this->codeAde->shouldAllowMockingProtectedMethods();
        $this->codeAde->shouldReceive('getDatabase')
                      ->andReturn($this->pdoMock);

        $this->codeAde->setId(1);
        $this->codeAde->setTitle('Code ade');
        $this->codeAde->setCode(1234);
        $this->codeAde->setType('year');
        $this->codeAde->setDeptId(10);


    }

    /**
     * Teste le bon fonctionnement de la fonction insert()
     *
     * @return void
     */
    public function testInsert()
    {
        $this->pdoMock->shouldReceive('prepare')
                  ->with($this->stringContains('INSERT INTO ecran_code_ade'))
                  ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':title', 'Code ade', $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':code', 1234, $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':type', 'year', $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':dept_id', 10, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->andReturn(1);

        $result = $this->codeAde->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $this->pdoMock->shouldReceive('prepare')
                      ->with($this->stringContains('UPDATE ecran_code_ade'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', '1', PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':title', 'Code ade', $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':code', 1234, $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':type', 'year', $this->anything());
        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':dept_id', 10, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->codeAde->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {

        $this->pdoMock->shouldReceive('prepare')->with(
            $this->stringContains('DELETE FROM ecran_code_ade WHERE id = :id'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':id', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->once()
                               ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('rowCount')
                               ->once()
                               ->andReturn(1);


        $result = $this->codeAde->delete();

        $this->assertEquals(1, $result);
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
