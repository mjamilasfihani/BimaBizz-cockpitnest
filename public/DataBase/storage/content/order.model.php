<?php
 return [
  'name' => 'order',
  'label' => 'Order Products',
  'info' => '',
  'type' => 'collection',
  'fields' => [
    0 => [
      'name' => 'items',
      'type' => 'set',
      'label' => '',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => true,
      'meta' => [
      ],
      'opts' => [
        'display' => '${data.products._id}',
        'fields' => [
          0 => [
            'name' => 'products',
            'type' => 'contentItemLink',
            'label' => 'Products',
            'info' => '',
            'group' => '',
            'i18n' => false,
            'required' => false,
            'multiple' => false,
            'meta' => [
            ],
            'opts' => [
              'link' => 'products',
              'filter' => NULL,
              'display' => '${data.title}',
            ],
          ],
          1 => [
            'name' => 'jumlah_barang',
            'type' => 'number',
            'label' => 'Jumlah Barang',
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
      ],
    ],
    1 => [
      'name' => 'totalHarga',
      'type' => 'number',
      'label' => 'Total Harga',
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
    2 => [
      'name' => 'penerima',
      'type' => 'set',
      'label' => '',
      'info' => '',
      'group' => '',
      'i18n' => false,
      'required' => false,
      'multiple' => false,
      'meta' => [
      ],
      'opts' => [
        'fields' => [
          0 => [
            'name' => 'nama',
            'type' => 'text',
            'label' => 'Nama',
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
            'name' => 'email',
            'type' => 'text',
            'label' => 'Email',
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
          2 => [
            'name' => 'noTlpn',
            'type' => 'number',
            'label' => 'No Tlpn',
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
          3 => [
            'name' => 'alamatTujuan',
            'type' => 'text',
            'label' => 'Alamat Tujuan',
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
        ],
        'display' => '${data.nama}',
      ],
    ],
  ],
  'preview' => [
  ],
  'group' => '',
  'meta' => NULL,
  '_created' => 1733553891,
  '_modified' => 1733561905,
  'color' => NULL,
  'revisions' => false,
];