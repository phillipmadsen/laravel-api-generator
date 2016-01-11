<?php

namespace Mitul\Generator\Commands;

use Mitul\Generator\CommandData;
use Mitul\Generator\Generators\API\APIControllerGenerator;
use Mitul\Generator\Generators\Common\MigrationGenerator;
use Mitul\Generator\Generators\Common\ModelGenerator;
use Mitul\Generator\Generators\Common\RepositoryGenerator;
use Mitul\Generator\Generators\Common\RequestGenerator;
use Mitul\Generator\Generators\Common\RoutesGenerator;
use Mitul\Generator\Generators\Scaffold\ViewControllerGenerator;
use Mitul\Generator\Generators\Scaffold\ViewGenerator;

class ScaffoldAPIGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
//        protected $name = 'mitul.generator:scaffold_api';  //renamed to keep separete

        protected $name = 'phillips:create-crud';
        
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD for given model with initial views and APIs';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD_API);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
           public function handle()
        {
            parent::handle();
            if (!$this->commandData->skipMigration) {
                $migrationGenerator = new MigrationGenerator($this->commandData);
                $migrationGenerator->generate();
            }
            $modelGenerator = new ModelGenerator($this->commandData);
            $modelGenerator->generate();

            \Storage::prepend('laravel.log', 'Model Generated');

            /**
             * new Admin output
             * @var string
             * @author  phillip madsen
             */            
            $AdminModelGenerator = new AdminModelGenerator($this->commandData);
            $AdminModelGenerator->generate();
            \Storage::prepend('laravel.log', 'Admin Model Generated');

            $requestGenerator = new RequestGenerator($this->commandData);
            $requestGenerator->generate();
            /**
             * new Admin Request output
             * @var string
             * @author  phillip madsen
             */
            $adminRequestGenerator = new AdminRequestGenerator($this->commandData);
            $adminRequestGenerator->generate();
            \Storage::prepend('laravel.log', 'Admin Requests Generated');

            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
            /**
             * new Admin Repository output
             * @var string
             * @author  phillip madsen
             */
            $adminRepositoryGenerator = new AdminRepositoryGenerator($this->commandData);
            $adminRepositoryGenerator->generate();
            \Storage::prepend('laravel.log', 'Admin Repos Generated');
            /**
             * new Admin Views output
             * @var string
             * @author  phillip madsen
             */
            $adminViewsGenerator = new AdminViewGenerator($this->commandData);
            $adminViewsGenerator->generate();
            \Storage::prepend('laravel.log', 'Admin Views Generated');
            /**
             * new live / frontend Views output
             * @var string
             * @author  phillip madsen
             */
            $liveViewsGenerator = new LiveViewGenerator($this->commandData);
            $liveViewsGenerator->generate();
            \Storage::prepend('laravel.log', 'live / frontend Views Generated');
            /**
             * new Admin controller output
             * @var string
             * @author  phillip madsen
             */
            $repoAdminControllerGenerator = new AdminControllerGenerator($this->commandData);
            $repoAdminControllerGenerator->generate();
            \Storage::prepend('laravel.log', 'Admin controller Generated');

            $repoControllerGenerator = new ViewControllerGenerator($this->commandData);
            $repoControllerGenerator->generate();

            $repoControllerGenerator = new APIControllerGenerator($this->commandData);
            $repoControllerGenerator->generate();

            $routeGenerator = new RoutesGenerator($this->commandData);
            $routeGenerator->generate();

            if ($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
                $this->call('migrate');
            }
        }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments(), []);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }
}
