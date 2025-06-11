<?php

namespace TypiCMS\Modules\Core\Http\Controllers;

use Bkwld\Croppa\Facades\Croppa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use stdClass;
use TypiCMS\Modules\Core\Http\Requests\FileFormRequest;
use TypiCMS\Modules\Core\Models\File;
use TypiCMS\Modules\Core\Services\FileUploader;

class FilesApiController extends BaseApiController
{
    public function index(Request $request): array
    {
        $folderId = $request->integer('folder_id');

        $data = [
            'models' => File::query()
                ->with('children')
                ->where('folder_id', $folderId)
                ->orderByRaw('type="f" desc')
                ->orderBy('name')
                ->get(),
            'path' => $this->getPath($folderId),
        ];

        return $data;
    }

    public function show(File $file): File
    {
        return $file;
    }

    public function store(FileFormRequest $request): JsonResponse
    {
        $data = $request->validated();
        $model = new File();
        $model->fill(Arr::except($data, 'name'));
        if ($request->hasFile('name')) {
            $file = (new FileUploader())->handle($request->file('name'));
            $model->name = $file['filename'];
            $model->fill(Arr::except($file, 'filename'));
        } else {
            $model->name = $data['name'];
        }
        $model->save();

        $model->load('children');

        return response()->json(compact('model'));
    }

    protected function move($ids, Request $request): JsonResponse
    {
        $idsArray = explode(',', $ids);
        foreach ($idsArray as $id) {
            File::query()->where('id', $id)->update(['folder_id' => $request->folder_id]);
        }

        return response()->json(['number' => count($idsArray)]);
    }

    public function destroy(File $file): JsonResponse
    {
        if ($file->children->count() > 0) {
            return response()->json(['message' => __('A non-empty folder cannot be deleted.')], 403);
        }
        $file->delete();
        try {
            Croppa::delete('storage/' . $file->getOriginal('path'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return response()->json(status: 204);
    }

    private function getPath($folderId): array
    {
        $folder = File::find($folderId);
        $path = [];
        while ($folder) {
            $path[] = $folder;
            $folder = $folder->folder;
        }

        $firstItem = new stdClass();
        $firstItem->name = __('Files');
        $firstItem->type = 'f';
        $firstItem->id = '';

        $path[] = $firstItem;
        $path = array_reverse($path);

        return $path;
    }
}
