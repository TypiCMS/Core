/**
 * Dropzone multi-upload
 */
Dropzone.autoDiscover = false;

angular.module('typicms').directive('dropzone', function () {

    $('#uploaderAddButton').on('click', function () {
        $('#dropzone').trigger('click').removeClass('hide');
    });

    return function (scope, element, attrs) {

        var acceptedFiles = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'application/msword', // doc
            'application/vnd.ms-powerpoint', // ppt
            'application/vnd.ms-excel', // xls
            'application/pdf',
            'application/postscript',
            'application/zip',
            'image/tiff',
            'image/jpeg',
            'image/gif',
            'image/png',
            'image/bmp',
            'image/gif'
        ];

        element.dropzone({
            url: '/admin/files',
            paramName: 'name',
            clickable: true,
            maxFilesize: 60, // MB
            acceptedFiles: acceptedFiles.join(),
            success: function (file, response) {

                // Fade out and add file after 1 sec.
                var $this = this;
                window.setTimeout(function () {
                    $(file.previewElement).fadeOut('fast', function () {
                        $this.removeFile(file);
                        scope.$apply(function () {
                            scope.models.splice(0, 0, response.model);
                        });
                    });
                }, 1000);

            },

            sending: function (file, xhr, formData) {
                var gallery_id = scope.gallery_id;
                if (gallery_id) {
                    formData.append('gallery_id', gallery_id);
                }
                formData.append('_token', TypiCMS._token);
                for (var i = TypiCMS.locales.length - 1; i >= 0; i--) {
                    formData.append('folder_id', attrs.folderId);
                    formData.append('description['+TypiCMS.locales[i]+']', '');
                    formData.append('alt_attribute['+TypiCMS.locales[i]+']', '');
                }
            }

        });

    }

});
