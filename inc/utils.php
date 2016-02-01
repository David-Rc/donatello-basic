<?php
/**
 * renvoie le 1er item d'un tableau pour lequel item[propName] = propValue
 * @param $items tableau
 * @param $propName proprieté de recherche
 * @param $propValue valeur recherchée
 * @return element du tableau || null
 */
function searchWhere ( $items, $propName, $propValue )
{
    error_log( "searchWhere $propName, $propValue" );
    foreach ( $items as $item ) {
        if ( $item[ $propName ] == $propValue )
            return $item;
    }
    return null;
}

?>