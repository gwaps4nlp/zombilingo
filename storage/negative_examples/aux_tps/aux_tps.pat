% negative items for the aux.tps relation 
match { AUX [lemma="avoir"|"être"]; V [cat=V]; V -[aux.pass]-> AUX; }
without { V -[aux.tps]-> * }