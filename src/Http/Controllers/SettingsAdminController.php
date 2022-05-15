<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Bkwld\Croppa\Facades\Croppa;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use stdClass;
use TypiCMS\Modules\Core\Models\Setting;
use TypiCMS\Modules\Core\Services\FileUploader;

class SettingsAdminController extends BaseAdminController
{
    public function index(): View
    {
        $data = new stdClass();
        foreach (Setting::get() as $model) {
            $value = is_numeric($model->value) ? (int) $model->value : $model->value;
            $group_name = $model->group_name;
            $key_name = $model->key_name;
            if ($group_name != 'config') {
                if (!isset($data->{$group_name})) {
                    $data->{$group_name} = new stdClass();
                }
                $data->{$group_name}->{$key_name} = $value;
            } else {
                $data->{$key_name} = $value;
            }
        }

        return view('settings::admin.index')
            ->with(compact('data'));
    }

    public function save(Request $request, FileUploader $fileUploader): RedirectResponse
    {
        $data = $request->except('_token', 'image');

        if ($request->hasFile('image')) {
            $validator = Validator::make($request->all(), [
                'image' => 'image',
            ]);
            if ($validator->passes()) {
                $file = $fileUploader->handle($request->file('image'), 'settings');
                $this->deleteImage();
                $data['image'] = $file['filename'];
            }
        }

        foreach ($data as $group_name => $array) {
            if (!is_array($array)) {
                $array = [$group_name => $array];
                $group_name = 'config';
            }
            foreach ($array as $key_name => $value) {
                $model = Setting::firstOrCreate(['key_name' => $key_name, 'group_name' => $group_name]);
                $model->value = $value;
                $model->save();
            }
        }

        return redirect()->route('admin::index-settings');
    }

    public function deleteImage(): void
    {
        if ($filename = Setting::where('key_name', 'image')->value('value')) {
            try {
                Croppa::delete('storage/settings/'.$filename);
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
        }
        Setting::where('key_name', 'image')->delete();
    }

    public function clearCache(): RedirectResponse
    {
        Cache::flush();
        $message = __('Cache cleared.');

        return redirect()->route('admin::index-settings')
            ->with(compact('message'));
    }
}
