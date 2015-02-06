<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TypiCMSSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('SettingsSeeder');
		$this->call('TranslationSeeder');
		$this->call('PageSeeder');
		$this->call('MenuSeeder');
	}

}
