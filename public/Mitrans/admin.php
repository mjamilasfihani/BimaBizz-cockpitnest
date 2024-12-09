<?php

$this->bindClass('Mitrans\\Controller\\Mitrans', '/mitrans');

$this->on(
    'app.layout.init', function () {
        $this->helper('menus')->addLink(
            'modules', [
            'label'  => 'Mitrans',
            'icon'   => 'mitrans:icon.svg',
            'route'  => '/mitrans',
            'active' => false,
            'group'  => 'Mitrans',
            ]
        );
    }
);

$this->on(
    'app.permissions.collect', function ($permissions) {
        $permissions['Mitrans'] = [
            'mitrans/manage' => 'Manage Mitrans',
            'mitrans/view' => 'View Mitrans',
            'mitrans/api/access' => 'API access'
        ];
    }
);

$this->on(
    'app.permissions.collect', function ($permissions) {
        $permissions['Mitrans'] = [
        'component' => 'MitransSettings',
        'src' => 'mitrans:assets/vue-components/mitrans-permissions.js',
        'props' => [
          'models' => $this->module('content')->models()
        ]
        ];
    }
);