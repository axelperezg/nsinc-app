<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Institution;
use App\Models\Sector;
use App\Models\JuridicalNature;
use App\Models\Ramo;

class InstitutionSeeder extends Seeder
{
    public function run()
    {
        // Verificar si hay registros existentes
        $existingCount = Institution::count();
        $wasTruncated = false;
        
        if ($existingCount > 0) {
            // Si hay registros, truncar la tabla para borrar todos y reiniciar el auto-increment
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Institution::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $wasTruncated = true;
            
            $this->command->info("🗑️  Tabla institutions truncada ({$existingCount} registros eliminados). IDs reiniciados desde 1.");
        } else {
            $this->command->info('📝 No hay registros existentes. Se crearán nuevos registros.');
        }
        
        // Obtener sectores existentes (deben estar creados previamente con SectorSeeder)
        // Mapeo de sectores por nombre o ID
        $sectors = Sector::all()->keyBy('id');
        
        // Obtener naturalezas jurídicas existentes (deben estar creadas previamente con JuridicalNatureSeeder)
        $juridicalNatures = JuridicalNature::all()->keyBy('id');
        
        // Obtener ramos existentes (deben estar creados previamente con RamoSeeder)
        $ramos = Ramo::all()->keyBy('id');

        // Crear instituciones
        // Datos completos de las 93 instituciones
        $institutions = [
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL ACAPULCO, S.A. DE C.V.', 'acronym' => 'ASIPONA ACAPULCO', 'code' => '13250', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL ALTAMIRA, S.A. DE C.V.', 'acronym' => 'ASIPONA ALTAMIRA', 'code' => '13176', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL COATZACOALCOS, S.A. DE C.V.', 'acronym' => 'ASIPONA COATZACOALCOS', 'code' => '13183', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL DOS BOCAS, S.A. DE C.V.', 'acronym' => 'ASIPONA DOS BOCAS', 'code' => '13167', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL ENSENADA, S.A. DE C.V.', 'acronym' => 'ASIPONA ENSENADA', 'code' => '13169', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL GUAYMAS, S.A. DE C.V.', 'acronym' => 'ASIPONA GUAYMAS', 'code' => '13177', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL LÁZARO CÁRDENAS, S.A. DE C.V.', 'acronym' => 'ASIPONA LÁZARO CARDENAS', 'code' => '13178', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL MANZANILLO, S.A. DE C.V.', 'acronym' => 'ASIPONA MANZANILLO', 'code' => '13179', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL MAZATLÁN, S.A. DE C.V.', 'acronym' => 'ASIPONA MAZATLÁN', 'code' => '13171', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL PROGRESO, S.A. DE C.V.', 'acronym' => 'ASIPONA PROGRESO', 'code' => '13172', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL PUERTO VALLARTA, S.A. DE C.V.', 'acronym' => 'ASIPONA PUERTO VALLARTA', 'code' => '13173', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL SALINA CRUZ, S.A. DE C.V.', 'acronym' => 'ASIPONA SALINA CRUZ', 'code' => '13184', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL TAMPICO, S.A. DE C.V.', 'acronym' => 'ASIPONA TAMPICO', 'code' => '13181', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL TOPOLOBAMPO, S.A. DE C.V.', 'acronym' => 'ASIPONA TOPOLOBAMPO', 'code' => '13174', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL TUXPAN, S.A. DE C.V.', 'acronym' => 'ASIPONA TUXPAN', 'code' => '13175', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'ADMINISTRACIÓN DEL SISTEMA PORTUARIO NACIONAL VERACRUZ, S.A. DE C.V.', 'acronym' => 'ASIPONA VERACRUZ', 'code' => '13182', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'AEROPUERTO INTERNACIONAL DE LA CIUDAD DE MÉXICO, S.A. DE C.V.', 'acronym' => 'AICM', 'code' => '13451', 'sector_id' => 23, 'juridical_nature_id' => 5, 'ramo_id' => 11, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'AEROPUERTO INTERNACIONAL FELIPE ÁNGELES, S.A. DE C.V.', 'acronym' => 'AIFA', 'code' => '07200', 'sector_id' => 21, 'juridical_nature_id' => 5, 'ramo_id' => 5, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'AEROPUERTOS Y SERVICIOS AUXILIARES', 'acronym' => 'ASA', 'code' => '09085', 'sector_id' => 20, 'juridical_nature_id' => 3, 'ramo_id' => 7, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'BANCO NACIONAL DE COMERCIO EXTERIOR, S.N.C.', 'acronym' => 'BANCOMEXT', 'code' => '06305', 'sector_id' => 19, 'juridical_nature_id' => 5, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'BANCO NACIONAL DE OBRAS Y SERVICIOS PÚBLICOS, S.N.C.', 'acronym' => 'BANOBRAS', 'code' => '06320', 'sector_id' => 19, 'juridical_nature_id' => 5, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'BANCO NACIONAL DEL EJÉRCITO, FUERZA AÉREA Y ARMADA, S.N.C.', 'acronym' => 'BANJERCITO', 'code' => '06325', 'sector_id' => 19, 'juridical_nature_id' => 5, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'CAMINOS Y PUENTES FEDERALES DE INGRESOS Y SERVICIOS CONEXOS', 'acronym' => 'CAPUFE', 'code' => '09120', 'sector_id' => 20, 'juridical_nature_id' => 3, 'ramo_id' => 7, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'CASA DE MONEDA DE MÉXICO', 'acronym' => 'CMM', 'code' => '06363', 'sector_id' => 19, 'juridical_nature_id' => 3, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'CENTRO DE INNOVACIÓN APLICADA EN TECNOLOGÍAS COMPETITIVAS, CIATEC, A.C.', 'acronym' => 'CIATEC', 'code' => '38105', 'sector_id' => 12, 'juridical_nature_id' => 4, 'ramo_id' => 19, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'CENTRO NACIONAL DE CONTROL DEL GAS NATURAL', 'acronym' => 'CENAGAS', 'code' => '18700', 'sector_id' => 17, 'juridical_nature_id' => 3, 'ramo_id' => 14, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN EJECUTIVA DE ATENCIÓN A VÍCTIMAS', 'acronym' => 'CEAV', 'code' => '00650', 'sector_id' => 1, 'juridical_nature_id' => 4, 'ramo_id' => 20, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DE ACUACULTURA Y PESCA', 'acronym' => 'CONAPESCA', 'code' => '08003', 'sector_id' => 10, 'juridical_nature_id' => 2, 'ramo_id' => 6, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DE ÁREAS NATURALES PROTEGIDAS', 'acronym' => 'CONANP', 'code' => '16999', 'sector_id' => 24, 'juridical_nature_id' => 2, 'ramo_id' => 13, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DE CULTURA FÍSICA Y DEPORTE', 'acronym' => 'CONADE', 'code' => '11139', 'sector_id' => 16, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DE LIBROS DE TEXTO GRATUITOS', 'acronym' => 'CONALITEG', 'code' => '11137', 'sector_id' => 16, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DEL AGUA', 'acronym' => 'CONAGUA', 'code' => '16005', 'sector_id' => 24, 'juridical_nature_id' => 2, 'ramo_id' => 13, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL DEL SISTEMA DE AHORRO PARA EL RETIRO', 'acronym' => 'CONSAR', 'code' => '06002', 'sector_id' => 19, 'juridical_nature_id' => 2, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL FORESTAL', 'acronym' => 'CONAFOR', 'code' => '16610', 'sector_id' => 24, 'juridical_nature_id' => 3, 'ramo_id' => 13, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL PARA EL USO EFICIENTE DE LA ENERGÍA', 'acronym' => 'CONUEE', 'code' => '18010', 'sector_id' => 17, 'juridical_nature_id' => 2, 'ramo_id' => 14, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMISIÓN NACIONAL PARA LA PROTECCIÓN Y DEFENSA DE LOS USUARIOS DE SERVICIOS FINANCIEROS', 'acronym' => 'CONDUSEF', 'code' => '06370', 'sector_id' => 19, 'juridical_nature_id' => 3, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'COMPAÑÍA OPERADORA DEL CENTRO CULTURAL Y TURÍSTICO DE TIJUANA, S.A. DE C.V.', 'acronym' => 'CECUT', 'code' => '48148', 'sector_id' => 13, 'juridical_nature_id' => 5, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'CONSEJO NACIONAL PARA PREVENIR LA DISCRIMINACIÓN', 'acronym' => 'CONAPRED', 'code' => '04998', 'sector_id' => 18, 'juridical_nature_id' => 3, 'ramo_id' => 2, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'FIDEICOMISO DE FOMENTO MINERO', 'acronym' => 'FIFOMI', 'code' => '10102', 'sector_id' => 15, 'juridical_nature_id' => 5, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'FINANCIERA PARA EL BIENESTAR', 'acronym' => 'FINABIEN', 'code' => '06437', 'sector_id' => 20, 'juridical_nature_id' => 3, 'ramo_id' => 7, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'FONDO DE CULTURA ECONÓMICA', 'acronym' => 'FCE', 'code' => '11615', 'sector_id' => 16, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'FONDO NACIONAL DE FOMENTO AL TURISMO', 'acronym' => 'FONATUR', 'code' => '21359', 'sector_id' => 27, 'juridical_nature_id' => 5, 'ramo_id' => 16, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'FONDO NACIONAL PARA EL FOMENTO DE LAS ARTESANÍAS', 'acronym' => 'FONART', 'code' => '20312', 'sector_id' => 13, 'juridical_nature_id' => 5, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'GRUPO AEROPORTUARIO, FERROVIARIO, DE SERVICIOS AUXILIARES Y CONEXOS, OLMECA-MAYA-MEXICA, S.A. DE C.V.', 'acronym' => 'GAFSACOMM', 'code' => '07100', 'sector_id' => 21, 'juridical_nature_id' => 5, 'ramo_id' => 5, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO DE SEGURIDAD SOCIAL PARA LAS FUERZAS ARMADAS MEXICANAS', 'acronym' => 'ISSFAM', 'code' => '07150', 'sector_id' => 21, 'juridical_nature_id' => 3, 'ramo_id' => 5, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO DE SEGURIDAD Y SERVICIOS SOCIALES DE LOS TRABAJADORES DEL ESTADO', 'acronym' => 'ISSSTE', 'code' => '00637', 'sector_id' => 2, 'juridical_nature_id' => 4, 'ramo_id' => 23, 'isSector' => true, 'control' => 'Directo'],
            ['name' => 'INSTITUTO DEL FONDO NACIONAL PARA EL CONSUMO DE LOS TRABAJADORES', 'acronym' => 'FONACOT', 'code' => '14120', 'sector_id' => 29, 'juridical_nature_id' => 3, 'ramo_id' => 12, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DE CINEMATOGRAFÍA', 'acronym' => 'IMCINE', 'code' => '48312', 'sector_id' => 13, 'juridical_nature_id' => 3, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DE LA JUVENTUD', 'acronym' => 'IMJUVE', 'code' => '14170', 'sector_id' => 29, 'juridical_nature_id' => 2, 'ramo_id' => 12, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DE LA PROPIEDAD INDUSTRIAL', 'acronym' => 'IMPI', 'code' => '10265', 'sector_id' => 15, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DE LA RADIO', 'acronym' => 'IMER', 'code' => '00651', 'sector_id' => 3, 'juridical_nature_id' => 4, 'ramo_id' => 20, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DEL PETRÓLEO', 'acronym' => 'IMP', 'code' => '18474', 'sector_id' => 17, 'juridical_nature_id' => 3, 'ramo_id' => 14, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO MEXICANO DEL SEGURO SOCIAL', 'acronym' => 'IMSS', 'code' => '00641', 'sector_id' => 4, 'juridical_nature_id' => 4, 'ramo_id' => 22, 'isSector' => true, 'control' => 'Directo'],
            ['name' => 'INSTITUTO NACIONAL DE ANTROPOLOGÍA E HISTORIA', 'acronym' => 'INAH', 'code' => '48010', 'sector_id' => 13, 'juridical_nature_id' => 2, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DE ASTROFÍSICA, ÓPTICA Y ELECTRÓNICA', 'acronym' => 'INAOE', 'code' => '38290', 'sector_id' => 12, 'juridical_nature_id' => 3, 'ramo_id' => 19, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DE BELLAS ARTES Y LITERATURA', 'acronym' => 'INBA', 'code' => '48011', 'sector_id' => 13, 'juridical_nature_id' => 2, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DE LAS PERSONAS ADULTAS MAYORES', 'acronym' => 'INAPAM', 'code' => '20227', 'sector_id' => 11, 'juridical_nature_id' => 3, 'ramo_id' => 15, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DE LENGUAS INDÍGENAS', 'acronym' => 'INALI', 'code' => '48311', 'sector_id' => 13, 'juridical_nature_id' => 3, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DE LOS PUEBLOS INDÍGENAS', 'acronym' => 'INPI', 'code' => '00625', 'sector_id' => 5, 'juridical_nature_id' => 4, 'ramo_id' => 20, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL DEL DERECHO DE AUTOR', 'acronym' => 'INDAUTOR', 'code' => '48512', 'sector_id' => 13, 'juridical_nature_id' => 2, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO NACIONAL PARA LA EDUCACIÓN DE LOS ADULTOS', 'acronym' => 'INEA', 'code' => '11310', 'sector_id' => 16, 'juridical_nature_id' => 3, 'ramo_id' => 9, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO PARA LA PROTECCIÓN AL AHORRO BANCARIO', 'acronym' => 'IPAB', 'code' => '06747', 'sector_id' => 19, 'juridical_nature_id' => 3, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'INSTITUTO POLITÉCNICO NACIONAL', 'acronym' => 'IPN', 'code' => '11013', 'sector_id' => 16, 'juridical_nature_id' => 2, 'ramo_id' => 9, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'LOTERÍA NACIONAL', 'acronym' => 'LOTENAL', 'code' => '06821', 'sector_id' => 19, 'juridical_nature_id' => 3, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'NACIONAL FINANCIERA, S.N.C.', 'acronym' => 'NAFIN', 'code' => '06780', 'sector_id' => 19, 'juridical_nature_id' => 5, 'ramo_id' => 4, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'PROCURADURÍA FEDERAL DE PROTECCIÓN AL AMBIENTE', 'acronym' => 'PROFEPA', 'code' => '00956', 'sector_id' => 24, 'juridical_nature_id' => 2, 'ramo_id' => 13, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'PROCURADURÍA FEDERAL DEL CONSUMIDOR', 'acronym' => 'PROFECO', 'code' => '10315', 'sector_id' => 15, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'RADIO EDUCACIÓN', 'acronym' => 'RADIO EDUCACION', 'code' => '48998', 'sector_id' => 13, 'juridical_nature_id' => 2, 'ramo_id' => 21, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA ANTICORRUPCIÓN Y BUEN GOBIERNO', 'acronym' => 'BUEN GOBIERNO', 'code' => '27000', 'sector_id' => 9, 'juridical_nature_id' => 1, 'ramo_id' => 20, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE AGRICULTURA Y DESARROLLO RURAL', 'acronym' => 'SADER', 'code' => '08000', 'sector_id' => 10, 'juridical_nature_id' => 1, 'ramo_id' => 6, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE BIENESTAR', 'acronym' => 'BIENESTAR', 'code' => '20000', 'sector_id' => 11, 'juridical_nature_id' => 1, 'ramo_id' => 15, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE CIENCIA, HUMANIDADES, TECNOLOGÍA E INNOVACIÓN', 'acronym' => 'SECIHTI', 'code' => '38000', 'sector_id' => 12, 'juridical_nature_id' => 1, 'ramo_id' => 19, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE CULTURA', 'acronym' => 'CULTURA', 'code' => '48000', 'sector_id' => 13, 'juridical_nature_id' => 1, 'ramo_id' => 21, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE ECONOMÍA', 'acronym' => 'SE', 'code' => '10000', 'sector_id' => 15, 'juridical_nature_id' => 1, 'ramo_id' => 8, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE EDUCACIÓN PÚBLICA', 'acronym' => 'SEP', 'code' => '11000', 'sector_id' => 16, 'juridical_nature_id' => 1, 'ramo_id' => 9, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE ENERGÍA', 'acronym' => 'SENER', 'code' => '18000', 'sector_id' => 17, 'juridical_nature_id' => 1, 'ramo_id' => 14, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE GOBERNACIÓN', 'acronym' => 'SEGOB', 'code' => '04000', 'sector_id' => 18, 'juridical_nature_id' => 1, 'ramo_id' => 2, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE HACIENDA Y CRÉDITO PÚBLICO', 'acronym' => 'SHCP', 'code' => '06000', 'sector_id' => 19, 'juridical_nature_id' => 1, 'ramo_id' => 4, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE INFRAESTRUCTURA, COMUNICACIONES Y TRANSPORTES', 'acronym' => 'SICT', 'code' => '09000', 'sector_id' => 20, 'juridical_nature_id' => 1, 'ramo_id' => 7, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE LA DEFENSA NACIONAL', 'acronym' => 'SEDENA', 'code' => '07000', 'sector_id' => 21, 'juridical_nature_id' => 1, 'ramo_id' => 5, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE LAS MUJERES', 'acronym' => 'MUJERES', 'code' => '04995', 'sector_id' => 22, 'juridical_nature_id' => 1, 'ramo_id' => 20, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE MARINA', 'acronym' => 'SEMAR', 'code' => '13000', 'sector_id' => 23, 'juridical_nature_id' => 1, 'ramo_id' => 11, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE MEDIO AMBIENTE Y RECURSOS NATURALES', 'acronym' => 'SEMARNAT', 'code' => '16000', 'sector_id' => 24, 'juridical_nature_id' => 1, 'ramo_id' => 13, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE RELACIONES EXTERIORES', 'acronym' => 'SRE', 'code' => '05000', 'sector_id' => 25, 'juridical_nature_id' => 1, 'ramo_id' => 3, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE SALUD', 'acronym' => 'SS', 'code' => '12000', 'sector_id' => 26, 'juridical_nature_id' => 1, 'ramo_id' => 10, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE SEGURIDAD Y PROTECCIÓN CIUDADANA', 'acronym' => 'SSPC', 'code' => '36000', 'sector_id' => 30, 'juridical_nature_id' => 1, 'ramo_id' => 18, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DE TURISMO', 'acronym' => 'SECTUR', 'code' => '21000', 'sector_id' => 27, 'juridical_nature_id' => 1, 'ramo_id' => 16, 'isSector' => true, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA DEL TRABAJO Y PREVISIÓN SOCIAL', 'acronym' => 'STPS', 'code' => '14000', 'sector_id' => 29, 'juridical_nature_id' => 1, 'ramo_id' => 12, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SECRETARÍA GENERAL DEL CONSEJO NACIONAL DE POBLACIÓN', 'acronym' => 'CONAPO', 'code' => '04959', 'sector_id' => 18, 'juridical_nature_id' => 2, 'ramo_id' => 2, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SERVICIO GEOLÓGICO MEXICANO', 'acronym' => 'SGM', 'code' => '10100', 'sector_id' => 15, 'juridical_nature_id' => 3, 'ramo_id' => 8, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SERVICIO NACIONAL DE SANIDAD, INOCUIDAD Y CALIDAD AGROALIMENTARIA', 'acronym' => 'SENASICA', 'code' => '08905', 'sector_id' => 10, 'juridical_nature_id' => 2, 'ramo_id' => 6, 'isSector' => false, 'control' => 'Normal'],
            ['name' => 'SERVICIOS DE SALUD DEL INSTITUTO MEXICANO DEL SEGURO SOCIAL PARA EL BIENESTAR', 'acronym' => 'IMSS-BIENESTAR', 'code' => '47653', 'sector_id' => 28, 'juridical_nature_id' => 4, 'ramo_id' => 23, 'isSector' => true, 'control' => 'Directo'],
            ['name' => 'TREN MAYA, S.A. DE C.V.', 'acronym' => 'TREN MAYA', 'code' => '07323', 'sector_id' => 21, 'juridical_nature_id' => 5, 'ramo_id' => 5, 'isSector' => false, 'control' => 'Normal'],
        ];

        $created = 0;
        $skipped = 0;
        
        foreach ($institutions as $index => $institutionData) {
            // Verificar que los IDs relacionados existan
            $sectorId = $institutionData['sector_id'];
            $juridicalNatureId = $institutionData['juridical_nature_id'];
            $ramoId = $institutionData['ramo_id'];
            
            if (!isset($sectors[$sectorId])) {
                $this->command->error("⚠️  Sector ID {$sectorId} no existe para: {$institutionData['name']}");
                $skipped++;
                continue;
            }
            
            if (!isset($juridicalNatures[$juridicalNatureId])) {
                $this->command->error("⚠️  Naturaleza Jurídica ID {$juridicalNatureId} no existe para: {$institutionData['name']}");
                $skipped++;
                continue;
            }
            
            if (!isset($ramos[$ramoId])) {
                $this->command->error("⚠️  Ramo ID {$ramoId} no existe para: {$institutionData['name']}");
                $skipped++;
                continue;
            }
            
            // Usar los IDs directamente (ya están en el array)
            $data = [
                'name' => $institutionData['name'],
                'acronym' => $institutionData['acronym'],
                'code' => $institutionData['code'],
                'sector_id' => $sectorId,
                'juridical_nature_id' => $juridicalNatureId,
                'ramo_id' => $ramoId,
                'isSector' => $institutionData['isSector'],
                'control' => $institutionData['control'] === 'null' ? null : $institutionData['control'],
            ];
            
            // Si se truncó, usar create (tabla está vacía, no hay riesgo de duplicados)
            // Si no se truncó, usar firstOrCreate para evitar duplicados
            if ($wasTruncated) {
                $institution = Institution::create($data);
                $created++;
                $this->command->info("✅ Creado [ID: {$institution->id}]: {$data['name']} ({$data['acronym']})");
            } else {
                $institution = Institution::firstOrCreate(
                    ['code' => $data['code']],
                    $data
                );
                
                if ($institution->wasRecentlyCreated) {
                    $created++;
                    $this->command->info("✅ Creado [ID: {$institution->id}]: {$data['name']} ({$data['acronym']})");
                } else {
                    $skipped++;
                    $this->command->line("⏭️  Ya existe [ID: {$institution->id}]: {$data['name']} ({$data['acronym']})");
                }
            }
        }

        $this->command->info("\n📊 Resumen:");
        $this->command->info("   - Instituciones creadas: {$created}");
        if ($skipped > 0) {
            $this->command->info("   - Instituciones existentes (omitidas): {$skipped}");
        }
        $this->command->info("   - Total procesados: " . count($institutions));
        
        if ($wasTruncated && $created > 0) {
            $this->command->info("   - IDs iniciales: desde 1 hasta {$created}");
        }
    }
}
