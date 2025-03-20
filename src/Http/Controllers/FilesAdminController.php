<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use TypiCMS\Modules\Core\Http\Requests\FileFormRequest;
use TypiCMS\Modules\Core\Models\File;
use TypiCMS\Modules\Core\Services\FileUploader;

class FilesAdminController extends BaseAdminController
{
    public function index(): View
    {
        return view('files::admin.index');
    }

    public function edit(File $file): View
    {
        return view('files::admin.edit')->with(['model' => $file]);
    }

    public function update(File $file, FileFormRequest $request): RedirectResponse
    {
        $file->fill(Arr::except($request->validated(), 'file'));

        if ($request->hasFile('file')) {
            Storage::delete($file->path);
            $newFile = (new FileUploader())->handle($request->file('file'));
            $file->name = $newFile['filename'];
            $file->fill(Arr::except($newFile, 'filename'));
        }
        $file->save();

        return $this->redirect($request, $file);
    }
}
