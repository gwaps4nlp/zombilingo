<?php
    if(isset($expression) && !empty($expression)){
        $total = $expression->getAttr('non_figee_mwe') + $expression->getAttr('figee_mwe');
        if($total != 0){
            $pourcentFigee = round($expression->getAttr('figee_mwe') / ($total) * 100);
            $pourcentNonFigee = 100 - $pourcentFigee;
            echo '<div class="progress">';
                echo '<div class="progress-bar progress-bar-success" style="width: '.$pourcentFigee.'%">';
                    echo '<span>Figée '.$pourcentFigee.'%</span>
                    ';
                echo '</div>';
                echo '<div class="progress-bar progress-bar-warning" style="width: '.$pourcentNonFigee.'%">';
                    echo '<span>Non figée '.$pourcentNonFigee.'%</span>';
                echo' </div>';
            echo '</div>';

            // echo '<div class="progression">';
            // echo '<div class="barFigee" style="width: '. $pourcentFigee .'%">';
            // echo $pourcentFigee . '%';
            // echo '</div>';
            // echo '<div class="barNonFigee" style="width: '. $pourcentNonFigee .'%">';
            // echo $pourcentNonFigee . '%';
            // echo '</div>';
            // echo '</div>';
        }else{
            echo "Pas d'avis pour l'instant";
        }
    }
?>

