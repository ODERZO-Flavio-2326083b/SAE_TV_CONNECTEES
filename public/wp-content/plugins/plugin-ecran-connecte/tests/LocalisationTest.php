<?php

use PHPUnit\Framework\TestCase;
use models\Localisation;
use Mockery\Mock;

class LocalisationTest extends TestCase
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
        $localisationMock = Mockery::mock(Localisation::class)->makePartial();
        $localisationMock->shouldAllowMockingProtectedMethods();
        $localisationMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('INSERT INTO ecran_localisation'))
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

        $localisationMock->shouldReceive('getLatitude')->andReturn(45.123);
        $localisationMock->shouldReceive('getLongitude')->andReturn(2.345);
        $localisationMock->shouldReceive('getUserId')->andReturn(1);

        $result = $localisationMock->insert();

        $this->assertEquals(1, $result);
    }

    public function testUpdate()
    {
        $localisationMock = Mockery::mock(Localisation::class)->makePartial();
        $localisationMock->shouldAllowMockingProtectedMethods();
        $localisationMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('UPDATE ecran_localisation'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $this->pdoStatementMock->shouldReceive('bindValue')
            ->with($this->anything(), $this->anything(), $this->anything())
            ->andReturn(true);

        $localisationMock->shouldReceive('getLocalisationId')->andReturn(1);
        $localisationMock->shouldReceive('getLatitude')->andReturn(45.678);
        $localisationMock->shouldReceive('getLongitude')->andReturn(2.890);
        $localisationMock->shouldReceive('getAdresse')->andReturn('New address');
        $localisationMock->shouldReceive('getUserId')->andReturn(1);

        $result = $localisationMock->update();

        $this->assertEquals(1, $result); // Check if one row was updated
    }

    public function testDelete()
    {
        $localisationMock = Mockery::mock(Localisation::class)->makePartial();
        $localisationMock->shouldAllowMockingProtectedMethods();
        $localisationMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('DELETE FROM ecran_localisation'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $localisationMock->shouldReceive('getLocalisationId')->andReturn(1);

        $result = $localisationMock->delete();

        $this->assertEquals(1, $result);
    }

    public function testGet()
    {
        $localisationMock = Mockery::mock(Localisation::class)->makePartial();
        $localisationMock->shouldAllowMockingProtectedMethods();
        $localisationMock->shouldReceive('getDatabase')->andReturn($this->pdoMock);

        $this->pdoMock->shouldReceive('prepare')
            ->once()
            ->with($this->stringContains('SELECT localisation_id'))
            ->andReturn($this->pdoStatementMock);

        $this->pdoStatementMock->shouldReceive('execute')
            ->once()
            ->andReturn(true);
        $this->pdoStatementMock->shouldReceive('fetch')
            ->once()
            ->andReturn([
                'localisation_id' => 1,
                'latitude' => 45.123,
                'longitude' => 2.345,
                'adresse' => 'Some address',
                'user_id' => 1
            ]);

        $result = $localisationMock->get(1);

        $this->assertInstanceOf(Localisation::class, $result); // Ensure the result is a Localisation object
        $this->assertEquals(1, $result->getLocalisationId()); // Check if the ID matches
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}