<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            // Analgésicos y antipiréticos
            ['Paracetamol', '500 mg', 'Tableta', 'Oral', 'Analgésico / Antipirético'],
            ['Paracetamol', '120 mg/5 ml', 'Jarabe', 'Oral', 'Analgésico / Antipirético'],
            ['Ibuprofeno', '400 mg', 'Tableta', 'Oral', 'AINE'],
            ['Ibuprofeno', '200 mg/5 ml', 'Suspensión', 'Oral', 'AINE'],
            ['Ácido acetilsalicílico', '100 mg', 'Tableta', 'Oral', 'Antiagregante plaquetario'],
            ['Metamizol', '500 mg', 'Tableta', 'Oral', 'Analgésico / Antipirético'],
            ['Tramadol', '50 mg', 'Cápsula', 'Oral', 'Analgésico opioide', true],
            ['Morfina', '10 mg/ml', 'Solución inyectable', 'IV / IM / SC', 'Analgésico opioide', true],
            ['Diclofenaco', '50 mg', 'Tableta', 'Oral', 'AINE'],
            ['Diclofenaco', '75 mg/3 ml', 'Solución inyectable', 'IM', 'AINE'],
            ['Naproxeno', '250 mg', 'Tableta', 'Oral', 'AINE'],
            ['Ketorolaco', '30 mg/ml', 'Solución inyectable', 'IV / IM', 'AINE'],

            // Antibióticos
            ['Amoxicilina', '500 mg', 'Cápsula', 'Oral', 'Antibiótico'],
            ['Amoxicilina', '250 mg/5 ml', 'Polvo para suspensión', 'Oral', 'Antibiótico'],
            ['Amoxicilina + Ácido clavulánico', '875 mg/125 mg', 'Tableta', 'Oral', 'Antibiótico'],
            ['Azitromicina', '500 mg', 'Tableta', 'Oral', 'Antibiótico'],
            ['Ciprofloxacino', '500 mg', 'Tableta', 'Oral', 'Antibiótico'],
            ['Metronidazol', '500 mg', 'Tableta', 'Oral', 'Antibiótico / Antiparasitario'],
            ['Clindamicina', '300 mg', 'Cápsula', 'Oral', 'Antibiótico'],
            ['Cefalexina', '500 mg', 'Cápsula', 'Oral', 'Antibiótico'],
            ['Ampicilina', '500 mg', 'Cápsula', 'Oral', 'Antibiótico'],
            ['Ceftriaxona', '1 g', 'Polvo para solución inyectable', 'IV / IM', 'Antibiótico'],
            ['Gentamicina', '80 mg/2 ml', 'Solución inyectable', 'IV / IM', 'Antibiótico'],
            ['Penicilina G benzatínica', '1 200 000 UI', 'Polvo para suspensión inyectable', 'IM', 'Antibiótico'],
            ['Sulfametoxazol + Trimetoprima', '800 mg/160 mg', 'Tableta', 'Oral', 'Antibiótico'],
            ['Doxiciclina', '100 mg', 'Cápsula', 'Oral', 'Antibiótico'],

            // Cardiovasculares y antihipertensivos
            ['Losartán', '50 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Enalapril', '10 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Amlodipino', '5 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Atenolol', '50 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Hidroclorotiazida', '25 mg', 'Tableta', 'Oral', 'Diurético / Antihipertensivo'],
            ['Nifedipino', '10 mg', 'Cápsula', 'Oral', 'Antihipertensivo'],
            ['Captopril', '25 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Metoprolol', '50 mg', 'Tableta', 'Oral', 'Antihipertensivo'],
            ['Furosemida', '40 mg', 'Tableta', 'Oral', 'Diurético'],
            ['Furosemida', '10 mg/ml', 'Solución inyectable', 'IV / IM', 'Diurético'],
            ['Espironolactona', '25 mg', 'Tableta', 'Oral', 'Diurético'],
            ['Digoxina', '0.25 mg', 'Tableta', 'Oral', 'Cardiotónico'],

            // Antidiabéticos
            ['Metformina', '850 mg', 'Tableta', 'Oral', 'Antidiabético'],
            ['Glibenclamida', '5 mg', 'Tableta', 'Oral', 'Antidiabético'],
            ['Insulina NPH', '100 UI/ml', 'Suspensión inyectable', 'SC', 'Antidiabético'],
            ['Insulina regular', '100 UI/ml', 'Solución inyectable', 'SC / IV', 'Antidiabético'],

            // Gastrointestinales
            ['Omeprazol', '20 mg', 'Cápsula', 'Oral', 'Antiulceroso'],
            ['Ranitidina', '150 mg', 'Tableta', 'Oral', 'Antiulceroso'],
            ['Metoclopramida', '10 mg', 'Tableta', 'Oral', 'Antiemético'],
            ['Domperidona', '10 mg', 'Tableta', 'Oral', 'Antiemético'],
            ['Loperamida', '2 mg', 'Cápsula', 'Oral', 'Antidiarreico'],
            ['Sales de rehidratación oral', 'Polvo', 'Polvo para solución oral', 'Oral', 'Rehidratación'],
            ['Hidróxido de aluminio + Hidróxido de magnesio', '400 mg/400 mg', 'Tableta', 'Oral', 'Antiácido'],
            ['Lactulosa', '667 mg/ml', 'Jarabe', 'Oral', 'Laxante'],
            ['Sucralfato', '1 g', 'Tableta', 'Oral', 'Antiulceroso'],

            // Respiratorios y antialérgicos
            ['Salbutamol', '100 mcg/dosis', 'Inhalador', 'Inhalatoria', 'Broncodilatador'],
            ['Salbutamol', '2 mg', 'Tableta', 'Oral', 'Broncodilatador'],
            ['Budesonida', '200 mcg/dosis', 'Inhalador', 'Inhalatoria', 'Corticosteroide inhalado'],
            ['Ipratropio', '20 mcg/dosis', 'Inhalador', 'Inhalatoria', 'Broncodilatador'],
            ['Ambroxol', '30 mg', 'Tableta', 'Oral', 'Mucolítico'],
            ['Loratadina', '10 mg', 'Tableta', 'Oral', 'Antihistamínico'],
            ['Cetirizina', '10 mg', 'Tableta', 'Oral', 'Antihistamínico'],
            ['Difenhidramina', '50 mg', 'Cápsula', 'Oral', 'Antihistamínico'],
            ['Beclometasona', '250 mcg/dosis', 'Inhalador', 'Inhalatoria', 'Corticosteroide inhalado'],

            // Corticosteroides
            ['Prednisona', '5 mg', 'Tableta', 'Oral', 'Corticosteroide'],
            ['Prednisona', '20 mg', 'Tableta', 'Oral', 'Corticosteroide'],
            ['Dexametasona', '4 mg/ml', 'Solución inyectable', 'IV / IM', 'Corticosteroide'],
            ['Betametasona', '0.05 %', 'Crema', 'Tópica', 'Corticosteroide'],
            ['Hidrocortisona', '100 mg', 'Polvo para solución inyectable', 'IV / IM', 'Corticosteroide'],

            // Vitaminas y minerales
            ['Sulfato ferroso', '300 mg', 'Tableta', 'Oral', 'Hierro / Antianémico'],
            ['Ácido fólico', '1 mg', 'Tableta', 'Oral', 'Vitamina'],
            ['Ácido ascórbico', '500 mg', 'Tableta', 'Oral', 'Vitamina'],
            ['Calcio + Vitamina D3', '500 mg/400 UI', 'Tableta', 'Oral', 'Mineral / Vitamina'],
            ['Vitamina A', '50 000 UI', 'Cápsula', 'Oral', 'Vitamina'],

            // Neurológicos y psiquiátricos
            ['Diazepam', '5 mg', 'Tableta', 'Oral', 'Ansiolítico', true],
            ['Alprazolam', '0.5 mg', 'Tableta', 'Oral', 'Ansiolítico', true],
            ['Haloperidol', '5 mg', 'Tableta', 'Oral', 'Antipsicótico'],
            ['Carbamazepina', '200 mg', 'Tableta', 'Oral', 'Anticonvulsivante'],
            ['Fenitoína', '100 mg', 'Cápsula', 'Oral', 'Anticonvulsivante'],
            ['Ácido valproico', '500 mg', 'Tableta', 'Oral', 'Anticonvulsivante'],
            ['Fenobarbital', '100 mg', 'Tableta', 'Oral', 'Anticonvulsivante', true],

            // Antiparasitarios y dermatológicos
            ['Albendazol', '400 mg', 'Tableta', 'Oral', 'Antiparasitario'],
            ['Mebendazol', '100 mg', 'Tableta', 'Oral', 'Antiparasitario'],
            ['Metronidazol', '250 mg/5 ml', 'Suspensión', 'Oral', 'Antibiótico / Antiparasitario'],
            ['Ivermectina', '6 mg', 'Tableta', 'Oral', 'Antiparasitario'],
            ['Praziquantel', '600 mg', 'Tableta', 'Oral', 'Antiparasitario'],
            ['Clotrimazol', '1 %', 'Crema', 'Tópica', 'Antifúngico'],
            ['Permetrina', '5 %', 'Loción', 'Tópica', 'Antiparasitario'],
        ];

        $timestamp = now();
        foreach ($medications as $medication) {
            DB::table('triage_medications')->updateOrInsert(
                [
                    'generic_name' => $medication[0],
                    'concentration' => $medication[1],
                    'form' => $medication[2],
                ],
                [
                    'route' => $medication[3],
                    'category' => $medication[4],
                    'controlled' => $medication[5] ?? false,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ],
            );
        }
    }
}
