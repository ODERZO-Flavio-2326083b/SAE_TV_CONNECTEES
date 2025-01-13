<?php

use PHPUnit\Framework\TestCase;
use models\Localisation;
use Mockery as m;

class LocalisationTest extends TestCase
{
    private $pdoMock;
    private $pdoStatementMock;
    private $loc;

    protected function setUp(): void
    {
        $this->pdoMock = Mockery::mock(PDO::class);
        $this->pdoStatementMock = Mockery::mock(PDOStatement::class);

        $this->loc = m::mock(Localisation::class)->makePartial();
        $this->loc->shouldAllowMockingProtectedMethods();
        $this->loc->shouldReceive('getDatabase')
            ->once()
            ->andReturn($this->pdoMock);

        $this->loc->setLocalisationId(1);
        $this->loc->setLatitude(10.0);
        $this->loc->setLongitude(20.0);
        $this->loc->setUserId(2);
        $this->loc->setAdresse('Adresse');

    }

    public function testInsert()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('INSERT INTO ecran_localisation'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':latitude', 10.0);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':longitude', 20.0);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':user_id', 2);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoMock->shouldReceive('lastInsertId')
            ->andReturn(1);

        $result = $this->loc->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('UPDATE ecran_localisation'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':latitude', 10.0);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':longitude', 20.0);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':user_id', 2);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', 1);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':adresse', 'Adresse');

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->loc->update();

        $this->assertEquals(1, $result);
    }

    public function testDelete()
    {
        $this->pdoMock->shouldReceive('prepare')
            ->with($this->stringContains('DELETE FROM ecran_localisation'))
            ->once()
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with(':id', 1, PDO::PARAM_INT);

        $this->pdoStatementMock->shouldReceive('execute')
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('rowCount')
            ->andReturn(1);

        $result = $this->loc->delete();

        $this->assertEquals(1, $result);

    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}