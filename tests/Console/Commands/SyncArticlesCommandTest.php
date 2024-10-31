<?php

it('command can be run', function () {
    $this->artisan(\App\Console\Commands\SyncArticlesCommand::class)->assertSuccessful();
});
