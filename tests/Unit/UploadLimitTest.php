<?php

use App\Support\Portal;

uses(Tests\SingleTestCase::class);

it('takes the upload limit from the setting', function () {
    config(['portal.max_upload_mb' => 1]);

    expect(Portal::maxUploadKilobytes())->toBe(1024)
        ->and(Portal::maxUploadLabel())->toBe('1MB');
});

it('never promises more than PHP accepts', function () {
    config(['portal.max_upload_mb' => 10_000]);

    $php = min(ini_parse_quantity(ini_get('upload_max_filesize')), ini_parse_quantity(ini_get('post_max_size')));

    expect(Portal::maxUploadKilobytes())->toBe(intdiv($php, 1024));
});
