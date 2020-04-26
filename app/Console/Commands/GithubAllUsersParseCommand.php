<?php

namespace App\Console\Commands;

use App\Services\Contracts\IGithubUserParserService;
use Illuminate\Console\Command;

/**
 * @property IGithubUserParserService githubUserParserService
 */
class GithubAllUsersParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'github:parse:all:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Github Parse all users and save our db.';

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

        // Parse github users and save our db.
        $this->githubUserParserService->parseGithubUsersByFilter();

    }

}
