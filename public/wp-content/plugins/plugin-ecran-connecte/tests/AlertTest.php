<?php

use PHPUnit\Framework\TestCase;
use models\Alert;
use models\CodeAde;
use Mockery as m;

class AlertTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $alert;

    protected function setUp(): void
    {
        $this->pdoMock = m::mock(PDO::class);
        $this->pdoStatementMock = m::mock(PDOStatement::class);

        $code1 = new CodeAde();
        $code1->setId(1);
        $code1->setCode(1234);
        $code2 = new CodeAde();
        $code2->setId(2);
        $code2->setCode(5678);

        $this->alert = m::mock(Alert::class)->makePartial();
        $this->alert->shouldAllowMockingProtectedMethods();
        $this->alert->shouldReceive('getDatabase')
                    ->andReturn($this->pdoMock);
        $this->alert->setId(1);
        $this->alert->setAuthor(1);
        $this->alert->setContent('Alerte test');
        $this->alert->setCreationDate('2025-01-01');
        $this->alert->setExpirationDate('2025-01-10');
        $this->alert->setCodes(array($code1, $code2));
    }

    /**
     * Teste le bon fonctionnement de la fonction insert()
     *
     * @return void
     */
    public function testInsert()
    {
        $this->pdoMock->shouldReceive('prepare')
                      ->with($this->stringContains('INSERT INTO ecran_alert'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                       ->with(':author', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('bindValue')
                       ->with(':content', 'Alerte test', PDO::PARAM_STR);
        $this->pdoStatementMock->shouldReceive('bindValue')
                       ->with(':creation_date', '2025-01-01', PDO::PARAM_STR);
        $this->pdoStatementMock->shouldReceive('bindValue')
                       ->with(':expirationDate', '2025-01-10', PDO::PARAM_STR);
        $this->pdoStatementMock->shouldReceive('execute')
                       ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')->andReturn(100);

        $this->pdoMock->shouldReceive('prepare')
                      ->with($this->stringContains
            ('INSERT INTO ecran_code_alert (alert_id, code_ade_id)')
        )->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':idAlert', 100, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':idCodeAde', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':idCodeAde', 2, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);

        $result = $this->alert->insert();

        $this->assertEquals(100, $result);
    }

    /**
     * Teste le bon fonctionnement de la fonction update().
     *
     * @return void
     */
    public function testUpdate()
    {
        $this->pdoMock->shouldReceive('prepare')
                      ->with($this->stringContains('UPDATE ecran_alert'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':id', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':content',
                                   'Alerte test',
                                   PDO::PARAM_STR);
        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':expirationDate',
                                   '2025-01-10',
                                   PDO::PARAM_STR);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('rowCount'
        )->andReturn(1);

        $this->pdoMock->shouldReceive('prepare')->with(
            'DELETE FROM ecran_code_alert WHERE alert_id = :alertId'
        )->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':alertId', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);

        $this->pdoMock->shouldReceive('prepare')->with(
                    $this->stringContains('INSERT INTO ecran_code_alert'))
                        ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':alertId', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':codeAdeId', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('rowCount')
                               ->andReturn(1);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':codeAdeId', 2, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('rowCount')
                               ->andReturn(1);

        $result = $this->alert->update();

        $this->assertEquals(3, $result);
    }

    /**
     * Teste le bon fonctionnement de la fonction delete()
     *
     * @return void
     */
    public function testDelete()
    {
        $this->pdoMock->shouldReceive('prepare')->with(
            $this->stringContains('DELETE FROM ecran_alert WHERE id = :id'))
                      ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
                               ->with(':id', 1, PDO::PARAM_INT);
        $this->pdoStatementMock->shouldReceive('execute')
                               ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('rowCount')
                               ->andReturn(1);

        $result = $this->alert->delete();

        $this->assertEquals(1, $result);
    }

    protected function tearDown(): void
    {
        m::close();
    }
}