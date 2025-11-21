<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\JuridicalNature;
use App\Models\Responsable;
use App\Models\User;
use App\Models\Role;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EstrategyModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_syncs_institution_name_when_institution_id_is_set()
    {
        $institution = Institution::factory()->create(['name' => 'Test Institution']);

        $estrategy = new Estrategy();
        $estrategy->institution_id = $institution->id;

        $this->assertEquals('Test Institution', $estrategy->institution_name);
        $this->assertEquals($institution->id, $estrategy->institution_id);
    }

    /** @test */
    public function it_syncs_responsable_when_institution_id_is_set_and_responsable_exists()
    {
        $institution = Institution::factory()->create();
        $responsable = Responsable::factory()->create([
            'institution_id' => $institution->id,
            'name' => 'John Doe'
        ]);

        $estrategy = new Estrategy();
        $estrategy->institution_id = $institution->id;

        $this->assertEquals($responsable->id, $estrategy->responsable_id);
        $this->assertEquals('John Doe', $estrategy->responsable_name);
    }

    /** @test */
    public function it_clears_institution_name_when_institution_id_is_null()
    {
        $estrategy = new Estrategy();
        $estrategy->institution_id = null;

        $this->assertNull($estrategy->institution_name);
        $this->assertNull($estrategy->responsable_id);
        $this->assertNull($estrategy->responsable_name);
    }

    /** @test */
    public function it_syncs_juridical_nature_name_when_juridical_nature_id_is_set()
    {
        $juridicalNature = JuridicalNature::factory()->create(['name' => 'Public Administration']);

        $estrategy = new Estrategy();
        $estrategy->juridical_nature_id = $juridicalNature->id;

        $this->assertEquals('Public Administration', $estrategy->juridical_nature_name);
        $this->assertEquals($juridicalNature->id, $estrategy->juridical_nature_id);
    }

    /** @test */
    public function it_clears_juridical_nature_name_when_juridical_nature_id_is_null()
    {
        $estrategy = new Estrategy();
        $estrategy->juridical_nature_id = null;

        $this->assertNull($estrategy->juridical_nature_name);
    }

    /** @test */
    public function it_syncs_responsable_name_when_responsable_id_is_set()
    {
        $responsable = Responsable::factory()->create(['name' => 'Jane Smith']);

        $estrategy = new Estrategy();
        $estrategy->responsable_id = $responsable->id;

        $this->assertEquals('Jane Smith', $estrategy->responsable_name);
        $this->assertEquals($responsable->id, $estrategy->responsable_id);
    }

    /** @test */
    public function it_clears_responsable_name_when_responsable_id_is_null()
    {
        $estrategy = new Estrategy();
        $estrategy->responsable_id = null;

        $this->assertNull($estrategy->responsable_name);
    }

    /** @test */
    public function global_scope_filters_by_institution_for_regular_users()
    {
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();
        $role = Role::factory()->create(['name' => 'institution_user']);

        $user = User::factory()->create([
            'institution_id' => $institution1->id,
            'role_id' => $role->id
        ]);

        Estrategy::factory()->create(['institution_id' => $institution1->id]);
        Estrategy::factory()->create(['institution_id' => $institution2->id]);

        $this->actingAs($user);

        $estrategies = Estrategy::all();

        $this->assertCount(1, $estrategies);
        $this->assertEquals($institution1->id, $estrategies->first()->institution_id);
    }

    /** @test */
    public function global_scope_does_not_filter_for_super_admin()
    {
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();
        $role = Role::factory()->superAdmin()->create();

        $user = User::factory()->create([
            'institution_id' => $institution1->id,
            'role_id' => $role->id
        ]);

        Estrategy::factory()->create(['institution_id' => $institution1->id]);
        Estrategy::factory()->create(['institution_id' => $institution2->id]);

        $this->actingAs($user);

        $estrategies = Estrategy::all();

        $this->assertCount(2, $estrategies);
    }

    /** @test */
    public function for_user_scope_filters_by_institution()
    {
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();
        $role = Role::factory()->create(['name' => 'institution_user']);

        $user = User::factory()->create([
            'institution_id' => $institution1->id,
            'role_id' => $role->id
        ]);

        Estrategy::factory()->create(['institution_id' => $institution1->id]);
        Estrategy::factory()->create(['institution_id' => $institution2->id]);

        $estrategies = Estrategy::withoutGlobalScopes()->forUser($user)->get();

        $this->assertCount(1, $estrategies);
        $this->assertEquals($institution1->id, $estrategies->first()->institution_id);
    }

    /** @test */
    public function for_user_scope_does_not_filter_for_super_admin()
    {
        $institution1 = Institution::factory()->create();
        $institution2 = Institution::factory()->create();
        $role = Role::factory()->superAdmin()->create();

        $user = User::factory()->create([
            'institution_id' => $institution1->id,
            'role_id' => $role->id
        ]);

        Estrategy::factory()->create(['institution_id' => $institution1->id]);
        Estrategy::factory()->create(['institution_id' => $institution2->id]);

        $estrategies = Estrategy::withoutGlobalScopes()->forUser($user)->get();

        $this->assertCount(2, $estrategies);
    }

    /** @test */
    public function is_latest_for_institution_and_year_returns_true_for_latest_strategy()
    {
        $institution = Institution::factory()->create();

        $old = Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()->subDays(5)
        ]);

        $latest = Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()
        ]);

        $this->assertTrue($latest->isLatestForInstitutionAndYear());
        $this->assertFalse($old->isLatestForInstitutionAndYear());
    }

    /** @test */
    public function is_latest_for_institution_and_year_considers_year_separately()
    {
        $institution = Institution::factory()->create();

        $strategy2023 = Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2023,
            'created_at' => now()->subYear()
        ]);

        $strategy2024 = Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()
        ]);

        $this->assertTrue($strategy2023->isLatestForInstitutionAndYear());
        $this->assertTrue($strategy2024->isLatestForInstitutionAndYear());
    }

    /** @test */
    public function get_latest_for_institution_and_year_returns_most_recent()
    {
        $institution = Institution::factory()->create();

        Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()->subDays(5)
        ]);

        $latest = Estrategy::factory()->create([
            'institution_id' => $institution->id,
            'anio' => 2024,
            'created_at' => now()
        ]);

        $result = Estrategy::getLatestForInstitutionAndYear($institution->id, 2024);

        $this->assertEquals($latest->id, $result->id);
    }

    /** @test */
    public function get_latest_for_institution_and_year_returns_null_when_not_found()
    {
        $institution = Institution::factory()->create();

        $result = Estrategy::getLatestForInstitutionAndYear($institution->id, 2024);

        $this->assertNull($result);
    }

    /** @test */
    public function it_has_campaigns_relationship()
    {
        $estrategy = Estrategy::factory()->create();
        Campaign::factory()->count(3)->create(['estrategy_id' => $estrategy->id]);

        $this->assertCount(3, $estrategy->campaigns);
        $this->assertInstanceOf(Campaign::class, $estrategy->campaigns->first());
    }

    /** @test */
    public function it_has_institution_relationship()
    {
        $institution = Institution::factory()->create();
        $estrategy = Estrategy::factory()->create(['institution_id' => $institution->id]);

        $this->assertInstanceOf(Institution::class, $estrategy->institution);
        $this->assertEquals($institution->id, $estrategy->institution->id);
    }

    /** @test */
    public function it_has_juridical_nature_relationship()
    {
        $juridicalNature = JuridicalNature::factory()->create();
        $estrategy = Estrategy::factory()->create(['juridical_nature_id' => $juridicalNature->id]);

        $this->assertInstanceOf(JuridicalNature::class, $estrategy->juridicalNature);
        $this->assertEquals($juridicalNature->id, $estrategy->juridicalNature->id);
    }

    /** @test */
    public function it_has_responsable_relationship()
    {
        $responsable = Responsable::factory()->create();
        $estrategy = Estrategy::factory()->create(['responsable_id' => $responsable->id]);

        $this->assertInstanceOf(Responsable::class, $estrategy->responsable);
        $this->assertEquals($responsable->id, $estrategy->responsable->id);
    }

    /** @test */
    public function it_has_estrategia_original_relationship()
    {
        $original = Estrategy::factory()->create();
        $modificacion = Estrategy::factory()->modificacion()->create([
            'estrategia_original_id' => $original->id
        ]);

        $this->assertInstanceOf(Estrategy::class, $modificacion->estrategiaOriginal);
        $this->assertEquals($original->id, $modificacion->estrategiaOriginal->id);
    }

    /** @test */
    public function it_has_modificaciones_relationship()
    {
        $original = Estrategy::factory()->create();
        Estrategy::factory()->modificacion()->count(2)->create([
            'estrategia_original_id' => $original->id
        ]);

        $this->assertCount(2, $original->modificaciones);
        $this->assertInstanceOf(Estrategy::class, $original->modificaciones->first());
    }

    /** @test */
    public function get_ejes_seleccionados_attribute_returns_array()
    {
        $estrategy = Estrategy::factory()->create([
            'ejes_plan_nacional' => ['eje_general_1_gobernanza', 'eje_general_2_desarrollo']
        ]);

        $ejes = $estrategy->ejes_seleccionados;

        $this->assertIsArray($ejes);
        $this->assertCount(2, $ejes);
        $this->assertContains('eje_general_1_gobernanza', $ejes);
    }

    /** @test */
    public function get_ejes_seleccionados_attribute_returns_empty_array_when_null()
    {
        $estrategy = Estrategy::factory()->create([
            'ejes_plan_nacional' => null
        ]);

        $ejes = $estrategy->ejes_seleccionados;

        $this->assertIsArray($ejes);
        $this->assertEmpty($ejes);
    }

    /** @test */
    public function presupuesto_is_cast_to_decimal()
    {
        $estrategy = Estrategy::factory()->create([
            'presupuesto' => 1234567.89
        ]);

        $this->assertIsString($estrategy->presupuesto);
        $this->assertEquals('1234567.89', $estrategy->presupuesto);
    }

    /** @test */
    public function dates_are_cast_correctly()
    {
        $estrategy = Estrategy::factory()->create([
            'fecha_elaboracion' => '2024-01-15',
            'fecha_envio_dgnc' => '2024-02-20',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $estrategy->fecha_elaboracion);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $estrategy->fecha_envio_dgnc);
    }

    /** @test */
    public function get_estados_options_returns_all_workflow_states()
    {
        $estados = Estrategy::getEstadosOptions();

        $this->assertIsArray($estados);
        $this->assertArrayHasKey('Creada', $estados);
        $this->assertArrayHasKey('Enviada a CS', $estados);
        $this->assertArrayHasKey('Aceptada CS', $estados);
        $this->assertArrayHasKey('Enviada a DGNC', $estados);
        $this->assertArrayHasKey('Autorizada', $estados);
        $this->assertArrayHasKey('Rechazada CS', $estados);
        $this->assertArrayHasKey('Rechazada DGNC', $estados);
        $this->assertArrayHasKey('Observada DGNC', $estados);
    }

    /** @test */
    public function get_conceptos_options_returns_all_concepts()
    {
        $conceptos = Estrategy::getConceptosOptions();

        $this->assertIsArray($conceptos);
        $this->assertArrayHasKey('Registro', $conceptos);
        $this->assertArrayHasKey('Modificacion', $conceptos);
        $this->assertArrayHasKey('Solventacion', $conceptos);
        $this->assertArrayHasKey('Cancelacion', $conceptos);
    }

    /** @test */
    public function get_ejes_generales_options_returns_all_general_axes()
    {
        $ejes = Estrategy::getEjesGeneralesOptions();

        $this->assertIsArray($ejes);
        $this->assertCount(4, $ejes);
        $this->assertArrayHasKey('eje_general_1_gobernanza', $ejes);
        $this->assertArrayHasKey('eje_general_2_desarrollo', $ejes);
        $this->assertArrayHasKey('eje_general_3_economia', $ejes);
        $this->assertArrayHasKey('eje_general_4_sustentable', $ejes);
    }

    /** @test */
    public function get_ejes_transversales_options_returns_all_transversal_axes()
    {
        $ejes = Estrategy::getEjesTransversalesOptions();

        $this->assertIsArray($ejes);
        $this->assertCount(3, $ejes);
        $this->assertArrayHasKey('eje_transversal_1_igualdad', $ejes);
        $this->assertArrayHasKey('eje_transversal_2_innovacion', $ejes);
        $this->assertArrayHasKey('eje_transversal_3_derechos', $ejes);
    }
}
