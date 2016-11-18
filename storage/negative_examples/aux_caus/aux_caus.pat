% items négatifs pour la relations aux.caux
match {
  FAIRE [lemma=faire];
  OBJ [];
  FAIRE -[obj]-> OBJ;
}
without { * -[aux.caus]-> FAIRE }
