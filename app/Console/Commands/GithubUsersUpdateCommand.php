<?php

namespace App\Console\Commands;

use App\Services\Contracts\IGithubUserParserService;
use Illuminate\Console\Command;

class GithubUsersUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:parse:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse some users info.';

    /**
     * @var IGithubUserParserService
     */
    private $githubUserParserService;

    /**
     * Create a new command instance.
     *
     * @param IGithubUserParserService $githubUserParserService
     */
    public function __construct(IGithubUserParserService $githubUserParserService)
    {
        $this->githubUserParserService = $githubUserParserService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // Update
        $this->githubUserParserService->updateUsersUpdateInfo();

    }

}
