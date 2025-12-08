<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert new categories if they don't exist
        $categories = [
            'ACCESSOIRES IRRIGATION',
            'ACCESSOIRES FORAGE',
            'TRACTEUR',
        ];
        $categoryIds = [];
        foreach ($categories as $cat) {
            $existing = DB::table('categories')->where('name', $cat)->first();
            if ($existing) {
                $categoryIds[$cat] = $existing->id;
            } else {
                $categoryIds[$cat] = DB::table('categories')->insertGetId([
                    'name' => $cat,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Insert products
        $products = [
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'BRIDE LIBRE  200'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'BRIDE LIBRE 160'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'BRIDE LIBRE 100  4"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'BRIDE LIBRE 65  1/2"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 225 Ǿ / 90˚'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 200 Ǿ / 45˚'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 160/45'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 140/90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 125/90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 125/45'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 110/45'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 90/45'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 75/90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 63/90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 63/45'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COUDE 50/90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLECTEUR 8 VOIES'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLET STRIE 200'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLET STRIE 160'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLET STRIE 100'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLET STRIE 90'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'CLAPET ANTI-RETOUR 90  3"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'CLAPET ANTI-RETOUR 75  2"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'CLAPET ANTI-RETOUR 50  1/"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'KIT DE BRIDES 110'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'KIT DE BRIDES 100'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'MANCHON ELECTROSOUDABLE'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'REDUCTION 160/140'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'REDUCTION 125/110'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'REDUCTION 90/75'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'REDUCTION 75/63'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'REDUCTION 63/50'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'EMBOUT 90/110*3"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'EMBOUT 63/75*3"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'EMBOUT 63/50'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'EMBOUT 32*1"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'EMBOUT 32*4'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'ADAPTATEUR FEMELLE 90*3"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'ADAPTATEUR FEMELLE 50*1/2"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE  125  4"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE  90  3"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE  50  1/2"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE  32  1"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNA PAPILLON COMPLET 200'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNA PAPILLON COMPLET 160'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNA PAPILLON COMPLET 125'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNA PAPILLON COMPLET'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE PAPILLON 200'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE PAPILLON 160'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE PAPILLON 150'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE A OPERCULE 100'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE DE CONTRÔLE HYDRAULIQUE 100 mm  4"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'POMPE A ROUE OUVERTE 2,2 KW'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'POMPE A ROUE OUVERTE 1,77 KW'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'POMPE 0,37 KW'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE A AIR GRAND 1"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE A AIR PETIT 1"'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'VANNE A AIR 2 "'],
            ['category' => 'ACCESSOIRES IRRIGATION', 'name' => 'COLLE  KG'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'POMPE IMMERGIE 37 KW'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'POMPE IMMERGIE 30 KW'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'POMPE IMMERGIE 26 KW'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'TURBINE'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'BOITE DE JONCTION'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'BOITE DE JONCTION'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'SONDE DE NIVEAU'],
            ['category' => 'ACCESSOIRES FORAGE', 'name' => 'SOLENOIDE'],
            ['category' => 'TRACTEUR', 'name' => 'REGULATEUR ATOMISEUR MANOMETRE 50bar '],
            ['category' => 'TRACTEUR', 'name' => 'CARDAN DE TRANSMITION'],
            ['category' => 'TRACTEUR', 'name' => 'FILTRE ATOMISEUR 50'],
            ['category' => 'TRACTEUR', 'name' => 'FILTRE ATOMISEUR 40'],
            ['category' => 'TRACTEUR', 'name' => 'FLEXIBLE D\'ATOMISEUR'],
            ['category' => 'TRACTEUR', 'name' => 'FLEXIBLE DE REMPLISAGE ATOMISEUR'],
            ['category' => 'TRACTEUR', 'name' => 'PULVERISATEUR HAUTE PRESSION (g) (Lance)'],
            ['category' => 'TRACTEUR', 'name' => 'PULVERISATEUR HAUTE PRESSION (p) Lance'],
            ['category' => 'TRACTEUR', 'name' => 'RACCORD DE JONCTION (Joint Jibault) DN 200'],
            ['category' => 'TRACTEUR', 'name' => 'RACCORD DE JONCTION (Joint Jibault) DN 160'],
            ['category' => 'TRACTEUR', 'name' => 'RACCORD DE JONCTION (Joint Jibault) DN 140'],
            ['category' => 'TRACTEUR', 'name' => 'RACCORD DE JONCTION (Joint Jibault) DN 125'],
            ['category' => 'TRACTEUR', 'name' => 'POMPE DE SURPRESSION 0,55 KW'],
            ['category' => 'TRACTEUR', 'name' => 'HUILE 15/40  L 15W40'],
            ['category' => 'TRACTEUR', 'name' => 'GRAISSE  KG'],
        ];
        $now = now();
        foreach ($products as $product) {
            DB::table('products')->insert([
                'category_id' => $categoryIds[$product['category']],
                'name' => $product['name'],
                'current_quantity' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all inserted products
        $productNames = [
            'BRIDE LIBRE  200','BRIDE LIBRE 160','BRIDE LIBRE 100  4"','BRIDE LIBRE 65  1/2"','COUDE 225 Ǿ / 90˚','COUDE 200 Ǿ / 45˚','COUDE 160/45','COUDE 140/90','COUDE 125/90','COUDE 125/45','COUDE 110/45','COUDE 90/45','COUDE 75/90','COUDE 63/90','COUDE 63/45','COUDE 50/90','COLLECTEUR 8 VOIES','COLLET STRIE 200','COLLET STRIE 160','COLLET STRIE 100','COLLET STRIE 90','CLAPET ANTI-RETOUR 90  3"','CLAPET ANTI-RETOUR 75  2"','CLAPET ANTI-RETOUR 50  1/"','KIT DE BRIDES 110','KIT DE BRIDES 100','MANCHON ELECTROSOUDABLE','REDUCTION 160/140','REDUCTION 125/110','REDUCTION 90/75','REDUCTION 75/63','REDUCTION 63/50','EMBOUT 90/110*3"','EMBOUT 63/75*3"','EMBOUT 63/50','EMBOUT 32*1"','EMBOUT 32*4','ADAPTATEUR FEMELLE 90*3"','ADAPTATEUR FEMELLE 50*1/2"','VANNE  125  4"','VANNE  90  3"','VANNE  50  1/2"','VANNE  32  1"','VANNA PAPILLON COMPLET 200','VANNA PAPILLON COMPLET 160','VANNA PAPILLON COMPLET 125','VANNA PAPILLON COMPLET','VANNE PAPILLON 200','VANNE PAPILLON 160','VANNE PAPILLON 150','VANNE A OPERCULE 100','VANNE DE CONTRÔLE HYDRAULIQUE 100 mm  4"','POMPE A ROUE OUVERTE 2,2 KW','POMPE A ROUE OUVERTE 1,77 KW','POMPE 0,37 KW','VANNE A AIR GRAND 1"','VANNE A AIR PETIT 1"','VANNE A AIR 2 "','COLLE  KG','POMPE IMMERGIE 37 KW','POMPE IMMERGIE 30 KW','POMPE IMMERGIE 26 KW','TURBINE','BOITE DE JONCTION','SONDE DE NIVEAU','SOLENOIDE','REGULATEUR ATOMISEUR MANOMETRE 50bar ','CARDAN DE TRANSMITION','FILTRE ATOMISEUR 50','FILTRE ATOMISEUR 40','FLEXIBLE D\'ATOMISEUR','FLEXIBLE DE REMPLISAGE ATOMISEUR','PULVERISATEUR HAUTE PRESSION (g) (Lance)','PULVERISATEUR HAUTE PRESSION (p) Lance','RACCORD DE JONCTION (Joint Jibault) DN 200','RACCORD DE JONCTION (Joint Jibault) DN 160','RACCORD DE JONCTION (Joint Jibault) DN 140','RACCORD DE JONCTION (Joint Jibault) DN 125','POMPE DE SURPRESSION 0,55 KW','HUILE 15/40  L 15W40','GRAISSE  KG',
        ];
        DB::table('products')->whereIn('name', $productNames)->delete();
    }
};
