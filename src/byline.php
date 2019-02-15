<?php

//phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

namespace omz13\byline;

use Kirby\Cms\Field;
use Kirby\Cms\User;

use function assert;
use function count;
use function explode;
use function kirby;
use function str_replace;
use function t;
use function ucwords;

function addBylineToStream( string &$r, string $authorEmail, bool $linked ) : int {
  $user = kirby()->users()->find( $authorEmail );
  if ( $user != null ) {
    $username = $user->name();
    if ( $username == null ) {
      # user found, but has no name, so guess from email
      $username .= getNameFromEmail( $authorEmail );
    }

    $uri = getUriForKirbyUser( $user );
    if ( $linked == true && $uri != "" ) {
      $r .= "<a href=\"" . $uri . "\">" . $username . "</a>";
    } else {
      $r .= $username;
    }
  } else {
    # user not found - guess from email address (better than just showing their email address)
    $r .= getNameFromEmail( $authorEmail );
  }//end if
  return 1;
}//end addBylineToStream()

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
}//end getUriForKirbyUser()

function getNameFromEmail( string $authorEmail ) : string {
  $emailParts = explode( "@", $authorEmail, 2 );
  return ucwords( $emailParts[0] );
}//end getNameFromEmail()

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
function generateByline( Field $field, bool $linked, bool $byPrefix ) : string {
  $r      = "";
  $countA = 0;

  if ( $byPrefix == true ) {
    $r .= t( 'by' );
  }

  if ( $field != null && $field != "" ) {
    $a = $field->toArray()[ 'author' ];
    if ( $a != null && $a[0] == '-' ) {
      // structured field
      foreach ( $field->yaml() as $k => $author ) {
        if ( $k == count( $field->yaml() ) - 1 && $countA > 0 ) {
          $r .= t( "and" ); // penultimate
        } else {
          if ( $countA > 0 ) { // not first
            $r .= t( 'comma' );
          }
        }
        $countA += addBylineToStream( $r, $author, $linked );
      }
    } else {
      // singleton
      $countA += addBylineToStream( $r, $a, $linked );
    }//end if
  }//end if

  if ( $countA == 0 ) {
    $o = kirby()->option( 'omz13.byline.author' );
    if ( $o != null ) {
      return $r . $o;
    }
  } else {
    return $r;
  }
}//end generateByline()
