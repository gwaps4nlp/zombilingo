aux_caus: ../sequoia.surf.conll
	rm -f aux_caus.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "aux_caus.pat|OBJ" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/aux.caus	/" \
		| sed "s/$$/	Le mot surligné est l'objet de \"faire\"/" \
		>> aux_caus.neg_items

../sequoia.surf.conll:
	wget "https://gforge.inria.fr/scm/viewvc.php/deep-sequoia/tags/sequoia-7.0/sequoia.surf.conll?view=co" -O $@

clean:
	rm -f *.neg_items