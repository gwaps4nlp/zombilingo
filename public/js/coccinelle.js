var coccinelle = {
    objet : $('#coccinelle'),
    direction : [-1,1],
    vitesse : 1.5,
    deplacement : 0,
    directions : [[-1,1], [1,1], [1,-1], [-1,-1], [0,1], [1,0], [-1,0], [0,-1]],
    delai : 13
}

function move(){
    var x = Math.round(coccinelle.objet.offset().left);
    var y = Math.round(coccinelle.objet.offset().top);
    coccinelle.deplacement += coccinelle.vitesse;
    if(coccinelle.deplacement >= 75){
        coccinelle.deplacement = 0;
        var rand = Math.floor(Math.random() * 8);
        coccinelle.direction = coccinelle.directions[rand];
    }
    x += coccinelle.direction[0] * coccinelle.vitesse;
    y += coccinelle.direction[1] * coccinelle.vitesse;
    if(x <= 0){
        x += 2 * coccinelle.vitesse;
    }
    if(y <= 0){
        y += 2 * coccinelle.vitesse;
    }
    if(x >= $(window).width() - 150){
        x -= 2 * coccinelle.vitesse;
    }
    if(y >= $(window).height() - 150){
        y -= 2 * coccinelle.vitesse;
    }
    coccinelle.objet.css({
        'top' : y,
        'left' : x
    });
}

setInterval(function(){move()}, coccinelle.delai);
