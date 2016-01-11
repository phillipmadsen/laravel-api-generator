<?php
    namespace Mitul\Generator\Generators\Scaffold;

    use Config;
    use Illuminate\Support\Str;
    use Mitul\Generator\CommandData;
    use Mitul\Generator\FormFieldsGenerator;
    use Mitul\Generator\Generators\GeneratorProvider;
    use Mitul\Generator\Utils\GeneratorUtils;

    class AdminViewGenerator implements GeneratorProvider
    {
        /** @var  CommandData */
        private $commandData;
        /** @var string */
        private $path;
        /** @var string */
        private $viewsPath;

        public function __construct($commandData)
        {
            $this->commandData = $commandData;
            $this->path = Config::get('generator.path_admin_views', base_path('resources/views/admin')) . '/' . $this->commandData->modelNamePluralCamel . '/';
            $this->viewsPath = 'scaffold/views/admin';
        }

        public function generate()
        {
            if (!file_exists($this->path)) {
                mkdir($this->path, 0755, true);
            }
            $this->commandData->commandObj->comment("\n Admin Views created: ");
            $this->generateAdminFields();
            $this->generateShowAdminFields();
            $this->generateAdminTable();
            $this->generateAdminIndex();
            $this->generateAdminShow();
            $this->generateAdminCreate();
            $this->generateAdminEdit();
        }

        private function generateAdminFields()
        {
            $fieldTemplate = $this->commandData->templatesHelper->getTemplate('field.blade', $this->viewsPath);
            $fieldsStr = '';
            foreach ($this->commandData->inputFields as $field) {
                switch ($field['type']) {
                    case 'text':
                        $fieldsStr .= FormFieldsGenerator::text($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'textarea':
                        $fieldsStr .= FormFieldsGenerator::textarea($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'password':
                        $fieldsStr .= FormFieldsGenerator::password($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'email':
                        $fieldsStr .= FormFieldsGenerator::email($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'file':
                        $fieldsStr .= FormFieldsGenerator::file($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'checkbox':
                        $fieldsStr .= FormFieldsGenerator::checkbox($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'radio':
                        $fieldsStr .= FormFieldsGenerator::radio($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'number':
                        $fieldsStr .= FormFieldsGenerator::number($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'date':
                        $fieldsStr .= FormFieldsGenerator::date($fieldTemplate, $field) . "\n\n";
                        break;
                    case 'select':
                        $fieldsStr .= FormFieldsGenerator::select($fieldTemplate, $field) . "\n\n";
                        break;
                }
            }
            $templateData = $this->commandData->templatesHelper->getTemplate('fields.blade', $this->viewsPath);
            $templateData = str_replace('$FIELDS$', $fieldsStr, $templateData);
            $fileName = 'fields.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin field.blade.php created');
        }

        private function generateShowAdminFields()
        {
            $fieldTemplate = $this->commandData->templatesHelper->getTemplate('show_field.blade', $this->viewsPath);
            $fieldsStr = '';
            foreach ($this->commandData->inputFields as $field) {
                $singleFieldStr = str_replace('$FIELD_NAME_TITLE$', Str::title(str_replace('_', ' ', $field['fieldName'])), $fieldTemplate);
                $singleFieldStr = str_replace('$FIELD_NAME$', $field['fieldName'], $singleFieldStr);
                $singleFieldStr = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $singleFieldStr);
                $fieldsStr .= $singleFieldStr . "\n\n";
            }
            $fileName = 'show_fields.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $fieldsStr);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin show-field.blade.php created');
        }

        private function generateAdminIndex()
        {
            $templateData = $this->commandData->templatesHelper->getTemplate('index.blade', $this->viewsPath);
            $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
            if ($this->commandData->paginate) {
                $paginateTemplate = $this->commandData->templatesHelper->getTemplate('paginate.blade', 'scaffold/views/admin');
                $paginateTemplate = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $paginateTemplate);
                $templateData = str_replace('$PAGINATE$', $paginateTemplate, $templateData);
            } else {
                $templateData = str_replace('$PAGINATE$', '', $templateData);
            }
            $fileName = 'index.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin index.blade.php created');
        }

        private function generateAdminTable()
        {
            $templateData = $this->commandData->templatesHelper->getTemplate('table.blade', $this->viewsPath);
            $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
            $fileName = 'table.blade.php';
            $headerFields = '';
            foreach ($this->commandData->inputFields as $field) {
                $headerFields .= '<th>' . Str::title(str_replace('_', ' ', $field['fieldName'])) . "</th>\n\t\t\t";
            }
            $headerFields = trim($headerFields);
            $templateData = str_replace('$FIELD_HEADERS$', $headerFields, $templateData);
            $tableBodyFields = '';
            foreach ($this->commandData->inputFields as $field) {
                $tableBodyFields .= '<td>{!! $' . $this->commandData->modelNameCamel . '->' . $field['fieldName'] . " !!}</td>\n\t\t\t";
            }
            $tableBodyFields = trim($tableBodyFields);
            $templateData = str_replace('$FIELD_BODY$', $tableBodyFields, $templateData);
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin table.blade.php created');
        }

        private function generateAdminShow()
        {
            $templateData = $this->commandData->templatesHelper->getTemplate('show.blade', $this->viewsPath);
            $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
            $fileName = 'show.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin show.blade.php created');
        }

        private function generateAdminCreate()
        {
            $templateData = $this->commandData->templatesHelper->getTemplate('create.blade', $this->viewsPath);
            $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
            $fileName = 'create.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin create.blade.php created');
        }

        private function generateAdminEdit()
        {
            $templateData = $this->commandData->templatesHelper->getTemplate('edit.blade', $this->viewsPath);
            $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);
            $fileName = 'edit.blade.php';
            $path = $this->path . $fileName;
            $this->commandData->fileHelper->writeFile($path, $templateData);
            \Log::info('Admin ' . $fileName . ' Was Generated');
            $this->commandData->commandObj->info('admin edit.blade.php created');
        }
    }
