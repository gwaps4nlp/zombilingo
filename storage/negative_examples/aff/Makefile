aff: ../sequoia.surf.conll
	rm -f aff.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "a_obj.pat|GOV" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/aff	/" \
		| sed 's/$$/	La bonne relation est "Complément en ≪ à ≫"/' \
		>> aff.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "de_obj.pat|GOV" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/aff	/" \
		| sed 's/$$/	La bonne relation est "Complément en ≪ de ≫"/' \
		>> aff.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "obj.pat|GOV" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/aff	/" \
		| sed 's/$$/	La bonne relation est "Complément direct"/' \
		>> aff.neg_items

../sequoia.surf.conll:
	wget "https://gforge.inria.fr/scm/viewvc.php/deep-sequoia/tags/sequoia-7.0/sequoia.surf.conll?view=co" -O $@

clean:
	rm -f *.neg_items