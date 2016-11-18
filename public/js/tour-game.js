// Instance the tour
var tourA = new Tour({
  name: "adv",
  debug: true,
  template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Précédent</button><span data-role='separator'>|</span><button class='btn btn-default' data-role='next'>Suivant »</button><button class='btn btn-default' data-role='end'>Fin du tutoriel</button></div></div>",
  steps: [
  {
    element: "#phrase",
    title: "Notions avancées",
    content: "Bien joué, te voilà maintenant dans le mode de jeu réel&nbsp;! Le principe reste le même, mais maintenant tu peux gagner des cerveaux. De plus, certains éléments ont été ajoutés, en voici un bref tour d'horizon.",
  },{
    element: "#resultat",
    title: "Points",
    content: "Ici est indiqué le nombre de cerveaux que tu peux gagner si tu joues bien&nbsp;!.",
    placement: "bottom",
  },{
    element: "#profil",
    title: "Statut",
    content: "La partie statut indique ton niveau (via l'image de zombie), le nombre de cerveaux nécessaires pour passer au niveau suivant et l'argent dont tu disposes."
  },{
    element: "#refuse",
    title: "Croix d'os",
    content: "Si tu penses que la relation demandée n'existe pas dans la phrase, il te suffit de cliquer sur ce bouton pour l'indiquer. Si une majorité de joueurs pense comme toi, ta réponse vaudra plus de points.",
    placement: "left",
  },{
    element: "#passer",
    title: "Passer une phrase",
    content: "Dans le cas où tu ne souhaites pas annoter la phrase, il est possible de passer à la suivante sans soumettre de résultat. Cette fonctionnalité est limitée (nombre indiqué sur l'icône)",
    placement: "left",
  },{
    element: "#menuObjet",
    title: "La sacoche",
    content: "Dans ta sacoche se trouvent tous les objets achetés en boutique ou trouvés au cours de tes pérégrinations. Tu trouveras une description détaillée de l'effet de chaque objet dans la boutique.",
  },{
    element: "#deconnection",
    title: "Déconnexion",
    content: "Une fois que tu as fini de jouer, un clic sur cette icône te permet de te déconnecter.",
    placement: "left",
  }

]});

// Initialize the tour
 tourA.init();

// // Start the tour
// tourA.start();