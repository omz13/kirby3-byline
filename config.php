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
      $emailParts = explode( "@", $user->email(), 2 );
      $username   = ucwords( $emailParts[0] );
    }

    $uri = getUriForKirbyUser( $user );
    if ( $linked == true && $uri != "" ) {
      $r .= "<a href=\"" . $uri . "\">" . $username . "</a>";
    } else {
      $r .= $username;
    }
  }
  else {
    $r .= $authorEmail;
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

function generateByline( Field $field, bool $linked ) : string {
  $r="";
  $countA=0;

  if ( $field != null && $field != "" ) {
    $a = $field->toArray()[ 'author' ];
    if ( $a != null && $a[0] == '-' ) {
      // structured field
      foreach ( $field->yaml() as $k => $author ) {
        if ( $k == count( $field->yaml() ) -1 && $countA > 0 )  {
          $r .= " and "; // penultimate
        } else {
          if ( $countA > 0 ) { // not first
            $r .= ", ";
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
      return $o;
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
      'fieldMethods' => [
        'byline' => function ($field) {
            // modify field to allow for chaining
            $field->value = generateByline( $field, false );
            return $field;
          },
        'bylineLinked' => function ($field) {
            // modify field to allow for chaining
            $field->value = generateByline( $field, true );
            return $field;
          },

      ],
    ]
);
