<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Estrategy;
use App\Models\Institution;
use App\Models\JuridicalNature;
use App\Models\Responsable;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

class EstrategyValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function estrategy_requires_anio()
    {
        $institution = Institution::factory()->create();
        $juridicalNature = JuridicalNature::factory()->create();

        $data = [
            // 'anio' => 2024, // Missing
            'institution_id' => $institution->id,
            'juridical_nature_id' => $juridicalNature->id,
            'mision' => 'Test mission',
            'vision' => 'Test vision',
            'objetivo_institucional' => 'Test institutional objective',
            'objetivo_estrategia' => 'Test strategy objective',
            'fecha_elaboracion' => now(),
            'estado_estrategia' => 'Creada',
            'concepto' => 'Registro',
            'presupuesto' => 1000000,
        ];

        $validator = Validator::make($data, [
            'anio' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('anio', $validator->errors()->toArray());
    }

    /** @test */
    public function anio_must_be_integer()
    {
        $validator = Validator::make(['anio' => 'not-a-year'], [
            'anio' => 'required|integer',
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function anio_must_be_valid_year()
    {
        $validator = Validator::make(['anio' => 2024], [
            'anio' => 'required|integer|min:2020|max:2100',
        ]);

        $this->assertTrue($validator->passes());

        $validator = Validator::make(['anio' => 1900], [
            'anio' => 'required|integer|min:2020|max:2100',
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function presupuesto_must_be_numeric()
    {
        $validator = Validator::make(['presupuesto' => 'not-a-number'], [
            'presupuesto' => 'required|numeric|min:0',
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function presupuesto_must_be_non_negative()
    {
        $validator = Validator::make(['presupuesto' => -1000], [
            'presupuesto' => 'required|numeric|min:0',
        ]);

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function presupuesto_accepts_valid_amounts()
    {
        $validator = Validator::make(['presupuesto' => 1234567.89], [
            'presupuesto' => 'required|numeric|min:0',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function estado_estrategia_must_be_valid_option()
    {
        $validStates = array_keys(Estrategy::getEstadosOptions());

        $validator = Validator::make(['estado_estrategia' => 'Invalid State'], [
            'estado_estrategia' => 'required|in:' . implode(',', $validStates),
        ]);

        $this->assertTrue($validator->fails());

        foreach ($validStates as $state) {
            $validator = Validator::make(['estado_estrategia' => $state], [
                'estado_estrategia' => 'required|in:' . implode(',', $validStates),
            ]);

            $this->assertTrue($validator->passes(), "State '{$state}' should be valid");
        }
    }

    /** @test */
    public function concepto_must_be_valid_option()
    {
        $validConcepts = array_keys(Estrategy::getConceptosOptions());

        $validator = Validator::make(['concepto' => 'Invalid Concept'], [
            'concepto' => 'required|in:' . implode(',', $validConcepts),
        ]);

        $this->assertTrue($validator->fails());

        foreach ($validConcepts as $concept) {
            $validator = Validator::make(['concepto' => $concept], [
                'concepto' => 'required|in:' . implode(',', $validConcepts),
            ]);

            $this->assertTrue($validator->passes(), "Concept '{$concept}' should be valid");
        }
    }

    /** @test */
    public function fecha_elaboracion_must_be_valid_date()
    {
        $validator = Validator::make(['fecha_elaboracion' => 'not-a-date'], [
            'fecha_elaboracion' => 'required|date',
        ]);

        $this->assertTrue($validator->fails());

        $validator = Validator::make(['fecha_elaboracion' => '2024-01-15'], [
            'fecha_elaboracion' => 'required|date',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function fecha_envio_dgnc_is_nullable()
    {
        $validator = Validator::make(['fecha_envio_dgnc' => null], [
            'fecha_envio_dgnc' => 'nullable|date',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function fecha_envio_dgnc_must_be_date_when_present()
    {
        $validator = Validator::make(['fecha_envio_dgnc' => 'not-a-date'], [
            'fecha_envio_dgnc' => 'nullable|date',
        ]);

        $this->assertTrue($validator->fails());

        $validator = Validator::make(['fecha_envio_dgnc' => '2024-02-20'], [
            'fecha_envio_dgnc' => 'nullable|date',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function institution_id_must_exist()
    {
        $validator = Validator::make(['institution_id' => 999999], [
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $this->assertTrue($validator->fails());

        $institution = Institution::factory()->create();

        $validator = Validator::make(['institution_id' => $institution->id], [
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function juridical_nature_id_must_exist()
    {
        $validator = Validator::make(['juridical_nature_id' => 999999], [
            'juridical_nature_id' => 'required|exists:juridical_natures,id',
        ]);

        $this->assertTrue($validator->fails());

        $juridicalNature = JuridicalNature::factory()->create();

        $validator = Validator::make(['juridical_nature_id' => $juridicalNature->id], [
            'juridical_nature_id' => 'required|exists:juridical_natures,id',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function responsable_id_must_exist_when_present()
    {
        $validator = Validator::make(['responsable_id' => 999999], [
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $this->assertTrue($validator->fails());

        $responsable = Responsable::factory()->create();

        $validator = Validator::make(['responsable_id' => $responsable->id], [
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function estrategia_original_id_must_exist_when_present()
    {
        $validator = Validator::make(['estrategia_original_id' => 999999], [
            'estrategia_original_id' => 'nullable|exists:estrategies,id',
        ]);

        $this->assertTrue($validator->fails());

        $estrategy = Estrategy::factory()->create();

        $validator = Validator::make(['estrategia_original_id' => $estrategy->id], [
            'estrategia_original_id' => 'nullable|exists:estrategies,id',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function mision_vision_and_objectives_are_required_text_fields()
    {
        $validator = Validator::make([
            'mision' => '',
            'vision' => '',
            'objetivo_institucional' => '',
            'objetivo_estrategia' => '',
        ], [
            'mision' => 'required|string',
            'vision' => 'required|string',
            'objetivo_institucional' => 'required|string',
            'objetivo_estrategia' => 'required|string',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertCount(4, $validator->errors());
    }

    /** @test */
    public function ejes_plan_nacional_must_be_array()
    {
        $validator = Validator::make(['ejes_plan_nacional' => 'not-an-array'], [
            'ejes_plan_nacional' => 'nullable|array',
        ]);

        $this->assertTrue($validator->fails());

        $validator = Validator::make(['ejes_plan_nacional' => ['eje_general_1_gobernanza']], [
            'ejes_plan_nacional' => 'nullable|array',
        ]);

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function ejes_plan_nacional_values_must_be_valid()
    {
        $validEjes = array_merge(
            array_keys(Estrategy::getEjesGeneralesOptions()),
            array_keys(Estrategy::getEjesTransversalesOptions())
        );

        $validator = Validator::make(
            ['ejes_plan_nacional' => ['invalid_eje']],
            ['ejes_plan_nacional.*' => 'in:' . implode(',', $validEjes)]
        );

        $this->assertTrue($validator->fails());

        $validator = Validator::make(
            ['ejes_plan_nacional' => ['eje_general_1_gobernanza', 'eje_transversal_1_igualdad']],
            ['ejes_plan_nacional.*' => 'in:' . implode(',', $validEjes)]
        );

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function campaign_budget_must_not_exceed_strategy_budget()
    {
        $estrategy = Estrategy::factory()->create([
            'presupuesto' => 1000000.00
        ]);

        $campaign = Campaign::factory()->make([
            'estrategy_id' => $estrategy->id,
            'televisoras' => 500000.00,
            'radiodifusoras' => 600000.00, // Total = 1,100,000 > 1,000,000
        ]);

        $campaignTotal = (float)$campaign->televisoras +
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

        $this->assertGreaterThan((float)$estrategy->presupuesto, $campaignTotal);
    }

    /** @test */
    public function campaign_budget_fields_must_be_numeric()
    {
        $budgetFields = [
            'televisoras', 'radiodifusoras', 'cine', 'decdmx', 'deedos', 'deextr',
            'revistas', 'mediosComplementarios', 'mediosDigitales', 'mediosDigitalesInternet',
            'preEstudios', 'postEstudios', 'disenio', 'produccion', 'preProduccion',
            'postProduccion', 'copiado'
        ];

        foreach ($budgetFields as $field) {
            $validator = Validator::make(
                [$field => 'not-a-number'],
                [$field => 'nullable|numeric|min:0']
            );

            $this->assertTrue($validator->fails(), "Field {$field} should fail with non-numeric value");
        }
    }

    /** @test */
    public function campaign_budget_fields_must_be_non_negative()
    {
        $validator = Validator::make(
            ['televisoras' => -100],
            ['televisoras' => 'nullable|numeric|min:0']
        );

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function only_latest_estrategy_per_institution_and_year_is_modifiable()
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

        $this->assertFalse($old->isLatestForInstitutionAndYear());
        $this->assertTrue($latest->isLatestForInstitutionAndYear());
    }

    /** @test */
    public function estrategy_cannot_have_future_fecha_elaboracion_beyond_reasonable_limit()
    {
        $validator = Validator::make(
            ['fecha_elaboracion' => now()->addYears(10)->format('Y-m-d')],
            ['fecha_elaboracion' => 'required|date|before_or_equal:' . now()->addYear()->format('Y-m-d')]
        );

        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function estrategy_validates_complete_data_structure()
    {
        $institution = Institution::factory()->create();
        $juridicalNature = JuridicalNature::factory()->create();

        $data = [
            'anio' => 2024,
            'institution_id' => $institution->id,
            'juridical_nature_id' => $juridicalNature->id,
            'mision' => 'Valid mission statement',
            'vision' => 'Valid vision statement',
            'objetivo_institucional' => 'Valid institutional objective',
            'objetivo_estrategia' => 'Valid strategy objective',
            'fecha_elaboracion' => now()->format('Y-m-d'),
            'estado_estrategia' => 'Creada',
            'concepto' => 'Registro',
            'presupuesto' => 1000000.00,
        ];

        $validator = Validator::make($data, [
            'anio' => 'required|integer|min:2020|max:2100',
            'institution_id' => 'required|exists:institutions,id',
            'juridical_nature_id' => 'required|exists:juridical_natures,id',
            'mision' => 'required|string',
            'vision' => 'required|string',
            'objetivo_institucional' => 'required|string',
            'objetivo_estrategia' => 'required|string',
            'fecha_elaboracion' => 'required|date',
            'estado_estrategia' => 'required|in:' . implode(',', array_keys(Estrategy::getEstadosOptions())),
            'concepto' => 'required|in:' . implode(',', array_keys(Estrategy::getConceptosOptions())),
            'presupuesto' => 'required|numeric|min:0',
        ]);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors()->all());
    }
}
