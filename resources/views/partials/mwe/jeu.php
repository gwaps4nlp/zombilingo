

<div class="row">
    <div class="col-md-10 col-md-offset-1 center text-center" id="blocJeu">
        <!-- <div id="aidePetit">
            <h3>Aide?</h3>
        </div>
        <div class="col-md-2 col-md-offset-9" id="aide">
            <h3>Besoin d'aide?<br />Demande moi</h3>
            <?php
                echo img('bulle.png');
            ?>
        </div> -->
        <!-- <div class="col-md-offset-11 savant"> -->
        <!-- </div> -->

        <div class="row">
            <div id="bloc-mwe" class="col-md-8 col-md-offset-2 text-center">
    
                <?php
                   if(isset($contenuJeu) && !empty($contenuJeu)){
                        echo_content($contenuJeu);
                    }
                ?>
           </div>
    
            <div class="col-md-1 col-md-offset-1">
                <div class="savant aideTool">
                    <div class="aideTip">
                        Une expression "figée" est un ensemble de mots dont le sens ne peut pas être déduit du sens de chaque mot.
                        Par exemple, un "cordon bleu" n'est pas un cordon et n'est pas bleu.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>