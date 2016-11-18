% negatives items for the aff relation: obj

match {
  GOV [];
  DEP [cat <> D, lemma="le/lui"|en|le|y];
  GOV -[obj]-> DEP
}
without { GOV -[aff]-> * }
without { D[lemma="le/lui"|en|le|y]; GOV -[a_obj]-> D }  % avoid message ambiguity
without { D[lemma="le/lui"|en|le|y]; GOV -[de_obj]-> D }  % avoid message ambiguity
