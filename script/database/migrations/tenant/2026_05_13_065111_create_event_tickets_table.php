<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_tickets', function (Blueprint $table) {
            $table->id();
        
            // Unique Ticket ID for QR
            $table->uuid('ticket_uuid')->unique();
        
            // Order / product relation
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('order_item_id')->nullable()->index();
            $table->unsignedBigInteger('term_id')->nullable()->index();
        
            // Attendee information
            $table->string('attendee_name')->nullable();
            $table->string('attendee_email')->nullable()->index();
            $table->string('attendee_phone')->nullable();
        
            // Event specifics
            $table->string('event_name')->nullable();
            
            $table->dateTime('event_start_at')->nullable();
            $table->dateTime('event_end_at')->nullable();
            
            $table->date('event_date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('event_location')->nullable();
        
            // Ticket status
            $table->enum('status', ['active', 'used', 'cancelled'])
                ->default('active')
                ->index();
        
            $table->timestamp('used_at')->nullable();
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_tickets');
    }
}
