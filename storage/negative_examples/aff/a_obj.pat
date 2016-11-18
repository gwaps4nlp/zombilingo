% negatives items for the aff relation: a_obj

match {
  GOV [];
  DEP [cat <> D, lemma="le/lui"|en|le|y];
  GOV -[a_obj]-> DEP
}
without { GOV -[aff]-> * }
without { D[lemma="le/lui"|en|le|y]; GOV -[obj]-> D }  % avoid message ambiguity
without { D[lemma="le/lui"|en|le|y]; GOV -[de_obj]-> D }  % avoid message ambiguity
