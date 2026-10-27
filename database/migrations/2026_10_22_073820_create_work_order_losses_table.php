
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_loss', function (Blueprint $table) {
            $table->id();

            $table->integer('work_order_id')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('sale_order_item_id')->nullable();

            $table->string('stage_name', 255)->nullable();
            $table->string('unique_stage_name', 255)->nullable();

            // Weight values: tons to kgs conversion as per business logic
            $table->decimal('initial_weight', 20, 3)->comment('tons to kgs conversion as per business logic')->nullable();

            // 1 = Recoverable, 2 = Non-recoverable
            $table->tinyInteger('scrap_type')->comment('1 = Recoverable, 2 = Non-recoverable')->nullable();

            $table->decimal('loss_percentage', 20, 3)->nullable();
            $table->decimal('loss_amount', 20, 3)->nullable();
            $table->decimal('loss_weight', 20, 3)->nullable();
            $table->decimal('weight_after_loss', 20, 3)->nullable();

            $table->decimal('adjustable_material', 20, 3)->nullable();
            $table->decimal('weight_after_adjustable_material', 20, 3)
                ->nullable();

            $table->integer('created_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_loss');
    }
};
