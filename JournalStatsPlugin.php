<?php

/**
 * Main class for journal stats plugin
 * 
 * @author Joe Simpson
 * 
 * @class JournalStatsPlugin
 *
 * @brief JournalStatsPlugin
 */

namespace APP\plugins\generic\journalStats;

use APP\core\Application;
use APP\template\TemplateManager;
use Illuminate\Support\Facades\DB;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\plugins\PluginRegistry;

class JournalStatsPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
            Hook::add( 'Form::config::after', [$this, 'contextSettings'] );
            Hook::add( 'Schema::get::context', [ $this, 'addToContextSchema' ] );
            Hook::add( 'Context::edit', [ $this, 'editContext' ] );
        }

        return $success;
    }

    // Add the journal stats to the schema return
    public function addToContextSchema(string $hookName, array $args): bool
    {
        $schema = &$args[0];

        $schema->properties->{"journalStats"} = (object)[
            'type' => 'array',
            'multilingual' => false,
            'apiSummary' => true,
            'validation' => ['nullable'],
            'items' => (object)[
                'type' => 'object',
                'properties' => (object)[
                    'key' => (object)[
                        'type' => 'string'
                    ],
                    'value' => (object)[
                        'type' => 'string'
                    ],
                ]
            ]
        ];

        return false;

    }

    // Save the Journal stats
    public function editContext(string $hookName, array $args): void
    {
        $context = $args[0];
        $params = $args[2];

        if(isset($params['journalStats'])) {
            $context->setData( 'journalStats', $params['journalStats'] );
        }

    }

    // Add the field data to be rendered by VueJS
    public function contextSettings( $hookName, &$args )
    {
        $config = &$args[0];
        if($config['id'] == 'masthead') {

            $context = $this->getRequest()->getContext();

            $value = $context->getData('journalStats') ?? [];
            foreach($value as $k => &$val) {
                $val['_id'] = $k;
            }

            $config['fields'][] = [
                'name' => 'journalStats',
                'component' => 'field-keyvalues',
                'label' => 'Journal Stats',
                'groupId' => 'identity',
                'value' => $value,
                'inputType' => 'text',
            ];


            $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
            $templateMgr->addJavaScript(
                'field-keyvalue-component',
                Application::get()->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/js/FieldKeyValue.js',
                [
                    'contexts' => 'backend',
                    'priority' => TemplateManager::STYLE_SEQUENCE_LAST,
                ]
            );

        }
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
    {
        return 'Journal Stats';
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return 'This plugin allows journals to provide some top-level stats in their themes.';
    }

}
