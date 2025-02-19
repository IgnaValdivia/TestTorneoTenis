<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\PartidaService;
use App\Repositories\PartidaRepository;
use App\Models\Partida;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;


class PartidaServiceTest extends TestCase
{
    use RefreshDatabase;

    private PartidaService $partidaService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->partidaService = new PartidaService(new PartidaRepository());
    }

    #[Test]
    public function puede_obtener_una_partida_por_id()
    {
        $partida = Partida::factory()->create();
        $partidaEncontrada = $this->partidaService->findById($partida->id);

        $this->assertNotNull($partidaEncontrada);
        $this->assertEquals($partida->id, $partidaEncontrada->id);
    }
}
