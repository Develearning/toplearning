<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElReportBc17Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_report_bc17', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('user_type')->default(1);
            $table->string('user_code')->nullable();
            $table->string('full_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('area_id')->index()->nullable();
            $table->string('area')->nullable();
            $table->integer('unit_id')->index()->nullable();
            $table->integer('unit1_id')->index()->nullable();
            $table->string('unit1_code')->nullable();
            $table->string('unit1_name')->nullable();
            $table->integer('unit2_id')->index()->nullable();
            $table->string('unit2_code')->nullable();
            $table->string('unit2_name')->nullable();
            $table->integer('unit3_id')->index()->nullable();
            $table->string('unit3_code')->nullable();
            $table->string('unit3_name')->nullable();
            $table->integer('position_id')->nullable();
            $table->string('position_name')->nullable();
            $table->integer('titles_id')->nullable();
            $table->string('titles_name')->nullable();
            $table->integer('training_program_id')->nullable();
            $table->string('training_program_name')->nullable();
            $table->integer('subject_id')->nullable();
            $table->string('subject_name')->nullable();
            $table->integer('course_id')->nullable();
            $table->string('course_code')->nullable();
            $table->string('course_name')->nullable();
            $table->string('training_unit')->nullable();
            $table->string('training_type_id')->nullable()->comment('Lo???i h??nh ????o t???o');
            $table->string('training_type')->nullable()->comment('Lo???i h??nh ????o t???o');
            $table->string('training_address')->nullable();
            $table->string('course_time')->nullable()->comment('Th???i l?????ng');
            $table->dateTime('start_date')->nullable()->comment('t??? ng??y');
            $table->dateTime('end_date')->nullable()->comment('?????n ng??y');
            $table->string('time_schedule')->nullable()->comment('Th???i gian');
            $table->decimal('cost_held',15,2)->nullable()->comment('Chi ph?? t??? ch???c');
            $table->decimal('cost_training',15,2)->nullable()->comment('Chi ph?? ph??ng ????o t???o');
            $table->decimal('cost_external',15,2)->nullable()->comment('Chi ph?? ????o t???o b??n ngo??i');
            $table->decimal('cost_teacher',15,2)->nullable()->comment('Chi ph?? gi???ng vi??n');
//            $table->decimal('cost_academy',15,2)->nullable()->comment('Chi ph?? h???c vi???n');
            $table->decimal('cost_student',15,2)->nullable()->comment('Chi ph?? h???c vi??n');
            $table->decimal('cost_total',15,2)->nullable()->comment('T???ng chi ph??');
            $table->integer('time_commit')->nullable()->comment('Th???i gian cam k???t');
            $table->dateTime('from_time_commit')->nullable()->comment('Th???i gian cam k???t t??? ng??y');
            $table->dateTime('to_time_commit')->nullable()->comment('Th???i gian cam k???t ?????n ng??y');
            $table->integer('time_rest')->nullable()->comment('Th???i gian c??n l???i');
            $table->decimal('cost_refund',18,2)->nullable()->comment('Chi ph?? b???i ho??n');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('el_report_bc17');
    }
}
