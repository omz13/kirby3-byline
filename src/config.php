<?php

Kirby::plugin(
    'omz13/byline',
    [
      'root'         => dirname( __FILE__, 2 ),
      'options'      => [
        'author' => 'Staff Writer',  // default author name
      ],
      'translations' => [
        'en' => [
          'and'   => ' and ',
          'by'    => 'By ',
          'comma' => ', ',
        ],
        'de' => [
          'and' => ' und ',
          'by'  => 'Von ',
        ],
        'el' => [
          'and' => ' και ',
          'by'  => 'Από τον ',
        ],
        'es' => [
          'and' => ' y ',
          'by'  => 'Por ',
        ],
        'fr' => [
          'and' => ' en ',
          'by'  => 'Par ',
        ],
        'it' => [
          'and' => ' e ',
          'by'  => 'Di ',
        ],
        'nl' => [
          'and' => ' en ',
          'by'  => 'Door ',
        ],
        'sv' => [
          'and' => ' och ',
          'by'  => 'Av ',
        ],
        'zh' => [
          'and'   => '和',
          'by'    => '由',
          'comma' => '、',
        ],
      ],
      'fieldMethods' => [
        'byline'         => function ( $field ) {
            $field->value = omz13\byline\generateByline( $field, false, false );
            return $field;
        },
        'bylineLinked'   => function ( $field ) {
            $field->value = omz13\byline\generateByline( $field, true, false );
            return $field;
        },
        'bylineBy'       => function ( $field ) {
            $field->value = omz13\byline\generateByline( $field, false, true );
            return $field;
        },
        'bylineByLinked' => function ( $field ) {
            $field->value = omz13\byline\generateByline( $field, true, true );
            return $field;
        },
      ],
    ]
);

require_once __DIR__ . '/byline.php';
