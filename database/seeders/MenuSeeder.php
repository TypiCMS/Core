<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['id' => 1, 'image_id' => null, 'name' => 'primary', 'class' => null, 'status' => '{"fr":"1","en":"1","nl":"1"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 2, 'image_id' => null, 'name' => 'footer', 'class' => null, 'status' => '{"fr":"1","en":"1","nl":"1"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 3, 'image_id' => null, 'name' => 'social', 'class' => null, 'status' => '{"fr":"1","en":"1","nl":"1"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => 4, 'image_id' => null, 'name' => 'legal', 'class' => null, 'status' => '{"fr": 1, "en": 1, "nl": 1}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        $menulinks = [
            ['menu_id' => 1, 'page_id' => 1, 'parent_id' => null, 'image_id' => null, 'position' => 1, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Home", "fr": "Accueil", "nl": "Home"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 1, 'page_id' => 2, 'parent_id' => null, 'image_id' => null, 'position' => 2, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Contact", "fr": "Contact", "nl": "Contact"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 2, 'page_id' => 2, 'parent_id' => null, 'image_id' => null, 'position' => 1, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Contact", "fr": "Contact", "nl": "Contact"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 2, 'target' => '_blank', 'class' => 'btn-linkedin', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "LinkedIn", "fr": "LinkedIn", "nl": "LinkedIn"}', 'website' => '{"en": "https://www.linkedin.com", "fr": "https://www.linkedin.com", "nl": "https://www.linkedin.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 1, 'target' => '_blank', 'class' => 'btn-facebook', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Facebook", "fr": "Facebook", "nl": "Facebook"}', 'website' => '{"en": "https://www.facebook.com", "fr": "https://www.facebook.com", "nl": "https://www.facebook.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 4, 'target' => '_blank', 'class' => 'btn-instagram', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Instagram", "fr": "Instagram", "nl": "Instagram"}', 'website' => '{"en": "https://www.instagram.com", "fr": "https://www.instagram.com", "nl": "https://www.instagram.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 5, 'target' => '_blank', 'class' => 'btn-youtube', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "YouTube", "fr": "YouTube", "nl": "YouTube"}', 'website' => '{"en": "https://www.youtube.com", "fr": "https://www.youtube.com", "nl": "https://www.youtube.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 6, 'target' => '_blank', 'class' => 'btn-bluesky', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Bluesky", "fr": "Bluesky", "nl": "Bluesky"}', 'website' => '{"en": "https://bsky.app", "fr": "https://bsky.app", "nl": "https://bsky.app"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 7, 'target' => '_blank', 'class' => 'btn-threads', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Threads", "fr": "Threads", "nl": "Threads"}', 'website' => '{"en": "https://threads.com", "fr": "https://threads.com", "nl": "https://threads.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 3, 'page_id' => null, 'parent_id' => null, 'image_id' => null, 'position' => 8, 'target' => '_blank', 'class' => 'btn-x', 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "X", "fr": "X", "nl": "X"}', 'website' => '{"en": "https://x.com", "fr": "https://x.com", "nl": "https://x.com"}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 4, 'page_id' => 4, 'parent_id' => null, 'image_id' => null, 'position' => 0, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Terms and conditions", "fr": "Conditions générales", "nl": "Algemene Voorwaarden"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 4, 'page_id' => 5, 'parent_id' => null, 'image_id' => null, 'position' => 0, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Privacy policy", "fr": "Charte vie privée", "nl": "Privacyverklaring"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['menu_id' => 4, 'page_id' => 6, 'parent_id' => null, 'image_id' => null, 'position' => 0, 'target' => null, 'class' => null, 'status' => '{"en": 1, "fr": 1, "nl": 1}', 'description' => '{"en": null, "fr": null, "nl": null}', 'title' => '{"en": "Cookie policy", "fr": "Politique des cookies", "nl": "Cookieverklaring"}', 'website' => '{"en": null, "fr": null, "nl": null}', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('menus')->insert($menus);
        DB::table('menulinks')->insert($menulinks);
    }
}
