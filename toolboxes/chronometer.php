<?php

function getmicrotime() 
{ 
    // découpe le tableau de microsecondes selon les espaces     
    list($usec, $sec) = explode(" ",microtime());     
    // replace dans l'ordre     
    return ((float)$usec + (float)$sec); 
}


/** *@desc Affiche le temps écoulé (en microsecondes) depuis la dernière étape. 
* L'argument $nom_etape permet de spécifier ce qui est mesuré (ex. "page de stats" ou "requête numéro 7") */ 
function benchmark ($nom_etape) 
{ 	
    global $etape_prec; 	
    $temps_ecoule = ($etape_prec) ? round((getmicrotime() - $etape_prec)*1000) : 0; 	
    $retour = '<p class="alerte">' . $nom_etape . ' : ' . $temps_ecoule . 'ms</p>'; 	
    $etape_prec = getmicrotime(); 	
    return $retour; 
}
?>
