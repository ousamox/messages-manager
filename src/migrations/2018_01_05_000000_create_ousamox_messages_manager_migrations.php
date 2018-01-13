<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 1/5/18
 * Time: 16:27
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOusamoxMessagesManagerMigrations extends Migration
{
    public function up()
    {
        Schema::create(config('omm.models.user.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('role')->default('user');
            $table->string('photo')->default('/profiles/placeholder.jpg');
            $table->string('password')->nullable();
            $table->enum('locale', ['en','fr','es'])->default('en');
            $table->rememberToken();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.device.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->foreign('user_id')->references('id')->on(config('omm.models.user.table_name'))
                ->onDelete('cascade');
            $table->string('token');
            $table->string('device_UID');
            $table->string('name')->nullable();
            $table->enum('platform', ['ios', 'android', 'web', 'other']);
            $table->string('version');
            $table->string('brand')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.channel.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('subject');
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.session.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('channel_id')->unsigned()->nullable()->index();
            $table->foreign('channel_id')->references('id')->on(config('omm.models.channel.table_name'))
                ->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->foreign('user_id')->references('id')->on(config('omm.models.user.table_name'))
                ->onDelete('cascade');
            $table->boolean('is_creator')->default(0);
            $table->boolean('share_location')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.message.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('channel_id')->unsigned()->nullable()->index();
            $table->foreign('channel_id')->references('id')->on(config('omm.models.channel.table_name'))
                ->onDelete('cascade');
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->foreign('user_id')->references('id')->on(config('omm.models.user.table_name'))
                ->onDelete('cascade');
            $table->integer('device_id')->unsigned()->nullable()->index();
            $table->foreign('device_id')->references('id')->on(config('omm.models.device.table_name'))
                ->onDelete('cascade');
            $table->text('content');
            $table->double('sent_from_lat')->nullable();
            $table->double('sent_from_long')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.message_seen.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('channel_id')->unsigned()->nullable()->index();
            $table->foreign('channel_id')->references('id')->on(config('omm.models.channel.table_name'))
                ->onDelete('cascade');
            $table->integer('seen_by_id')->unsigned()->nullable()->index();
            $table->foreign('seen_by_id')->references('id')->on(config('omm.models.user.table_name'))
                ->onDelete('cascade');
            $table->double('seen_from_lat')->nullable();
            $table->double('seen_from_long')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create(config('omm.models.message_file.table_name'), function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('message_id')->unsigned()->nullable()->index();
            $table->foreign('message_id')->references('id')->on(config('omm.models.message.table_name'))
                ->onDelete('cascade');
            $table->string('title');
            $table->string('filename');
            $table->string('extension');
            $table->string('path');
            $table->string('size');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('omm.models.message_file.table_name'));
        Schema::dropIfExists(config('omm.models.message_seen.table_name'));
        Schema::dropIfExists(config('omm.models.message.table_name'));
        Schema::dropIfExists(config('omm.models.session.table_name'));
        Schema::dropIfExists(config('omm.models.channel.table_name'));
        Schema::dropIfExists(config('omm.models.device.table_name'));
        Schema::dropIfExists(config('omm.models.user.table_name'));
    }
}