<?php

namespace TypiCMS\Modules\Core\Http\Requests;

class FileFormRequest extends AbstractFormRequest
{
    /** @return array<string, list<string>> */
    public function rules(): array
    {
        $fileRule = 'mimes:jpeg,gif,png,bmp,tiff,pdf,eps,svg,json,rtf,txt,md,doc,xls,ppt,docx,xlsx,ppsx,pptx,sldx,mp4,m4a,aac,aiff,mov,avi,mp3,wav,zip|max:' . config('typicms.max_file_upload_size');
        $rules = [
            'folder_id' => ['nullable', 'integer'],
            'alt_attribute.*' => ['nullable', 'max:255'],
            'type' => ['nullable', 'string', 'max:1'],
            'title.*' => ['nullable', 'max:255'],
            'description.*' => ['nullable', 'max:255'],
            'name' => ['required', 'max:255'],
            'file' => ['nullable', $fileRule],
        ];

        if ($this->hasFile('name')) {
            $rules['name'] = [$fileRule];
        }

        return $rules;
    }
}
