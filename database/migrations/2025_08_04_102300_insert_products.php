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
        DB::table('products')->insert([
            ['id'=>1,'category_id'=>1,'name'=>'AMMONITRATE','current_quantity'=>0],
            ['id'=>2,'category_id'=>1,'name'=>'MAP','current_quantity'=>0],
            ['id'=>3,'category_id'=>1,'name'=>'SULFATE DE POTASSE','current_quantity'=>0],
            ['id'=>4,'category_id'=>1,'name'=>'NITRATE DE CALCIUM','current_quantity'=>0],
            ['id'=>5,'category_id'=>1,'name'=>'UREA PHOSPHATE','current_quantity'=>0],
            ['id'=>6,'category_id'=>1,'name'=>'SULFATE Mg','current_quantity'=>0],
            ['id'=>7,'category_id'=>1,'name'=>'SULFATE DE CUIVRE','current_quantity'=>0],
            ['id'=>8,'category_id'=>1,'name'=>'VALABORE','current_quantity'=>0],
            ['id'=>9,'category_id'=>1,'name'=>'VALAMIX','current_quantity'=>0],
            ['id'=>10,'category_id'=>1,'name'=>'VALAMAX','current_quantity'=>0],
            ['id'=>11,'category_id'=>1,'name'=>'VALASTIM','current_quantity'=>0],
            ['id'=>12,'category_id'=>1,'name'=>'VALAROOT','current_quantity'=>0],
            ['id'=>13,'category_id'=>1,'name'=>'VALATOP','current_quantity'=>0],
            ['id'=>14,'category_id'=>1,'name'=>'VALACOP','current_quantity'=>0],
            ['id'=>15,'category_id'=>1,'name'=>'VALAMUS','current_quantity'=>0],
            ['id'=>16,'category_id'=>1,'name'=>'VALASET','current_quantity'=>0],
            ['id'=>17,'category_id'=>1,'name'=>'VALASUN','current_quantity'=>0],
            ['id'=>18,'category_id'=>1,'name'=>'VALAZINC','current_quantity'=>0],
            ['id'=>19,'category_id'=>1,'name'=>'ACIDE NITRIQUE','current_quantity'=>0],
            ['id'=>20,'category_id'=>2,'name'=>'MASTIQUE','current_quantity'=>0],
            ['id'=>21,'category_id'=>2,'name'=>'DYNASTY','current_quantity'=>0],
            ['id'=>22,'category_id'=>2,'name'=>'SOUFRE 98.5','current_quantity'=>0],
            ['id'=>23,'category_id'=>2,'name'=>'OXYCUIVRE','current_quantity'=>0],
            ['id'=>24,'category_id'=>2,'name'=>'COMFORT','current_quantity'=>0],
            ['id'=>25,'category_id'=>2,'name'=>'SCORE','current_quantity'=>0],
            ['id'=>26,'category_id'=>2,'name'=>'KATANGA','current_quantity'=>0],
            ['id'=>27,'category_id'=>2,'name'=>'KARATE','current_quantity'=>0],
            ['id'=>28,'category_id'=>2,'name'=>'TRIVIA','current_quantity'=>0],
            ['id'=>29,'category_id'=>2,'name'=>'ALTACOR','current_quantity'=>0],
            ['id'=>30,'category_id'=>2,'name'=>'AZOXTAR','current_quantity'=>0],
            ['id'=>31,'category_id'=>2,'name'=>'CRUCIAL','current_quantity'=>0],
            ['id'=>32,'category_id'=>2,'name'=>'MOSPILAN','current_quantity'=>0],
            ['id'=>33,'category_id'=>2,'name'=>'MILBEKNOCK','current_quantity'=>0],
            ['id'=>34,'category_id'=>2,'name'=>'BOUILLE EXPRESS','current_quantity'=>0],
            ['id'=>35,'category_id'=>2,'name'=>'FOSTAR','current_quantity'=>0],
            ['id'=>36,'category_id'=>2,'name'=>'BASAGRAN','current_quantity'=>0],
            ['id'=>37,'category_id'=>2,'name'=>'SPARTACUS','current_quantity'=>0],
            ['id'=>38,'category_id'=>2,'name'=>'GLOBAMEC','current_quantity'=>0],
            ['id'=>39,'category_id'=>2,'name'=>'THIOVIT JET','current_quantity'=>0],
            ['id'=>40,'category_id'=>2,'name'=>'HECTOR','current_quantity'=>0],
            ['id'=>41,'category_id'=>2,'name'=>'THE POWER OF GROWTH','current_quantity'=>0],
            ['id'=>42,'category_id'=>2,'name'=>'ACERTO','current_quantity'=>0],
            ['id'=>43,'category_id'=>2,'name'=>'POLYVERSUM','current_quantity'=>0],
            ['id'=>44,'category_id'=>2,'name'=>'SPRAY OIL','current_quantity'=>0],
            ['id'=>45,'category_id'=>2,'name'=>'N.ERGY','current_quantity'=>0],
            ['id'=>46,'category_id'=>2,'name'=>'BORONIA','current_quantity'=>0],
            ['id'=>47,'category_id'=>2,'name'=>'ACRECIO','current_quantity'=>0],
            ['id'=>48,'category_id'=>2,'name'=>'OLIGONIA','current_quantity'=>0],
            ['id'=>49,'category_id'=>2,'name'=>'BM 104','current_quantity'=>0],
            ['id'=>50,'category_id'=>3,'name'=>'LA TURBE','current_quantity'=>0],
            ['id'=>51,'category_id'=>3,'name'=>'COMPOSTE','current_quantity'=>0],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')->whereIn('id', range(1,51))->delete();
    }
};
