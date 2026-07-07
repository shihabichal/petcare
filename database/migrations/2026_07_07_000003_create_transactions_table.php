<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();

            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('pet_id')->constrained('pets')->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');

            $table->decimal('total_price', 15, 2);

            $table->string('payment_status', 20)->default('unpaid');
            // unpaid, paid, refunded

            $table->string('status', 20)->default('pending');
            // pending, confirmed, ongoing, completed, cancelled

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('days')->default(1);

            $table->boolean('pickup_required')->default(false);
            $table->text('pickup_address')->nullable();
            $table->timestamp('pickup_time')->nullable();

            $table->text('notes')->nullable();
            $table->text('notes_internal')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
