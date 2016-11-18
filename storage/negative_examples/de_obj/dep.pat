match {
  GOV [cat=A|V];
  DEP [lemma=de];
  GOV.position < DEP.position;
  N [cat=N]; N -[dep]-> DEP; N.position < GOV.position;
}
without { I[]; GOV.position < I.position; I.position < DEP.position; }
without { GOV -[*]-> DEP; }
without { X[]; GOV -[de_obj]-> X; }
