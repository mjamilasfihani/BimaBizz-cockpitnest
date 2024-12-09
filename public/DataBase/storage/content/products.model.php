<?php
 return [
  'name' => 'products',
  'label' => 'Products',
  'info' => '',
  'type' => 'collection',
  'fields' => [
    0 => [
      'name' => 'seo',
      'type' => 'seo',
      'label' => 'SEO',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
      ],
    ],
    1 => [
      'name' => 'img',
      'type' => 'asset',
      'label' => 'img',
      'info' => 'max image is 4 over that will break style frontend',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => true,
      'meta' => [
      ],
      'opts' => [
      ],
    ],
    2 => [
      'name' => 'title',
      'type' => 'text',
      'label' => 'Title',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
        'multiline' => false,
        'showCount' => true,
        'readonly' => false,
        'placeholder' => NULL,
        'minlength' => NULL,
        'maxlength' => NULL,
        'list' => NULL,
      ],
    ],
    3 => [
      'name' => 'slug',
      'type' => 'text',
      'label' => 'Slug',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
        'multiline' => false,
        'showCount' => true,
        'readonly' => true,
        'placeholder' => NULL,
        'minlength' => NULL,
        'maxlength' => NULL,
        'list' => NULL,
        'slugField' => 'title',
      ],
    ],
    4 => [
      'name' => 'price',
      'type' => 'number',
      'label' => 'Price',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
      ],
    ],
    5 => [
      'name' => 'varian',
      'type' => 'set',
      'label' => 'Varian',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => true,
      'meta' => [
      ],
      'opts' => [
        'fields' => [
          0 => [
            'name' => 'nameVarian',
            'type' => 'text',
            'label' => '',
            'info' => '',
            'group' => '',
            'i18n' => false,
            'required' => false,
            'multiple' => false,
            'meta' => [
            ],
            'opts' => [
              'multiline' => false,
              'showCount' => true,
              'readonly' => false,
              'placeholder' => NULL,
              'minlength' => NULL,
              'maxlength' => NULL,
              'list' => NULL,
            ],
          ],
          1 => [
            'name' => 'jenisVarian',
            'type' => 'text',
            'label' => '',
            'info' => '',
            'group' => '',
            'i18n' => false,
            'required' => false,
            'multiple' => true,
            'meta' => [
            ],
            'opts' => [
            ],
          ],
        ],
        'display' => '${data.nameVarian}',
      ],
    ],
    6 => [
      'name' => 'description',
      'type' => 'wysiwyg',
      'label' => 'Description',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
      ],
    ],
  ],
  'preview' => [
  ],
  'group' => '',
  'meta' => NULL,
  '_created' => 1733241685,
  '_modified' => 1733456522,
  'color' => NULL,
  'revisions' => false,
];