// Instance the tour
var tour = new Tour({
  name: "basic",
  debug: true,
  template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Précédent</button><span data-role='separator'>|</span><button class='btn btn-default' data-role='next'>Suivant »</button><button class='btn btn-default' data-role='end'>Fin du tutoriel</button></div></div>",
  steps: [
  {
    element: "#logo",
    title: "Bienvenue sur Zombilingo&nbsp;!",
    content: "Ce tutoriel a pour but de t'expliquer les rudiments de ZombiLingo",
  },
  {
    element: "#information",
    title: "Le principe",
    content: "Le principe de ZombiLingo est de trouver des relations entre les mots d'une phrase. Pour plus d'informations concernant ZombiLingo et l'utilisation scientifique des données, tu peux cliquer ici.",
    placement: "left",
  },
  {
    element: "#phrase",
    title: "La phrase",
    content: "C'est ici que s'affiche la phrase dans laquelle apparaît la relation.",
  },
  {
    element: "#nom_phenomene",
    title: "La relation",
    content: "Le type de relation qu'il faut retrouver dans la phrase est indiqué ici.",
    placement: "top",
  },
  {
    element: ".highlight",
    title: "Le focus",
    content: "Le mot sur lequel porte la relation&nbsp;: par exemple, dans le cas d'un sujet, il faudra trouver le sujet du verbe souligné",
    placement: "top",
  },
  {
    element: "#phase",
    title: "Progression",
    content: "Cette barre indique l'avancement dans la série actuelle. Une série est constituée de 10 phrases.",
  },
  {
    element: ".savant",
    title: "Le professeur",
    content: "Si tu ne comprends pas ce qu'on te demande, tu peux consulter le professeur. Il t'expliquera ce que tu dois chercher et te donnera des exemples.",
    placement: "left",
  }
]});

// Initialize the tour


// // Start the tour

  tour.init();
  // tour.start(0);

