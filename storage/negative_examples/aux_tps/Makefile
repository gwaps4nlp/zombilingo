aux_tps: ../sequoia.surf.conll
	rm -f aux_tps.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "aux_tps.pat|V" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/aux.tps	/" \
		| sed "s/$$/	Il n'y a qu'un auxiliaire passif ici/" \
		>> aux_tps.neg_items

../sequoia.surf.conll:
	wget "https://gforge.inria.fr/scm/viewvc.php/deep-sequoia/tags/sequoia-7.0/sequoia.surf.conll?view=co" -O $@

clean:
	rm -f *.neg_items