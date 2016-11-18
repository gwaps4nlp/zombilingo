% le complément en de est un objet et pas un de_obj
match {
  GOV [cat=A|V];
  DEP [lemma=de];
  GOV -[obj]-> DEP;
}
