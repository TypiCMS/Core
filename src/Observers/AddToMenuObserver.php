<?php

namespace TypiCMS\Modules\Core\Observers;

use TypiCMS\Modules\Core\Models\Menulink;
use TypiCMS\Modules\Core\Models\Page;

class AddToMenuObserver
{
    /**
     * If a new homepage is defined, cancel previous homepage.
     */
    public function created(Page $model)
    {
        if ($menu_id = request('add_to_menu')) {
            $position = $this->getPositionFormMenu($menu_id);
            $data = [
                'menu_id' => $menu_id,
                'page_id' => $model->id,
                'position' => $position,
            ];
            foreach (locales() as $locale) {
                $data['title'][$locale] = $model->translate('title', $locale);
                $data['status'][$locale] = 0;
                $data['description'][$locale] = null;
                $data['url'][$locale] = '';
            }
            Menulink::create($data);
        }
    }

    private function getPositionFormMenu($id)
    {
        $position = Menulink::where('menu_id', $id)->max('position');

        return $position + 1;
    }
}
