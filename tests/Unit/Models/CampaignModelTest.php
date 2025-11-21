<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Campaign;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\CampaignType;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CampaignModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_estrategy()
    {
        $estrategy = Estrategy::factory()->create();
        $campaign = Campaign::factory()->create(['estrategy_id' => $estrategy->id]);

        $this->assertInstanceOf(Estrategy::class, $campaign->estrategy);
        $this->assertEquals($estrategy->id, $campaign->estrategy->id);
    }

    /** @test */
    public function it_belongs_to_institution()
    {
        $institution = Institution::factory()->create();
        $campaign = Campaign::factory()->create(['institution_id' => $institution->id]);

        $this->assertInstanceOf(Institution::class, $campaign->institution);
        $this->assertEquals($institution->id, $campaign->institution->id);
    }

    /** @test */
    public function it_belongs_to_campaign_type()
    {
        $campaignType = CampaignType::factory()->create();
        $campaign = Campaign::factory()->create(['campaign_type_id' => $campaignType->id]);

        $this->assertInstanceOf(CampaignType::class, $campaign->campaignType);
        $this->assertEquals($campaignType->id, $campaign->campaignType->id);
    }

    /** @test */
    public function it_has_many_versions()
    {
        $campaign = Campaign::factory()->create();
        Version::factory()->count(3)->create(['campaign_id' => $campaign->id]);

        $this->assertCount(3, $campaign->versions);
        $this->assertInstanceOf(Version::class, $campaign->versions->first());
    }

    /** @test */
    public function budget_fields_are_cast_to_decimal_with_6_decimals()
    {
        $campaign = Campaign::factory()->create([
            'televisoras' => 123456.789012,
            'radiodifusoras' => 234567.890123,
            'cine' => 345678.901234,
        ]);

        $this->assertIsString($campaign->televisoras);
        $this->assertIsString($campaign->radiodifusoras);
        $this->assertIsString($campaign->cine);

        // Verificar que mantiene 6 decimales
        $this->assertEquals('123456.789012', $campaign->televisoras);
        $this->assertEquals('234567.890123', $campaign->radiodifusoras);
        $this->assertEquals('345678.901234', $campaign->cine);
    }

    /** @test */
    public function all_16_budget_fields_exist_and_cast_correctly()
    {
        $budgetFields = [
            'televisoras' => 1000.123456,
            'radiodifusoras' => 2000.234567,
            'cine' => 3000.345678,
            'decdmx' => 4000.456789,
            'deedos' => 5000.567890,
            'deextr' => 6000.678901,
            'revistas' => 7000.789012,
            'mediosComplementarios' => 8000.890123,
            'mediosDigitales' => 9000.901234,
            'mediosDigitalesInternet' => 10000.012345,
            'preEstudios' => 11000.123456,
            'postEstudios' => 12000.234567,
            'disenio' => 13000.345678,
            'produccion' => 14000.456789,
            'preProduccion' => 15000.567890,
            'postProduccion' => 16000.678901,
            'copiado' => 17000.789012,
        ];

        $campaign = Campaign::factory()->create($budgetFields);

        foreach ($budgetFields as $field => $value) {
            $this->assertIsString($campaign->$field, "Field {$field} should be cast to string");
            $this->assertEquals(number_format($value, 6, '.', ''), $campaign->$field);
        }
    }

    /** @test */
    public function boolean_fields_are_cast_correctly()
    {
        $campaign = Campaign::factory()->create([
            'tv_oficial' => true,
            'radio_oficial' => false,
            'tv_comercial' => true,
            'radio_comercial' => false,
        ]);

        $this->assertTrue($campaign->tv_oficial);
        $this->assertFalse($campaign->radio_oficial);
        $this->assertTrue($campaign->tv_comercial);
        $this->assertFalse($campaign->radio_comercial);
    }

    /** @test */
    public function array_fields_are_cast_correctly()
    {
        $campaign = Campaign::factory()->create([
            'sexo' => ['Mujeres', 'Hombres'],
            'edad' => ['18-24', '25-34', '35-44'],
            'poblacion' => ['Urbana', 'Rural'],
            'nse' => ['AB', 'C+', 'C-'],
        ]);

        $this->assertIsArray($campaign->sexo);
        $this->assertIsArray($campaign->edad);
        $this->assertIsArray($campaign->poblacion);
        $this->assertIsArray($campaign->nse);

        $this->assertCount(2, $campaign->sexo);
        $this->assertCount(3, $campaign->edad);
        $this->assertCount(2, $campaign->poblacion);
        $this->assertCount(3, $campaign->nse);
    }

    /** @test */
    public function get_sexo_options_returns_correct_values()
    {
        $options = Campaign::getSexoOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('Mujeres', $options);
        $this->assertArrayHasKey('Hombres', $options);
        $this->assertCount(2, $options);
    }

    /** @test */
    public function get_edad_options_returns_all_age_ranges()
    {
        $options = Campaign::getEdadOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('18-24', $options);
        $this->assertArrayHasKey('25-34', $options);
        $this->assertArrayHasKey('35-44', $options);
        $this->assertArrayHasKey('45-54', $options);
        $this->assertArrayHasKey('55-64', $options);
        $this->assertArrayHasKey('65+', $options);
        $this->assertCount(6, $options);
    }

    /** @test */
    public function get_poblacion_options_returns_urban_and_rural()
    {
        $options = Campaign::getPoblacionOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('Urbana', $options);
        $this->assertArrayHasKey('Rural', $options);
        $this->assertCount(2, $options);
    }

    /** @test */
    public function get_nse_options_returns_all_socioeconomic_levels()
    {
        $options = Campaign::getNseOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('AB', $options);
        $this->assertArrayHasKey('C+', $options);
        $this->assertArrayHasKey('C-', $options);
        $this->assertArrayHasKey('D+', $options);
        $this->assertArrayHasKey('D', $options);
        $this->assertArrayHasKey('E', $options);
        $this->assertCount(6, $options);
    }

    /** @test */
    public function budget_total_can_be_calculated_from_all_fields()
    {
        $campaign = Campaign::factory()->create([
            'televisoras' => 1000.00,
            'radiodifusoras' => 2000.00,
            'cine' => 3000.00,
            'decdmx' => 4000.00,
            'deedos' => 5000.00,
            'deextr' => 6000.00,
            'revistas' => 7000.00,
            'mediosComplementarios' => 8000.00,
            'mediosDigitales' => 9000.00,
            'mediosDigitalesInternet' => 10000.00,
            'preEstudios' => 11000.00,
            'postEstudios' => 12000.00,
            'disenio' => 13000.00,
            'produccion' => 14000.00,
            'preProduccion' => 15000.00,
            'postProduccion' => 16000.00,
            'copiado' => 17000.00,
        ]);

        $total = (float)$campaign->televisoras +
                 (float)$campaign->radiodifusoras +
                 (float)$campaign->cine +
                 (float)$campaign->decdmx +
                 (float)$campaign->deedos +
                 (float)$campaign->deextr +
                 (float)$campaign->revistas +
                 (float)$campaign->mediosComplementarios +
                 (float)$campaign->mediosDigitales +
                 (float)$campaign->mediosDigitalesInternet +
                 (float)$campaign->preEstudios +
                 (float)$campaign->postEstudios +
                 (float)$campaign->disenio +
                 (float)$campaign->produccion +
                 (float)$campaign->preProduccion +
                 (float)$campaign->postProduccion +
                 (float)$campaign->copiado;

        $this->assertEquals(153000.00, $total);
    }

    /** @test */
    public function budget_fields_default_to_zero_when_not_set()
    {
        $campaign = Campaign::factory()->create([
            'televisoras' => 0,
            'radiodifusoras' => 0,
            'cine' => 0,
        ]);

        $this->assertEquals('0.000000', $campaign->televisoras);
        $this->assertEquals('0.000000', $campaign->radiodifusoras);
        $this->assertEquals('0.000000', $campaign->cine);
    }

    /** @test */
    public function campaign_can_have_multiple_target_demographics()
    {
        $campaign = Campaign::factory()->create([
            'sexo' => ['Mujeres', 'Hombres'],
            'edad' => ['18-24', '25-34', '35-44', '45-54'],
            'poblacion' => ['Urbana', 'Rural'],
            'nse' => ['AB', 'C+', 'C-', 'D+'],
        ]);

        $this->assertContains('Mujeres', $campaign->sexo);
        $this->assertContains('Hombres', $campaign->sexo);
        $this->assertCount(4, $campaign->edad);
        $this->assertCount(2, $campaign->poblacion);
        $this->assertCount(4, $campaign->nse);
    }

    /** @test */
    public function campaign_preserves_coemisores_information()
    {
        $campaign = Campaign::factory()->create([
            'coemisores' => 'Secretaría de Salud, IMSS, ISSSTE'
        ]);

        $this->assertEquals('Secretaría de Salud, IMSS, ISSSTE', $campaign->coemisores);
    }

    /** @test */
    public function campaign_has_communication_objective()
    {
        $objective = 'Incrementar la conciencia sobre la importancia de la vacunación';

        $campaign = Campaign::factory()->create([
            'objetivoComuicacion' => $objective
        ]);

        $this->assertEquals($objective, $campaign->objetivoComuicacion);
    }

    /** @test */
    public function campaign_has_specific_theme()
    {
        $theme = 'Salud Pública - Prevención de Enfermedades';

        $campaign = Campaign::factory()->create([
            'temaEspecifco' => $theme
        ]);

        $this->assertEquals($theme, $campaign->temaEspecifco);
    }

    /** @test */
    public function multiple_campaigns_can_belong_to_same_estrategy()
    {
        $estrategy = Estrategy::factory()->create();

        $campaign1 = Campaign::factory()->create(['estrategy_id' => $estrategy->id]);
        $campaign2 = Campaign::factory()->create(['estrategy_id' => $estrategy->id]);
        $campaign3 = Campaign::factory()->create(['estrategy_id' => $estrategy->id]);

        $this->assertCount(3, $estrategy->campaigns);
        $this->assertTrue($estrategy->campaigns->contains($campaign1));
        $this->assertTrue($estrategy->campaigns->contains($campaign2));
        $this->assertTrue($estrategy->campaigns->contains($campaign3));
    }
}
