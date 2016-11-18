de_obj: ../sequoia.surf.conll
	rm -f de_obj.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "obj.pat|GOV" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/de_obj	/" \
		| sed 's/$$/	Le complément en de est un objet et pas un complément en "de" (on ne peut pas poser la question "de quoi")/' \
		>> de_obj.neg_items
	grew_dev -grep -grs ../sequoia_decl.grs -i ../sequoia.surf.conll -pattern "dep.pat|GOV" \
		| grep "00[0-9][0-9][02468]" \
		| sed "s/^/de_obj	/" \
		| sed "s/$$/	le ≪ de ≫ qui suit ne dépend pas du mot surligné mais d'un nom avant dans la phrase/" \
		>> de_obj.neg_items

../sequoia.surf.conll:
	wget "https://gforge.inria.fr/scm/viewvc.php/deep-sequoia/tags/sequoia-7.0/sequoia.surf.conll?view=co" -O $@

clean:
	rm -f *.neg_items