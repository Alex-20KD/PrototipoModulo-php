<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Cie10Seeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            // Enfermedades infecciosas y parasitarias (A00-B99)
            ['code' => 'A09',   'description' => 'Diarrea y gastroenteritis de presunto origen infeccioso'],
            ['code' => 'A15',   'description' => 'Tuberculosis respiratoria confirmada bacteriológicamente'],
            ['code' => 'A90',   'description' => 'Fiebre del dengue (dengue clásico)'],
            ['code' => 'A91',   'description' => 'Fiebre del dengue hemorrágico'],
            ['code' => 'B34',   'description' => 'Infección viral no especificada'],
            ['code' => 'B37',   'description' => 'Candidiasis'],
            ['code' => 'B82',   'description' => 'Parasitosis intestinal sin otra especificación'],

            // Neoplasias (C00-D48)
            ['code' => 'D50',   'description' => 'Anemia por deficiencia de hierro'],

            // Enfermedades endocrinas, nutricionales y metabólicas (E00-E90)
            ['code' => 'E03',   'description' => 'Hipotiroidismo no especificado'],
            ['code' => 'E04',   'description' => 'Bocio no tóxico no especificado'],
            ['code' => 'E10',   'description' => 'Diabetes mellitus tipo 1'],
            ['code' => 'E11',   'description' => 'Diabetes mellitus tipo 2'],
            ['code' => 'E44',   'description' => 'Desnutrición proteico-calórica leve a moderada'],
            ['code' => 'E46',   'description' => 'Desnutrición proteico-calórica no especificada'],
            ['code' => 'E55',   'description' => 'Deficiencia de vitamina D'],
            ['code' => 'E66',   'description' => 'Obesidad'],
            ['code' => 'E78',   'description' => 'Trastornos del metabolismo de lipoproteínas (dislipidemia)'],

            // Trastornos mentales y del comportamiento (F00-F99)
            ['code' => 'F32',   'description' => 'Episodio depresivo'],
            ['code' => 'F41',   'description' => 'Trastorno de ansiedad generalizada'],
            ['code' => 'F51',   'description' => 'Trastornos no orgánicos del sueño (insomnio)'],

            // Enfermedades del sistema nervioso (G00-G99)
            ['code' => 'G43',   'description' => 'Migraña'],
            ['code' => 'G44',   'description' => 'Otros síndromes de cefalea'],
            ['code' => 'G47',   'description' => 'Trastornos del sueño'],

            // Enfermedades del ojo (H00-H59)
            ['code' => 'H10',   'description' => 'Conjuntivitis'],
            ['code' => 'H52',   'description' => 'Trastornos de la refracción y de la acomodación'],
            ['code' => 'H66',   'description' => 'Otitis media supurativa y no especificada'],

            // Enfermedades del oído (H60-H95)
            ['code' => 'H60',   'description' => 'Otitis externa'],
            ['code' => 'H65',   'description' => 'Otitis media no supurativa'],

            // Enfermedades del sistema circulatorio (I00-I99)
            ['code' => 'I10',   'description' => 'Hipertensión esencial (primaria)'],
            ['code' => 'I11',   'description' => 'Enfermedad cardíaca hipertensiva'],
            ['code' => 'I20',   'description' => 'Angina de pecho'],
            ['code' => 'I25',   'description' => 'Enfermedad isquémica crónica del corazón'],
            ['code' => 'I50',   'description' => 'Insuficiencia cardíaca'],
            ['code' => 'I64',   'description' => 'Accidente cerebrovascular no especificado'],
            ['code' => 'I84',   'description' => 'Hemorroides'],

            // Enfermedades del sistema respiratorio (J00-J99)
            ['code' => 'J00',   'description' => 'Rinofaringitis aguda (resfriado común)'],
            ['code' => 'J01',   'description' => 'Sinusitis aguda'],
            ['code' => 'J02',   'description' => 'Faringitis aguda'],
            ['code' => 'J03',   'description' => 'Amigdalitis aguda'],
            ['code' => 'J04',   'description' => 'Laringitis y traqueítis agudas'],
            ['code' => 'J06',   'description' => 'Infecciones agudas de las vías respiratorias superiores'],
            ['code' => 'J11',   'description' => 'Influenza (gripe) con virus no identificado'],
            ['code' => 'J15',   'description' => 'Neumonía bacteriana no clasificada'],
            ['code' => 'J18',   'description' => 'Neumonía sin especificar organismo causal'],
            ['code' => 'J20',   'description' => 'Bronquitis aguda'],
            ['code' => 'J30',   'description' => 'Rinitis alérgica y vasomotora'],
            ['code' => 'J31',   'description' => 'Rinitis, nasofaringitis y faringitis crónica'],
            ['code' => 'J40',   'description' => 'Bronquitis no especificada como aguda o crónica'],
            ['code' => 'J42',   'description' => 'Bronquitis crónica no especificada'],
            ['code' => 'J44',   'description' => 'Enfermedad pulmonar obstructiva crónica (EPOC)'],
            ['code' => 'J45',   'description' => 'Asma'],

            // Enfermedades del sistema digestivo (K00-K93)
            ['code' => 'K02',   'description' => 'Caries dental'],
            ['code' => 'K04',   'description' => 'Enfermedades de la pulpa y tejidos periapicales'],
            ['code' => 'K05',   'description' => 'Gingivitis y enfermedades periodontales'],
            ['code' => 'K21',   'description' => 'Enfermedad de reflujo gastroesofágico'],
            ['code' => 'K25',   'description' => 'Úlcera gástrica'],
            ['code' => 'K26',   'description' => 'Úlcera duodenal'],
            ['code' => 'K29',   'description' => 'Gastritis y duodenitis'],
            ['code' => 'K30',   'description' => 'Dispepsia funcional'],
            ['code' => 'K35',   'description' => 'Apendicitis aguda'],
            ['code' => 'K40',   'description' => 'Hernia inguinal'],
            ['code' => 'K59',   'description' => 'Otros trastornos funcionales del intestino (estreñimiento)'],
            ['code' => 'K76',   'description' => 'Hígado graso no alcohólico (esteatosis hepática)'],
            ['code' => 'K80',   'description' => 'Colelitiasis (cálculos biliares)'],
            ['code' => 'K92',   'description' => 'Otras enfermedades del sistema digestivo'],

            // Enfermedades de la piel (L00-L99)
            ['code' => 'L02',   'description' => 'Absceso cutáneo, furúnculo y ántrax'],
            ['code' => 'L20',   'description' => 'Dermatitis atópica'],
            ['code' => 'L23',   'description' => 'Dermatitis alérgica de contacto'],
            ['code' => 'L30',   'description' => 'Dermatitis no especificada'],
            ['code' => 'L50',   'description' => 'Urticaria'],
            ['code' => 'L60',   'description' => 'Trastornos de las uñas (onicomicosis)'],
            ['code' => 'L70',   'description' => 'Acné'],

            // Enfermedades del sistema osteomuscular (M00-M99)
            ['code' => 'M13',   'description' => 'Artritis no especificada'],
            ['code' => 'M15',   'description' => 'Poliartrosis'],
            ['code' => 'M25',   'description' => 'Trastornos articulares no clasificados'],
            ['code' => 'M42',   'description' => 'Osteocondrosis de la columna vertebral'],
            ['code' => 'M54',   'description' => 'Dorsalgia (dolor de espalda / lumbalgia)'],
            ['code' => 'M62',   'description' => 'Otros trastornos de los músculos (espasmo muscular)'],
            ['code' => 'M79',   'description' => 'Otros trastornos de tejidos blandos (mialgia)'],

            // Enfermedades del sistema genitourinario (N00-N99)
            ['code' => 'N12',   'description' => 'Nefritis tubulointersticial no especificada'],
            ['code' => 'N30',   'description' => 'Cistitis'],
            ['code' => 'N39',   'description' => 'Infección de vías urinarias, sitio no especificado'],
            ['code' => 'N72',   'description' => 'Enfermedad inflamatoria del cuello uterino (cervicitis)'],
            ['code' => 'N76',   'description' => 'Otras afecciones inflamatorias de la vagina (vaginitis)'],
            ['code' => 'N92',   'description' => 'Menstruación excesiva, frecuente e irregular'],
            ['code' => 'N94',   'description' => 'Dolor y otras afecciones del aparato genital femenino (dismenorrea)'],

            // Embarazo, parto y puerperio (O00-O99)
            ['code' => 'O23',   'description' => 'Infecciones de las vías genitourinarias en el embarazo'],
            ['code' => 'O80',   'description' => 'Parto único espontáneo'],

            // Malformaciones y afecciones del período perinatal (P00-Q99)
            ['code' => 'R05',   'description' => 'Tos'],
            ['code' => 'R10',   'description' => 'Dolor abdominal y pélvico'],
            ['code' => 'R11',   'description' => 'Náusea y vómito'],
            ['code' => 'R42',   'description' => 'Mareo y desvanecimiento (vértigo)'],
            ['code' => 'R50',   'description' => 'Fiebre de origen desconocido'],
            ['code' => 'R51',   'description' => 'Cefalea'],
            ['code' => 'R53',   'description' => 'Malestar y fatiga'],

            // Traumatismos y causas externas (S00-T98)
            ['code' => 'S00',   'description' => 'Traumatismo superficial de la cabeza'],
            ['code' => 'S61',   'description' => 'Herida de la muñeca y de la mano'],
            ['code' => 'S93',   'description' => 'Luxación, esguince y torcedura del tobillo'],
            ['code' => 'T14',   'description' => 'Traumatismo de región no especificada'],
            ['code' => 'T78',   'description' => 'Efectos adversos no clasificados (reacción alérgica)'],

            // Factores que influyen en el estado de salud (Z00-Z99)
            ['code' => 'Z00',   'description' => 'Examen general e investigación de personas sin quejas'],
            ['code' => 'Z01',   'description' => 'Otros exámenes especiales e investigaciones'],
            ['code' => 'Z30',   'description' => 'Atención para anticoncepción (planificación familiar)'],
            ['code' => 'Z34',   'description' => 'Supervisión de embarazo normal'],
            ['code' => 'Z71',   'description' => 'Personas en contacto con servicios de salud para consejería'],
            ['code' => 'Z76',   'description' => 'Personas en contacto con servicios de salud por otras circunstancias'],
        ];

        foreach ($codes as $code) {
            DB::table('triage_cie10')->updateOrInsert(
                ['code' => $code['code']],
                ['description' => $code['description']],
            );
        }
    }
}
