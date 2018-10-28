<?php

namespace omz13\kirbyByline;

use Kirby;
use Kirby\Cms\Field;
use Kirby\Cms\User;

function addBylineToStream( string &$r, string $authorEmail, bool $linked ) : int {
  $user = kirby()->users()->find( $authorEmail );
  if ( $user != null ) {
    $username = $user->name();
    if ( $username == null ) {
      # user found, but has no name, so guess from email
      $username .= getNameFromEmail($authorEmail);
    }

    $uri = getUriForKirbyUser( $user );
    if ( $linked == true && $uri != "" ) {
      $r .= "<a href=\"" . $uri . "\">" . $username . "</a>";
    } else {
      $r .= $username;
    }
  }
  else {
    # user not found - guess from email address (better than just showing their email address)
    $r .= getNameFromEmail($authorEmail);
  }
  return 1;
}

function getUriForKirbyUser( User $user ) : string {
  assert( $user != null );
  if ( $user->website()->value() != null ) {
    return $user->website()->value();
  }

  if ( $user->twitter()->value() != null ) {
    return "https://twitter.com/" . str_replace( '@', '', $user->twitter()->value() );
  }

  if ( $user->instagram()->value() != null ) {
    return "https://instagram.com/" . str_replace( '@', '', $user->instagram()->value() );
  }

  return "";
}

function getNameFromEmail( string $authorEmail ) : string {
  $emailParts = explode( "@", $authorEmail, 2 );
  $username   = ucwords( $emailParts[0] );
  return $username;
}

function generateByline( Field $field, bool $linked, bool $byPrefix ) : string {
  $r="";
  $countA=0;

  if ($byPrefix == true) {
    $r .= t('by');
  }

  if ( $field != null && $field != "" ) {
    $a = $field->toArray()[ 'author' ];
    if ( $a != null && $a[0] == '-' ) {
      // structured field
      foreach ( $field->yaml() as $k => $author ) {
        if ( $k == count( $field->yaml() ) -1 && $countA > 0 )  {
          $r .= t("and"); // penultimate
        } else {
          if ( $countA > 0 ) { // not first
            $r .= t('comma');
          }
        }
        $countA += addBylineToStream( $r, $author, $linked );
      }
    } else {
      // singleton
      $countA += addBylineToStream( $r, $a, $linked );
    }
  }

  if ($countA==0) {
    $o = kirby()->option( 'omz13.byline.author' );
    if ( $o != null ) {
      return $r . $o;
    }
  } else {
    return $r;
  }
}

Kirby::plugin(
    'omz13/byline',
    [
      'root'     => dirname(__FILE__, 1),
      'options'  => [
        'author' => 'Staff Writer',  // default author name
      ],
      'translations' => [
        'en' => [
          'and' => ' and ',
          'by' => 'By ',
          'comma' => ', '
        ],
        'de' => [
          'and' => ' und ',
          'by' => 'Von ',
        ],
        'el' => [
          'and' => ' και ',
          'by' => 'Από τον '
        ],
        'es' => [
          'and' => ' y ',
          'by' => 'Por ',
        ],
        'fr' => [
          'and' => ' en ',
          'by' => 'Par ',
        ],
        'it' => [
          'and' => ' e ',
          'by' => 'Di ',
        ],
        'nl' => [
          'and' => ' en ',
          'by' => 'Door ',
        ],
        'sv' => [
          'and' => ' och ',
          'by' => 'Av ',
        ],
        'zh' => [
          'and' => '和',
          'by' => '由',
          'comma' => '、'
        ]
      ],
      'fieldMethods' => [
        'byline' => function ($field) {
            $field->value = generateByline( $field, false, false );
            return $field;
          },
        'bylineLinked' => function ($field) {
            $field->value = generateByline( $field, true, false );
            return $field;
          },
        'bylineBy' => function ($field) {
            $field->value = generateByline( $field, false, true );
            return $field;
          },
        'bylineByLinked' => function ($field) {
            $field->value = generateByline( $field, true, true );
            return $field;
          },
      ],
    ]
);
