<?php

    namespace Mitul\Generator\Commands;

    use Config;
    use File;
    use Illuminate\Console\Command;
    use Mitul\Generator\CommandData;
    use Mitul\Generator\File\FileHelper;
    use Mitul\Generator\TemplatesHelper;
    use Mitul\Generator\Utils\GeneratorUtils;
    use Symfony\Component\Console\Input\InputOption;

class PublisherCommand extends Command
{
    private $commandData;



    /**
         * The console command name.
         *
         * @var string
         */

    // protected $name = 'mitul.generator:publish'; renamed to keep track of

    /**
     * new command for new output
     * @var string
     * @author  phillip madsen
     */

    protected $name = 'phillip:publishstartfiles';

    /**
         * The console command description.
         *
         * @var string
         */
    protected $description = 'Publishes a various things of generator. use --templates --baseController --all to add others';

    /**
         * Execute the command.
         *
         * @return void
         */
    public function handle()
    {
        if ($this->option('all')) {
            $this->publishCommonViews();
            $this->publishAPIRoutes();
            $this->initAPIRoutes();
            $this->publishAdminRoutes(); // newly added
            $this->publishDashboardController(); // newly added
            $this->publishDashboardView(); // newly added
            $this->initAdminRoutes(); // newly added
            $this->publishLiveRoutes(); // newly added
            $this->initLiveRoutes(); // newly added
            $this->publishTemplates();
            $this->publishAppBaseController();
        } elseif ($this->option('templates')) {
            $this->publishTemplates();
        } elseif ($this->option('baseController')) {
            $this->publishAppBaseController();
        } else {
            $this->publishCommonViews();
            $this->publishAPIRoutes();
            $this->initAPIRoutes();
            $this->publishLiveRoutes(); // newly added
            $this->initLiveRoutes(); // newly added
            $this->publishAdminRoutes(); // newly added
            $this->initAdminRoutes(); // newly added
            $this->publishDashboardController(); // newly added
            $this->publishDashboardView(); // newly added

        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['templates', null, InputOption::VALUE_NONE, 'Publish templates'],
            ['baseController', null, InputOption::VALUE_NONE, 'Publish base controller'],
            ['all', null, InputOption::VALUE_NONE, 'Publish all options'],
        ];
    }

    /**
     * Publishes templates.
     */
    public function publishTemplates()
    {
        $templatesPath = __DIR__.'/../../../../templates';

        $templatesCopyPath = base_path('resources/api-generator-templates');

        $this->publishDirectory($templatesPath, $templatesCopyPath, 'templates');
    }

    /**
     * Publishes common views.
     */
    public function publishCommonViews()
    {
        $viewsPath = __DIR__.'/../../../../views/common';

        $viewsCopyPath = base_path('resources/views/common');

        $this->publishDirectory($viewsPath, $viewsCopyPath, 'common views');
    }

    /**
     * Publishes base controller.
     */
    private function publishAppBaseController()
    {
        $templateHelper = new TemplatesHelper();
        $templateData = $templateHelper->getTemplate('AppBaseController', 'controller');

        $templateData = GeneratorUtils::fillTemplate(CommandData::getConfigDynamicVariables(), $templateData);

        $fileName = 'AppBaseController.php';

        $filePath = Config::get('generator.path_controller', app_path('Http/Controllers/'));

        $fileHelper = new FileHelper();
        $fileHelper->writeFile($filePath.$fileName, $templateData);
        $this->comment('AppBaseController generated');
        $this->info($fileName);
    }

    /**
     * Publishes api_routes.php.
     */
    public function publishAPIRoutes()
    {
        $routesPath = __DIR__.'/../../../../templates/routes/api_routes.stub';

        $apiRoutesPath = Config::get('generator.path_api_routes', app_path('Http/api_routes.php'));

        $this->publishFile($routesPath, $apiRoutesPath, 'api_routes.php');
    }

    public function publishFile($sourceFile, $destinationFile, $fileName)
    {
        if (file_exists($destinationFile)) {
            $answer = $this->ask('Do you want to overwrite '.$fileName.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        }

        copy($sourceFile, $destinationFile);

        $this->comment($fileName.' generated');
        $this->info($destinationFile);
    }

    public function publishDirectory($sourceDir, $destinationDir, $dirName)
    {
        if (file_exists($destinationDir)) {
            $answer = $this->ask('Do you want to overwrite '.$dirName.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        } else {
            File::makeDirectory($destinationDir);
        }

        File::copyDirectory($sourceDir, $destinationDir);

        $this->comment($dirName.' published');
        $this->info($destinationDir);
    }







   /**
         * Publishes admin dashboard blade template file.
         */
    public function publishDashboardView()
    {

        $adminControllersPath = __DIR__.'/../../../../templates/scaffold/views/dashboard.blade.stub';

        $dashPath = Config::get('generator.path_dashboard_view', base_path('resources/views/admin/dashboard.blade.php'));

        $this->publishFile($adminControllersPath, $dashPath, 'dashboard.blade.php');
    }

    /**
         * Publishes admin dashboard controller.
         */
    public function publishDashboardController()
    {

        $adminControllersPath = __DIR__.'/../../../../templates/controller/DashboardController.stub';
        $adminControllersPath = __DIR__.'/../../../../templates/scaffold/views/dashboard.blade.stub';

        $dashPath = Config::get('generator.path_dashboard_controller', app_path('Http/Controllers/Admin/DashboardController.php'));

        $this->publishFile($adminControllersPath, $dashPath, 'DashboardController.php');
    }

    /**
         * Publishes admin_routes.php.
         */
    public function publishAdminRoutes()
    {
        $routesPath = __DIR__.'/../../../../templates/routes/admin_routes.stub';

        $adminRoutesPath = Config::get('generator.path_admin_routes', app_path('Http/admin_routes.php'));

        $this->publishFile($routesPath, $adminRoutesPath, 'admin_routes.php');
    }

    /**
         * Publishes live_routes.php.
         */

    public function publishLiveRoutes()
    {
        $routesPath = __DIR__.'/../../../../templates/routes/live_routes.stub';

        $liveRoutesPath = Config::get('generator.path_live_routes', app_path('Http/live_routes.php'));

        $this->publishFile($routesPath, $liveRoutesPath, 'live_routes.php');
    }

	/**
     * @author phillip madsen
     *         newly added live routes file
     */
    private function initLiveRoutes()
    {
        $path = Config::get('generator.path_routes', app_path('Http/routes.php'));
        $fileHelper = new FileHelper();
        $routeContents = $fileHelper->getFileContents($path);

         $template = 'live_routes_group';

        $templateHelper = new TemplatesHelper();
        $templateData = $templateHelper->getTemplate($template, 'routes');
        $templateData = $this->fillTemplate($templateData);
        $fileHelper->writeFile($path, $routeContents."\n\n".$templateData);
        $this->comment("\n LIVE group added to routes.php");
    }

    /**
     * @author phillip madsen
     *         newly added admin routes file
     */
    private function initAdminRoutes()
    {
        $path = Config::get('generator.path_routes', app_path('Http/routes.php'));
        $fileHelper = new FileHelper();
        $routeContents = $fileHelper->getFileContents($path);

        $template = 'admin_routes_group';

        $templateHelper = new TemplatesHelper();
        $templateData = $templateHelper->getTemplate($template, 'routes');
        $templateData = $this->fillTemplate($templateData);
        $fileHelper->writeFile($path, $routeContents."\n\n".$templateData);
        $this->comment("\n ADMIN group added to routes.php");
    }

    /**
     * Initialize routes group based on route integration.
     */
    private function initAPIRoutes()
    {
        $path = Config::get('generator.path_routes', app_path('Http/routes.php'));

        $fileHelper = new FileHelper();
        $routeContents = $fileHelper->getFileContents($path);

        $useDingo = Config::get('generator.use_dingo_api', false);

        if ($useDingo) {
            $template = 'dingo_api_routes_group';
        } else {
            $template = 'api_routes_group';
        }

        $templateHelper = new TemplatesHelper();
        $templateData = $templateHelper->getTemplate($template, 'routes');

        $templateData = $this->fillTemplate($templateData);

        $fileHelper->writeFile($path, $routeContents."\n\n".$templateData);
        $this->comment("\nAPI group added to routes.php");
    }

    /**
     * Replaces dynamic variables of template.
     *
     * @param string $templateData
     *
     * @return string
     */
    private function fillTemplate($templateData)
    {
        $apiVersion = Config::get('generator.api_version');
        $apiPrefix = Config::get('generator.api_prefix');
           	$adminPrefix = Config::get('generator.admin_prefix');  // newly added
        	$adminNamespace = Config::get('generator.namespace_admin_controller');  // newly added
        $apiNamespace = Config::get('generator.namespace_api_controller');

        $templateData = str_replace('$API_VERSION$', $apiVersion, $templateData);
        $templateData = str_replace('$NAMESPACE_API_CONTROLLER$', $apiNamespace, $templateData);
        $templateData = str_replace('$API_PREFIX$', $apiPrefix, $templateData);

        $templateData = str_replace('$ADMIN_PREFIX$', $adminPrefix, $templateData); // newly added
        $templateData = str_replace('$NAMESPACE_ADMIN_CONTROLLER$', $adminNamespace, $templateData); // newly added

        return $templateData;
    }
}
