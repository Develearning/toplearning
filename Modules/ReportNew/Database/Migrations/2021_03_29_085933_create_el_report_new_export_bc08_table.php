<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateElReportNewExportBc08Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('el_report_new_export_bc08', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('course_id')->nullable();
            $table->string('course_code')->nullable();
            $table->string('course_name')->nullable();
            $table->string('lecturer')->nullable();
            $table->string('tuteurs')->nullable();
            $table->string('training_form_name')->nullable();
            $table->integer('training_type_id')->nullable();
            $table->string('training_type_name')->nullable();
            $table->string('level_subject')->nullable();
            $table->string('training_location')->nullable();
            $table->string('training_unit')->nullable();
            $table->string('title_join')->nullable();
            $table->string('training_object')->nullable();
            $table->string('course_time')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('time_schedule')->nullable();
            $table->string('created_by')->nullable();
            $table->integer('registers')->default(0);
            $table->integer('join_100')->default(0);
            $table->integer('join_75')->default(0);
            $table->integer('join_below_75')->default(0);
            $table->integer('students_absent')->default(0);
            $table->integer('students_pass')->default(0);
            $table->integer('students_fail')->default(0);
            $table->string('course_cost')->nullable();
            $table->string('student_cost')->nullable();
            $table->string('total_cost')->nullable();
            $table->integer('recruits')->default(0)->comment('1: t??n tuy???n');
            $table->integer('exist')->default(0)->comment('1: hi???n h???u');
            $table->integer('plan')->default(0)->comment('1: k??? ho???ch');
            $table->integer('incurred')->default(0)->comment('1: ph??t sinh');
            $table->string('monitoring_staff')->nullable()->comment('c??n b??? theo d??i');
            $table->string('monitoring_staff_note')->nullable()->comment('?? ki???n c??n b??? theo d??i');
            $table->string('teacher_note')->nullable()->comment('?? ki???n gi???ng vi??n');
            $table->integer('unit_by')->nullable();
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
        Schema::dropIfExists('el_report_new_export_bc08');
    }
}
