<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('evento_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('votaciones_activas')->default(false);
            $table->boolean('registro_activo')->default(true);
            $table->boolean('resultados_finalizados')->default(false);
            $table->timestamps();
        });
        DB::table('evento_config')->insert([
            'votaciones_activas' => false,
            'registro_activo' => true,
            'resultados_finalizados' => false,
            'created_at' => now(), 'updated_at' => now()
        ]);
        Schema::create('concursantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_invitado', 100);
            $table->string('nombre_disfraz', 140);
            $table->string('categoria', 20);
            $table->longText('foto')->nullable();
            $table->unsignedInteger('votos')->default(0);
            $table->timestamps();
        });
        Schema::create('votos_emitidos', function (Blueprint $table) {
            $table->id();
            $table->string('dispositivo_hash', 120);
            $table->string('categoria', 20);
            $table->foreignId('concursante_id')->constrained('concursantes')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['dispositivo_hash', 'categoria']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('votos_emitidos');
        Schema::dropIfExists('concursantes');
        Schema::dropIfExists('evento_config');
    }
};
