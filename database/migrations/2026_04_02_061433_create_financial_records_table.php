<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();

            // Every record must have an owner — preserves audit trail
            // If admin creates a record, it belongs to the admin user
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Use decimal(10,2) — NEVER float for monetary values
            // float introduces rounding errors: 0.1 + 0.2 !== 0.3
            $table->decimal('amount', 10, 2);

            $table->enum('type', ['income', 'expense']);
            $table->string('category', 100);

            // date not datetime — we care about the day, not the time
            $table->date('date');

            $table->text('notes')->nullable();

            // Soft deletes — financial records should never be permanently destroyed
            $table->softDeletes();

            $table->timestamps();

            // Composite indexes for the filter queries we know will run frequently
            $table->index('type');
            $table->index('category');
            $table->index('date');
            $table->index('user_id');
            $table->index(['type', 'category']);
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};