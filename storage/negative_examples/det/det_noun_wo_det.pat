% recherche d'un nom sans déterminant

match { N [cat="N", s=c] }
without { D[]; N -[det]-> D }
without { G[cat="P+D"]; G -> N }
without { G[]; G -[dep.coord]-> N }