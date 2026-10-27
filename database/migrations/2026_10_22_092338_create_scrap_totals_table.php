
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrap_totals', function (Blueprint $table) {
            $table->id();

            $table->double('acid_pickling', 20, 3)
                ->default(0)
                ->comment('no-recoverable; value in kg');

            $table->double('short_blasting', 20, 3)
                ->default(0)
                ->comment('no-recoverable; value in kg');

            $table->double('pointing', 20, 3)
                ->default(0);

            $table->double('point_discard', 20, 3)
                ->default(0);

            $table->double('bedsawcut', 20, 3)
                ->default(0);

            $table->double('misc', 20, 3)
                ->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrap_totals');
    }
};
