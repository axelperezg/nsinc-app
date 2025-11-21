<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EstrategyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function estrategy_starts_in_creada_state()
    {
        $estrategy = Estrategy::factory()->create();

        $this->assertEquals('Creada', $estrategy->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_transition_from_creada_to_enviada_cs()
    {
        $estrategy = Estrategy::factory()->creada()->create();

        $estrategy->update(['estado_estrategia' => 'Enviada a CS']);

        $this->assertEquals('Enviada a CS', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_be_accepted_by_sector_coordinator()
    {
        $estrategy = Estrategy::factory()->enviadaCS()->create();

        $estrategy->update(['estado_estrategia' => 'Aceptada CS']);

        $this->assertEquals('Aceptada CS', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_be_rejected_by_sector_coordinator()
    {
        $estrategy = Estrategy::factory()->enviadaCS()->create();

        $estrategy->update(['estado_estrategia' => 'Rechazada CS']);

        $this->assertEquals('Rechazada CS', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_be_sent_to_dgnc()
    {
        $estrategy = Estrategy::factory()->aceptadaCS()->create();

        $estrategy->update([
            'estado_estrategia' => 'Enviada a DGNC',
            'fecha_envio_dgnc' => now()
        ]);

        $this->assertEquals('Enviada a DGNC', $estrategy->fresh()->estado_estrategia);
        $this->assertNotNull($estrategy->fresh()->fecha_envio_dgnc);
    }

    /** @test */
    public function estrategy_can_be_authorized_by_dgnc()
    {
        $estrategy = Estrategy::factory()->enviadaDGNC()->create();

        $estrategy->update(['estado_estrategia' => 'Autorizada']);

        $this->assertEquals('Autorizada', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_be_rejected_by_dgnc()
    {
        $estrategy = Estrategy::factory()->enviadaDGNC()->create();

        $estrategy->update(['estado_estrategia' => 'Rechazada DGNC']);

        $this->assertEquals('Rechazada DGNC', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function estrategy_can_be_observed_by_dgnc()
    {
        $estrategy = Estrategy::factory()->enviadaDGNC()->create();

        $estrategy->update(['estado_estrategia' => 'Observada DGNC']);

        $this->assertEquals('Observada DGNC', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function modificacion_can_be_created_from_authorized_strategy()
    {
        $original = Estrategy::factory()->autorizada()->create();

        $modificacion = Estrategy::factory()->modificacion()->create([
            'estrategia_original_id' => $original->id,
            'institution_id' => $original->institution_id,
            'anio' => $original->anio,
        ]);

        $this->assertEquals('Modificacion', $modificacion->concepto);
        $this->assertEquals($original->id, $modificacion->estrategia_original_id);
    }

    /** @test */
    public function solventacion_can_be_created_from_observed_strategy()
    {
        $original = Estrategy::factory()->observadaDGNC()->create();

        $solventacion = Estrategy::factory()->solventacion()->create([
            'estrategia_original_id' => $original->id,
            'institution_id' => $original->institution_id,
            'anio' => $original->anio,
        ]);

        $this->assertEquals('Solventacion', $solventacion->concepto);
        $this->assertEquals($original->id, $solventacion->estrategia_original_id);
    }

    /** @test */
    public function cancelacion_can_be_created_from_authorized_strategy()
    {
        $original = Estrategy::factory()->autorizada()->create();

        $cancelacion = Estrategy::factory()->cancelacion()->create([
            'estrategia_original_id' => $original->id,
            'institution_id' => $original->institution_id,
            'anio' => $original->anio,
        ]);

        $this->assertEquals('Cancelacion', $cancelacion->concepto);
        $this->assertEquals($original->id, $cancelacion->estrategia_original_id);
    }

    /** @test */
    public function rejected_strategy_returns_to_creada_state()
    {
        $estrategy = Estrategy::factory()->rechazadaCS()->create();

        $estrategy->update(['estado_estrategia' => 'Creada']);

        $this->assertEquals('Creada', $estrategy->fresh()->estado_estrategia);
    }

    /** @test */
    public function multiple_modifications_can_reference_same_original()
    {
        $original = Estrategy::factory()->autorizada()->create();

        $mod1 = Estrategy::factory()->modificacion()->create([
            'estrategia_original_id' => $original->id,
            'institution_id' => $original->institution_id,
        ]);

        $mod2 = Estrategy::factory()->modificacion()->create([
            'estrategia_original_id' => $original->id,
            'institution_id' => $original->institution_id,
        ]);

        $this->assertEquals($original->id, $mod1->estrategia_original_id);
        $this->assertEquals($original->id, $mod2->estrategia_original_id);
        $this->assertCount(2, $original->fresh()->modificaciones);
    }

    /** @test */
    public function estrategy_maintains_institution_relationship_through_workflow()
    {
        $institution = Institution::factory()->create();
        $estrategy = Estrategy::factory()->create(['institution_id' => $institution->id]);

        $estrategy->update(['estado_estrategia' => 'Enviada a CS']);
        $this->assertEquals($institution->id, $estrategy->fresh()->institution_id);

        $estrategy->update(['estado_estrategia' => 'Aceptada CS']);
        $this->assertEquals($institution->id, $estrategy->fresh()->institution_id);

        $estrategy->update(['estado_estrategia' => 'Enviada a DGNC']);
        $this->assertEquals($institution->id, $estrategy->fresh()->institution_id);

        $estrategy->update(['estado_estrategia' => 'Autorizada']);
        $this->assertEquals($institution->id, $estrategy->fresh()->institution_id);
    }

    /** @test */
    public function only_latest_strategy_per_institution_and_year_can_be_modified()
    {
        $institution = Institution::factory()->create();

        $old = Estrategy::factory()->autorizada()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()->subDays(5)
        ]);

        $latest = Estrategy::factory()->autorizada()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()
        ]);

        $this->assertTrue($latest->isLatestForInstitutionAndYear());
        $this->assertFalse($old->isLatestForInstitutionAndYear());
    }

    /** @test */
    public function each_year_can_have_latest_strategy_per_institution()
    {
        $institution = Institution::factory()->create();

        $strategy2023 = Estrategy::factory()->autorizada()->create([
            'institution_id' => $institution->id,
            'anio' => 2023,
        ]);

        $strategy2024 = Estrategy::factory()->autorizada()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
        ]);

        $this->assertTrue($strategy2023->isLatestForInstitutionAndYear());
        $this->assertTrue($strategy2024->isLatestForInstitutionAndYear());
    }

    /** @test */
    public function estrategy_preserves_all_fields_through_transitions()
    {
        $estrategy = Estrategy::factory()->create([
            'mision' => 'Original Mission',
            'vision' => 'Original Vision',
            'objetivo_institucional' => 'Original Institutional Objective',
            'objetivo_estrategia' => 'Original Strategy Objective',
            'presupuesto' => 1000000.00,
        ]);

        $estrategy->update(['estado_estrategia' => 'Enviada a CS']);

        $fresh = $estrategy->fresh();
        $this->assertEquals('Original Mission', $fresh->mision);
        $this->assertEquals('Original Vision', $fresh->vision);
        $this->assertEquals('Original Institutional Objective', $fresh->objetivo_institucional);
        $this->assertEquals('Original Strategy Objective', $fresh->objetivo_estrategia);
        $this->assertEquals('1000000.00', $fresh->presupuesto);
    }

    /** @test */
    public function fecha_envio_dgnc_is_set_when_sent_to_dgnc()
    {
        $estrategy = Estrategy::factory()->aceptadaCS()->create([
            'fecha_envio_dgnc' => null
        ]);

        $this->assertNull($estrategy->fecha_envio_dgnc);

        $estrategy->update([
            'estado_estrategia' => 'Enviada a DGNC',
            'fecha_envio_dgnc' => now()
        ]);

        $this->assertNotNull($estrategy->fresh()->fecha_envio_dgnc);
    }

    /** @test */
    public function concepto_registro_is_default_for_new_strategies()
    {
        $estrategy = Estrategy::factory()->create();

        $this->assertEquals('Registro', $estrategy->concepto);
    }

    /** @test */
    public function ejes_plan_nacional_persists_through_workflow()
    {
        $ejes = ['eje_general_1_gobernanza', 'eje_transversal_1_igualdad'];

        $estrategy = Estrategy::factory()->create([
            'ejes_plan_nacional' => $ejes
        ]);

        $estrategy->update(['estado_estrategia' => 'Enviada a CS']);
        $this->assertEquals($ejes, $estrategy->fresh()->ejes_plan_nacional);

        $estrategy->update(['estado_estrategia' => 'Autorizada']);
        $this->assertEquals($ejes, $estrategy->fresh()->ejes_plan_nacional);
    }
}
