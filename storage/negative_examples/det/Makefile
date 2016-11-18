det: ../sequoia.surf.conll
	rm -f det.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "det_noun_wo_det.pat|N" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/det	/" \
		| sed "s/$$/	Ce nom n'a pas de déterminant/" \
		>> det.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "det_clitique.pat|V" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/det	/" \
		| sed "s/$$/	Le mot surligné est un verbe et son prédécesseur est un pronom/" \
		>> det.neg_items


../sequoia.surf.conll:
	wget "https://gforge.inria.fr/scm/viewvc.php/deep-sequoia/tags/sequoia-7.0/sequoia.surf.conll?view=co" -O $@

clean:
	rm -f *.neg_items