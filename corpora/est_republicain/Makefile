parse:
	find . -name "*.txt" -type f -print | xargs -I {} basename {} .txt | xargs -I {} make "{}.conll"

%.conll: %.txt
	../tools/run_talismane `basename $< .txt`
